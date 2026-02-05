<?php
/*
 * IntegracaoSiccauService.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */


namespace App\Service;

use App\Config\AppConfig;
use stdClass;

/**
 * Serviço responsável pela implementação das chamadas aos serviços do SICCAU.
 *
 * @package App\Service
 * @author Squadra Tecnologia S/A.
 */
class IntegracaoSiccauService
{

    /**
     * Retorna os dados complemenentares dos profissionais conforme os ids pessoas informada.
     * @param $idsPessoa
     * @return stdClass|null
     */
    public function getDadosComplementaresProfissionais($idsPessoa)
    {
        $dadosProfissionais = null;

        if ($idsPessoa != null) {
            $url = AppConfig::getUrlServicoComplementoProfissionais();

            $retorno = $this->getDadosProfissionais($url, ["pessoa_id" => $idsPessoa]);

            if ($retorno != null) {
                $dadosProfissionais = json_decode($retorno);
            }
        }

        return $dadosProfissionais;
    }

    /**
     *
     * @param string $enderecoServico
     * @param array $data
     * @return bool|string
     */
    private function getDadosProfissionais(string $enderecoServico, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $enderecoServico);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $retorno = curl_exec($ch);
        curl_close($ch);

        return $retorno;
    }


}