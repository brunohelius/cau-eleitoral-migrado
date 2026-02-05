<?php
/*
 * Cors.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Middleware;

use App\Util\Utils;
use Closure;
use Symfony\Component\HttpFoundation\Response;

/**
 * Implementação para solucionar problema de 'Cross-Origin Resource Sharing'.
 *
 * @package App\Http\Middleware
 * @author Squadra Tecnologia
 */
class Cors
{

    /**
     * Intercepta a 'Request' e aplica a solução 'Cors'.
     *
     * @param $request
     * @param Closure $next
     *
     * @return \Illuminate\Http\Response
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

        $allowHeader = 'Accept, Content-Type, Access-Control-Allow-Headers, Access-Control-Request-Method, ';
        $allowHeader .= 'Authorization, X-Requested-With';

        $response->header('Access-Control-Allow-Headers', $allowHeader);
        $response->header('Access-Control-Max-Age', '3600');
        $response->header('Cache-Control', 'must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('X-XSS-Protection', '1');
        $response->header('X-Content-Type-Options', 'nosniff');

        if ($request->isMethod('OPTIONS')) {
            $response->setStatusCode(Response::HTTP_OK);
            $statusText = Utils::getValue(Response::HTTP_OK, Response::$statusTexts);
            $response->setContent($statusText);
        }

        return $response;
    }
}
