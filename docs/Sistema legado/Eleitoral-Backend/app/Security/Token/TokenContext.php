<?php
/*
 * TokenContext.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Security\Token;

/**
 * Classe responsável por encapsular as configurações necessárias para a emissão de um 'Token JWT'.
 *
 * @author Squadra Tecnologia S/A.
 */
class TokenContext
{

    private $secret;

    private $iss;

    private $exp;

    /**
     * Construtor da classe.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->secret = $config['secret'];

        $claims = $config['claims'];
        $this->iss = $claims['iss'];
        $this->exp = $claims['exp'];
    }

    /**
     *
     * @return array
     */
    public function getIss()
    {
        return $this->iss;
    }

    /**
     *
     * @return array
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     *
     * @return array
     */
    public function getExp()
    {
        return $this->exp;
    }

    /**
     * Retorna a instância de JWTBuilder.
     *
     * @param array $data
     *
     * @return TokenBuilder
     */
    public function createTokenBuilder($data = array())
    {
        return new TokenBuilder($this, $data);
    }
}