<?php
/*
 * DiplomaEleitoralService.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade do CAU/BR.
 * Não é permitida a distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Services;

use App\Config\Constants;
use App\Exceptions\NegocioException;
use App\Models\Conselheiro;
use App\Models\Declaracao;
use Illuminate\Http\Request;
use App\Service\AbstractService;
use Exception;
use App\Models\DiplomaTermo;
use App\Models\DiplomaTermoAssinatura;
use App\Util\ImageUtils;
use App\Util\Utils;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Endroid\QrCode\QrCode;

/**
 * Classe responsável por encapsular as implementações de serviço referente à entidade 'Diploma Eleitoral'.
 */
class DiplomaEleitoralService extends AbstractService
{

    public function __construct()
    {
    }

    /**
     * Cadastra o DiplomaEleitoral
     *
     * @param Request $request
     * @return DiplomaEleitoral
     * @throws NegocioException
     */
    public function create(Request $request): DiplomaTermo
    {   
        DB::beginTransaction();
        try {
            if (!$request['diploma']['idConselheiro']) {
                $conselheiroService = app()->make(ConselheiroService::class);
                $conselheiro = $conselheiroService->create($request);
            }
            $requestDiploma = [
                'conselheiro_id' => $request['diploma']['idConselheiro'] ? $request['diploma']['idConselheiro'] : $conselheiro['id'],
                'dt_eleicao' => date($request['diploma']['diaRealizacao'] . '-' . $request['diploma']['mesRealizacao'] . '-' . $request['diploma']['anoRealizacao']),
                'dt_eleicao2' => $request['diploma']['diaRealizacao2'] > 0 ? date($request['diploma']['diaRealizacao2'] . '-' . $request['diploma']['mesRealizacao'] . '-' . $request['diploma']['anoRealizacao']) : null,
                'numero_resolucao' => intval($request['diploma']['numeroResolucao']),
                'dt_resolucao' => date($request['diploma']['diaResolucao'] . '-' . $request['diploma']['mesResolucao'] . '-' . $request['diploma']['anoResolucao']),
                'nome_arquiteto' => $request['diploma']['nomeConselheiro'],
                'dt_emissao' => date($request['diploma']['diaEmissao'] . '-' . $request['diploma']['mesEmissao'] . '-' . $request['diploma']['anoEmissao']),
                'preposicao_cidade' => null,
                'cidade_emissao' => $request['diploma']['cidadeEmissao'],
                'uf_emissao' => $request['diploma']['UfEmissao'],
                'cpf_coordenador' => $request['diploma']['cpfCoordenador'],
                'nome_coordenador' => $request['diploma']['nomeCoordenador'],
                'cpf_presidente' => null,
                'nome_presidente' => null,
                'uf_presidente' => null,
                'uf_comissao' => $request['diploma']['UfComissao'] == 'CAU/BR' ? 'BR' : $request['diploma']['UfComissao'],
                'tipo_diploma_termo' => Constants::TIPO_DIPLOMA,
                'dt_cadastro' => date('Y-m-d H:i:s'),
                'co_autenticidade' => null,
                'acessor_cen' => $request['cen']
            ];
           
            $diploma = DiplomaTermo::create($requestDiploma);
            if (!empty($request['diploma']['assinatura']['arquivo'])) {
                $this->createAssinatura($diploma->id, $request['diploma']['assinatura']);
            }

            $this->salvarDescricaoDocumento($diploma->id, $request['ufLogado']);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $diploma;
    }

    public function createAssinatura($idDiploma, $assinatura)
    {
        $dadosArquivos = [
            'nome' => $assinatura['nome'],
            'arquivo' => $assinatura['arquivo'],
            'nomeFisico' => $this->createNomeArquivo($assinatura),
            'tamanho' => 1
        ];
        $this->salvarArquivoDiretorio($dadosArquivos);

        DiplomaTermoAssinatura::create([
            'nome_arquivo' => $assinatura['nome'],
            'nome_gerado' => $dadosArquivos['nomeFisico'],
            'descricao' => null,
            'caminho' => Constants::PATH_STORAGE_ARQUIVO_ASSINATURA,
            'dt_cadastro' => date('Y-m-d H:i:s'),
            'diploma_termo_id' => $idDiploma
        ]);
    }

    public function createNomeArquivo($arquivo)
    {
        $nomeArquivo = self::getNomeArquivoFormatado(
            $arquivo['nome'],
            Constants::PREFIXO_ELEITORAL_ARQUIVO
        );
       
        return $nomeArquivo;
    }

    /**
     * Retorna o nome do arquivo considerando o seguinte padrão:
     * <i>[prefixo]_[epoctime].[extensao]</i>
     * <b>ex:</b>
     * doc_comprobatorio_123456665.jpg
     *
     * @param string $nomeOriginal
     * @param string $prefixo
     *
     * @return string
     */
    public function getNomeArquivoFormatado($nomeOriginal, $prefixo): string
    {
        $extensao = Utils::getExtensaoArquivoPorNome($nomeOriginal);

        return $prefixo . '_' . rand(1111111111, mt_getrandmax()) . '.' . $extensao;
    }

    /**
     * Responsável por salvar os arquivos no diretório
     *
     * @param $idAlegacaoFinal
     */
    public function salvarArquivoDiretorio($arquivo)
    {
        $data = explode(',', $arquivo['arquivo']);

        if(!empty($data) && count($data) > 1){
            $bin = base64_decode($data[1]);

            $size = getImageSizeFromString($bin);

            if (!(empty($size['mime']) || strpos($size['mime'], 'image/') !== 0)) {
                $pathArquivo = getenv('SICCAU_STORAGE_PATH') . '/' . Constants::PATH_STORAGE_ARQUIVO_ASSINATURA;

                $filename = $arquivo['nomeFisico'];
       
                if (!is_dir($pathArquivo)) {
                    mkdir($pathArquivo, 0777);
                }
                
                $file = fopen($pathArquivo .'/'. $filename, "w+");
      
                fwrite($file, $bin);
                fclose($file);
            }
        }
    }
    
    /**
     * Salva a descrição do documento no banco de dados
     *
     * @param int $id
     * @return Array
     */
    public function salvarDescricaoDocumento(int $idDiploma, $ufLogado): void
    {
        $codigoAutenticidade = Utils::gerarCodigoAutenticidade($idDiploma, Constants::TIPO_DIPLOMA);
        DiplomaTermo::where('id', $idDiploma)->update(['co_autenticidade' => $codigoAutenticidade]);
        
        $diplomaEleitoral = DiplomaTermo::where('id', $idDiploma)
            ->with('conselheiro.profissional')
            ->with('assinatura')
            ->first();

            $imagemCauUf = $diplomaEleitoral->acessor_cen || $ufLogado == 'DF' ? 'images/cabecalho_BR.jpg' : getenv('SICCAU_STORAGE_PATH') . '/logos/CAU_' .  $ufLogado . '.jpg';
            $logo = ImageUtils::getImageBase64($imagemCauUf);

        $assinatura = !empty($diplomaEleitoral->assinatura) ?
            getenv('SICCAU_STORAGE_PATH') . '/' . Constants::PATH_STORAGE_ARQUIVO_ASSINATURA . '/' . $diplomaEleitoral->assinatura->nome_gerado : null;
        $assinatura = ImageUtils::getImageBase64($assinatura);
        
        $qrCode = new QrCode(getenv('URL_ACESSO_FRONTEND') . '/autenticidade/diploma-eleitoral?registroProfissional=' . $diplomaEleitoral['conselheiro']['profissional']['registro_nacional'] . '&codigoAutenticidade='.$codigoAutenticidade);
        $qrCode->setSize(100);
        $imgQRCode = imagecreatefromstring($qrCode->writeString());
        $pathImage = 'images/';
        imagejpeg($imgQRCode, $pathImage . 'qrcode-footer.jpg');
        $footerQrCode = ImageUtils::getImageBase64($pathImage . '/qrcode-footer.jpg');

        $dataResolucao = Utils::getDataDesmembrada($diplomaEleitoral->dt_resolucao);
        $dataRealizacao = Utils::getDataDesmembrada($diplomaEleitoral->dt_eleicao);
        $dataEmissao = Utils::getDataDesmembrada($diplomaEleitoral->dt_emissao);

        $texto = Declaracao::find(Constants::ID_DECLARACAO_DIPLOMA);
        $texto = str_replace(
            [
                '{Nacional ou Unidade da Federação}',
                '{CEN ou CE-UF}',
                '{nº}',
                '{dia}',
                '{mês}',
                '{aaaa}',
                '{NOME}',
                '{Cidade}',
                '{UF}',
                '{Nome do(a) coordenador(a)}',
                '{CE-UF ou CEN}',
                '{tipoConselheiro}',
                '{diaRealizacao}',
                '{mesRealizacao}',
                '{anoRealizacao}',
                '{diaEmissao}',
                '{mesEmissao}',
                '{anoEmissao}',
                '{localConselho}'
            ],
            [
                $this->getComissaoEleitoral($diplomaEleitoral),
                $diplomaEleitoral->uf_comissao == 'BR' ? 'CEN-CAU/BR' : 'CE-' . $diplomaEleitoral->uf_comissao,
                $diplomaEleitoral->numero_resolucao,
                $dataResolucao[2],
                Utils::monthNumberToStringEncode($dataResolucao[1]),
                $dataResolucao[0],
                $diplomaEleitoral->nome_arquiteto,
                $diplomaEleitoral->cidade_emissao,
                $diplomaEleitoral->uf_emissao,
                $diplomaEleitoral->nome_coordenador,
                $diplomaEleitoral->uf_comissao == 'BR' ? 'CEN-CAU/BR' : 'CE-' . $diplomaEleitoral->uf_comissao,
                $diplomaEleitoral->conselheiro->tipo_conselheiro_id == 1 ? 'Titular' : 'Suplente',
                $this->getDiaRealizacao($diplomaEleitoral),
                Utils::monthNumberToStringEncode($dataRealizacao[1]),
                $dataRealizacao[0],
                $dataEmissao[2],
                Utils::monthNumberToStringEncode($dataEmissao[1]),
                $dataEmissao[0],
                $this->getLocalConselheiro($diplomaEleitoral),
            ],
            $texto->ds_texto_inicial
        );


        $comissao = $diplomaEleitoral->uf_comissao == 'BR' ||  $diplomaEleitoral->uf_comissao == 'CAU/BR' ? 'CEN-CAU/BR' : 'CE-' . $diplomaEleitoral->uf_comissao;
        $html = "<div id='cabecalho'><img width='100%' src='$logo'></div><div class='diploma'><br/><strong class='tituloDiploma'>DIPLOMA</strong><span>$texto</span><img src='$assinatura' class='imgAssinatura'><p>$diplomaEleitoral->nome_coordenador<br>Coordenador(a) da $comissao</p><div class='codigoAutenticador'><img src='$footerQrCode' class='qrcode'/><br>Código autenticador:$codigoAutenticidade</div></div>";

        DiplomaTermo::where('id', $idDiploma)->update(['descricao_documento' => $html]);
    }

    /**
     * Busca dias da realização
     *
     */
    public function getDiaRealizacao($diplomaEleitoral)
    {  
        $dataRealizacao = Utils::getDataDesmembrada($diplomaEleitoral->dt_eleicao);
        $dataRealizacao2 = Utils::getDataDesmembrada($diplomaEleitoral->dt_eleicao_2);

        return $diplomaEleitoral->dt_eleicao_2 ? $dataRealizacao[2] . ' e ' . $dataRealizacao2[2] : $dataRealizacao[2];
    }

    /**
     * Busca o Diploma Eleitoral pelo Id
     *
     * @param int $id
     * @return Array
     */
    public function getById(int $id): Array
    {
        $diplomaEleitoral = DiplomaTermo::where('id', $id)
            ->with('assinatura')
            ->first();

        return [
            'diploma' => $diplomaEleitoral,
            'assinatura' => [
                'base64' => $diplomaEleitoral['assinatura'] ? $this->getImagem($diplomaEleitoral['assinatura']) : null,
                'nome' => $diplomaEleitoral['assinatura']['nome_arquivo']
            ]
        ];
    }

    /**
     * Busca a imagem formato Base64
     *
     * @param $id
     */
    public function getImagem($assinatura)
    {
        $path = getenv('SICCAU_STORAGE_PATH') . '/' . Constants::PATH_STORAGE_ARQUIVO_ASSINATURA . '/' . $assinatura['nome_gerado'];
        $imagemBase64 = ImageUtils::getImageBase64($path);
       
        return $imagemBase64;
    }
    

    /**
     * Atualizar o Diploma Eleitoral
     *
     * @param Request $request
     * @param int $id
     * @return Array
     * @throws NegocioException
     */
    public function update(int $id, Request $request): Array
    {   
        DB::beginTransaction();
        try {
            Conselheiro::where('id', $request['membro']['idConselheiro'])
                ->update(['ies' => $request['membro']['idRepresentacao'] == 3 ? true : false]);
                
            $requestDiploma = [
                'conselheiro_id' => $request['diploma']['idConselheiro'],
                'dt_eleicao' => date($request['diploma']['diaRealizacao'] . '-' . $request['diploma']['mesRealizacao'] . '-' . $request['diploma']['anoRealizacao']),
                'dt_eleicao_2' => $request['diploma']['diaRealizacao2'] > 0 ? date($request['diploma']['diaRealizacao2'] . '-' . $request['diploma']['mesRealizacao'] . '-' . $request['diploma']['anoRealizacao']) : null,
                'numero_resolucao' => intval($request['diploma']['numeroResolucao']),
                'dt_resolucao' => date($request['diploma']['diaResolucao'] . '-' . $request['diploma']['mesResolucao'] . '-' . $request['diploma']['anoResolucao']),
                'nome_arquiteto' => $request['diploma']['nomeConselheiro'],
                'dt_emissao' => date($request['diploma']['diaEmissao'] . '-' . $request['diploma']['mesEmissao'] . '-' . $request['diploma']['anoEmissao']),
                'preposicao_cidade' => null,
                'cidade_emissao' => $request['diploma']['cidadeEmissao'],
                'uf_emissao' => $request['diploma']['UfEmissao'],
                'cpf_coordenador' => $request['diploma']['cpfCoordenador'],
                'nome_coordenador' => $request['diploma']['nomeCoordenador'],
                'cpf_presidente' => null,
                'nome_presidente' => null,
                'uf_presidente' => null,
                'uf_comissao' => $request['diploma']['UfComissao'] == 'CAU/BR' ? 'BR' : $request['diploma']['UfComissao'],
                'tipo_diploma_termo' => Constants::TIPO_DIPLOMA,
                'dt_cadastro' => date('Y-m-d H:i:s'),
                'co_autenticidade' => null,
                'acessor_cen' => $request['cen']
            ];
            
            DiplomaTermo::where('id', $id)->update($requestDiploma);

            DiplomaTermoAssinatura::where('diploma_termo_id', $id)->delete();
            if (!empty($request['diploma']['assinatura']['arquivo'])) {
                $this->createAssinatura($id, $request['diploma']['assinatura']);
            }

            $this->salvarDescricaoDocumento($id, $request['ufLogado']);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
       
        return $this->getById($id);
    }

    /**
     * Imprimir Diploma Eleitoral
     *
     * @param int $id
     */
    public function imprimir(int $idDiploma)
    {
        $diploma = DiplomaTermo::where('id', $idDiploma)->first();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('pdf.diploma',
            [
                'html' => $diploma->descricao_documento
            ]))
            ->setPaper('A4', 'landscape');

        return $pdf->stream();
    }

    /**
     * Monta texto de comissão eleitoral
     *
     * @param int $id
     */
    public function getLocalConselheiro($diplomaEleitoral) 
    {
        $conselheiro = Conselheiro::find($diplomaEleitoral->conselheiro_id);
        if ($conselheiro->representacao_conselheiro_id == 1 || $conselheiro->ies) {
            return 'do Brasil';
        }
        return $this->getComissaoEleitoral($diplomaEleitoral);
    }

    /**
     * Monta texto de comissão eleitoral
     *
     * @param int $id
     */
    public function getComissaoEleitoral($diplomaEleitoral) 
    {
        
        return $this->getEstadoByUf($diplomaEleitoral->uf_comissao, true);
    }

    /**
     * Busca nome do estado com preposição
     *
     * @param int $id
     */
    function getEstadoByUf($uf, $exigePreposicao = false, $retornaUf = false)
    {
        $ufs = array(
            'N' => 'Nacional',
            'BR' => 'Nacional',
            'AC'=>'Acre',
            'AL'=>'Alagoas',
            'AP'=>'Amapá',
            'AM'=>'Amazonas',
            'BA'=>'Bahia',
            'CE'=>'Ceará',
            'DF'=>'Distrito Federal',
            'ES'=>'Espírito Santo',
            'GO'=>'Goiás',
            'MA'=>'Maranhão',
            'MT'=>'Mato Grosso',
            'MS'=>'Mato Grosso do Sul',
            'MG'=>'Minas Gerais',
            'PA'=>'Pará',
            'PB'=>'Paraíba',
            'PR'=>'Paraná',
            'PE'=>'Pernambuco',
            'PI'=>'Piauí',
            'RJ'=>'Rio de Janeiro',
            'RN'=>'Rio Grande do Norte',
            'RS'=>'Rio Grande do Sul',
            'RO'=>'Rondônia',
            'RR'=>'Roraima',
            'SC'=>'Santa Catarina',
            'SP'=>'São Paulo',
            'SE'=>'Sergipe',
            'TO'=>'Tocantins'
        );

        if (in_array($uf, array('BR', 'AC', 'AP', 'AM', 'CE', 'DF', 'ES', 'MA', 'PA', 'PR', 'PI', 'RJ', 'RN', 'RS', 'TO'))) {
        	$preposição = 'do ';
        } elseif (in_array($uf, array('BA', 'PB'))) {
            $preposição = 'da ';
		} else {
            $preposição = 'de ';
		}

        if (in_array($uf, array('BR'))) {
            $preposição = '';
        }

		if ($retornaUf && $exigePreposicao && $uf != 'N') {
			return $preposição . $uf;
		}

		if($exigePreposicao && $uf != 'N') {
            return $preposição . $ufs[$uf];
		}

        return $ufs[$uf];
    }
}
