<?php

namespace App\Repository;

use App\Entities\JulgamentoRecursoImpugnacao;
use App\To\JulgamentoRecursoImpugnacaoTO;
use App\To\JulgamentoRecursoSubstituicaoTO;
use App\To\JulgamentoSubstituicaoTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'JulgamentoRecursoImpugnacao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoRecursoImpugnacaoRepository extends AbstractRepository
{

    /**
     * Retorna a julganento do recurso do pedido de impugnalção conforme o id
     *
     * @param int $id
     * @return JulgamentoRecursoImpugnacaoTO
     * @throws \Exception
     */
    public function getPorId(int $id)
    {
        try {
            $query = $this->getQueryJulgamentoRecursoImpugnacao();
            $query->addSelect('pedidoImpugnacao');

            $query->andWhere('julgamentoRecursoImpugnacao.id = :id');
            $query->setParameter('id', $id);

            $julgamento = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return JulgamentoRecursoImpugnacaoTO::newInstance($julgamento);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }


    /**
     * Retorna a quantidade de Julgamento Impugnação
     *
     * @param int $idPedidoImpugnacao
     * @return JulgamentoRecursoImpugnacaoTO
     * @throws \Exception
     */
    public function getPorPedidoImpugnacao(int $idPedidoImpugnacao)
    {
        try {
            $query = $this->getQueryJulgamentoRecursoImpugnacao();

            $query->andWhere('pedidoImpugnacao.id = :id');
            $query->setParameter('id', $idPedidoImpugnacao);

            $julgamento = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return JulgamentoRecursoImpugnacaoTO::newInstance($julgamento);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Julgamento Substituiçao
     *
     * @param int $idCalendario
     * @param bool $addPedidoImpugnacao
     * @return JulgamentoRecursoImpugnacaoTO[]|null
     * @throws \Exception
     */
    public function getPorCalendario(int $idCalendario, bool $addPedidoImpugnacao = false)
    {
        try {
            $query = $this->getQueryJulgamentoRecursoImpugnacao();

            $query->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundaria');
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");

            if($addPedidoImpugnacao) {
                $this->addRelacoesPedidoImpugnacao($query);
            }

            $query->andWhere('calendario.id = :idCalendario');
            $query->setParameter('idCalendario', $idCalendario);

            $julgamentosRecursoImpugnacao = $query->getQuery()->getArrayResult();

            return array_map(function ($julgamentoRecursoImpugnacao) {
                return JulgamentoRecursoImpugnacaoTO::newInstance($julgamentoRecursoImpugnacao);
            }, $julgamentosRecursoImpugnacao);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a query com os joins dos relacionamentos
     *
     * @return QueryBuilder
     */
    private function getQueryJulgamentoRecursoImpugnacao(): QueryBuilder
    {
        $query = $this->createQueryBuilder('julgamentoRecursoImpugnacao')
            ->innerJoin('julgamentoRecursoImpugnacao.statusJulgamentoImpugnacao', 'statusJulgamento')
            ->innerJoin('julgamentoRecursoImpugnacao.usuario', 'usuario')
            ->innerJoin('julgamentoRecursoImpugnacao.pedidoImpugnacao', 'pedidoImpugnacao')
            ->innerJoin('pedidoImpugnacao.membroChapa', 'membroChapa')
            ->innerJoin('membroChapa.chapaEleicao', 'chapaEleicao')
            ->addSelect('statusJulgamento')
            ->addSelect('usuario');

        $query->where('chapaEleicao.excluido = false');

        return $query;
    }

    /**
     * Método auxiliar adiciona relações do pedido de substituição chapa
     *
     * @param QueryBuilder $query
     */
    private function addRelacoesPedidoImpugnacao(QueryBuilder $query): void
    {
        $query->innerJoin('julgamentoImpugnacao.pedidoImpugnacao', 'pedidoImpugnacao')
            ->innerJoin('pedidoImpugnacao.statusPedidoImpugnacao', 'statusPedidoImpugnacao')
            ->innerJoin('pedidoImpugnacao.profissional', 'profissional')
            ->innerJoin('pedidoImpugnacao.membroChapa', 'membroChapa')
            ->innerJoin('membroChapa.chapaEleicao', 'chapaEleicao')
            ->innerJoin('chapaEleicao.tipoCandidatura', 'tipoCandidatura')
            ->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundariaCalendario')
            ->innerJoin('atividadeSecundariaCalendario.atividadePrincipalCalendario', 'atividadePrincipalCalendario')
            ->innerJoin('atividadePrincipalCalendario.calendario', 'calendario')
            ->innerJoin('chapaEleicao.membrosChapa', 'membrosChapa')
            ->innerJoin('membrosChapa.profissional', 'profissionalMembro')
            ->addSelect('pedidoImpugnacao')
            ->addSelect('statusPedidoImpugnacao')
            ->addSelect('profissional')
            ->addSelect('membroChapa')
            ->addSelect('chapaEleicao')
            ->addSelect('membrosChapa')
            ->addSelect('profissionalMembro');
    }
}
