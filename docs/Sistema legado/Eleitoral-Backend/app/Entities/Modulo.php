<?php
/*
 * Modulo.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use App\Util\Utils;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use OpenApi\Annotations as OA;

/**
 * Entidade de representação da 'Declaracao' no portal SICCAU.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ModuloRepository")
 * @ORM\Table(schema="portal", name="TB_MODULO")
 *
 * @OA\Schema(schema="Modulo")
 *
 * @package App\Entities
 */
class Modulo extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="ID_MODULO", type="integer")
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="portal.TB_MODULO_ID_SEQ", initialValue=1, allocationSize=1)
     *
     * @OA\Property()
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="DS_MODULO", type="string", nullable=false)
     *
     * @OA\Property()
     * @var string
     */
    private $descricao;

    /**
     * Fábrica de instância de 'Modulo'.
     *
     * @param array $data
     * @return \App\Entities\Declaracao
     */
    public static function newInstance($data = null)
    {
        $modulo = new Modulo();
        if ($data != null) {
            $modulo->setId(Utils::getValue('id', $data));
            $modulo->setDescricao(Utils::getValue('descricao', $data));
        }

        return $modulo;
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
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao): void
    {
        $this->descricao = $descricao;
    }
}