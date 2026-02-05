<?php
/*
 * AuthProviderInterface.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Security;

/**
 * Interface de contrato referente a implementação de Autorização/Permisão do Cliente da API.
 *
 * @package App\Security
 * @author Squadra Tecnologia
 */
interface AuthProviderInterface
{

    /**
     * Verifica se o Token de Autorização do client é válido.
     *
     * @param string $token
     * @return boolean
     */
    public function isTokenValid($token);

    /**
     * Verifica se o client possui as permissões necessárias para acessar o recurso.
     *
     * @param string $token
     * @param array<string> $roles
     */
    public function hasRoles($token, $roles);
}
