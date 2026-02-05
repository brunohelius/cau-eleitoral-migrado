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

use App\Config\Constants;
use App\Entities\RecursoDenuncia;
use App\Util\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada ao 'ContrarrazaoDenuncia'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="ContrarrazaoDenuncia")
 */
class ContrarrazaoRecursoDenunciaTO
{
    /** @var int|null */
    private $id;

    /** @var \DateTime */
    private $data;

    /** @var integer|null */
    private $tpRecurso;

    /** @var integer|null */
    private $idDenuncia;

    /** @var string|null */
    private $responsavel;

    /** @var string|null */
    private $descricaoRecurso;

    /** @var array|\Doctrine\Common\Collections\ArrayCollection */
    private $arquivosContrarrazao;

    /**
     * Retorna uma nova instância de 'ContrarrazaoDenunciaTO'.
     *
     * @param null $data
     *
     * @return \App\To\ContrarrazaoRecursoDenunciaTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $instance = new self();

        if ($data !== null) {
            $instance->setData(Utils::getValue('dtRecurso', $data));
            $instance->setIdDenuncia(Utils::getValue('idDenuncia', $data));
            $instance->setDescricaoRecurso(Utils::getValue('descricao', $data));

            $arquivos = Utils::getValue('arquivosContrarrazao', $data);
            if (!empty($arquivos)) {
                $instance->setArquivosContrarrazao(array_map(static function($arquivo) {
                    return ArquivoGenericoTO::newInstance($arquivo);
                }, $arquivos));
            }
        }

        return $instance;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoDenunciaTO'.
     *
     * @param RecursoDenuncia $contrarrazaoRecursoDenuncia
     * @return self
     */
    public static function newInstanceFromEntity($contrarrazaoRecursoDenuncia = null)
    {
        $instance = new self();

        if (null !== $contrarrazaoRecursoDenuncia) {
            $instance->setId($contrarrazaoRecursoDenuncia->getId());
            $instance->setData($contrarrazaoRecursoDenuncia->getDtRecurso());
            $instance->setDescricaoRecurso($contrarrazaoRecursoDenuncia->getDsRecurso());
            $instance->setResponsavel($contrarrazaoRecursoDenuncia->getProfissional()->getNome());
            $instance->setTpRecurso($contrarrazaoRecursoDenuncia->getTipoRecursoContrarrazaoDenuncia());

            $denuncia = $contrarrazaoRecursoDenuncia->getDenuncia();
            if (null !== $denuncia) {
                $instance->setIdDenuncia($denuncia->getId());
            }

            $arquivos = $contrarrazaoRecursoDenuncia->getArquivos() ?? [];
            if (!is_array($arquivos)) {
                $arquivos = $arquivos->toArray();
            }

            $instance->setArquivosContrarrazao($arquivos);
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
     * @return string|null
     */
    public function getDescricaoRecurso()
    {
        return $this->descricaoRecurso;
    }

    /**
     * @param string $descricaoRecurso
     */
    public function setDescricaoRecurso(?string $descricaoRecurso): void
    {
        $this->descricaoRecurso = $descricaoRecurso;
    }

    /**
     * @return string|null
     */
    public function getResponsavel()
    {
        return $this->responsavel;
    }

    /**
     * @param string|null $responsavel
     */
    public function setResponsavel(?string $responsavel): void
    {
        $this->responsavel = $responsavel;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getArquivosContrarrazao()
    {
        return $this->arquivosContrarrazao;
    }

    /**
     * @param array|ArrayCollection $arquivosContrarrazao
     */
    public function setArquivosContrarrazao($arquivosContrarrazao): void
    {
        $this->arquivosContrarrazao = $arquivosContrarrazao;
    }

    /**
     * @return int|null
     */
    public function getTpRecurso()
    {
        return $this->tpRecurso;
    }

    /**
     * @param int|null $tpRecurso
     */
    public function setTpRecurso(?int $tpRecurso): void
    {
        $this->tpRecurso = $tpRecurso;
    }

    /**
     * Verifica se a contrarrazao foi efetuada pelo denunciante
     * @return bool
     */
    public function isContrarrazaoDenunciante() {
        return $this->getTpRecurso() == Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIADO;
    }


}
