<?php


namespace App\Business;

use App\Config\Constants;
use App\Entities\ChapaEleicao;
use App\Entities\RedeSocialChapa;
use App\Repository\ChapaEleicaoRepository;
use App\Service\ArquivoService;
use Illuminate\Support\Facades\Log;

class ExportarChapaXMLBO extends AbstractBO
{
    /**
     * Retorna uma nova instância de 'FilialBO'.
     *
     * @return FilialBO|mixed
     */
    private function getFilialBO()
    {
        if (empty($this->filialBO)) {
            $this->filialBO = app()->make(FilialBO::class);
        }

        return $this->filialBO;
    }

    public function getCaminhoXML()
    {
        return getenv('SICCAU_STORAGE_PATH') . '/eleitoral/relatorioChapa.xml';
    }

    private function deleteArquivoOld($caminho)
    {
        if (file_exists($caminho)) {
            unlink($caminho);
        }
    }

    private function criarArquivo($caminho)
    {
        $this->deleteArquivoOld($caminho);

        $manipulador_arq = fopen($caminho, "w+");
        fwrite($manipulador_arq, "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>");
        return $manipulador_arq;
    }

    private function montarDadosEleicao($registros)
    {
        $calendario = $registros[0]->getAtividadeSecundariaCalendario()->getAtividadePrincipalCalendario()->getCalendario();
        $eleicao = $calendario->getEleicao();

        $xml = "\n<eleicao>\n";
        $xml .= "<ano>" . $eleicao->getAno() . "</ano>\n";
        $xml .= "<tipoProcesso>" . $eleicao->getTipoProcesso()->getDescricao() . "</tipoProcesso>\n";
        $this->montarDadosChapa($registros, $xml);
        $xml .= "\n</eleicao>";
        return $xml;
    }

    private function montarDadosChapa($registros, &$xml)
    {
        foreach ($registros as $key => $chapasEleicao) {

            $xml .= "<chapa>\n";
            if ($chapasEleicao->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES) {
                $xml .= "<uf>IES</uf>\n";
            } else {
                $xml .= "<uf>";
                $xml .= $chapasEleicao->getFilial()->getPrefixo();
                $xml .= "</uf>\n";
            }

            $xml .= "<numeroChapa>" . $chapasEleicao->getNumeroChapa() . "</numeroChapa>\n";
            $xml .= "<statusChapa>" . $chapasEleicao->getStatusChapaJulgamentoFinal()->getParecer() . "</statusChapa>\n";
            $xml .= "<planoTrabalho>" . html_entity_decode($chapasEleicao->getDescricaoPlataforma()) . "</planoTrabalho>\n";

            $this->montarRedesSociais($chapasEleicao->getRedesSociaisChapa(), $xml);
            $this->montarDadosMembro($chapasEleicao, $xml, $chapasEleicao->getTipoCandidatura()->getId());

            $xml .= "</chapa>\n";
        }
        /**
         * @var  $key
         * @var ChapaEleicao $chapasEleicao
         */
    }

    private function montarRedesSociais($redesSociais, &$xml)
    {
        $arrayOutros = [];
        if (!empty($redesSociais)) {
            foreach ($redesSociais as $redesSocial) {
                if ($redesSocial->getTipoRedeSocial()->getId() == Constants::TIPO_REDE_SOCIAL_FACEBOOK) {
                    $xml .= "<plataformaPropagandaFacebook>" . $redesSocial->getDescricao() . "</plataformaPropagandaFacebook>\n";
                } else if ($redesSocial->getTipoRedeSocial()->getId() == Constants::TIPO_REDE_SOCIAL_INSTAGRAM) {
                    $xml .= "<plataformaPropagandaInstagram>" . $redesSocial->getDescricao() . "</plataformaPropagandaInstagram>\n";
                } else if ($redesSocial->getTipoRedeSocial()->getId() == Constants::TIPO_REDE_SOCIAL_LINKEDIN) {
                    $xml .= "<plataformaPropagandaLinkedin>" . $redesSocial->getDescricao() . "</plataformaPropagandaLinkedin>\n";
                } else if ($redesSocial->getTipoRedeSocial()->getId() == Constants::TIPO_REDE_SOCIAL_TWITTER) {
                    $xml .= "<plataformaPropagandaTwitter>" . $redesSocial->getDescricao() . "</plataformaPropagandaTwitter>\n";
                } else if ($redesSocial->getTipoRedeSocial()->getId() == Constants::TIPO_REDE_SOCIAL_OUTROS) {
                    $arrayOutros[] = $redesSocial->getDescricao();
                }
            }
            if (!empty($arrayOutros)) {
                $xml .= "<plataformaPropagandaOutros>" . implode("|", $arrayOutros) . "</plataformaPropagandaOutros>\n";
            }
        }
    }

    private function montarDadosMembro($chapa, &$xml, $tipo = null)
    {
        $membrosFederaisTitular = null;
        $membrosFederaisSuplentes = null;
        $membrosEstaduais = null;

        $membros = $chapa->getMembrosChapa();
        if (!empty($membros)) {
            foreach ($membros as $membro) {
                $xmlMEmbro = "<membro>\n";

                $xmlMEmbro .= "<numeroPosicaoChapa>" . $membro->getNumeroOrdem() . "</numeroPosicaoChapa>\n";
                $xmlMEmbro .= "<nomeCandidato>" . $membro->getProfissional()->getNome() . "</nomeCandidato>\n";

                $situacaoResponsavel = $membro->isSituacaoResponsavel() ? 'Sim' : 'Não';
                $xmlMEmbro .= "<responsavelChapa>" . $situacaoResponsavel . "</responsavelChapa>\n";


                if ($membro->getTipoMembroChapa()->getId() == 1 || $chapa->getTipoCandidatura()->getId() == Constants::TIPO_CANDIDATURA_IES) {
                    $xmlMEmbro .= "<tipoRepresentacao>Federal</tipoRepresentacao>\n";
                } else {
                    $xmlMEmbro .= "<tipoRepresentacao>Estadual</tipoRepresentacao>\n";
                }


                if ($membro->getTipoParticipacaoChapa()->getId() == 1) {
                    $xmlMEmbro .= "<tipoMembro>Titular</tipoMembro>\n";
                } else {
                    $xmlMEmbro .= "<tipoMembro>Suplente</tipoMembro>\n";
                }

                if ($membro->getTipoParticipacaoChapa()->getId() == 1) {
                    if (!empty($membro->getRespostaDeclaracaoRepresentatividade())) {
                        $xmlMEmbro .= "<tipoRepresentatividade>Representatividade</tipoRepresentatividade>\n";
                    }
                }

                $xmlMEmbro .= "<numeroRegistro>" . $membro->getProfissional()->getRegistroNacional() . "</numeroRegistro>\n";

                if (!empty($membro->getSinteseCurriculo())) {
                    $xmlMEmbro .= "<curriculo>" . html_entity_decode($membro->getSinteseCurriculo()) . "</curriculo>\n";
                }

                if (!empty($membro->getNomeArquivoFoto())) {
                    $foto = url() . '/membros/download-foto/' . $membro->getId();
                    $xmlMEmbro .= "<foto>" . $foto . "</foto>\n";
                } else {
                    $xmlMEmbro .= "<foto></foto>\n";
                }

                $xmlMEmbro .= "</membro>\n";

                $xml .= $xmlMEmbro;
            }
        }
    }

    public function exportar($registros)
    {
        $caminho = $this->getCaminhoXML();

        $manipulador_arq = $this->criarArquivo($caminho);

        $xml = $this->montarDadosEleicao($registros);

        fwrite($manipulador_arq, $xml);
        fclose($manipulador_arq);

        return $this->getArquivoService()->getArquivoCaminhoAbsoluto($caminho, 'relatorioChapas.xml');
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
}
