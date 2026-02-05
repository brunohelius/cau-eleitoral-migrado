<?php

namespace App\To;

use App\Entities\DenunciaInadmitida;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada ao 'DenunciaInadmitida'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="DenunciaInadmitida")
 */
class DenunciaInadmitidaTO
{
    /**
     * @var integer
     * @OA\Property()
     */
    private $id;

    /**
     * @var null|ArquivoDenunciaTO[]
     * @OA\Property()
     */
    private $arquivos;

    /**
     * @var \DateTime
     * @OA\Property()
     */
    private $dataInadmissao;

    /**
     * @var string
     * @OA\Property()
     */
    private $descricaoInadmissao;

    /**
     * Retorna uma nova instância de 'DenunciaInadmitidaTO'.
     *
     * @param DenunciaInadmitida $denunciaInadmitida
     * @return self
     */
    public static function newInstanceFromEntity($denunciaInadmitida = null)
    {
        $instance = new self;

        if (null !== $denunciaInadmitida) {
            $instance->setId($denunciaInadmitida->getId());
            $instance->setDataInadmissao($denunciaInadmitida->getDataInadmissao());
            $instance->setDescricaoInadmissao($denunciaInadmitida->getDescricao());

            $arquivos = $denunciaInadmitida->getArquivoDenunciaInadmitida() ?? [];
            if (!is_array($arquivos)) {
                $arquivos = $arquivos->toArray();
            }

            $instance->setArquivos(array_map(static function($arquivo) {
                return ArquivoDenunciaTO::newInstanceFromEntity($arquivo);
            }, $arquivos));
        }
        return $instance;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return \DateTime
     */
    public function getDataInadmissao(): \DateTime
    {
        return $this->dataInadmissao;
    }

    /**
     * @param \DateTime $dataInadmissao
     */
    public function setDataInadmissao($dataInadmissao): void
    {
        $this->dataInadmissao = $dataInadmissao;
    }

    /**
     * @return string
     */
    public function getDescricaoInadmissao(): string
    {
        return $this->descricaoInadmissao;
    }

    /**
     * @param string $descricaoInadmissao
     */
    public function setDescricaoInadmissao($descricaoInadmissao): void
    {
        $this->descricaoInadmissao = $descricaoInadmissao;
    }

    /**
     * @return null|ArquivoDenunciaTO[]
     */
    public function getArquivos(): ?array
    {
        return $this->arquivos;
    }

    /**
     * @param ArquivoDenunciaTO[] $arquivos
     */
    public function setArquivos($arquivos): void
    {
        $this->arquivos = $arquivos;
    }
}
