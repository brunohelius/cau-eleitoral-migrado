<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 19/11/2019
 * Time: 10:37
 */

namespace App\Repository;

use App\Entities\HistoricoExtratoConselheiro;
use App\To\HistoricoExtratoConselheiroTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'HistoricoExtratoConselheiro'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class HistoricoExtratoConselheiroRepository extends AbstractRepository
{
    /**
     * Retorna o 'Histórico de Extrato de Conselheiros' conforme o id informado.
     *
     * @param int $id
     * @return HistoricoExtratoConselheiro
     * @throws Exception
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('historicoExtratoConselheiro');
            $query->innerJoin("historicoExtratoConselheiro.atividadeSecundaria", "atividadeSecundaria")->addSelect('atividadeSecundaria');
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal")->addSelect('atividadePrincipal');
            $query->innerJoin("atividadePrincipal.calendario", "calendario")->addSelect('calendario');

            $query->where("historicoExtratoConselheiro.id = :id")->setParameter("id", $id);

            $historico = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return HistoricoExtratoConselheiro::newInstance($historico);
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Método que retorna o maior numero de um Histórico de Extrato de Conselheiros
     *
     * @param $idAtividadeSecundaria
     * @return mixed|null
     */
    public function getNumeroHistoricoExtratoPorAtvSec($idAtividadeSecundaria)
    {
        try {
            $query = $this->createQueryBuilder('historicoExtratoConselheiro')->select('Max(historicoExtratoConselheiro.numero) numero');
            $query->where('historicoExtratoConselheiro.atividadeSecundaria = :idAtvSec');

            $query->setParameter('idAtvSec', $idAtividadeSecundaria);

            return $query->getQuery()->getArrayResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Método que retorna Historico Extrato Conselheiro por atividade secundaria
     *
     * @param $idAtividadeSecundaria
     * @return array|null
     */
    public function getPorAtividadeSecundaria($idAtividadeSecundaria)
    {
        try {
            $query = $this->createQueryBuilder('historicoExtratoConselheiro')->select('historicoExtratoConselheiro.id');
            $query->innerJoin('historicoExtratoConselheiro.atividadeSecundaria', 'atividadeSecundaria');
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");
            $query->innerJoin('calendario.eleicao', 'eleicao');
            $query->addSelect('historicoExtratoConselheiro.numero');
            $query->addSelect('IDENTITY(historicoExtratoConselheiro.atividadeSecundaria)');
            $query->addSelect('historicoExtratoConselheiro.dataHistorico');
            $query->addSelect('historicoExtratoConselheiro.descricao');
            $query->addSelect('historicoExtratoConselheiro.responsavel');
            $query->addSelect('eleicao.ano anoEleicao');
            $query->addSelect('eleicao.sequenciaAno sequenciaAnoEleicao');
            $query->where('atividadeSecundaria = :idAtvSec');
            $query->setParameter('idAtvSec', $idAtividadeSecundaria);
            $query->orderBy('historicoExtratoConselheiro.numero', 'asc');

            $arrayResult = $query->getQuery()->getArrayResult();

            return array_map(function ($dados) {
                return HistoricoExtratoConselheiroTO::newInstance($dados);
            }, $arrayResult);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
    * Método que retorna total de Historico Extrato Conselheiro por atividade secundaria
    *
    * @param $idAtividadeSecundaria
    * @return integer
    */
    public function getTotalExtratoPorAtividadeSecundaria($idAtividadeSecundaria)
    {
        try {
            $query = $this->createQueryBuilder('historicoExtratoConselheiro');
            $query->select('count(historicoExtratoConselheiro.id)');
            $query->where('historicoExtratoConselheiro.atividadeSecundaria = :idAtvSec');
            $query->setParameter('idAtvSec', $idAtividadeSecundaria);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    /**
     * Método que retorna Historico Extrato Conselheiro por atividade principal independente do nivel
     *
     * @param $idAtividadeSecundaria
     * @return HistoricoExtratoConselheiro|null
     * @throws Exception
     */
    public function getUltimoExtratoPorAtividadePrincipal($idAtividadeSecundaria)
    {
        try {
            $query = $this->createQueryBuilder('historicoExtratoConselheiro');
            $query->innerJoin("historicoExtratoConselheiro.atividadeSecundaria", "atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");
            $query->innerJoin("calendario.atividadesPrincipais", "atividadesPrincipais");

            $query->where('atividadesPrincipais.id = :idAtividadePrincipal');
            $query->setParameter('idAtividadePrincipal', $idAtividadeSecundaria);

            $query->orderBy("historicoExtratoConselheiro.dataHistorico", "DESC");
            $query->setMaxResults(1);

            $dadosExtrato = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return HistoricoExtratoConselheiro::newInstance($dadosExtrato);
        } catch (NoResultException | NonUniqueResultException $e) {
            return null;
        }
    }
}
