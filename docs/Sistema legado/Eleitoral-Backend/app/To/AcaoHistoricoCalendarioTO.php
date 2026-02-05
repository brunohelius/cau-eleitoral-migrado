<?php

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada ao campo 'Ação' de 'HistoricoCalendario'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 **/
class AcaoHistoricoCalendarioTO
{

    /**
     * @var string
     */
    private $descricao;

    /**
     * Retorna uma nova instância de AcaoHistoricoCalendarioTO.
     *
     * @param array|null $data
     * @return AcaoHistoricoCalendarioTO
     */
    public static function newInstance($data = null)
    {
        $acaoHistoricoCalendarioTO = new AcaoHistoricoCalendarioTO();

        if ($data != null) {
            $acaoHistoricoCalendarioTO->setDescricao(Utils::getValue("descricao", $data));
        }

        return $acaoHistoricoCalendarioTO;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }
}
