<?php

namespace App\To;

use App\Entities\DenunciaDefesa;
use App\Entities\EncaminhamentoDenuncia;
use OpenApi\Annotations as OA;

/**
 * Classe de transferência associada ao 'EncaminhamentoDenuncia'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 *
 * @OA\Schema(schema="EncaminhamentoDenuncia")
 */
class EncaminhamentoDenunciaTO
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
    private $idDenuncia;

    /**
     * @var string
     */
    private $descricaoEncaminhamento;

    /**
     * Retorna uma nova instância de 'EncaminhamentoDenunciaTO'.
     *
     * @param EncaminhamentoDenuncia $encaminhamentoDenuncia
     * @return self
     */
    public static function newInstanceFromEntity($encaminhamentoDenuncia = null)
    {
        $instance = new self;

        if (null !== $encaminhamentoDenuncia) {
            $instance->setId($encaminhamentoDenuncia->getId());
            $instance->setData($encaminhamentoDenuncia->getData());
            $instance->setDescricaoEncaminhamento($encaminhamentoDenuncia->getDescricao());

            $denuncia = $encaminhamentoDenuncia->getDenuncia();
            if (null !== $denuncia) {
                $instance->setIdDenuncia($denuncia->getId());
            }
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
    public function getDescricaoEncaminhamento()
    {
        return $this->descricaoEncaminhamento;
    }

    /**
     * @param $descricaoEncaminhamento
     */
    public function setDescricaoEncaminhamento($descricaoEncaminhamento)
    {
        $this->descricaoEncaminhamento = $descricaoEncaminhamento;
    }
}
