<?php
/*
 * Permissao.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use OpenApi\Annotations as OA;

/**
 * Entidade de representação da 'Filial' / 'CAU/UF'
 *
 * @ORM\Entity(repositoryClass="App\Repository\PermissaoRepository")
 * @ORM\Table(schema="public", name="tb_permissoes")
 *
 * @OA\Schema(schema="Permissao")
 *
 * @package App\Entities
 */
class Permissao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     *
     * @OA\Property()
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="numero", type="integer")
     *
     * @OA\Property()
     * @var integer
     */
    private $numero;

    /**
     * @ORM\Column(name="descricao", type="string", nullable=false)
     *
     * @OA\Property()
     * @var string
     */
    private $descricao;

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
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param int $numero
     */
    public function setNumero(int $numero)
    {
        $this->numero = $numero;
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
    public function setDescricao(string $descricao)
    {
        $this->descricao = $descricao;
    }

}
