<?php
/*
 * RetificacaoJulgamentoDenunciaTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Entities\RetificacaoJulgamentoDenuncia;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada ao 'RetificacaoJulgamentoDenuncia'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="RetificacaoJulgamentoDenuncia")
 */
class RetificacaoJulgamentoDenunciaTO
{
    /** @var int|null */
    private $id;

    /** @var \DateTime */
    private $data;

    /** @var boolean */
    private $multa;

    /** @var UsuarioTO */
    private $usuario;

    /** @var integer|null */
    private $idDenuncia;

    /** @var boolean */
    private $retificacao;

    /** @var string|null */
    private $justificativa;

    /** @var integer|null */
    private $idTipoJulgamento;

    /** @var string|null */
    private $descricaoJulgamento;

    /** @var array */
    private $idsArquivosExcluidos;

    /** @var integer|null */
    private $valorPercentualMulta;

    /** @var string|null */
    private $descricaoTipoJulgamento;

    /** @var integer|null */
    private $idTipoSentencaJulgamento;

    /** @var array|\Doctrine\Common\Collections\ArrayCollection */
    private $arquivosJulgamentoDenuncia;

    /** @var string|null */
    private $descricaoTipoSentencaJulgamento;

    /** @var integer|null */
    private $quantidadeDiasSuspensaoPropaganda;

    /**
     * Retorna uma nova instância de 'RetificacaoJulgamentoDenunciaTO'.
     *
     * @param RetificacaoJulgamentoDenuncia $retificacaoJulgamentoDenuncia
     *
     * @return self
     * @throws \Exception
     */
    public static function newInstanceFromEntity($retificacaoJulgamentoDenuncia = null)
    {
        $instance = new self();

        if (null !== $retificacaoJulgamentoDenuncia) {
            $instance->setId($retificacaoJulgamentoDenuncia->getId());
            $instance->setData($retificacaoJulgamentoDenuncia->getData());
            $instance->setMulta($retificacaoJulgamentoDenuncia->isMulta());
            $instance->setDescricaoJulgamento($retificacaoJulgamentoDenuncia->getDescricao());

            $usuario = $retificacaoJulgamentoDenuncia->getUsuario();
            if (!is_null($usuario)) {
                $usuario->definirNomes();
                $instance->setUsuario(UsuarioTO::newInstanceFromEntity($usuario));
            }

            $denuncia = $retificacaoJulgamentoDenuncia->getDenuncia();
            if (null !== $denuncia) {
                $instance->setIdDenuncia($denuncia->getId());
            }

            $tipoJulgamento = $retificacaoJulgamentoDenuncia->getTipoJulgamento();
            if (null !== $tipoJulgamento) {
                $instance->setIdTipoJulgamento($tipoJulgamento->getId());
                $instance->setDescricaoTipoJulgamento($tipoJulgamento->getDescricao());
            }

            $justificativa = $retificacaoJulgamentoDenuncia->getJustificativa();
            if (!empty($justificativa)) {
                $instance->setJustificativa($justificativa);
            }

            $valorPercentualMulta = $retificacaoJulgamentoDenuncia->getValorPercentualMulta();
            if (null !== $valorPercentualMulta) {
                $instance->setValorPercentualMulta($valorPercentualMulta);
            }

            $quantidadeDiasSuspensaoPropaganda = $retificacaoJulgamentoDenuncia->getQuantidadeDiasSuspensaoPropaganda();
            if (null !== $quantidadeDiasSuspensaoPropaganda) {
                $instance->setQuantidadeDiasSuspensaoPropaganda($quantidadeDiasSuspensaoPropaganda);
            }

            $tipoSentencaJulgamento = $retificacaoJulgamentoDenuncia->getTipoSentencaJulgamento();
            if (null !== $tipoSentencaJulgamento) {
                $instance->setIdTipoSentencaJulgamento($retificacaoJulgamentoDenuncia->getTipoSentencaJulgamento()->getId());
                $instance->setDescricaoTipoSentencaJulgamento(
                    $retificacaoJulgamentoDenuncia->getTipoSentencaJulgamento()->getDescricao()
                );
            }

            $arquivos = $retificacaoJulgamentoDenuncia->getArquivosJulgamentoDenuncia() ?? [];
            if (!is_array($arquivos)) {
                $arquivos = $arquivos->toArray();
            }

            $instance->setArquivosJulgamentoDenuncia($arquivos);
        }

        return $instance;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return \DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \DateTime $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function isMulta()
    {
        return $this->multa;
    }

    /**
     * @param bool $multa
     */
    public function setMulta(bool $multa): void
    {
        $this->multa = $multa;
    }

    /**
     * @return UsuarioTO
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param UsuarioTO $usuario
     */
    public function setUsuario($usuario): void
    {
        $this->usuario = $usuario;
    }

    /**
     * @return int
     */
    public function getIdDenuncia()
    {
        return $this->idDenuncia;
    }

    /**
     * @param int $idDenuncia
     */
    public function setIdDenuncia(int $idDenuncia): void
    {
        $this->idDenuncia = $idDenuncia;
    }

    /**
     * @return bool
     */
    public function isRetificacao()
    {
        return $this->retificacao;
    }

    /**
     * @param bool $retificacao
     */
    public function setRetificacao(bool $retificacao): void
    {
        $this->retificacao = $retificacao;
    }

    /**
     * @return string|null
     */
    public function getJustificativa()
    {
        return $this->justificativa;
    }

    /**
     * @param string|null $justificativa
     */
    public function setJustificativa($justificativa): void
    {
        $this->justificativa = $justificativa;
    }

    /**
     * @return int
     */
    public function getIdTipoJulgamento()
    {
        return $this->idTipoJulgamento;
    }

    /**
     * @param int
     */
    public function setIdTipoJulgamento(int $idTipoJulgamento): void
    {
        $this->idTipoJulgamento = $idTipoJulgamento;
    }

    /**
     * @return string|null
     */
    public function getDescricaoJulgamento()
    {
        return $this->descricaoJulgamento;
    }

    /**
     * @param string $descricaoJulgamento
     */
    public function setDescricaoJulgamento(?string $descricaoJulgamento): void
    {
        $this->descricaoJulgamento = $descricaoJulgamento;
    }

    /**
     * @return array
     */
    public function getIdsArquivosExcluidos()
    {
        return $this->idsArquivosExcluidos ?? [];
    }

    /**
     * @param string|null $idsArquivosExcluidos
     */
    public function setIdsArquivosExcluidos($idsArquivosExcluidos): void
    {
        $this->idsArquivosExcluidos = Utils::getArrayFromString($idsArquivosExcluidos);
    }

    /**
     * @return int|null
     */
    public function getValorPercentualMulta()
    {
        return $this->valorPercentualMulta;
    }

    /**
     * @param int $valorPercentualMulta
     */
    public function setValorPercentualMulta(?int $valorPercentualMulta): void
    {
        $this->valorPercentualMulta = $valorPercentualMulta;
    }

    /**
     * @return string|null
     */
    public function getDescricaoTipoJulgamento(): ?string
    {
        return $this->descricaoTipoJulgamento;
    }

    /**
     * @param string|null $descricaoTipoJulgamento
     */
    public function setDescricaoTipoJulgamento(?string $descricaoTipoJulgamento): void
    {
        $this->descricaoTipoJulgamento = $descricaoTipoJulgamento;
    }

    /**
     * @return int|null
     */
    public function getIdTipoSentencaJulgamento()
    {
        return $this->idTipoSentencaJulgamento;
    }

    /**
     * @param int $idTipoSentencaJulgamento
     */
    public function setIdTipoSentencaJulgamento(int $idTipoSentencaJulgamento): void
    {
        $this->idTipoSentencaJulgamento = $idTipoSentencaJulgamento;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getArquivosJulgamentoDenuncia()
    {
        return $this->arquivosJulgamentoDenuncia;
    }

    /**
     * @param array|ArrayCollection $arquivosJulgamentoDenuncia
     */
    public function setArquivosJulgamentoDenuncia($arquivosJulgamentoDenuncia): void
    {
        $this->arquivosJulgamentoDenuncia = $arquivosJulgamentoDenuncia;
    }

    /**
     * @return string|null
     */
    public function getDescricaoTipoSentencaJulgamento(): ?string
    {
        return $this->descricaoTipoSentencaJulgamento;
    }

    /**
     * @param string|null $descricaoTipoSentencaJulgamento
     */
    public function setDescricaoTipoSentencaJulgamento(?string $descricaoTipoSentencaJulgamento): void
    {
        $this->descricaoTipoSentencaJulgamento = $descricaoTipoSentencaJulgamento;
    }

    /**
     * @return int
     */
    public function getQuantidadeDiasSuspensaoPropaganda()
    {
        return $this->quantidadeDiasSuspensaoPropaganda;
    }

    /**
     * @param int $quantidadeDiasSuspensaoPropaganda
     */
    public function setQuantidadeDiasSuspensaoPropaganda(?int $quantidadeDiasSuspensaoPropaganda): void
    {
        $this->quantidadeDiasSuspensaoPropaganda = $quantidadeDiasSuspensaoPropaganda;
    }
}
