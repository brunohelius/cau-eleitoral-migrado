<?php
/*
 * Usuario.php
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

/**
 * Entidade de representação de 'Usuario'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\UsuarioRepository")
 * @ORM\Table(schema="public", name="tb_usuarios")
 *
 * @package App\Entities
 * @author Squadra Tecnologia S/A.
 */
class Usuario extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\Column(name="id", type="integer")
     * @ORM\SequenceGenerator(sequenceName="tb_pessoa_id_seq", initialValue=1, allocationSize=1)
     *
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(name="usuario", type="string", nullable=false)
     *
     * @var string
     */
    private $usuario;

    /**
     * @ORM\Column(name="nome", type="string", nullable=false)
     *
     * @var string
     */
    private $nome;

    /**
     * @ORM\Column(name="email", type="string", nullable=false)
     *
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(name="cpf", type="string", nullable=false)
     *
     * @var string
     */
    private $cpf;

    /**
     * @ORM\Column(name="telefone", type="string", nullable=false)
     *
     * @var string
     */
    private $telefone;

    /**
     * @ORM\Column(name="ativo", type="string", nullable=false)
     *
     * @var string
     */
    private $ativo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entities\Filial")
     * @ORM\JoinColumn(name="filial_id", referencedColumnName="id", nullable=false)
     * @var Filial
     */
    private $filial;

    /**
     * @ORM\OneToMany(targetEntity="App\Entities\UsuarioPermissao", mappedBy="usuario")
     * @var ArrayCollection
     */
    private $permissoesUsuario;

    /**
     * Transient
     *
     * @var string
     */
    private $avatar;

    /**
     * Transient
     *
     * @var string
     */
    private $nomeCompleto;


    /**
     * Fábricação estática de 'Usuario'.
     *
     * @param array $data
     * @return Usuario
     */
    public static function newInstance($data = null)
    {
        $usuario = new Usuario();

        if ($data != null) {
            $usuario->setEmail('email');
            $usuario->setAvatar('avatar');
            $usuario->setUsuario('usuario');
            $usuario->setId(Utils::getValue('id', $data));
            $usuario->setCpf(Utils::getValue('cpf', $data));
            $usuario->setNome(Utils::getValue('nome', $data));
            $usuario->setEmail(Utils::getValue('email', $data));
            $usuario->setAtivo(Utils::getValue('ativo', $data));
            $usuario->setTelefone(Utils::getValue('telefone', $data));
        }

        return $usuario;
    }

    /**
     * Configura o nome do usuário para apresentar somente o primeiro e o último nome
     * e define o valor do atributo nomeCompleto.
     */
    public function definirNomes()
    {
        $this->setNomeCompleto($this->getNome());
        $this->nome = Utils::getPrimeiraEUltimaPalavra($this->getNome());
    }


    /**
     * Retorna um array com das permissões do usuário.
     *
     * @return array
     */
    public function getNumerosPemissoes()
    {
        $numerosPermissoes = [];

        if (!empty($this->getPermissoesUsuario())) {

            $numerosPermissoes = array_map(function ($permissaoUsuario) {
                return $permissaoUsuario->getPermissao()->getNumero();
            }, $this->getPermissoesUsuario()->toArray());
        }

        return $numerosPermissoes;
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
    public function setId(?int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param string $usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return ArrayCollection
     */
    public function getPermissoesUsuario()
    {
        return $this->permissoesUsuario;
    }

    /**
     * @param ArrayCollection $permissoesUsuario
     */
    public function setPermissoesUsuario($permissoesUsuario)
    {
        $this->permissoesUsuario = $permissoesUsuario;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @return Filial
     */
    public function getFilial()
    {
        return $this->filial;
    }

    /**
     * @param Filial $filial
     */
    public function setFilial($filial)
    {
        $this->filial = $filial;
    }

    /**
     * @return string
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * @param string $cpf
     */
    public function setCpf(?string $cpf)
    {
        $this->cpf = $cpf;
    }

    /**
     * @return string
     */
    public function getTelefone()
    {
        return $this->telefone;
    }

    /**
     * @param string $telefone
     */
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
    }

    /**
     * @return string
     */
    public function getAtivo()
    {
        return $this->ativo;
    }

    /**
     * @param string $ativo
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }

    /**
     * @return string
     */
    public function getNomeCompleto()
    {
        return $this->nomeCompleto;
    }

    /**
     * @param string $nomeCompleto
     */
    public function setNomeCompleto($nomeCompleto)
    {
        $this->nomeCompleto = $nomeCompleto;
    }
}
