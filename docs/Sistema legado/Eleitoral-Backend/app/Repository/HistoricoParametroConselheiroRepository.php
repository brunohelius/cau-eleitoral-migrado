<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 19/11/2019
 * Time: 10:37
 */

namespace App\Repository;

use App\Entities\HistoricoExtratoConselheiro;
use App\Entities\HistoricoParametroConselheiro;
use App\Entities\Historico;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NoResultException;
use Illuminate\Support\Facades\Date;
use App\Config\Constants;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'HistoricoParametroConselheiro'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class HistoricoParametroConselheiroRepository extends AbstractRepository
{
    /**
     * Método que retorna Historico Parametro de Conselheiro por atividade secundaria
     *
     * @param $filtroTO
     * @return array|null
     */
    public function getPorFiltro($filtroTO)
    {
        try {
            $query = $this->createQueryBuilder('historicoParametroConselheiro')->select('historicoParametroConselheiro');
            $query->where('historicoParametroConselheiro.atividadeSecundaria = :idAtvSec');

            $query->setParameter('idAtvSec', $filtroTO->idAtividadeSecundaria);
        
            $query->orderBy('historicoParametroConselheiro.dataHistorico', 'asc');

            $arrayResult = $query->getQuery()->getArrayResult();

            return array_map(function ($dados) {
                return HistoricoExtratoConselheiro::newInstance($dados);
            }, $arrayResult);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Método que retorna Historico Completo de Parametro Conselheiro por Filtro
     * @throws DBALException
     */
    public function getHistoricoCompleto($filtroTO)
    {
        $sql = " SELECT hist.id_historico as id, hist.ds_historico as descricao, ";
        $sql .= "       hist.ds_justificativa as justificativa, ";
        $sql .= "       hist.dt_historico as dataHistorico, ";
        $sql .= "       hist.id_acao as acao, hist.id_usuario as responsavel ";
        $sql .= " FROM eleitoral.tb_historico as hist ";
        $sql .= " WHERE hist.id_tp_historico = ".Constants::HISTORICO_TIPO_REFERENCIA_PARAMETRO_CONSELHEIRO;
        $sql .= " AND hist.id_referencia = ".$filtroTO->idAtividadeSecundaria;
        $sql .= " UNION ";
        $sql .= " SELECT histP.id_hist_param_conselheiro as id, histP.ds_historico as descricao, ";
        $sql .= "        histP.ds_justificativa as justificativa, ";
        $sql .= "        histP.dt_historico as dataHistorico, ";
        $sql .= "        histP.id_acao as acao, histP.id_usuario as responsavel ";
        $sql .= " FROM eleitoral.tb_hist_param_conselheiro as histP ";
        $sql .= " WHERE histP.id_ativ_secundaria = ".$filtroTO->idAtividadeSecundaria; 
        $sql .= " ORDER BY dataHistorico ";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}