<?php
/*
 * UsuarioPermissao.php
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
 * @ORM\Entity(repositoryClass="App\Repository\UsuarioPermissaoRepository")
 * @ORM\Table(schema="public", name="tb_usuarios_permissoes")
 *
 * @OA\Schema(schema="UsuarioPermissao")
 *
 * @package App\Entities
 */
class UsuarioPermissao extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="id", type="integer")
     * @ORM\SequenceGenerator(sequenceName="tb_permissoes_id_seq", initialValue=1, allocationSize=1)
     *
     * @OA\Property()
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Usuario")
     * @ORM\JoinColumn(name="usuario", referencedColumnName="usuario", nullable=false)
     * @var \App\Entities\Usuario
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Permissao")
     * @ORM\JoinColumn(name="permissao_id", referencedColumnName="id", nullable=false)
     * @var \App\Entities\Permissao
     */
    private $permissao;

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
     * @return Usuario
     */
    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    /**
     * @param Usuario $usuario
     */
    public function setUsuario(Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }

    /**
     * @return Permissao
     */
    public function getPermissao(): Permissao
    {
        return $this->permissao;
    }

    /**
     * @param Permissao $permissao
     */
    public function setPermissao(Permissao $permissao): void
    {
        $this->permissao = $permissao;
    }
}
