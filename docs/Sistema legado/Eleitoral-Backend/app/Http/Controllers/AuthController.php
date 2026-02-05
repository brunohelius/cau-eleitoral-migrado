<?php
/*
 * AuthController.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Service\AuthService;

/**
 * Classe de controle referente a 'Autenticação do Usuário'.
 *
 * @package App\Http\Controllers
 * @author Squadra Tecnologia S/A
 *
 */
class AuthController extends Controller
{
    /**
     * @var AuthService
     */
    private $authService;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->authService = app()->make(AuthService::class);
    }
}