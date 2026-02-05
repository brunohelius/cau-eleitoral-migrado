<?php


namespace App\Business;

use App\Config\Constants;
use App\Entities\ChapaEleicao;
use App\Entities\MembroChapa;
use App\Entities\MembroChapaPendencia;
use App\Entities\Profissional;
use App\Entities\RedeSocialChapa;
use App\Entities\TipoParticipacaoChapa;
use App\Repository\ChapaEleicaoRepository;
use App\Service\ArquivoService;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Facades\Log;
use App\Util\Utils;

class ExportarChapaCSVBO extends AbstractBO
{
    /**
     * @var MembroChapaBO
     */
    private $membroChapaBO;

    /**
     * @var ChapaEleicaoBO
     */
    private $chapaEleicaoBO;

    /**
     * Retorna uma nova instância de 'MembroChapaBO'.
     *
     * @return MembroChapaBO|mixed
     */
    private function getMembroChapaBO()
    {
        if (empty($this->membroChapaBO)) {
            $this->membroChapaBO = app()->make(MembroChapaBO::class);
        }

        return $this->membroChapaBO;
    }

    /**
     * Retorna uma nova instância de 'ChapaEleicaoBO'.
     *
     * @return ChapaEleicaoBO|mixed
     */
    private function getChapaEleicaoBO()
    {
        if (empty($this->chapaEleicaoBO)) {
            $this->chapaEleicaoBO = app()->make(ChapaEleicaoBO::class);
        }

        return $this->chapaEleicaoBO;
    }

    public function getCaminhoCSV()
    {
        return getenv('SICCAU_STORAGE_PATH') . '/eleitoral/relatorioChapa.csv';
    }

    private function deleteArquivoOld($caminho)
    {
        if (file_exists($caminho)) {
            unlink($caminho);
        }
    }

    private function criarArquivoComCabecalho($arrayCamposCabecalho, $caminho)
    {
        $this->deleteArquivoOld($caminho);

        $manipulador_arq = fopen($caminho, "w+");
        fputs($manipulador_arq, "\xEF\xBB\xBF");
        fputcsv($manipulador_arq, $arrayCamposCabecalho, ';');
        return $manipulador_arq;
    }

    private function montarDadosChapa($registros, $manipulador_arq)
    {
        $calendario = $registros[0]->getAtividadeSecundariaCalendario()->getAtividadePrincipalCalendario()->getCalendario();
        $eleicao = $calendario->getEleicao();

        /**
         * @var ChapaEleicao $chapa
         */
        foreach ($registros as $chapa) {
            $array = [
                0 => $calendario->getEleicao()->getAno(),
                1 => $eleicao->getTipoProcesso()->getDescricao()
            ];

            $array[2] = $chapa->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES ? 'IES' : $chapa->getFilial()->getPrefixo();
            $array[3] = $chapa->getNumeroChapa();
            $array[4] = $chapa->getStatusChapaJulgamentoFinal()->getParecer();

            $planoTrabalho = html_entity_decode($chapa->getDescricaoPlataforma());
            $planoTrabalho = str_ireplace(';', '', $planoTrabalho);
            $array[5] = $planoTrabalho;

            $this->montarRedesSociais($chapa->getRedesSociaisChapa(), $array);
            $manipulador_arq = $this->montarDadosMembro($chapa, $array, $manipulador_arq);
        }

        return $manipulador_arq;

    }

    /**
     * Preenche o arquivo de acordo com as informaçoes
     *
     * @param $registros
     * @param $manipulador_arq
     * @return mixed
     */
    private function montarDadosChapaPorUf($registros, $manipulador_arq)
    {
        /**
         * @var ChapaEleicao $chapa
         */
        foreach ($registros as $chapa) {
            $this->getChapaEleicaoBO()->definirInformacoesComplementarChapa($chapa, false);
            $array[0] = $chapa->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES ?
                Constants::PREFIXO_IES : $chapa->getFilial()
                ->getPrefixo();
            $array[1] = empty($chapa->getNumeroChapa()) ? 'N/A' : $chapa->getNumeroChapa();
            $chapa->definirStatusChapaVigente();
            $manipulador_arq = $this->montarDadosMembroPorUf($chapa, $array, $manipulador_arq);
        }

        return $manipulador_arq;

    }

    private function montarRedesSociais($redesSociais, &$array)
    {
        $array[6] = '';
        $array[7] = '';
        $array[8] = '';
        $array[9] = '';
        $arrayOutros = [];

        /** @var RedeSocialChapa $redesSocial */
        foreach ($redesSociais as $redesSocial) {
            if($redesSocial->getTipoRedeSocial()->getId() == Constants::TIPO_REDE_SOCIAL_FACEBOOK) {
                $array[6] = $redesSocial->getDescricao();
            }
            else if($redesSocial->getTipoRedeSocial()->getId() == Constants::TIPO_REDE_SOCIAL_INSTAGRAM) {
                $array[7] = $redesSocial->getDescricao();
            }
            else if($redesSocial->getTipoRedeSocial()->getId() == Constants::TIPO_REDE_SOCIAL_LINKEDIN) {
                $array[8] = $redesSocial->getDescricao();
            }
            else if($redesSocial->getTipoRedeSocial()->getId() == Constants::TIPO_REDE_SOCIAL_TWITTER) {
                $array[9] = $redesSocial->getDescricao();
            }
            else if($redesSocial->getTipoRedeSocial()->getId() == Constants::TIPO_REDE_SOCIAL_OUTROS) {
                $arrayOutros[] = $redesSocial->getDescricao();
            }
        }

        $array[10] = implode("|", $arrayOutros);
    }

    private function montarDadosMembro($chapa, $arrayDadosChapa, $manipulador_arq)
    {
        $membros = $chapa->getMembrosChapa();
        if(!empty($membros)) {
            foreach ($membros as $membro) {

                $arrDadosMembro = $arrayDadosChapa;

                $arrDadosMembro[11] = $membro->getNumeroOrdem();
                $arrDadosMembro[12] = $membro->getProfissional()->getNome();

                $situacaoResponsavel = $membro->isSituacaoResponsavel() ? 'Sim' : 'Não';
                $arrDadosMembro[13] = $situacaoResponsavel;

                if ($membro->getTipoMembroChapa()->getId() == 1 || $chapa->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES) {
                    $tipoRepresentacao = "Federal";
                } else {
                    $tipoRepresentacao = "Estadual";
                }
                $arrDadosMembro[14] = $tipoRepresentacao;

                if ($membro->getTipoParticipacaoChapa()->getId() == 1) {
                    $tipoMembro = "Titular";
                } else {
                    $tipoMembro = "Suplente";
                }

                $arrDadosMembro[15] = $tipoMembro;

                $representatividade = '';
                if ($membro->getTipoParticipacaoChapa()->getId() == 1) {
                    if (!empty($membro->getRespostaDeclaracaoRepresentatividade())) {
                        $representatividade = "Representatividade";
                    }
                }

                $arrDadosMembro[16] = $representatividade;
                $arrDadosMembro[17] = $membro->getProfissional()->getRegistroNacional();

                $curriculo = '';
                if (!empty($membro->getSinteseCurriculo())) {
                    $curriculo = html_entity_decode($membro->getSinteseCurriculo());
                }
                $arrDadosMembro[18] = $curriculo;

                $foto = '';
                if (!empty($membro->getNomeArquivoFoto())) {
                    $foto = url() . '/membros/download-foto/' . $membro->getId();
                }
                $arrDadosMembro[19] = $foto;

                fputcsv($manipulador_arq, $arrDadosMembro, ';');
            }

            return $manipulador_arq;
        }
    }

    /**
     * Preenche o CSV por UF com os dados dos membros
     *
     * @param $chapa
     * @param $arrayDadosChapa
     * @param $manipulador_arq
     * @return mixed
     */
    private function montarDadosMembroPorUf($chapa, $arrayDadosChapa, $manipulador_arq)
    {
        $membros = $chapa->getMembrosChapa();

        for($i = 0; $i <= $chapa->getNumeroProporcaoConselheiros(); $i++) {

            $membrosPosicaoTitular = array_filter($membros, function ($membro) use ($i) {
                /** @var MembroChapa $membro */
                return $membro->getNumeroOrdem() == $i && $membro->getTipoParticipacaoChapa()->getId() == 1;
            });

            $membrosPosicaoSuplente = $membrosPosicaoSuplente = array_filter($membros, function ($membro) use ($i) {
                /** @var MembroChapa $membro */
                return $membro->getNumeroOrdem() == $i && $membro->getTipoParticipacaoChapa()->getId() == 2;
            });

            if (empty($membrosPosicaoSuplente)){
                $membrosPosicaoSuplente = MembroChapa::newInstance([
                    'numeroOrdem' => $i,
                    'profissional' => ['nome' => 'Não houve preenchimento'],
                    'tipoParticipacaoChapa' => ['id' => 2, 'descricao' => 'Suplente']]);

                array_push($membros, $membrosPosicaoSuplente);
            }

            if (empty($membrosPosicaoTitular)){
                $membrosPosicaoTitular = MembroChapa::newInstance([
                    'numeroOrdem' => $i,
                    'profissional' => ['nome' => 'Não houve preenchimento'],
                    'tipoParticipacaoChapa' => ['id' => 1, 'descricao' => 'Titular']]);

                array_push($membros, $membrosPosicaoTitular);
            }
        }
        $membros = new \ArrayObject($membros);
        $iterator = $membros->getIterator();

        $iterator->uasort(function ($a, $b) {
            if($a->getNumeroOrdem() == $b->getNumeroOrdem()) {
                return ($a->getTipoParticipacaoChapa()->getId() < $b->getTipoParticipacaoChapa()->getId()) ? -1 : 1;
            } else {
                return ($a->getNumeroOrdem() < $b->getNumeroOrdem()) ? -1 : 1;
            }
        });
        $membros = new ArrayCollection(iterator_to_array($iterator));

        /** @var MembroChapa $membro */
        foreach ($membros as $membro) {

            $arrDadosMembro = $arrayDadosChapa;

            $arrDadosMembro[2] = $membro->getNumeroOrdem() == 0 ? 'Federal' : $membro->getNumeroOrdem();
            $arrDadosMembro[3] = $membro->getTipoParticipacaoChapa()->getDescricao();
            if (is_null($membro->isSituacaoResponsavel())) {
                $arrDadosMembro[4] = 'Não houve preenchimento';
            }
            else {
                $arrDadosMembro[4] = $membro->isSituacaoResponsavel() ? 'Sim' : 'Nao';
            }

            $arrDadosMembro[5] = $membro->getProfissional()->getNome();
            $arrDadosMembro[6] = empty($membro->getProfissional()->getRegistroNacional()) ? 'Não houve preenchimento'
                : $membro->getProfissional()->getRegistroNacional();
            if (empty($membro->getTipoMembroChapa())) {
                $arrDadosMembro[7] = 'Não houve preenchimento';
            }
            else {
                if ($membro->getTipoMembroChapa()->getId() == 1 || $chapa->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES) {
                    $tipoRepresentacao = "Federal";
                } else {
                    $tipoRepresentacao = "Estadual";
                }
                $arrDadosMembro[7] = $tipoRepresentacao;
            }
            if (empty($membro->getStatusParticipacaoChapa())) {
                $arrDadosMembro[8] = 'Não houve preenchimento';
            }
            else {
                $arrDadosMembro[8] = $membro->getStatusParticipacaoChapa()->getDescricao();
            }

            if(empty($membro->getTipoMembroChapa())) {
                $arrDadosMembro[9] = 'Não houve preenchimento';
            }
            else if(empty($membro->getPendencias())){
                $arrDadosMembro[9] = 'Não há pendência';
            }
            else {
                $pendencias = '';
                /** @var MembroChapaPendencia $pendencia */
                $listaPendencias = $membro->getPendencias();
                for ($i = 0; $i < count($listaPendencias); $i++) {
                    $pendencias .= $listaPendencias[$i]->getTipoPendencia()->getDescricao();
                    if($i+1 < count($listaPendencias)){
                        $pendencias.="\n";
                    }
                }
                $arrDadosMembro[9] = $pendencias;
            }
            fputcsv($manipulador_arq, $arrDadosMembro, ';');
        }

        return $manipulador_arq;
    }

    public function exportar($registros)
    {
        $caminho = $this->getCaminhoCSV();

        $arrayCamposCabecalho = [
            'Eleição',
            'Tipo Processo',
            'UF',
            'Número Chapa',
            'Status Chapa',
            'Plano Trabalho',
            'Plataforma Propaganda Facebook',
            'Plataforma Propaganda Instagram',
            'Plataforma Propaganda LinkedIn',
            'Plataforma Propaganda Twitter',
            'Plataforma Propaganda Outros',
            'Número Posição Chapa',
            'Nome Candidato',
            'Responsável Chapa',
            'Tipo Representação',
            'Tipo Membro',
            'Tipo Representatividade',
            'Número Registro',
            'Cúrriculo',
            'Foto',
        ];

        return $this->criaArquivoCSV($arrayCamposCabecalho, $caminho, $registros);
    }

    public function exportarPorUf($registros)
    {
        $caminho = $this->getCaminhoCSV();

        $arrayCamposCabecalho = [
            'UF',
            'Número Chapa',
            'Número Posição Chapa',
            'Tipo Participação',
            'Responsável',
            'Nome Candidato',
            'Número Registro',
            'Tipo Candidatura',
            'Status Confirmação',
            'Status Pendência',
        ];

        return $this->criaArquivoCSVPorUf($arrayCamposCabecalho, $caminho, $registros);
    }

    /**
     * Retorna a instância do 'ArquivoService'.
     *
     * @return ArquivoService
     */
    private function getArquivoService()
    {
        if (empty($this->arquivoService)) {
            $this->arquivoService = app()->make(ArquivoService::class);
        }
        return $this->arquivoService;
    }

    /**
     * @param array $arrayCamposCabecalho
     * @param string $caminho
     * @param $registros
     * @return \App\To\ArquivoTO
     * @throws \App\Exceptions\NegocioException
     */
    private function criaArquivoCSV(array $arrayCamposCabecalho, string $caminho, $registros): \App\To\ArquivoTO
    {
        $manipulador_arq = $this->criarArquivoComCabecalho($arrayCamposCabecalho, $caminho);

        $manipulador_arq = $this->montarDadosChapa($registros, $manipulador_arq);
        if(!empty($manipulador_arq)) {
            fclose($manipulador_arq);
        }

        return $this->getArquivoService()->getArquivoCaminhoAbsoluto($caminho, 'relatorioChapas.csv');
    }

    /**
     * Cria o arquivo CSV por UF
     *
     * @param array $arrayCamposCabecalho
     * @param string $caminho
     * @param $registros
     * @return \App\To\ArquivoTO
     * @throws \App\Exceptions\NegocioException
     */
    private function criaArquivoCSVPorUf(array $arrayCamposCabecalho, string $caminho, $registros): \App\To\ArquivoTO
    {
        $filial = $registros[0]->getFilial()->getPrefixo();
        $manipulador_arq = $this->criarArquivoComCabecalho($arrayCamposCabecalho, $caminho);

        $manipulador_arq = $this->montarDadosChapaPorUf($registros, $manipulador_arq);
        fclose($manipulador_arq);

        return $this->getArquivoService()
            ->getArquivoCaminhoAbsoluto($caminho, 'Extrato_Chapa_'.$filial.'.csv');
    }

    public function exportarTre($registros)
    {
        $caminho = $this->getCaminhoCSV();

        $arrayCamposCabecalho = [
            'nome',
            'numero',
            'cpf',
            'cargo',
            'genero',
            'tipo',
            'chapa',
            'voto',
            'complemento',
            'ordem',
        ];

        return $this->criaArquivoCSVTre($arrayCamposCabecalho, $caminho, $registros);
    }

    /**
     * @param array $arrayCamposCabecalho
     * @param string $caminho
     * @param $registros
     * @return \App\To\ArquivoTO
     * @throws \App\Exceptions\NegocioException
     */
    private function criaArquivoCSVTre(array $arrayCamposCabecalho, string $caminho, $registros): \App\To\ArquivoTO
    {
        $manipulador_arq = $this->criarArquivoComCabecalho($arrayCamposCabecalho, $caminho);

        $manipulador_arq = $this->montarDadosChapaTre($registros, $manipulador_arq);
        if(!empty($manipulador_arq)) {
            fclose($manipulador_arq);
        }

        return $this->getArquivoService()->getArquivoCaminhoAbsoluto($caminho, 'relatorioChapas.csv');
    }

    private function montarDadosChapaTre($registros, $manipulador_arq)
    {

        /**
         * @var ChapaEleicao $chapa
         */
        foreach ($registros as $chapa) {
            $texto = 'Chapa ' .$chapa->getNumeroChapa();
            $arr = [
                0 => $texto,
                1 => $chapa->getNumeroChapa(),
                2 => 'N.A',
                3 => '8',
                4 => 'N.A',
                5 => 'CHAPA',
                6 => 'N.A',
                7 => 'S',
                8 => 'N.A',
                9 => 'N.A',
            ];
            
            $manipulador_arq = $this->montarChapaTre($arr, $manipulador_arq);
        
            $array = [];

            $manipulador_arq = $this->montarDadosMembroTre($chapa, $array, $manipulador_arq);
        }

        return $manipulador_arq;

    }

    private function montarChapaTre($array, $manipulador_arq)
    {
        if($manipulador_arq == ''){
        } else {
            fputcsv($manipulador_arq, $array, ';');
        }        
        return $manipulador_arq;
    }

    private function montarDadosMembroTre($chapa, $arrayDadosChapa, $manipulador_arq)
    {
        $nroOrdem = 1;
        $numero = 12;
        $membros = $chapa->getMembrosChapa();
        if(!empty($membros)) {
            foreach ($membros as $membro) {
                if ($membro->getTipoMembroChapa()->getId() == 1 || $chapa->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES) {
                    $tipoRepresentacao = "Federal";
                } else {
                    $tipoRepresentacao = "Estadual";
                }              

                if ($membro->getTipoParticipacaoChapa()->getId() == 1) {
                    $tipoMembro = "Titular";
                } else {
                    $tipoMembro = "Suplente";
                }

                $complemento = $tipoRepresentacao .' ' .$tipoMembro;
                $cargo = 12;
                if($complemento == 'Federal Titular'){
                    $cargo = 9;
                } else if($complemento == 'Federal Suplente'){
                    $cargo = 10;
                } else if($complemento == 'Estadual Titular'){
                    $cargo = 11;
                } else if($complemento == 'Conselheiro'){
                    $cargo = 8;
                }
                $cargo = 8;//Solicitado por Rafael fixar em 8

                $arrDadosMembro = $arrayDadosChapa;
                $arrDadosMembro[0] = $membro->getProfissional()->getNome();
                $arrDadosMembro[1] = $chapa->getNumeroChapa() . $numero++;
                $arrDadosMembro[2] = str_pad($membro->getCpf(), 11, '0', STR_PAD_LEFT);
                $arrDadosMembro[3] = $cargo;
                $arrDadosMembro[4] = $membro->getSexo();
                $arrDadosMembro[5] = 'CANDIDATO';
                $arrDadosMembro[6] = $chapa->getNumeroChapa();
                $arrDadosMembro[7] = 'N';

                $arrDadosMembro[8] = $complemento;

                $arrDadosMembro[9] = $nroOrdem++;

                if($manipulador_arq == ''){
                    
                } else {
                    fputcsv($manipulador_arq, $arrDadosMembro, ';');
                }
                
            }

            return $manipulador_arq;
        }
    }

}
