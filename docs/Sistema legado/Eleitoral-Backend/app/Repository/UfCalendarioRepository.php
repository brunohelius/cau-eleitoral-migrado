<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 16/09/2019
 * Time: 15:56
 */

namespace App\Repository;

use App\Entities\HistoricoCalendario;
use Illuminate\Support\Facades\Date;
use App\Config\Constants;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'UfCalendario'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class UfCalendarioRepository extends AbstractRepository
{

    /**
     * Recupera a lista de UFs calendário de acordo com o calendário informado.
     *
     * @param $idCalendario
     * @return mixed
     */
    public function getPorCalendario($idCalendario)
    {
        $query = $this->createQueryBuilder('ufCalendario');
        $query->leftJoin("ufCalendario.calendario", "calendario")->addSelect("calendario");

        $query->where("calendario.id = :id");
        $query->setParameter("id", $idCalendario);

        return $query->getQuery()->getResult();
    }

    /**
     * Recupera a lista de UFs calendário de acordo com o calendário informado.
     *
     * @param $idCalendario
     * @param $idCauUf
     * @return mixed
     */
    public function getPorCalendarioCauUf($idCalendario, $idCauUf)
    {
        $query = $this->createQueryBuilder('ufCalendario');
        $query->leftJoin("ufCalendario.calendario", "calendario")->addSelect("calendario");

        $query->where("calendario.id = :id AND ufCalendario.idCauUf = :idCauUf");
        $query->setParameter("id", $idCalendario);
        $query->setParameter('idCauUf', $idCauUf);

        return $query->getQuery()->getResult();
    }

}
