<?php

namespace App\Repository;

use App\To\RecursoSubstituicaoTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'RecursoSubstituicao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class RecursoSubstituicaoRepository extends AbstractRepository
{

    /**
     * Retorna o recurso de Julgamento Substituiçao
     *
     * @param int $idPedidoSubstituicao
     * @return RecursoSubstituicaoTO
     * @throws \Exception
     */
    public function getPorPedidosSubstituicao(int $idPedidoSubstituicao)
    {
        try {
            $query = $this->getQueryRecursoSubstituicao();

            $query->andWhere('pedidoSubstituicaoChapa.id = :id');
            $query->setParameter('id', $idPedidoSubstituicao);

            $recursoSubstituicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return RecursoSubstituicaoTO::newInstance($recursoSubstituicao);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }
    
    /**
     * Método que retorno o id do calendário conforme o id do recurso do julgameneto de substituição
     *
     * @param $idRecursoSubstituicao
     * @return integer|null
     */
    public function getIdCalendarioRecursoJulgamento($idRecursoSubstituicao)
    {
        try {
            $query = $this->createQueryBuilder('recursoSubstituicao');
            $query->select('calendario.id');
            $query->innerJoin('recursoSubstituicao.julgamentoSubstituicao', 'julgamentoSubstituicao');
            $query->innerJoin('julgamentoSubstituicao.pedidoSubstituicaoChapa', 'pedidoSubstituicao');
            $query->innerJoin('pedidoSubstituicao.chapaEleicao', 'chapaEleicao');
            $query->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundaria');
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");

            $query->where('recursoSubstituicao.id = :id');
            $query->setParameter('id', $idRecursoSubstituicao);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a query com os joins dos relacionamentos
     *
     * @return QueryBuilder
     */
    private function getQueryRecursoSubstituicao(): QueryBuilder
    {
        $query = $this->createQueryBuilder('recursoSubstituicao')
            ->innerJoin('recursoSubstituicao.julgamentoSubstituicao', 'julgamentoSubstituicao')
            ->innerJoin('julgamentoSubstituicao.pedidoSubstituicaoChapa', 'pedidoSubstituicaoChapa')
            ->innerJoin('recursoSubstituicao.profissional', 'profissional')
            ->addSelect('profissional');

        return $query;
    }
}