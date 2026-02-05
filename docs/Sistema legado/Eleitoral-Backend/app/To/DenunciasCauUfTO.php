<?php

namespace App\To;

use App\Config\Constants;
use App\Entities\Denuncia;
use App\Factory\UsuarioFactory;
use App\Util\Utils;

/**
 * Classe de transferência associada a listagem de 'Denuncias' por CAU UF.
 *
 * @package App\To
 * @author  Squadra Tecnologia S/A.
 **/
class DenunciasCauUfTO
{

    /**
     * @var boolean
     */
    private $sigiloso;

    /**
     * @var integer
     */
    private $id_cau_uf;

    /**
     * @var integer
     */
    private $id_denuncia;

    /**
     * @var \DateTime
     */
    private $dt_denuncia;

    /**
     * @var string
     */
    private $ds_situacao;

    /**
     * @var string
     */
    private $nome_denunciado;

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
     * @var string
     */
    private $quantidade_encaminhamentos_pendentes;

    /**
     * @var string
     */
    private $descricao_encaminhamentos_pendentes;

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
            $instance->setDtDenuncia($denuncia->getDataHora());
            $instance->setNumeroDenuncia($denuncia->getNumeroSequencial());
            $instance->setIdTipoDenuncia($denuncia->getTipoDenuncia()->getId());
            $instance->setIdSituacaoDenuncia($denuncia
                ->getDenunciaSituacao()->last()->getSituacaoDenuncia()->getId());
            $instance->setDsSituacao($denuncia
                ->getDenunciaSituacao()->last()->getSituacaoDenuncia()->getDescricao());

            $filial = $denuncia->getFilial();
            if (!empty($filial)) {
                $instance->setIdCauUf($filial->getId());
            }

            $usuarioFactory = app()->make(UsuarioFactory::class);

            $profissional = $denuncia->getPessoa()->getProfissional();
            $instance->setNomeDenunciante($usuarioFactory->isCorporativo() || !$denuncia->isSigiloso()
                ? $profissional->getNome()
                : Utils::ofuscarCampo($profissional->getNome())
            );

            $instance->setNomeDenunciado($instance
                ->getNomeDenunciadoPorTipoDenuncia($denuncia));
        }

        return $instance;
    }

    /**
     * Retorna uma nova instância de 'DenunciaTO'.
     *
     * @param Denuncia $denuncia
     * @return self
     */
    public static function newInstance($data = null)
    {
        $instance = new self;

        if (null !== $data) {
            $instance->setIdCauUf(Utils::getValue('id_cau_uf', $data));
            $instance->setIdDenuncia(Utils::getValue('id_denuncia', $data));
            $instance->setDtDenuncia(Utils::getValue('dt_denuncia', $data));
            $instance->setDsSituacao(Utils::getValue('ds_situacao', $data));
            $instance->setNumeroDenuncia(Utils::getValue('numero_denuncia', $data));
            $instance->setIdTipoDenuncia(Utils::getValue('id_tipo_denuncia', $data));
            $instance->setIdSituacaoDenuncia(Utils::getValue('id_situacao_denuncia', $data));

            $isSigiloso = Utils::getBooleanValue('is_sigiloso', $data);
            $instance->setSigiloso($isSigiloso);

            $usuarioFactory = app()->make(UsuarioFactory::class);

            $nomeDenunciante = Utils::getValue('nome_denunciante', $data);
            $instance->setNomeDenunciante($usuarioFactory->isCorporativo() || !$isSigiloso
                ? $nomeDenunciante
                : Utils::ofuscarCampo($nomeDenunciante));
            $instance->setNomeDenunciado(Utils::getValue('nome_denunciado', $data));
        }

        return $instance;
    }

    /**
     * @return bool
     */
    public function isSigiloso()
    {
        return $this->sigiloso ?? false;
    }

    /**
     * @param bool $sigiloso
     */
    public function setSigiloso($sigiloso): void
    {
        $this->sigiloso = $sigiloso;
    }

    /**
     * @return int
     */
    public function getIdCauUf()
    {
        return $this->id_cau_uf;
    }

    /**
     * @param int $idCauUf
     */
    public function setIdCauUf($idCauUf)
    {
        $this->id_cau_uf = $idCauUf;
    }

    /**
     * @return int
     */
    public function getIdDenuncia()
    {
        return $this->id_denuncia;
    }

    /**
     * @param int $id
     */
    public function setIdDenuncia($id)
    {
        $this->id_denuncia = $id;
    }

    /**
     * @return int
     */
    public function getIdTipoDenuncia()
    {
        return $this->id_tipo_denuncia;
    }

    /**
     * @param int $idTipoDenuncia
     */
    public function setIdTipoDenuncia($idTipoDenuncia)
    {
        $this->id_tipo_denuncia = $idTipoDenuncia;
    }

    /**
     * @return string
     */
    public function getNomeDenunciado()
    {
        return $this->nome_denunciado;
    }

    /**
     * @param string $nomeDenunciado
     */
    public function setNomeDenunciado($nomeDenunciado)
    {
        $this->nome_denunciado = $nomeDenunciado;
    }

    /**
     * @return string
     */
    public function getNomeDenunciante()
    {
        return $this->nome_denunciante;
    }

    /**
     * @param string $nomeDenunciante
     */
    public function setNomeDenunciante($nomeDenunciante)
    {
        $this->nome_denunciante = $nomeDenunciante;
    }

    /**
     * @return \DateTime
     */
    public function getDtDenuncia()
    {
        return $this->dt_denuncia;
    }

    /**
     * @param $dtDenuncia
     */
    public function setDtDenuncia($dtDenuncia)
    {
        $this->dt_denuncia = $dtDenuncia;
    }

    /**
     * @return int
     */
    public function getNumeroDenuncia()
    {
        return $this->numero_denuncia;
    }

    /**
     * @return string
     */
    public function getDsSituacao()
    {
        return $this->ds_situacao;
    }

    /**
     * @param string $dsSituacao
     */
    public function setDsSituacao($dsSituacao)
    {
        $this->ds_situacao = $dsSituacao;
    }

    /**
     * @param int $numeroSequencial
     */
    public function setNumeroDenuncia($numeroSequencial)
    {
        $this->numero_denuncia = $numeroSequencial;
    }

    /**
     * @return int
     */
    public function getIdSituacaoDenuncia()
    {
        return $this->id_situacao_denuncia;
    }

    /**
     * @param int $idSituacaoDenuncia
     */
    public function setIdSituacaoDenuncia($idSituacaoDenuncia)
    {
        $this->id_situacao_denuncia = $idSituacaoDenuncia;
    }

    /**
     * @return string
     */
    public function getQuantidadeEncaminhamentosPendentes()
    {
        return $this->quantidade_encaminhamentos_pendentes;
    }

    /**
     * @param string $quantidade_encaminhamentos_pendentes
     */
    public function setQuantidadeEncaminhamentosPendentes($quantidade_encaminhamentos_pendentes)
    {
        $this->quantidade_encaminhamentos_pendentes = $quantidade_encaminhamentos_pendentes;
    }

    /**
     * @return string
     */
    public function getDescricaoEncaminhamentosPendentes()
    {
        return $this->descricao_encaminhamentos_pendentes;
    }

    /**
     * @param string $descricao_encaminhamentos_pendentes
     */
    public function setDescricaoEncaminhamentosPendentes($descricao_encaminhamentos_pendentes)
    {
        $this->descricao_encaminhamentos_pendentes = $descricao_encaminhamentos_pendentes;
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

        if (Constants::TIPO_MEMBRO_CHAPA === $tipoDenuncia) {

            $profissional = !empty($denuncia->getDenunciaMembroChapa())
                ? $denuncia->getDenunciaMembroChapa()->getMembroChapa()->getProfissional()
                : null;
                
            $denunciado = $profissional ? $profissional->getNome() : null;
        }

        if (Constants::TIPO_MEMBRO_COMISSAO === $tipoDenuncia) {

            $profissional = !empty($denuncia->getDenunciaMembroComissao())
                ? $denuncia->getDenunciaMembroComissao()->getMembroComissao()->getProfissionalEntity()
                : null;

            $denunciado = $profissional ? $profissional->getNome() : null;
        }

        return $denunciado ?? '-';
    }
}
