<?php
/*
 * ExportarParecerTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Entities\EncaminhamentoDenuncia;
use App\Util\Utils;

/**
 * Classe de transferência associada a exportar encaminhamentos.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class ExportarParecerTO
{

    /** @var int|null */
    private $numero;

    /** @var string|null */
    private $relator;

    /** @var int|null */
    private $idStatus;

    /** @var \DateTime|null */
    private $dataHora;

    /** @var string|null */
    private $descricao;

    /** @var ArquivoDescricaoTO[]|null */
    private $documentos;

    /** @var \DateTime|null */
    private $agendamento;

    /** @var string|null */
    private $justificativa;

    /** @var string|null */
    private $encaminhamento;

    /** @var int|null */
    private $idTipoEncaminhamento;

    /** @var ExportarImpedimentoSuspeicaoTO|null */
    private $impedimentoSuspeicao;

    /** @var DenunciaProvasTO|null */
    private $producaoProvas;

    /** @var DenunciaAudienciaInstrucaoTO|null */
    private $audienciaInstrucao;

    /** @var AlegacaoFinalTO|null */
    private $alegacaoFinal;

    /** @var ParecerFinalTO|null */
    private $parecerFinal;

    /**
     * Retorna uma nova instância de 'ExportarParecerTO'.
     *
     * @param EncaminhamentoDenuncia $encaminhamento
     *
     * @return ExportarParecerTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity(EncaminhamentoDenuncia $encaminhamento)
    {
        $instance = new self();

        if ($encaminhamento !== null) {
            $instance->setDataHora($encaminhamento->getData());
            $instance->setNumero($encaminhamento->getSequencia());
            $instance->setDescricao($encaminhamento->getDescricao());
            $instance->setJustificativa($encaminhamento->getJustificativa());
            $instance->setIdStatus($encaminhamento->getTipoSituacaoEncaminhamento()->getId());
            $instance->setIdTipoEncaminhamento($encaminhamento->getTipoEncaminhamento()->getId());
            $instance->setEncaminhamento($encaminhamento->getTipoEncaminhamento()->getDescricao());
            $instance->setRelator($encaminhamento->getMembroComissao()->getProfissionalEntity()->getNome());

            $agendamento = $encaminhamento->getAgendamentoEncaminhamento();
            if (!empty($agendamento->current())) {
                $instance->setAgendamento($agendamento[0]->getData());
            }

            $impedimentoSuspeicao = $encaminhamento->getImpedimentoSuspeicao();
            if (!empty($impedimentoSuspeicao)) {
                $instance->setImpedimentoSuspeicao(ExportarImpedimentoSuspeicaoTO::newInstanceFromEntity(
                    $impedimentoSuspeicao
                ));
            }

            $provas = $encaminhamento->getDenunciaProvas();
            if (!empty($provas->current())) {
                $instance->setProducaoProvas(DenunciaProvasTO::newInstanceFromEntity($provas->current()));
            }

            $audiencia = $encaminhamento->getAudienciaInstrucao();
            if (!empty($audiencia->current())) {
                $instance->setAudienciaInstrucao(DenunciaAudienciaInstrucaoTO::newInstanceFromEntity($audiencia->current()));
            }

            $alegacaoFinal = $encaminhamento->getAlegacaoFinal();
            if (!empty($alegacaoFinal)) {
                $instance->setAlegacaoFinal(AlegacaoFinalTO::newInstanceFromEntity($alegacaoFinal));
            }

            $parecerFinal = $encaminhamento->getParecerFinal();
            if (!empty($parecerFinal)) {
                $instance->setParecerFinal(ParecerFinalTO::newInstanceFromEntity($parecerFinal));
            }
        }

        return $instance;
    }

    /**
     * @return int|null
     */
    public function getNumero(): ?int
    {
        return $this->numero;
    }

    /**
     * @param int|null $numero
     */
    public function setNumero(?int $numero): void
    {
        $this->numero = $numero;
    }

    /**
     * @return string|null
     */
    public function getRelator(): ?string
    {
        return $this->relator;
    }

    /**
     * @param string|null $relator
     */
    public function setRelator(?string $relator): void
    {
        $this->relator = $relator;
    }

    /**
     * @return int|null
     */
    public function getIdStatus(): ?int
    {
        return $this->idStatus;
    }

    /**
     * @param int|null $idStatus
     */
    public function setIdStatus(?int $idStatus): void
    {
        $this->idStatus = $idStatus;
    }

    /**
     * @return \DateTime|null
     */
    public function getDataHora(): ?\DateTime
    {
        return $this->dataHora;
    }

    /**
     * @param \DateTime|null $dataHora
     */
    public function setDataHora(?\DateTime $dataHora): void
    {
        $this->dataHora = $dataHora;
    }

    /**
     * @return string|null
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * @param string|null $descricao
     */
    public function setDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return ArquivoDescricaoTO[]|null
     */
    public function getDocumentos(): ?array
    {
        return $this->documentos;
    }

    /**
     * @param ArquivoDescricaoTO[]|null $documentos
     */
    public function setDocumentos(?array $documentos): void
    {
        $this->documentos = $documentos;
    }

    /**
     * @return \DateTime|null
     */
    public function getAgendamento(): ?\DateTime
    {
        return $this->agendamento;
    }

    /**
     * @param \DateTime|null $agendamento
     */
    public function setAgendamento(?\DateTime $agendamento): void
    {
        $this->agendamento = $agendamento;
    }

    /**
     * @return string|null
     */
    public function getJustificativa(): ?string
    {
        return $this->justificativa;
    }

    /**
     * @param string|null $justificativa
     */
    public function setJustificativa(?string $justificativa): void
    {
        $this->justificativa = $justificativa;
    }

    /**
     * @return string|null
     */
    public function getEncaminhamento(): ?string
    {
        return $this->encaminhamento;
    }

    /**
     * @param string|null $encaminhamento
     */
    public function setEncaminhamento(?string $encaminhamento): void
    {
        $this->encaminhamento = $encaminhamento;
    }

    /**
     * @return int|null
     */
    public function getIdTipoEncaminhamento(): ?int
    {
        return $this->idTipoEncaminhamento;
    }

    /**
     * @param int|null $idTipoEncaminhamento
     */
    public function setIdTipoEncaminhamento(?int $idTipoEncaminhamento): void
    {
        $this->idTipoEncaminhamento = $idTipoEncaminhamento;
    }

    /**
     * @return ExportarImpedimentoSuspeicaoTO|null
     */
    public function getImpedimentoSuspeicao(): ?ExportarImpedimentoSuspeicaoTO
    {
        return $this->impedimentoSuspeicao;
    }

    /**
     * @param ExportarImpedimentoSuspeicaoTO|null $impedimentoSuspeicao
     */
    public function setImpedimentoSuspeicao(?ExportarImpedimentoSuspeicaoTO $impedimentoSuspeicao): void
    {
        $this->impedimentoSuspeicao = $impedimentoSuspeicao;
    }

    /**
     * @return DenunciaProvasTO|null
     */
    public function getProducaoProvas(): ?DenunciaProvasTO
    {
        return $this->producaoProvas;
    }

    /**
     * @param DenunciaProvasTO|null $producaoProvas
     */
    public function setProducaoProvas(?DenunciaProvasTO $producaoProvas): void
    {
        $this->producaoProvas = $producaoProvas;
    }

    /**
     * @return DenunciaAudienciaInstrucaoTO|null
     */
    public function getAudienciaInstrucao(): ?DenunciaAudienciaInstrucaoTO
    {
        return $this->audienciaInstrucao;
    }

    /**
     * @param DenunciaAudienciaInstrucaoTO|null $audienciaInstrucao
     */
    public function setAudienciaInstrucao(?DenunciaAudienciaInstrucaoTO $audienciaInstrucao): void
    {
        $this->audienciaInstrucao = $audienciaInstrucao;
    }

    /**
     * @return AlegacaoFinalTO|null
     */
    public function getAlegacaoFinal(): ?AlegacaoFinalTO
    {
        return $this->alegacaoFinal;
    }

    /**
     * @param AlegacaoFinalTO|null $alegacaoFinal
     */
    public function setAlegacaoFinal(?AlegacaoFinalTO $alegacaoFinal): void
    {
        $this->alegacaoFinal = $alegacaoFinal;
    }

    /**
     * @return ParecerFinalTO|null
     */
    public function getParecerFinal(): ?ParecerFinalTO
    {
        return $this->parecerFinal;
    }

    /**
     * @param ParecerFinalTO|null $parecerFinal
     */
    public function setParecerFinal(?ParecerFinalTO $parecerFinal): void
    {
        $this->parecerFinal = $parecerFinal;
    }

}
