<?php


namespace App\Service;


use App\Config\AppConfig;
use App\Util\RestClient;
use App\Util\Utils;

class CalendarioApiService
{
    private $restClient;
    private $urlTokenApi;

    const FERIADO_NACIONAL = 1;
    const FACULTATIVO = 4;
    const DIA_CONVENCIONAL = 9;

    public function __construct()
    {
        $this->setUrlTokenApi(AppConfig::getCalendarioUrlApi() . '?json=true&token=' . AppConfig::getCalendarioTokenApi());
    }

    /**
     * Retorna um array com as datas dos feriados nacionais
     * @throws \Exception
     */
    public function getFeriadosNacionais($ano)
    {
        $feriados = null;
        try {
            $feriados = array();
            $feriadosGerais = json_decode($this->getRestClient()->sendGet($this->getUrlTokenApi() . "&ano=" . $ano),
                true);
            if (!empty($feriadosGerais)) {
                foreach ($feriadosGerais as $feriado) {
                    $feriados[$feriado['type_code']][] = $feriado;
                }
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
        return $feriados;
    }

    /**
     * Retorna um array com as datas dos feriados nacionais
     * @return \DateTime[]
     * @throws \Exception
     */
    public function getDatasFeriadosNacionais($ano)
    {
        $feriados = $this->getFeriadosNacionais($ano);

        if(!empty($feriados)) {
            return array_map(function ($feriado) {
                return Utils::getDataHoraZero(Utils::getDataToString($feriado['date'], 'd/m/Y'));
            }, $feriados[CalendarioApiService::FERIADO_NACIONAL]);
        }

        return [];
    }

    /**
     * @return RestClient
     */
    public function getRestClient()
    {
        return RestClient::newInstance();
    }

    /**
     * @return string
     */
    public function getUrlTokenApi()
    {
        return $this->urlTokenApi;
    }

    /**
     * @param string $urlTokenApi
     */
    public function setUrlTokenApi($urlTokenApi): void
    {
        $this->urlTokenApi = $urlTokenApi;
    }

    /**
     * @return array
     */
    public function getTipoFeriados(): array
    {
        return $this->tipoFeriados;
    }

    /**
     * @param array $tipoFeriados
     */
    public function setTipoFeriados(array $tipoFeriados): void
    {
        $this->tipoFeriados = $tipoFeriados;
    }
}
