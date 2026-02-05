<?php
/*
 * AuthProvider.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Security\Providers;

use App\Security\AuthProviderInterface;
use App\Security\Middleware\Auth;
use App\Security\Token\TokenContext;
use Illuminate\Support\ServiceProvider;

/**
 * Class auth provider.
 *
 * @package App\Security\Providers
 * @author Squadra Tecnologia
 */
class AuthProvider extends ServiceProvider
{
    /**
     * Registra as dependências
     *
     * @return void
     */
    public function register()
    {
        $this->app->configure('auth');

        /**
         * Register SecurityContext
         */
        $this->app->singleton(
            TokenContext::class,
            function () {
                return new TokenContext(config('auth.jwt'));
            }
        );

        /**
         * Register AuthInterface
         */
        $this->app->bind(
            AuthProviderInterface::class,
            function ($app) {
                $provider = config('auth.jwt.provider');
                return $app->make($provider);
            }
        );

        /**
         * Register AuthMiddleware
         */
        $this->app->routeMiddleware([
            'secured' => Auth::class
        ]);
    }
}
