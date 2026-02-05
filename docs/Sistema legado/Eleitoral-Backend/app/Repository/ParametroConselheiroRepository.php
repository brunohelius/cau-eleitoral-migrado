<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 13/11/2019
 * Time: 10:24
 */

namespace App\Repository;
use App\Entities\ParametroConselheiro;
use App\Entities\ProporcaoConselheiroExtrato;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'ParametroConselheiro'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ParametroConselheiroRepository extends AbstractRepository
{
    /**
     * Retorna os parametros conselheiros conforme o filtro informado
     *
     * @param $filtroTO
     * @return array|null
     */
    public function getParametroConselheiroPorFiltro($filtroTO)
    {
        try {
            $query = $this->createQueryBuilder('parametroConselheiro');
            $query->innerJoin("parametroConselheiro.atividadeSecundaria", "atividadeSecundaria")->addSelect("atividadeSecundaria");
            $query->leftJoin("parametroConselheiro.lei", "lei")->addSelect("lei");
            $query->where('parametroConselheiro.atividadeSecundaria = :atvSecundaria');
            $query->setParameter("atvSecundaria", $filtroTO->idAtividadeSecundaria);

            if (!empty($filtroTO->idsCauUf)) {
                $query->andWhere('parametroConselheiro.idCauUf IN (:idsCauUf)');
                $query->setParameter("idsCauUf", $filtroTO->idsCauUf);
            }

            $query->orderBy('parametroConselheiro.idCauUf');

            return $query->getQuery()->getArrayResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna os parametros conselheiros com o histórico conforme o filtro informado
     *
     * @param $filtroTO
     * @return array|null
     */
    public function getParametroConselheiroComHistorico($filtroTO)
    {
        try {
            $query = $this->createQueryBuilder('parametroConselheiro');
            $query->innerJoin("parametroConselheiro.atividadeSecundaria", "atividadeSecundaria")->addSelect("atividadeSecundaria");
            $query->innerJoin("parametroConselheiro.lei", "lei")->addSelect("lei");
            $query->leftJoin("atividadeSecundaria.historicoParametroConselheiro", "historicoParametroConselheiro")->addSelect("historicoParametroConselheiro");
            $query->where('parametroConselheiro.atividadeSecundaria = :atvSecundaria');
            $query->setParameter("atvSecundaria", $filtroTO->idAtividadeSecundaria);
            $query->orderBy('parametroConselheiro.idCauUf');

            $lista = $query->getQuery()->getArrayResult();
            
            return array_map(function ($dados) {
                return ParametroConselheiro::newInstance($dados);
            }, $lista);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Método que retorna último parâmetro Conselheiro por atividade principal independente do nivel
     *
     * @param $idAtividadePrincipal
     * @return ParametroConselheiro|null
     */
    public function getUltimaPorAtividadePrincipalECauUf($idAtividadePrincipal, $idCauUf)
    {
        try {
            $query = $this->createQueryBuilder('parametroConselheiro');
            $query->innerJoin("parametroConselheiro.atividadeSecundaria", "atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");
            $query->innerJoin("calendario.atividadesPrincipais", "atividadesPrincipais");

            $query->where('atividadesPrincipais.id = :idAtividadePrincipal');
            $query->setParameter('idAtividadePrincipal', $idAtividadePrincipal);

            $query->andWhere('parametroConselheiro.idCauUf = :idCauUf');
            $query->setParameter('idCauUf', $idCauUf);

            $query->orderBy("parametroConselheiro.id", "DESC");
            $query->setMaxResults(1);

            $dados = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return ParametroConselheiro::newInstance($dados);
        } catch (NoResultException | NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Método que retorna último parâmetro Conselheiro por calendario
     *
     * @param $idCalendario
     * @return ParametroConselheiro|null
     */
    public function getUltimaPorCalendarioECauUf($idCalendario, $idCauUf)
    {
        try {
            $query = $this->createQueryBuilder('parametroConselheiro');
            $query->innerJoin("parametroConselheiro.atividadeSecundaria", "atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");

            $query->where('calendario.id = :idCalendario');
            $query->setParameter('idCalendario', $idCalendario);

            $query->andWhere('parametroConselheiro.idCauUf = :idCauUf');
            $query->setParameter('idCauUf', $idCauUf);

            $query->orderBy("parametroConselheiro.id", "DESC");
            $query->setMaxResults(1);

            $dados = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return ParametroConselheiro::newInstance($dados);
        } catch (NoResultException | NonUniqueResultException $e) {
            return null;
        }
    }
}
