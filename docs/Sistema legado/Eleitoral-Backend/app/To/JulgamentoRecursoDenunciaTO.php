<?php
/*
 * JulgamentoRecursoDenunciaTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Entities\JulgamentoRecursoDenuncia;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada ao 'JulgamentoRecursoDenuncia'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="JulgamentoRecursoDenuncia")
 */
class JulgamentoRecursoDenunciaTO
{
    /** @var int|null */
    private $id;

    /** @var \DateTime */
    private $data;

    /** @var boolean */
    private $multa;

    /** @var boolean */
    private $sancao;

    /** @var UsuarioTO */
    private $usuario;

    /** @var boolean */
    private $retificacao;

    /** @var string|null */
    private $justificativa;

    /** @var string|null */
    private $descricaoSancao;

    /** @var string|null */
    private $descricaoJulgamento;

    /** @var integer|null */
    private $idRecursoDenunciado;

    /** @var integer|null */
    private $idRecursoDenunciante;

    /** @var integer|null */
    private $valorPercentualMulta;

    /** @var integer|null */
    private $idTipoSentencaJulgamento;

    /** @var integer|null */
    private $idTipoJulgamentoDenunciado;

    /** @var integer|null */
    private $idTipoJulgamentoDenunciante;

    /** @var string|null */
    private $descricaoTipoSentencaJulgamento;

    /** @var array|\Doctrine\Common\Collections\ArrayCollection */
    private $arquivosJulgamentoRecursoDenuncia;

    /** @var integer|null */
    private $quantidadeDiasSuspensaoPropaganda;

    /** @var string|null */
    private $descricaoTipoJulgamentoDenunciado;

    /** @var string|null */
    private $descricaoTipoJulgamentoDenunciante;

    /**
     * Retorna uma nova instância de 'JulgamentoDenunciaTO'.
     *
     * @param null $data
     *
     * @return self
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data !== null) {
            $instance->setData(Utils::getValue('data', $data));
            $instance->setSancao(Utils::getBooleanValue('sancao', $data));
            $instance->setDescricaoJulgamento(Utils::getValue('descricao', $data));
            $instance->setRetificacao(Utils::getBooleanValue('retificacao', $data));
            $instance->setIdRecursoDenunciado(Utils::getValue('idRecursoDenuncia', $data));
            $instance->setIdTipoJulgamentoDenunciado(Utils::getValue('tpJulgamentoDenunciado', $data));
            $instance->setIdTipoJulgamentoDenunciante(Utils::getValue('tpJulgamentoDenunciante', $data));

            $multa = Utils::getValue('multa', $data);
            if (!empty($multa)) {
                $instance->setMulta($multa);
            }

            $justificativa = Utils::getValue('justificativa', $data);
            if (!empty($justificativa)) {
                $instance->setJustificativa($justificativa);
            }

            $valorPercentualMulta = Utils::getValue('vlPercentualMulta', $data);
            if (!empty($valorPercentualMulta)) {
                $instance->setValorPercentualMulta($valorPercentualMulta);
            }

            $tipoSentencaJulgamento = Utils::getValue('tpSentencaJulgamento', $data);
            if (!empty($tipoSentencaJulgamento)) {
                $instance->setIdTipoSentencaJulgamento($tipoSentencaJulgamento);
            }

            $qtDiasSuspensaoPropaganda = Utils::getValue('qtDiasSuspensaoPropaganda', $data);
            if (!empty($qtDiasSuspensaoPropaganda)) {
                $instance->setQuantidadeDiasSuspensaoPropaganda($qtDiasSuspensaoPropaganda);
            }

            $arquivos = Utils::getValue('arquivos', $data);
            if (!empty($arquivos)) {
                $instance->setArquivosJulgamentoRecursoDenuncia(array_map(static function($arquivo) {
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            }
        }

        return $instance;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoRecursoDenunciaTO'.
     *
     * @param JulgamentoRecursoDenuncia $julgamentoRecurso
     * @return self
     */
    public static function newInstanceFromEntity($julgamentoRecurso = null)
    {
        $instance = new self();

        if (null !== $julgamentoRecurso) {
            $instance->setId($julgamentoRecurso->getId());
            $instance->setData($julgamentoRecurso->getData());
            $instance->setMulta($julgamentoRecurso->isMulta());
            $instance->setSancao($julgamentoRecurso->isSancao());
            $instance->setDescricaoJulgamento($julgamentoRecurso->getDescricao());
            $instance->setDescricaoSancao($instance->getDescricaoSancaoFormatada($julgamentoRecurso->isSancao()));

            $usuario = $julgamentoRecurso->getUsuario();
            if (!is_null($usuario)) {
                $usuario->definirNomes();
                $instance->setUsuario(UsuarioTO::newInstanceFromEntity($usuario));
            }

            $justificativa = $julgamentoRecurso->getJustificativa();
            if (!empty($justificativa)) {
                $instance->setJustificativa($justificativa);
            }

            $tipoJulgamentoDenunciado = $julgamentoRecurso->getTipoJulgamentoDenunciado();
            if ($tipoJulgamentoDenunciado !== null) {
                $instance->setIdTipoJulgamentoDenunciado($tipoJulgamentoDenunciado->getId());
                $instance->setDescricaoTipoJulgamentoDenunciado($tipoJulgamentoDenunciado->getDescricao());
            }

            $tipoJulgamentoDenunciante = $julgamentoRecurso->getTipoJulgamentoDenunciante();
            if ($tipoJulgamentoDenunciante !== null) {
                $instance->setIdTipoJulgamentoDenunciante($tipoJulgamentoDenunciante->getId());
                $instance->setDescricaoTipoJulgamentoDenunciante($tipoJulgamentoDenunciante->getDescricao());
            }

            $valorPercentualMulta = $julgamentoRecurso->getValorPercentualMulta();
            if (null !== $valorPercentualMulta) {
                $instance->setValorPercentualMulta($valorPercentualMulta);
            }

            $quantidadeDiasSuspensaoPropaganda = $julgamentoRecurso->getQuantidadeDiasSuspensaoPropaganda();
            if (null !== $quantidadeDiasSuspensaoPropaganda) {
                $instance->setQuantidadeDiasSuspensaoPropaganda($quantidadeDiasSuspensaoPropaganda);
            }

            $recursoDenunciado = $julgamentoRecurso->getRecursoDenunciado();
            if (null !== $recursoDenunciado) {
                $instance->setIdRecursoDenunciado($recursoDenunciado->getId());
            }

            $recursoDenunciante = $julgamentoRecurso->getRecursoDenunciante();
            if (null !== $recursoDenunciante) {
                $instance->setIdRecursoDenunciante($recursoDenunciante->getId());
            }

            $tipoSentencaJulgamento = $julgamentoRecurso->getTipoSentencaJulgamento();
            if (null !== $tipoSentencaJulgamento) {
                $instance->setIdTipoSentencaJulgamento($julgamentoRecurso->getTipoSentencaJulgamento()->getId());
                $instance->setDescricaoTipoSentencaJulgamento(
                    $julgamentoRecurso->getTipoSentencaJulgamento()->getDescricao()
                );
            }

            $arquivos = $julgamentoRecurso->getArquivosJulgamentoRecursoDenuncia() ?? [];
            if (!is_array($arquivos)) {
                $arquivos = $arquivos->toArray();
            }

            $instance->setArquivosJulgamentoRecursoDenuncia($arquivos);
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
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getDescricaoSancao()
    {
        return $this->descricaoSancao;
    }

    /**
     * @param string|null $descricaoSancao
     */
    public function setDescricaoSancao($descricaoSancao): void
    {
        $this->descricaoSancao = $descricaoSancao;
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
     * @return bool
     */
    public function isMulta()
    {
        return $this->multa;
    }

    /**
     * @param bool $multa
     */
    public function setMulta(?bool $multa): void
    {
        $this->multa = $multa;
    }

    /**
     * @return bool
     */
    public function isSancao(): bool
    {
        return $this->sancao;
    }

    /**
     * @param bool $sancao
     */
    public function setSancao(bool $sancao): void
    {
        $this->sancao = $sancao;
    }

    /**
     * @return int
     */
    public function getIdRecursoDenunciado()
    {
        return $this->idRecursoDenunciado;
    }

    /**
     * @param int $idRecursoDenunciado
     */
    public function setIdRecursoDenunciado($idRecursoDenunciado): void
    {
        $this->idRecursoDenunciado = $idRecursoDenunciado;
    }

    /**
     * @return int|null
     */
    public function getIdRecursoDenunciante()
    {
        return $this->idRecursoDenunciante;
    }

    /**
     * @param int|null $idRecursoDenunciante
     */
    public function setIdRecursoDenunciante($idRecursoDenunciante): void
    {
        $this->idRecursoDenunciante = $idRecursoDenunciante;
    }

    /**
     * @return int
     */
    public function getIdTipoJulgamentoDenunciado()
    {
        return $this->idTipoJulgamentoDenunciado;
    }

    /**
     * @param int
     */
    public function setIdTipoJulgamentoDenunciado($idTipoJulgamentoDenunciado): void
    {
        $this->idTipoJulgamentoDenunciado = $idTipoJulgamentoDenunciado;
    }

    /**
     * @return int
     */
    public function getIdTipoJulgamentoDenunciante()
    {
        return $this->idTipoJulgamentoDenunciante;
    }

    /**
     * @param int
     */
    public function setIdTipoJulgamentoDenunciante($idTipoJulgamentoDenunciante): void
    {
        $this->idTipoJulgamentoDenunciante = $idTipoJulgamentoDenunciante;
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
    public function setDescricaoJulgamento($descricaoJulgamento): void
    {
        $this->descricaoJulgamento = $descricaoJulgamento;
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
    public function setValorPercentualMulta($valorPercentualMulta): void
    {
        $this->valorPercentualMulta = $valorPercentualMulta;
    }

    /**
     * @return string|null
     */
    public function getDescricaoTipoJulgamentoDenunciado()
    {
        return $this->descricaoTipoJulgamentoDenunciado;
    }

    /**
     * @param string|null $descricaoTipoJulgamentoDenunciado
     */
    public function setDescricaoTipoJulgamentoDenunciado($descricaoTipoJulgamentoDenunciado): void
    {
        $this->descricaoTipoJulgamentoDenunciado = $descricaoTipoJulgamentoDenunciado;
    }

    /**
     * @return string|null
     */
    public function getDescricaoTipoJulgamentoDenunciante()
    {
        return $this->descricaoTipoJulgamentoDenunciante;
    }

    /**
     * @param string|null $descricaoTipoJulgamentoDenunciante
     */
    public function setDescricaoTipoJulgamentoDenunciante($descricaoTipoJulgamentoDenunciante): void
    {
        $this->descricaoTipoJulgamentoDenunciante = $descricaoTipoJulgamentoDenunciante;
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
    public function setIdTipoSentencaJulgamento($idTipoSentencaJulgamento): void
    {
        $this->idTipoSentencaJulgamento = $idTipoSentencaJulgamento;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getArquivosJulgamentoRecursoDenuncia()
    {
        return $this->arquivosJulgamentoRecursoDenuncia;
    }

    /**
     * @param array|ArrayCollection $arquivosJulgamentoRecursoDenuncia
     */
    public function setArquivosJulgamentoRecursoDenuncia($arquivosJulgamentoRecursoDenuncia): void
    {
        $this->arquivosJulgamentoRecursoDenuncia = $arquivosJulgamentoRecursoDenuncia;
    }

    /**
     * @return string|null
     */
    public function getDescricaoTipoSentencaJulgamento()
    {
        return $this->descricaoTipoSentencaJulgamento;
    }

    /**
     * @param string|null $descricaoTipoSentencaJulgamento
     */
    public function setDescricaoTipoSentencaJulgamento($descricaoTipoSentencaJulgamento): void
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
    public function setQuantidadeDiasSuspensaoPropaganda($quantidadeDiasSuspensaoPropaganda): void
    {
        $this->quantidadeDiasSuspensaoPropaganda = $quantidadeDiasSuspensaoPropaganda;
    }

    /**
     * Retorna se existe sanção aplicada.
     *
     * @param $isSancao
     * @return string
     */
    private function getDescricaoSancaoFormatada($isSancao)
    {
        return $isSancao ? 'Sim' : 'Não';
    }
}
