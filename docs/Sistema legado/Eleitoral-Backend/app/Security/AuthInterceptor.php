<?php
/*
 * AuthInterceptor.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Security;

use App\Security\Token\TokenContext;

/**
 * Classe interceptor para autorização/permissão de Usuário.
 *
 * @package App\Security
 * @author Squadra Tecnologia
 */
class AuthInterceptor implements AuthProviderInterface
{
    /**
     * Verifica se o Token de Autorização do client é válido.
     *
     * @param string $token
     * @return bool
     */
    public function isTokenValid($token)
    {
        return $this->getTokenContext()->createTokenBuilder()->isValid($token);
    }

    /**
     * Verifica se o client possui as permissões necessárias para acessar o recurso.
     *
     * @param string $token
     * @param
     *            $roles
     * @return bool
     */
    public function hasRoles($token, $roles)
    {
        return false;
    }

    /**
     * Retorna a instância de 'TokenContext'.
     *
     * @return TokenContext
     */
    private function getTokenContext()
    {
        return app()->make(TokenContext::class);
    }
}
