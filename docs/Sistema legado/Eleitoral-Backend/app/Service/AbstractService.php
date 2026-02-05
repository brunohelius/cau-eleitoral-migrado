<?php
/*
 * AbstractService.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Service;

use App\Config\Constants;
use App\Util\RestClient;
use Illuminate\Support\Facades\Input;

/**
 * Classe abstrata de serviço com as implementações comuns entre todos os serviços.
 *
 * @package App\Service
 * @author Squadra Tecnologia S/A.
 */
class AbstractService
{
    /**
     * Retorna uma instância de 'RestClient'.
     *
     * @param null $authorization
     * @return RestClient
     */
    protected function getRestClient($authorization = null): RestClient
    {
        $headers = ["Content-Type: application/json"];

        if ($authorization) {
            $headers[] = sprintf("Authorization: %s %s", Constants::PARAM_BEARER, Input::bearerToken());
        }

        return RestClient::newInstance()->addHeaders($headers);
    }
}
