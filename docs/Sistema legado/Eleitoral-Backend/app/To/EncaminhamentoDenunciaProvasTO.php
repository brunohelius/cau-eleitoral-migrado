<?php

namespace App\To;

use App\Util\Utils;

/**
 * Classe de transferência associada a 'EncaminhamentoDenunciaProvas'.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 **/
class EncaminhamentoDenunciaProvasTO
{

    /**
     * @var array
     */
    private $encaminhamento;

    /**
     * @var array
     */
    private $denunciaProvas;

    /**
     * Retorna uma nova instância de EncaminhamentoDenunciaProvasTO.
     *
     */
    public static function newInstance($data = null)
    {
        $encaminhamentoDenunciaProvasTO = new EncaminhamentoDenunciaProvasTO();

        if ($data != null) {
            $encaminhamentoDenunciaProvasTO->setEncaminhamento(Utils::getValue("encaminhamento", $data));
            $encaminhamentoDenunciaProvasTO->setDenunciaProvas(Utils::getValue("denunciaProvas", $data));
        }

        return $encaminhamentoDenunciaProvasTO;
    }

    /**
     * @return string
     */
    public function getEncaminhamento()
    {
        return $this->encaminhamento;
    }

    /**
     * @param string $encaminhamento
     */
    public function setEncaminhamento($encaminhamento)
    {
        $this->encaminhamento = $encaminhamento;
    }

    /**
     * @return string
     */
    public function getDenunciaProvas()
    {
        return $this->denunciaProvas;
    }

    /**
     * @param string $denunciaProvas
     */
    public function setDenunciaProvas($denunciaProvas)
    {
        $this->denunciaProvas = $denunciaProvas;
    }
}
