<?php

namespace App\To;

use App\Config\Constants;
use App\Entities\RecursoDenuncia;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada ao 'RecursoDenuncia'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="RecursoDenuncia")
 */
class RecursoDenunciaTO
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $data;

    /**
     * @var integer
     */
    private $tpRecurso;

    /**
     * @var integer
     */
    private $idDenuncia;

    /**
     * @var ContrarrazaoRecursoDenunciaTO|null
     */
    private $contrarrazao;

    /**
     * @var JulgamentoRecursoDenunciaTO|null
     */
    private $julgamentoRecurso;

    /**
     * @var string
     */
    private $responsavel;

    /**
     * @var string
     */
    private $descricaoRecurso;

    /**
     * @var integer
     */
    private $id_responsavel;

    /** @var array|\Doctrine\Common\Collections\ArrayCollection */
    private $arquivos;

    /**
     * Transient
     *
     * @var integer|null
     */
    private $isPrazoRecurso;

    /**
     * Transient
     *
     * @var integer|null
     */
    private $isPrazoContrarrazao;

    /**
     * Retorna uma nova instância de 'RecursoDenunciaTO'.
     *
     * @param RecursoDenuncia $recursoDenuncia
     * @return self
     */
    public static function newInstanceFromEntity($recursoDenuncia = null)
    {
        $instance = new self;

        if (null !== $recursoDenuncia) {
            $instance->setId($recursoDenuncia->getId());
            $instance->setData($recursoDenuncia->getDtRecurso());
            $instance->setDescricaoRecurso($recursoDenuncia->getDsRecurso());
            $instance->setResponsavel($recursoDenuncia->getProfissional()
                ->getNome());
            $instance->setIdResponsavel($recursoDenuncia->getProfissional()->getId());
            $instance->setTipoRecurso(
                $recursoDenuncia->getTipoRecursoContrarrazaoDenuncia());

            $denuncia = $recursoDenuncia->getDenuncia();
            if (null !== $denuncia) {
                $instance->setIdDenuncia($denuncia->getId());
            }

            $contrarrazao = $recursoDenuncia->getContrarrazao();
            if (null !== $contrarrazao) {
                $instance->setContrarrazao(
                    ContrarrazaoRecursoDenunciaTO::newInstanceFromEntity($contrarrazao)
                );
            }

            $julgamentoRecursoDenuncia = $recursoDenuncia->getUltimoJulgamentoDenunciado()
                ?? $recursoDenuncia->getUltimoJulgamentoDenunciante();
            if (null !== $julgamentoRecursoDenuncia) {
                $julgamentoDenunciaTO = JulgamentoRecursoDenunciaTO::newInstanceFromEntity(
                    $julgamentoRecursoDenuncia);

                $julgamentosRecursoDenuncia = $recursoDenuncia->getJulgamentoRecursoDenunciado()
                    ?? $recursoDenuncia->getJulgamentoRecursoDenunciante();

                $julgamentoDenunciaTO->setRetificacao(
                    count($julgamentosRecursoDenuncia) > 1
                );

                $instance->setJulgamentoRecurso($julgamentoDenunciaTO);
            }

            $arquivos = $recursoDenuncia->getArquivos() ?? [];
            if (!is_array($arquivos)) {
                $arquivos = $arquivos->toArray();
            }

            $instance->setArquivos($arquivos);
        }

        return $instance;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getResponsavel()
    {
        return $this->responsavel;
    }

    /**
     * @param string $responsavel
     */
    public function setResponsavel($responsavel): void
    {
        $this->responsavel = $responsavel;
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
    public function setData($data)
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
     * @param $idDenuncia
     */
    public function setIdDenuncia($idDenuncia)
    {
        $this->idDenuncia = $idDenuncia;
    }

    /**
     * @return string
     */
    public function getDescricaoRecurso()
    {
        return $this->descricaoRecurso;
    }

    /**
     * @param $descricaoRecurso
     */
    public function setDescricaoRecurso($descricaoRecurso)
    {
        $this->descricaoRecurso = $descricaoRecurso;
    }

    /**
     * @return int
     */
    public function getTipoRecurso()
    {
        return $this->tpRecurso;
    }

    /**
     * @param int $tpRecurso
     */
    public function setTipoRecurso($tpRecurso): void
    {
        $this->tpRecurso = $tpRecurso;
    }

    /**
     * @return ContrarrazaoRecursoDenunciaTO|null
     */
    public function getContrarrazao()
    {
        return $this->contrarrazao;
    }

    /**
     * @param ContrarrazaoRecursoDenunciaTO $contrarrazao
     */
    public function setContrarrazao($contrarrazao): void
    {
        $this->contrarrazao = $contrarrazao;
    }

    /**
     * @return JulgamentoRecursoDenunciaTO|null
     */
    public function getJulgamentoRecurso()
    {
        return $this->julgamentoRecurso;
    }

    /**
     * @param JulgamentoRecursoDenunciaTO|null $julgamentoRecurso
     */
    public function setJulgamentoRecurso($julgamentoRecurso): void
    {
        $this->julgamentoRecurso = $julgamentoRecurso;
    }

    /**
     * @return int
     */
    public function getIdResponsavel(): int
    {
        return $this->id_responsavel;
    }

    /**
     * @param int $id_responsavel
     */
    public function setIdResponsavel(int $id_responsavel): void
    {
        $this->id_responsavel = $id_responsavel;
    }

    /**
     * @return array|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getArquivos()
    {
        return $this->arquivos;
    }

    /**
     * @param array|\Doctrine\Common\Collections\ArrayCollection $arquivos
     */
    public function setArquivos($arquivos): void
    {
        $this->arquivos = $arquivos;
    }

    /**
     * Verifica se o recurso em questão é do denunciante
     * @return bool
     */
    public function isRecursoDenunciante() {
        return $this->getTipoRecurso() == Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE;
    }

    /**
     * @return bool
     */
    public function isPrazoRecurso(): bool
    {
        return $this->isPrazoRecurso;
    }

    /**
     * @param int|null $isPrazoRecurso
     */
    public function setIsPrazoRecurso($isPrazoRecurso): void
    {
        $this->isPrazoRecurso = $isPrazoRecurso;
    }

    /**
     * @return bool
     */
    public function isPrazoContrarrazao(): bool
    {
        return $this->isPrazoContrarrazao;
    }

    /**
     * @param int|null $isPrazoContrarrazao
     */
    public function setIsPrazoContrarrazao($isPrazoContrarrazao): void
    {
        $this->isPrazoContrarrazao = $isPrazoContrarrazao;
    }
}
