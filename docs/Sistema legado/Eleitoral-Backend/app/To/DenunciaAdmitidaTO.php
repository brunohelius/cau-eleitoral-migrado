<?php

namespace App\To;

use App\Entities\DenunciaAdmitida;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada ao 'DenunciaAdmitida'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="DenunciaAdmitida")
 */
 class DenunciaAdmitidaTO
{
    /**
     * @var integer
     * @OA\Property()
     */
    private $id;

    /**
     * @var \DateTime
     * @OA\Property()
     */
    private $dataAdmissao;

    /**
     * @var string
     * @OA\Property()
     */
    private $descricaoDespacho;

    /**
     * @var null|MembroComissaoTO
     * @OA\Property()
     */
    private $membroComissao;

    /**
     * Retorna uma nova instância de 'DenunciaAdmitidaTO'.
     *
     * @param DenunciaAdmitida $denunciaAdmitida
     * @return self
     */
    public static function newInstanceFromEntity($denunciaAdmitida = null)
    {
        $instance = new self;

        if ($denunciaAdmitida != null) {
            $instance->setId($denunciaAdmitida->getId());
            $instance->setDataAdmissao($denunciaAdmitida->getDataAdmissao());
            $instance->setDescricaoDespacho($denunciaAdmitida->getDescricaoDespacho());
            $instance->setMembroComissao(
                MembroComissaoTO::newInstanceFromEntity($denunciaAdmitida->getMembroComissao())
            );
        }

        return $instance;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return \DateTime
     */
    public function getDataAdmissao(): \DateTime
    {
        return $this->dataAdmissao;
    }

    /**
     * @param \DateTime $dataAdmissao
     */
    public function setDataAdmissao($dataAdmissao): void
    {
        $this->dataAdmissao = $dataAdmissao;
    }

    /**
     * @return string
     */
    public function getDescricaoDespacho(): string
    {
        return $this->descricaoDespacho;
    }

    /**
     * @param string $descricaoDespacho
     */
    public function setDescricaoDespacho(string $descricaoDespacho): void
    {
        $this->descricaoDespacho = $descricaoDespacho;
    }

    /**
     * @return null|MembroComissaoTO
     */
    public function getMembroComissao(): ?MembroComissaoTO
    {
        return $this->membroComissao;
    }

    /**
     * @param MembroComissaoTO $membroComissao
     */
    public function setMembroComissao($membroComissao): void
    {
        $this->membroComissao = $membroComissao;
    }
}