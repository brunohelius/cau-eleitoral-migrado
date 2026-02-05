<?php

namespace App\To;

use App\Config\Constants;
use App\Entities\Denuncia;
use App\Factory\UsuarioFactory;
use App\Util\Utils;

/**
 * Classe de transferência associada a listagem de 'Denuncias' em relatoria.
 *
 * @package App\To
 * @author  Squadra Tecnologia S/A.
 **/
class DenunciaEmRelatoriaTO
{

    /**
     * @var boolean
     */
    private $sigiloso;

    /**
     * @var integer
     */
    private $id_denuncia;

    /**
     * @var string
     */
    private $ds_situacao;

    /**
     * @var string
     */
    private $nome_denunciado_comissao;

    /**
     * @var int
     */
    private $numero_denuncia;

    /**
     * @var integer
     */
    private $id_tipo_denuncia;

    /**
     * @var string
     */
    private $nome_denunciante;

    /**
     * @var integer
     */
    private $id_situacao_denuncia;

    /**
     * @var \DateTime
     */
    private $dt_denuncia;

    /**
     * Retorna uma nova instância de 'DenunciaTO'.
     *
     * @param Denuncia $denuncia
     * @return self
     */
    public static function newInstanceFromEntity($denuncia = null)
    {
        $instance = new self;

        if (null !== $denuncia) {
            $instance->setIdDenuncia($denuncia->getId());
            $instance->setSigiloso($denuncia->isSigiloso());
            $instance->setNumeroDenuncia($denuncia->getNumeroSequencial());
            $instance->setIdTipoDenuncia($denuncia->getTipoDenuncia()->getId());
            $instance->setIdSituacaoDenuncia($denuncia
                ->getDenunciaSituacao()->last()->getSituacaoDenuncia()->getId());
            $instance->setDsSituacao($denuncia
                ->getDenunciaSituacao()->last()->getSituacaoDenuncia()->getDescricao());
            $instance->setNomeDenunciadoComissao($instance
                ->getNomeDenunciadoPorTipoDenuncia($denuncia));
            $instance->setDtDenuncia($denuncia->getDataHora());

            $usuarioFactory = app()->make(UsuarioFactory::class);

            $profissional = $denuncia->getPessoa()->getProfissional();
            $instance->setNomeDenunciante($usuarioFactory->isCorporativo() || !$denuncia->isSigiloso()
                ? $profissional->getNome()
                : Utils::ofuscarCampo($profissional->getNome())
            );
        }

        return $instance;
    }

    /**
     * @return bool
     */
    public function isSigiloso(): bool
    {
        return $this->sigiloso ?? false;
    }

    /**
     * @param bool $sigiloso
     */
    public function setSigiloso(bool $sigiloso): void
    {
        $this->sigiloso = $sigiloso;
    }

    /**
     * @return int
     */
    public function getIdDenuncia(): ?int
    {
        return $this->id_denuncia;
    }

    /**
     * @param int $id
     */
    public function setIdDenuncia(int $id): void
    {
        $this->id_denuncia = $id;
    }

    /**
     * @return int
     */
    public function getIdTipoDenuncia(): int
    {
        return $this->id_tipo_denuncia;
    }

    /**
     * @param int $idTipoDenuncia
     */
    public function setIdTipoDenuncia(int $idTipoDenuncia): void
    {
        $this->id_tipo_denuncia = $idTipoDenuncia;
    }

    /**
     * @return string
     */
    public function getNomeDenunciadoComissao(): string
    {
        return $this->nome_denunciado_comissao;
    }

    /**
     * @param string $nomeDenunciado
     */
    public function setNomeDenunciadoComissao(string $nomeDenunciado): void
    {
        $this->nome_denunciado_comissao = $nomeDenunciado;
    }

    /**
     * @return string
     */
    public function getNomeDenunciante(): string
    {
        return $this->nome_denunciante;
    }

    /**
     * @param string $nomeDenunciante
     */
    public function setNomeDenunciante(string $nomeDenunciante): void
    {
        $this->nome_denunciante = $nomeDenunciante;
    }

    /**
     * @return string
     */
    public function getDsSituacao(): string
    {
        return $this->ds_situacao;
    }

    /**
     * @param string $dsSituacao
     */
    public function setDsSituacao(string $dsSituacao): void
    {
        $this->ds_situacao = $dsSituacao;
    }

    /**
     * @return int
     */
    public function getNumeroDenuncia(): int
    {
        return $this->numero_denuncia;
    }

    /**
     * @param int $numeroSequencial
     */
    public function setNumeroDenuncia(int $numeroSequencial): void
    {
        $this->numero_denuncia = $numeroSequencial;
    }

    /**
     * @return int
     */
    public function getIdSituacaoDenuncia(): int
    {
        return $this->id_situacao_denuncia;
    }

    /**
     * @param int $idSituacaoDenuncia
     */
    public function setIdSituacaoDenuncia(int $idSituacaoDenuncia): void
    {
        $this->id_situacao_denuncia = $idSituacaoDenuncia;
    }

    /**
     * @return \DateTime
     */
    public function getDtDenuncia(): \DateTime
    {
        return $this->dt_denuncia;
    }

    /**
     * @param \DateTime $dt_denuncia
     */
    public function setDtDenuncia(\DateTime $dt_denuncia): void
    {
        $this->dt_denuncia = $dt_denuncia;
    }

    /**
     * @param \App\Entities\Denuncia $denuncia
     *
     * @return string
     */
    private function getNomeDenunciadoPorTipoDenuncia(Denuncia $denuncia): string
    {
        $tipoDenuncia = $denuncia->getTipoDenuncia()->getId();

        if(Constants::TIPO_CHAPA === $tipoDenuncia) {
            $chapa = $denuncia->getDenunciaChapa()->getChapaEleicao();
            $denunciado = $chapa ? $chapa->getNumeroChapa() : null;
        }

        if(Constants::TIPO_MEMBRO_CHAPA === $tipoDenuncia) {
            $profissional = $denuncia->getDenunciaMembroChapa()->getMembroChapa()
                                     ->getProfissional();
            $denunciado = $profissional ? $profissional->getNome() : null;
        }

        if(Constants::TIPO_MEMBRO_COMISSAO === $tipoDenuncia) {
            $profissional = $denuncia->getDenunciaMembroComissao()->getMembroComissao()
                                     ->getProfissionalEntity();
            $denunciado = $profissional ? $profissional->getNome() : null;
        }

        return $denunciado ?? '-';
    }
}
