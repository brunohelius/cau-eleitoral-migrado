<?php
/*
 * EmailDenunciaTO.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Config\Constants;
use App\Entities\Denuncia;
use App\Entities\EncaminhamentoDenuncia;
use App\Entities\ParecerFinal;

/**
 * Classe de transferência associada a enviar e-mails.
 *
 * @package App\To
 * @author  Squadra Tecnologia S/A.
 **/
class EmailDenunciaTO
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
     * @var ParecerFinalTO
     */
    private $parecerFinal;

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
    private $statusDenuncia;

    /**
     * @var string
     */
    private $nomeDenunciante;

    /**
     * @var string
     */
    private $processoEleitoral;

    /**
     * @var JulgamentoDenunciaTO
     */
    private $julgamentoDenuncia;

    /**
     * @var RecursoDenunciaTO
     */
    private $recursoDenuncia;

    /**
     * @var ContrarrazaoRecursoDenunciaTO
     */
    private $contrarrazaoRecursoDenuncia;

    private $julgamentoRecursoDenuncia;

    /**
     * Retorna uma nova instância de 'EmailDenunciaTO'.
     *
     * @param Denuncia $denuncia
     * @return self
     */
    public static function newInstanceFromEntity(Denuncia $denuncia): self
    {
        $instance = new self;

        $instance->setProtocolo($denuncia->getNumeroSequencial());
        $instance->setIdTipoDenuncia($denuncia->getTipoDenuncia()->getId());
        $instance->setTipoDenuncia($denuncia->getTipoDenuncia()->getDescricao());
        $instance->setNomeDenunciante($denuncia->getPessoa()->getProfissional()->getNome());
        $filial = !empty($denuncia->getFilial()) ? $denuncia->getFilial()->getPrefixo() : Constants::PREFIXO_IES;
        $instance->setUf($filial);

        if ($denuncia->getUltimaDenunciaAdmitida() !== null) {
            $instance->setRelatorAtual(
                $denuncia->getUltimaDenunciaAdmitida()->getMembroComissao()->getProfissionalEntity()->getNome()
            );
        }

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

    /**
     * @return ParecerFinalTO
     */
    public function getParecerFinal(): ?ParecerFinalTO
    {
        return $this->parecerFinal;
    }

    /**
     * @param ParecerFinalTO $parecerFinal
     */
    public function setParecerFinal(?ParecerFinalTO $parecerFinal): void
    {
        $this->parecerFinal = $parecerFinal;
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
    public function getNomeDenunciante(): ?string
    {
        return $this->nomeDenunciante;
    }

    /**
     * @param string $nomeDenunciante
     */
    public function setNomeDenunciante(?string $nomeDenunciante): void
    {
        $this->nomeDenunciante = $nomeDenunciante;
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
     * @return JulgamentoDenunciaTO
     */
    public function getJulgamentoDenuncia(): ?JulgamentoDenunciaTO
    {
        return $this->julgamentoDenuncia;
    }

    /**
     * @param JulgamentoDenunciaTO $julgamentoDenuncia
     */
    public function setJulgamentoDenuncia(?JulgamentoDenunciaTO $julgamentoDenuncia): void
    {
        $this->julgamentoDenuncia = $julgamentoDenuncia;
    }

    /**
     * @return RecursoDenunciaTO
     */
    public function getRecursoDenuncia(): ?RecursoDenunciaTO
    {
        return $this->recursoDenuncia;
    }

    /**
     * @param RecursoDenunciaTO $recursoDenuncia
     */
    public function setRecursoDenuncia(?RecursoDenunciaTO $recursoDenuncia): void
    {
        $this->recursoDenuncia = $recursoDenuncia;
    }

    /**
     * @return ContrarrazaoRecursoDenunciaTO
     */
    public function getContrarrazaoRecursoDenuncia(): ?ContrarrazaoRecursoDenunciaTO
    {
        return $this->contrarrazaoRecursoDenuncia;
    }

    /**
     * @param ContrarrazaoRecursoDenunciaTO $contrarrazaoRecursoDenuncia
     */
    public function setContrarrazaoRecursoDenuncia(?ContrarrazaoRecursoDenunciaTO $contrarrazaoRecursoDenuncia): void
    {
        $this->contrarrazaoRecursoDenuncia = $contrarrazaoRecursoDenuncia;
    }

    /**
     * @return mixed
     */
    public function getJulgamentoRecursoDenuncia()
    {
        return $this->julgamentoRecursoDenuncia;
    }

    /**
     * @param mixed $julgamentoRecursoDenuncia
     */
    public function setJulgamentoRecursoDenuncia($julgamentoRecursoDenuncia): void
    {
        $this->julgamentoRecursoDenuncia = $julgamentoRecursoDenuncia;
    }


}
