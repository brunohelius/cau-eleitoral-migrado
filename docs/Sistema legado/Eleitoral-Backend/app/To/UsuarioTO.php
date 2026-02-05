<?php
/*
 * UsuarioTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Entities\Usuario;
use Illuminate\Support\Arr;

/**
 * Classe de transferência associada ao 'Usuário' (Corporativo).
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class UsuarioTO
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $nome;

    /**
     * @var string
     */
    private $email;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Retorna uma nova instância de 'UsuarioTO'.
     *
     * @param null $data
     * @return UsuarioTO
     */
    public static function newInstance($data = null)
    {
        $usuarioTO = new UsuarioTO();

        if ($data != null) {
            $usuarioTO->setId(Arr::get($data, 'id'));
            $usuarioTO->setNome(Arr::get($data, 'nome'));
            $usuarioTO->setEmail(Arr::get($data, 'email'));
        }

        return $usuarioTO;
    }

    /**
     * Retorna uma nova instância de 'PedidoImpugnacaoTO'.
     *
     * @param Usuario $usuario
     * @return UsuarioTO
     * @throws \Exception
     */
    public static function newInstanceFromEntity($usuario)
    {
        $usuarioTO = new UsuarioTO();

        if (!empty($usuario)) {
            $usuarioTO->setId($usuario->getId());
            $usuarioTO->setNome($usuario->getNome());
            $usuarioTO->setEmail($usuario->getEmail());
        }

        return $usuarioTO;
    }
}
