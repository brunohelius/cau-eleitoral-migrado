<?php
/*
 * JulgamentoDenunciaTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Entities\ArquivoJulgamentoDenuncia;
use App\Entities\DenunciaDefesa;
use App\Entities\JulgamentoDenuncia;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada ao 'JulgamentoDenuncia'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="JulgamentoDenuncia")
 */
class JulgamentoDenunciaTO
{
    /** @var int|null */
    private $id;

    /** @var \DateTime */
    private $data;

    /** @var boolean */
    private $multa;

    /** @var integer|null */
    private $idDenuncia;

    /** @var boolean */
    private $retificacao;

    /** @var string|null */
    private $justificativa;

    /** @var integer|null */
    private $idTipoJulgamento;

    /** @var ArquivoDescricaoTO[]|null */
    private $descricaoArquivo;

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
     * Retorna uma nova instância de 'JulgamentoDenunciaTO'.
     *
     * @param null $data
     *
     * @return \App\To\JulgamentoDenunciaTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data !== null) {
            $instance->setData(Utils::getValue('data', $data));
            $instance->setMulta(Utils::getBooleanValue('multa', $data));
            $instance->setIdDenuncia(Utils::getValue('idDenuncia', $data));
            $instance->setIdTipoJulgamento(Utils::getValue('tpJulgamento', $data));
            $instance->setDescricaoJulgamento(Utils::getValue('descricao', $data));
            $instance->setRetificacao(Utils::getBooleanValue('retificacao', $data));

            $justificativa = Utils::getValue('justificativa', $data);
            if (!empty($justificativa)) {
                $instance->setJustificativa($justificativa);
            }

            $arquivosExcluidos = Utils::getValue('arquivosExcluidos', $data);
            if (!empty($arquivosExcluidos)) {
                $instance->setIdsArquivosExcluidos($arquivosExcluidos);
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
                $instance->setArquivosJulgamentoDenuncia(array_map(static function($arquivo) {
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            }
        }

        return $instance;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoDenunciaTO'.
     *
     * @param JulgamentoDenuncia $julgamentoDenuncia
     * @return self
     */
    public static function newInstanceFromEntity($julgamentoDenuncia = null)
    {
        $instance = new self();

        if (null !== $julgamentoDenuncia) {
            $instance->setId($julgamentoDenuncia->getId());
            $instance->setData($julgamentoDenuncia->getData());
            $instance->setMulta($julgamentoDenuncia->isMulta());
            $instance->setDescricaoJulgamento($julgamentoDenuncia->getDescricao());
            $instance->setIdTipoJulgamento($julgamentoDenuncia->getTipoJulgamento()->getId());
            $instance->setDescricaoTipoJulgamento($julgamentoDenuncia->getTipoJulgamento()->getDescricao());

            $justificativa = $julgamentoDenuncia->getJustificativa();
            if (!empty($justificativa)) {
                $instance->setJustificativa($justificativa);
            }

            $valorPercentualMulta = $julgamentoDenuncia->getValorPercentualMulta();
            if (null !== $valorPercentualMulta) {
                $instance->setValorPercentualMulta($valorPercentualMulta);
            }

            $quantidadeDiasSuspensaoPropaganda = $julgamentoDenuncia->getQuantidadeDiasSuspensaoPropaganda();
            if (null !== $quantidadeDiasSuspensaoPropaganda) {
                $instance->setQuantidadeDiasSuspensaoPropaganda($quantidadeDiasSuspensaoPropaganda);
            }

            $denuncia = $julgamentoDenuncia->getDenuncia();
            if (null !== $denuncia) {
                $instance->setIdDenuncia($denuncia->getId());
            }

            $tipoSentencaJulgamento = $julgamentoDenuncia->getTipoSentencaJulgamento();
            if (null !== $tipoSentencaJulgamento) {
                $instance->setIdTipoSentencaJulgamento($julgamentoDenuncia->getTipoSentencaJulgamento()->getId());
                $instance->setDescricaoTipoSentencaJulgamento(
                    $julgamentoDenuncia->getTipoSentencaJulgamento()->getDescricao()
                );
            }

            $arquivos = $julgamentoDenuncia->getArquivosJulgamentoDenuncia() ?? [];
            if (!is_array($arquivos)) {
                $arquivos = $arquivos->toArray();
            }
            if (!empty($arquivos)) {
                $instance->setArquivosJulgamentoDenuncia(array_map(static function(ArquivoJulgamentoDenuncia $arquivo) {
                    return ArquivoGenericoTO::newInstance([
                        'id' => $arquivo->getId(),
                        'nome' => $arquivo->getNome(),
                        'nomeFisico' => $arquivo->getNomeFisico()
                    ]);
                }, $arquivos));
            }

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
     * @return int
     */
    public function getIdDenuncia()
    {
        return $this->idDenuncia;
    }

    /**
     * @param int $idDenuncia
     */
    public function setIdDenuncia($idDenuncia): void
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
     * @return ArquivoDescricaoTO[]|null
     */
    public function getDescricaoArquivo(): ?array
    {
        return $this->descricaoArquivo;
    }

    /**
     * @param ArquivoDescricaoTO[]|null $descricaoArquivo
     */
    public function setDescricaoArquivo(?array $descricaoArquivo): void
    {
        $this->descricaoArquivo = $descricaoArquivo;
    }

    /**
     * @param int
     */
    public function setIdTipoJulgamento(?int $idTipoJulgamento): void
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
