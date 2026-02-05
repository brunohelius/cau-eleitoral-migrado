<?php


namespace App\To;

use App\Util\Utils;
use Illuminate\Support\Arr;
use App\Entities\Filial;
use App\Entities\Calendario;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Classe de transferência para a Impugnacao de Resultado.
 *
 * @package App\To
 * @author Squadra Tecnologia S/A.
 */
class UfCalendarioTO
{

    /**
     * @var integer|null $id
     */
    private $id;

    /**
     * @var Calendario|null $idCalendario
     */
    private $calendario;

    /**
     * @var Filial|null $idCalendario
     */
    private $uf;

    /**
     * @return Integer|null
     */
    public function getId(): ?Integer
    {
        return $this->id;
    }

    /**
     * @param Integer|null $id
     */
    public function setId(?Integer $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Calendario|null
     */
    public function getCalendario(): ?Calendario
    {
        return $this->calendario;
    }

    /**
     * @param Calendario|null $calendario
     */
    public function setCalendario(?Calendario $calendario): void
    {
        $this->calendario = $calendario;
    }

    /**
     * @return Filial|null
     */
    public function getUf(): ?Filial
    {
        return $this->uf;
    }

    /**
     * @param Filial|null $uf
     */
    public function setUf(?Filial $uf): void
    {
        $this->uf = $uf;
    }

    /**
     * Retorna uma nova instância de 'JulgamentoFinalTO'.
     *
     * @param null $data
     * @return UfCalendarioTO
     * @throws \Exception
     */
    public static function newInstance($data = null)
    {
        $ufCalendarioTO = new UfCalendarioTO();

        if ($data != null) {

            $ufCalendarioTO->setId(Arr::get($data,'id'));
            $calendario = Utils::getValue('calendario', $data);
            if(!empty($calendario)) {
                $ufCalendarioTO->setCalendario(Calendario::newInstance($calendario));
            }
            $uf = Utils::getValue('uf', $data);
            if(!empty($uf)) {
                $ufCalendarioTO->setUf(Filial::newInstance($uf));
            }
        }
        return $ufCalendarioTO;
    }
}