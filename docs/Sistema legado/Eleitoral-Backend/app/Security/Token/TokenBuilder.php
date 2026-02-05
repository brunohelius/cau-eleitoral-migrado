<?php
/*
 * TokenBuilder.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Security\Token;

use Exception;
use Firebase\JWT\JWT;
use InvalidArgumentException;

/**
 * Classe 'Builder' responsável por encapsular a complexidade no encode/decode de 'Token JWT'.
 *
 * @author Squadra Tecnologia S/A.
 */
class TokenBuilder
{

    private $exp;

    private $data;

    private $context;

    /**
     * Construtor da classe.
     *
     * @param TokenContext $context
     * @param array $data
     */
    public function __construct(TokenContext $context, $data = array())
    {
        $this->data = $data;
        $this->context = $context;
        $this->exp = $context->getExp();
    }

    /**
     * Adiciona o parâmetro que será anexado ao 'Token JWT'.
     *
     * @param string $key
     * @param object $value
     * @return \App\Security\Token\TokenBuilder
     */
    public function addParam($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     *
     * @param integer $exp
     * @return TokenBuilder
     */
    public function setExp($exp)
    {
        $this->exp = $exp;
        return $this;
    }

    /**
     * Retorna o array de configuração conforme a especificada pela API 'firebase/php-jwt'.
     *
     * @return array
     */
    private function getConfig()
    {
        $id = uniqid();

        return [
            'jti' => $id,
            'iat' => time(),
            'nbf' => time(),
            'iss' => $this->context->getIss()
        ];
    }

    /**
     * Retorna o 'Token JWT' considerando os parâmetros de configuração.
     *
     * @param array $data
     * @return string[]|mixed[]
     */
    private function generateToken($data)
    {
        $params = $this->getConfig();
        $params['exp'] = $params['iat'] + $this->context->getExp();
        $params['data'] = $data;

        $encode = array();
        $token = JWT::encode($params, $this->context->getSecret());

        $encode['expiresIn'] = $this->exp;
        $encode['accessToken'] = $token;

        return $encode;
    }

    /**
     * Verifica se o 'Token JWT' informado é válido.
     *
     * @param string $token
     * @return bool
     */
    public function isValid($token)
    {
        try {
            $params = $this->decode($token);
            $valid = $params != null;
        } catch (InvalidArgumentException $e) {
            $valid = false;
        }
        return $valid;
    }

    /**
     * Retorna o 'Token JWT' considerando os parâmetros de configuração.
     *
     * @return string[]|array[]|mixed[]
     */
    public function encode()
    {
        return $this->generateToken($this->data);
    }

    /**
     * Retorna os 'Parametros' recuperados atravéOs do 'Token JWT'.
     *
     * @param string $token
     * @throws InvalidArgumentException
     * @return NULL|object|mixed
     */
    public function decode($token)
    {
        try {
            if (empty($token)) {
                throw new InvalidArgumentException('Token não especificado.');
            }

            JWT::$leeway = 60;
            $params = JWT::decode($token, $this->context->getSecret(), [
                'HS256'
            ]);
        } catch (Exception $e) {
            $params = null;
        }
        return $params;
    }

    /**
     * Retorna o novo 'Token JWT' considerando o 'Token' informado.
     *
     * @param string $token
     * @throws InvalidArgumentException
     * @return string[]|array|mixed[]
     */
    public function refresh($token)
    {
        $params = $this->decode($token);

        if ($params == null) {
            throw new InvalidArgumentException('Token inválido!');
        }
        $data = (array) $params->data;
        return $this->generateToken($data);
    }
}
