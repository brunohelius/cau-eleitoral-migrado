<?php
/*
 * TermoDePosseService.php
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
use App\To\ArquivoGenericoTO;
use App\Util\ImageUtils;
use App\Util\Utils;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Endroid\QrCode\QrCode;

/**
 * Classe responsável por encapsular as implementações de serviço referente à entidade 'Termo de Posse'.
 */
class TermoDePosseService extends AbstractService
{

    public function __construct()
    {
    }

    /**
     * Cadastra o Termo de Posse
     *
     * @param Request $request
     * @return DiplomaTermo
     * @throws NegocioException
     */
    public function create(Request $request): DiplomaTermo
    {   
        DB::beginTransaction();
        try {
            if (!$request['termo']['idConselheiro']) {
                $conselheiroService = app()->make(ConselheiroService::class);
                $conselheiro = $conselheiroService->create($request);
            }

            $requestTermo = [
                'conselheiro_id' => $request['termo']['idConselheiro'] ? $request['termo']['idConselheiro'] : $conselheiro['id'],
                'dt_eleicao' => date($request['termo']['diaRealizacao'] . '-' . $request['termo']['mesRealizacao'] . '-' . $request['termo']['anoRealizacao']),
                'dt_eleicao_2' => $request['termo']['diaRealizacao2'] > 0 ? date($request['termo']['diaRealizacao2'] . '-' . $request['termo']['mesRealizacao'] . '-' . $request['termo']['anoRealizacao']) : null,
                'numero_resolucao' => intval($request['termo']['numeroResolucao']),
                'dt_resolucao' => date($request['termo']['diaResolucao'] . '-' . $request['termo']['mesResolucao'] . '-' . $request['termo']['anoResolucao']),
                'nome_arquiteto' => $request['termo']['nomeConselheiro'],
                'dt_emissao' => date($request['termo']['diaEmissao'] . '-' . $request['termo']['mesEmissao'] . '-' . $request['termo']['anoEmissao']),
                'preposicao_cidade' => $request['termo']['preposicao'],
                'cidade_emissao' => $request['termo']['cidadeEmissao'],
                'uf_emissao' => $request['termo']['UfEmissao'],
                'cpf_coordenador' => null,
                'nome_coordenador' => null,
                'cpf_presidente' => $request['termo']['cpfPresidente'],
                'nome_presidente' => $request['termo']['nomePresidente'],
                'uf_presidente' => $request['termo']['ufPresidente'] == 'CAU/BR' ? 'BR' : $request['termo']['ufPresidente'],
                'uf_comissao' => $request['termo']['UfConselheiro'],
                'tipo_diploma_termo' => Constants::TIPO_TERMO_POSSE,
                'dt_cadastro' => date('Y-m-d H:i:s'),
                'co_autenticidade' => null,
                'acessor_cen' => $request['cen']
            ];
         
            $termo = DiplomaTermo::create($requestTermo);
            if (!empty($request['termo']['assinatura']['arquivo'])) {
                $this->createAssinatura($termo->id, $request['termo']['assinatura']);
            }

            $this->salvarDescricaoDocumento($termo->id, $request['ufLogado']);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $termo;
    }

    public function createAssinatura($idTermo, $assinatura)
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
            'diploma_termo_id' => $idTermo
        ]);
    }

    public function createNomeArquivo($assinatura)
    {
        $nomeArquivo = self::getNomeArquivoFormatado(
            $assinatura['nome'],
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
     * @param ArquivoGenericoTO[] $arquivos
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
     * Salvar texto do documento no banco
     *
     * @param int $id
     */
    public function salvarDescricaoDocumento(int $idTermo, $ufLogado): void
    {
        $codigoAutenticidade = Utils::gerarCodigoAutenticidade($idTermo, Constants::TIPO_DIPLOMA);
        DiplomaTermo::where('id', $idTermo)->update(['co_autenticidade' => $codigoAutenticidade]);
        
        $termoDePosse = DiplomaTermo::where('id', $idTermo)
            ->with('conselheiro.filial')
            ->with('conselheiro.profissional')
            ->with('assinatura')
            ->first();

        $imagemCauUf = $termoDePosse->acessor_cen || $ufLogado == 'DF' ? 'images/cabecalho_BR.jpg' : getenv('SICCAU_STORAGE_PATH') . '/logos/CAU_' .  $ufLogado . '.jpg';
        $logo = ImageUtils::getImageBase64($imagemCauUf);

        $assinatura = !empty($termoDePosse->assinatura) ?
            getenv('SICCAU_STORAGE_PATH') . '/' . Constants::PATH_STORAGE_ARQUIVO_ASSINATURA . '/' . $termoDePosse->assinatura->nome_gerado : null;
        $assinatura = ImageUtils::getImageBase64($assinatura);
        
        $qrCode = new QrCode(getenv('URL_ACESSO_FRONTEND') . '/autenticidade/termo-de-posse?registroProfissional=' . $termoDePosse['conselheiro']['profissional']['registro_nacional'] . '&codigoAutenticidade='.$codigoAutenticidade);
        $qrCode->setSize(100);
        $imgQRCode = imagecreatefromstring($qrCode->writeString());
        $pathImage = 'images/';
        imagejpeg($imgQRCode, $pathImage . 'qrcode-footer.jpg');
        $footerQrCode = ImageUtils::getImageBase64($pathImage . '/qrcode-footer.jpg');

        $dataEmissao =  Utils::getDataDesmembrada($termoDePosse->dt_emissao);
        $dataResolucao =  Utils::getDataDesmembrada($termoDePosse->dt_resolucao);
        $dataRealizacao =  Utils::getDataDesmembrada($termoDePosse->dt_eleicao);

        $texto = Declaracao::find(Constants::ID_DECLARACAO_TERMO);
        $texto = str_replace(
            [
                '{dia}',
                '{mes}',
                '{ano}',
                '{de}',
                '{Cidade}',
                '{UF}',
                '{diaEleicao}',
                '{nº}',
                '{diaResolucao}',
                '{mêsResolucao}',
                '{aaaaResolucao}',
                '{NOME}',
                '{tipoConselheiro}',
                '{diaRealizacao}',
                '{mesRealizacao}',
                '{anoRealizacao}',
                '{localConselheiro}',
                '{ufConselheiro}',
                '{localCandidato}'
            ],
            [
                $dataEmissao[2],
                Utils::monthNumberToStringEncode($dataEmissao[1]),
                $dataEmissao[0],
                $termoDePosse->preposicao_cidade,
                $termoDePosse->cidade_emissao,
                $termoDePosse->uf_emissao,
                $dataEmissao[2],
                $termoDePosse->numero_resolucao,
                $dataResolucao[2],
                Utils::monthNumberToStringEncode($dataResolucao[1]),
                $dataResolucao[0],
                $termoDePosse->nome_arquiteto,
                $termoDePosse->conselheiro->tipo_conselheiro_id == 1 ? 'Titular' : 'Suplente',
                $this->getDiaRealizacao($termoDePosse),
                Utils::monthNumberToStringEncode($dataRealizacao[1]),
                $dataRealizacao[0],
                $this->getLocalConselheiro($termoDePosse),
                $this->getEstadoByUf($termoDePosse->uf_comissao, true),
                $this->getLocalCandidato($termoDePosse, true),
            ],
            $texto->ds_texto_inicial
        );

        $conselheiro = Conselheiro::find($termoDePosse->conselheiro_id);
        $filial = $conselheiro->representacao_conselheiro_id == 1 || $conselheiro->ies ? 'BR' : $termoDePosse->conselheiro->filial->prefixo;  
        
        $comissao = $termoDePosse->uf_presidente != 'CAU/BR' ? 'CAU/' . $termoDePosse->uf_presidente : 'CAU/BR';

        $html = "<div id='cabecalho'> <img width='100%' src='$logo'> </div> <div class='diploma'> <br/> <strong class='tituloDiploma'>TERMO DE POSSE</strong> <span> $texto </span>";

        $assinaturas = "<table class='tabelaAssinatura'> <tr> <td> <img src='$assinatura' class='imgAssinatura'> <p> $termoDePosse->nome_presidente <br> Presidente do $comissao </p> </td> <td> <p> $termoDePosse->nome_arquiteto <br> Conselheiro(a) do CAU/$filial </p> </td> </tr> </table>
        <div class='codigoAutenticador'><img src='$footerQrCode' class='qrcode'/><br>Código autenticador:$codigoAutenticidade</div></div>";

        DiplomaTermo::where('id', $idTermo)->update(['descricao_documento' => $html . ' ' .$assinaturas]);
    }

    /**
     * Busca o Termo de posse pelo Id
     *
     * @param int $id
     * @return Array
     */
    public function getById(int $id): Array
    {
        $TermoDePosse = DiplomaTermo::where('id', $id)
            ->with('assinatura')
            ->first();
        
        return [
            'termo' => $TermoDePosse,
            'assinatura' => [
                'base64' => $TermoDePosse['assinatura'] ? $this->getImagem($TermoDePosse['assinatura']) : null,
                'nome' => $TermoDePosse['assinatura']['nome_arquivo']
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
     * Atualizar o Termo de Posse
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

            $requestTermo = [
                'conselheiro_id' => $request['termo']['idConselheiro'],
                'dt_eleicao' => date($request['termo']['diaRealizacao'] . '-' . $request['termo']['mesRealizacao'] . '-' . $request['termo']['anoRealizacao']),
                'dt_eleicao_2' => $request['termo']['diaRealizacao2'] > 0 ? date($request['termo']['diaRealizacao2'] . '-' . $request['termo']['mesRealizacao'] . '-' . $request['termo']['anoRealizacao']) : null,
                'numero_resolucao' => intval($request['termo']['numeroResolucao']),
                'dt_resolucao' => date($request['termo']['diaResolucao'] . '-' . $request['termo']['mesResolucao'] . '-' . $request['termo']['anoResolucao']),
                'nome_arquiteto' => $request['termo']['nomeConselheiro'],
                'dt_emissao' => date($request['termo']['diaEmissao'] . '-' . $request['termo']['mesEmissao'] . '-' . $request['termo']['anoEmissao']),
                'preposicao_cidade' => $request['termo']['preposicao'],
                'cidade_emissao' => $request['termo']['cidadeEmissao'],
                'uf_emissao' => $request['termo']['UfEmissao'],
                'cpf_coordenador' => null,
                'nome_coordenador' => null,
                'cpf_presidente' => $request['termo']['cpfPresidente'],
                'nome_presidente' => $request['termo']['nomePresidente'],
                'uf_presidente' => $request['termo']['ufPresidente'] == 'CAU/BR' ? 'BR' : $request['termo']['ufPresidente'],
                'uf_comissao' => $request['termo']['UfConselheiro'],
                'tipo_diploma_termo' => Constants::TIPO_TERMO_POSSE,
                'dt_cadastro' => date('Y-m-d H:i:s'),
                'co_autenticidade' => null,
                'acessor_cen' => $request['cen']
            ];
            
            DiplomaTermo::where('id', $id)->update($requestTermo);
            DiplomaTermoAssinatura::where('diploma_termo_id', $id)->delete();

            if (!empty($request['termo']['assinatura']['arquivo'])) {
                $this->createAssinatura($id, $request['termo']['assinatura']);
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
     * Imprimir Termo de Posse
     *
     * @param int $id
     */
    public function imprimir(int $idTermo)
    {   
        $termoDePosse = DiplomaTermo::where('id', $idTermo)->first();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('pdf.termo',
            [
                'html' => $termoDePosse->descricao_documento
            ]))
            ->setPaper('A4', 'landscape');

        return $pdf->stream();
    }

     /**
     * Busca dias da realização
     *
     */
    public function getDiaRealizacao($termoDePosse)
    {  
        $dataRealizacao = Utils::getDataDesmembrada($termoDePosse->dt_eleicao);
        $dataRealizacao2 = Utils::getDataDesmembrada($termoDePosse->dt_eleicao_2);

        return $termoDePosse->dt_eleicao_2 ? $dataRealizacao[2] . ' e ' . $dataRealizacao2[2] : $dataRealizacao[2];
    }

    /**
     * Monta texto do local do conselheiro
     *
     * @param int $id
     */
    public function getLocalConselheiro($termoDePosse) 
    {
        $conselheiro = Conselheiro::find($termoDePosse->conselheiro_id);
        if ($conselheiro->representacao_conselheiro_id == 1 || $conselheiro->ies) {
            return 'do Brasil';
        }
        return $this->getEstadoByUf($termoDePosse->uf_comissao, true);
    }

    /**
     * Monta texto do local do conselheiro
     *
     * @param int $id
     */
    public function getLocalCandidato($termoDePosse) 
    {
        $conselheiro = Conselheiro::find($termoDePosse->conselheiro_id);
        if ($conselheiro->ies) {
            return 'Nacional';
        }
        
        return $this->getEstadoByUf($termoDePosse->uf_comissao, true);
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
