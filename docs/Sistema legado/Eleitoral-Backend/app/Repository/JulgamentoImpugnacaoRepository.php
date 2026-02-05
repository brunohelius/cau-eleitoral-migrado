<?php

namespace App\Repository;

use App\To\JulgamentoImpugnacaoTO;
use App\To\JulgamentoRecursoImpugnacaoTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'JulgamentoImpugnacao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoImpugnacaoRepository extends AbstractRepository
{

    /**
     * Retorna o julgamento de acordo com id informado
     *
     * @param int $id
     * @param bool $addPedidoImpugnacao
     * @return JulgamentoImpugnacaoTO
     * @throws \Exception
     */
    public function getPorId(int $id, $addPedidoImpugnacao = false)
    {
        try {
            $query = $this->getQueryJulgamentoImpugnacao($addPedidoImpugnacao);

            $query->andWhere('julgamentoImpugnacao.id = :id');
            $query->setParameter('id', $id);

            $julgamentoImpugnacao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return JulgamentoImpugnacaoTO::newInstance($julgamentoImpugnacao);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna o julgamento de acordo com id do pedido de impugnacao
     *
     * @param int $idPedidoImpugnacao
     * @param bool $addPedidoImpugnacao
     * @return JulgamentoImpugnacaoTO
     * @throws \Exception
     */
    public function getPorPedidoImpugnacao(int $idPedidoImpugnacao, $addPedidoImpugnacao = false)
    {
        try {
            $query = $this->getQueryJulgamentoImpugnacao($addPedidoImpugnacao);

            $query->andWhere('pedidoImpugnacao.id = :id');
            $query->setParameter('id', $idPedidoImpugnacao);

            $julgamentoImpugnacao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return JulgamentoImpugnacaoTO::newInstance($julgamentoImpugnacao);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Método que retorno o id
     *
     * @param $idJulgamento
     * @return integer|null
     */
    public function getIdCalendarioJulgamento($idJulgamento)
    {
        try {
            $query = $this->createQueryBuilder('julgamentoImpugnacao');
            $query->select('calendario.id');
            $query->innerJoin('julgamentoImpugnacao.pedidoImpugnacao', 'pedidoImpugnacao');
            $query->innerJoin("pedidoImpugnacao.membroChapa", "membroChapa");
            $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");
            $query->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundaria');
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");

            $query->where('julgamentoImpugnacao.id = :id');
            $query->setParameter('id', $idJulgamento);

            return $query->getQuery()->getSingleScalarResult();
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
            $query = $this->getQueryJulgamentoImpugnacao($addPedidoImpugnacao);

            $query->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundaria');
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");

            $query->andWhere('calendario.id = :idCalendario');
            $query->setParameter('idCalendario', $idCalendario);

            $julgamentos = $query->getQuery()->getArrayResult();

            return array_map(function ($julgamento) {
                return JulgamentoImpugnacaoTO::newInstance($julgamento);
            }, $julgamentos);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a query com os joins dos relacionamentos
     *
     * @param $addPedidoSubstituicao
     * @return QueryBuilder
     */
    private function getQueryJulgamentoImpugnacao($addPedidoSubstituicao): QueryBuilder
    {
        $query = $this->createQueryBuilder('julgamentoImpugnacao')
            ->innerJoin('julgamentoImpugnacao.statusJulgamentoImpugnacao', 'statusJulgamento')
            ->innerJoin('julgamentoImpugnacao.usuario', 'usuario')
            ->addSelect('statusJulgamento')
            ->addSelect('usuario');

        if ($addPedidoSubstituicao) {
            $this->addRelacoesPedidoImpugnacao($query);
        } else {
            $query->innerJoin('julgamentoImpugnacao.pedidoImpugnacao', 'pedidoImpugnacao');
            $query->innerJoin("pedidoImpugnacao.membroChapa", "membroChapa");
            $query->innerJoin('membroChapa.chapaEleicao', 'chapaEleicao');
        }

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
