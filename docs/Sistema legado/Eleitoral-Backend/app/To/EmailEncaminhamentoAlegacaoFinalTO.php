<?php

namespace App\To;

use App\Config\Constants;
use App\Entities\EncaminhamentoDenuncia;

/**
 * Classe de transferência associada a visualização de 'Alegações Finais'.
 *
 * @package App\To
 * @author  Squadra Tecnologia S/A.
 **/
class EmailEncaminhamentoAlegacaoFinalTO
{
    /**
     * @var string
     */
    private $uf;

    /**
     * @var int
     */
    private $protocolo;

    /**
     * @var integer
     */
    private $numeroChapa;

    /**
     * @var string
     */
    private $tipoDenuncia;

    /**
     * @var string
     */
    private $relatorAtual;

    /**
     * @var string
     */
    private $nomeDenunciado;

    /**
     * @var integer
     */
    private $idTipoDenuncia;

    /**
     * @var string
     */
    private $encaminhamento;

    /**
     * @var string
     */
    private $statusDenuncia;

    /**
     * @var string
     */
    private $alegacoesFinais;

    /**
     * @var string
     */
    private $processoEleitoral;

    /**
     * @var string
     */
    private $descricaoEncaminhamento;

    /**
     * Retorna uma nova instância de 'EmailEncaminhamentoAlegacaoFinalTO'.
     *
     * @param EncaminhamentoDenuncia $encaminhamento
     * @return self
     */
    public static function newInstanceFromEntity(EncaminhamentoDenuncia $encaminhamento): self
    {
        $instance = new self;
        $denuncia = $encaminhamento->getDenuncia();

        $instance->setProtocolo($denuncia->getNumeroSequencial());
        $instance->setEncaminhamento($encaminhamento->getSequencia());
        $instance->setIdTipoDenuncia($denuncia->getTipoDenuncia()->getId());
        $instance->setDescricaoEncaminhamento($encaminhamento->getDescricao());
        $instance->setTipoDenuncia($denuncia->getTipoDenuncia()->getDescricao());

        if ($encaminhamento->getAlegacaoFinal() !== null) {
            $instance->setAlegacoesFinais($encaminhamento->getAlegacaoFinal()->getDescricaoAlegacaoFinal());
        }

        if($denuncia->getUltimaDenunciaAdmitida() !== null)
        $instance->setRelatorAtual(
            $denuncia->getUltimaDenunciaAdmitida()->getMembroComissao()->getProfissionalEntity()->getNome()
        );

        $filial = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getPrefixo() : Constants::PREFIXO_IES;
        $instance->setUf($filial);

        if ($instance->getIdTipoDenuncia() === Constants::TIPO_CHAPA) {
            $instance->setNumeroChapa($denuncia->getDenunciaChapa()->getChapaEleicao()->getNumeroChapa());
        }

        if ($instance->getIdTipoDenuncia() === Constants::TIPO_MEMBRO_CHAPA) {
            $instance->setNomeDenunciado(
                $denuncia->getDenunciaMembroChapa()->getMembroChapa()->getProfissional()->getNome()
            );
            $instance->setNumeroChapa(
                $denuncia->getDenunciaMembroChapa()->getMembroChapa()->getChapaEleicao()->getNumeroChapa()
            );
        }

        if ($instance->getIdTipoDenuncia() === Constants::TIPO_MEMBRO_COMISSAO) {
            $instance->setNomeDenunciado(
                $denuncia->getDenunciaMembroComissao()->getMembroComissao()->getProfissionalEntity()->getNome()
            );
            $instance->setUf($denuncia->getDenunciaMembroComissao()->getMembroComissao()->getFilial()->getPrefixo());
        }

        return $instance;
    }

    /**
     * @return int
     */
    public function getProtocolo(): ?int
    {
        return $this->protocolo;
    }

    /**
     * @param int $protocolo
     */
    public function setProtocolo(?int $protocolo): void
    {
        $this->protocolo = $protocolo;
    }

    /**
     * @return string
     */
    public function getProcessoEleitoral(): ?string
    {
        return $this->processoEleitoral;
    }

    /**
     * @param string $processoEleitoral
     */
    public function setProcessoEleitoral(?string $processoEleitoral): void
    {
        $this->processoEleitoral = $processoEleitoral;
    }

    /**
     * @return string
     */
    public function getTipoDenuncia(): ?string
    {
        return $this->tipoDenuncia;
    }

    /**
     * @param string $tipoDenuncia
     */
    public function setTipoDenuncia(?string $tipoDenuncia): void
    {
        $this->tipoDenuncia = $tipoDenuncia;
    }

    /**
     * @return string
     */
    public function getNomeDenunciado(): ?string
    {
        return $this->nomeDenunciado;
    }

    /**
     * @param string $nomeDenunciado
     */
    public function setNomeDenunciado(?string $nomeDenunciado): void
    {
        $this->nomeDenunciado = $nomeDenunciado;
    }

    /**
     * @return int
     */
    public function getNumeroChapa(): ?int
    {
        return $this->numeroChapa;
    }

    /**
     * @param int $numeroChapa
     */
    public function setNumeroChapa(?int $numeroChapa): void
    {
        $this->numeroChapa = $numeroChapa;
    }

    /**
     * @return string
     */
    public function getUf(): ?string
    {
        return $this->uf;
    }

    /**
     * @param string $uf
     */
    public function setUf(?string $uf): void
    {
        $this->uf = $uf;
    }

    /**
     * @return string
     */
    public function getStatusDenuncia(): ?string
    {
        return $this->statusDenuncia;
    }

    /**
     * @param string $statusDenuncia
     */
    public function setStatusDenuncia(?string $statusDenuncia): void
    {
        $this->statusDenuncia = $statusDenuncia;
    }

    /**
     * @return string
     */
    public function getEncaminhamento(): ?string
    {
        return $this->encaminhamento;
    }

    /**
     * @param string $encaminhamento
     */
    public function setEncaminhamento(?string $encaminhamento): void
    {
        $this->encaminhamento = $encaminhamento;
    }

    /**
     * @return string
     */
    public function getDescricaoEncaminhamento(): ?string
    {
        return $this->descricaoEncaminhamento;
    }

    /**
     * @param string $descricaoEncaminhamento
     */
    public function setDescricaoEncaminhamento(?string $descricaoEncaminhamento): void
    {
        $this->descricaoEncaminhamento = $descricaoEncaminhamento;
    }

    /**
     * @return string
     */
    public function getAlegacoesFinais(): ?string
    {
        return $this->alegacoesFinais;
    }

    /**
     * @param string $alegacoesFinais
     */
    public function setAlegacoesFinais(?string $alegacoesFinais): void
    {
        $this->alegacoesFinais = $alegacoesFinais;
    }

    /**
     * @return int
     */
    public function getIdTipoDenuncia(): ?int
    {
        return $this->idTipoDenuncia;
    }

    /**
     * @param int $idTipoDenuncia
     */
    public function setIdTipoDenuncia(?int $idTipoDenuncia): void
    {
        $this->idTipoDenuncia = $idTipoDenuncia;
    }

    /**
     * @return string
     */
    public function getRelatorAtual(): ?string
    {
        return $this->relatorAtual;
    }

    /**
     * @param string $relatorAtual
     */
    public function setRelatorAtual(?string $relatorAtual): void
    {
        $this->relatorAtual = $relatorAtual;
    }

}
