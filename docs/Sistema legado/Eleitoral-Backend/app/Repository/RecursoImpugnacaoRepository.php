<?php

namespace App\Repository;

use App\To\RecursoImpugnacaoTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'RecursoImpugnacao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class RecursoImpugnacaoRepository extends AbstractRepository
{
    /**
     * Retorna o recurso de Julgamento Impugnação
     *
     * @param int $idPedidoImpugnacao
     * @param int $idTipoSolicitacao
     * @param bool $isRetornarContrarrazao
     * @return RecursoImpugnacaoTO
     */
    public function getPorPedidoImpugnacaoAndTipoSolicitacao($idPedidoImpugnacao, $idTipoSolicitacao,
                                                             $isRetornarContrarrazao = false)
    {
        try {
            $query = $this->getQueryRecursoImpugnacao($isRetornarContrarrazao);

            $query->andWhere('pedidoImpugnacao.id = :id');
            $query->setParameter('id', $idPedidoImpugnacao);

            $query->andWhere("tipoSolicitacante.id = :idTipoSolicitacao");
            $query->setParameter('idTipoSolicitacao', $idTipoSolicitacao);

            $recursoImpugnacao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return RecursoImpugnacaoTO::newInstance($recursoImpugnacao);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a query com os joins dos relacionamentos
     *
     * @return QueryBuilder
     */
    private function getQueryRecursoImpugnacao($isRetornarContrarrazao): QueryBuilder
    {
        $query = $this->createQueryBuilder('recursoImpugnacao')
            ->innerJoin('recursoImpugnacao.julgamentoImpugnacao', 'julgamentoImpugnacao')
            ->innerJoin('julgamentoImpugnacao.pedidoImpugnacao', 'pedidoImpugnacao')
            ->innerJoin('recursoImpugnacao.profissional', 'profissional')->addSelect('profissional')
            ->innerJoin('recursoImpugnacao.tipoSolicitacaoRecursoImpugnacao', 'tipoSolicitacante')->addSelect('tipoSolicitacante');
        //HST73
        if ($isRetornarContrarrazao) {
            $query->leftJoin('recursoImpugnacao.contrarrazaoRecursoImpugnacao', 'contrarrazaoRecursoImpugnacao')->addSelect('contrarrazaoRecursoImpugnacao')
                ->leftJoin('contrarrazaoRecursoImpugnacao.profissional', 'profissionalContrarrazao')->addSelect('profissionalContrarrazao');
        }
        return $query;
    }

    /**
     * Retorna os recursos de Julgamento Impugnação
     *
     * @param int $idPedidoImpugnacao
     * @return RecursoImpugnacaoTO[]|array
     */
    public function getTodosPorPedidoImpugnacao($idPedidoImpugnacao, $idTipoSolicitacao)
    {
        $query = $this->getQueryRecursoImpugnacao();

        //HST73 - Buscar a contrazaçao do recurso
        $query->leftJoin(
            'recursoImpugnacao.contrarrazaoRecursoImpugnacao',
            'contrarrazaoRecursoImpugnacao',
            \Doctrine\ORM\Query\Expr\Join::WITH,
            "recursoImpugnacao.tipoSolicitacaoRecursoImpugnacao = {$idTipoSolicitacao}")
            ->addSelect('contrarrazaoRecursoImpugnacao')
            ->leftJoin('contrarrazaoRecursoImpugnacao.profissional', 'profissionalContrarrazao')->addSelect('profissionalContrarrazao');

        $query->andWhere('pedidoImpugnacao.id = :id');
        $query->setParameter('id', $idPedidoImpugnacao);

        $recursosImpugnacao = $query->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

        return array_map(function ($recursoImpugnacao) {
            return RecursoImpugnacaoTO::newInstance($recursoImpugnacao);
        }, $recursosImpugnacao);
    }
}