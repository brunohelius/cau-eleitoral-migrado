<?php
/*
 * AuthTO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada ao 'Autenticação'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class AuthTO
{
    /**
     * @var string
     */
    private $cpf;

    /**
     * @var string
     */
    private $senha;

    /**
     * Fabricação estática de 'AuthTO'.
     *
     * @param array $data
     *
     * @return AuthTO
     */
    public static function newInstance($data = null)
    {
        $authTO = new AuthTO();

        if ($data != null) {
            //CPF
            $cpf = Utils::getValue("cpf", $data);
            $cpf = Utils::getOnlyNumbers($cpf);
            $authTO->setCpf($cpf);

            // Senha
            $authTO->setSenha(Utils::getValue("senha", $data));
        }
        return $authTO;
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
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;
    }

    /**
     * @return string
     */
    public function getSenha()
    {
        return $this->senha;
    }

    /**
     * @param string $senha
     */
    public function setSenha($senha)
    {
        $this->senha = $senha;
    }

    /**
     * @return string
     */
    public function getSenhaMD5()
    {
        return md5($this->senha);
    }
}