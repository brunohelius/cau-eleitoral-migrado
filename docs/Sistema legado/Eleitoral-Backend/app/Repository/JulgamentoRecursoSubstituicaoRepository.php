<?php

namespace App\Repository;

use App\Entities\JulgamentoRecursoSubstituicao;
use App\To\JulgamentoRecursoSubstituicaoTO;
use App\To\JulgamentoSubstituicaoTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'JulgamentoRecursoSubstituicao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoRecursoSubstituicaoRepository extends AbstractRepository
{
    /**
     * Retorna a quantidade de Julgamento Substituiçao
     *
     * @param int $idPedidoSubstituicao
     * @return JulgamentoRecursoSubstituicaoTO
     * @throws \Exception
     */
    public function getPorPedidosSubstituicao(int $idPedidoSubstituicao)
    {
        try {
            $query = $this->getQueryJulgamentoRecursoSubstituicao();

            $query->andWhere('pedidoSubstituicaoChapa.id = :id');
            $query->setParameter('id', $idPedidoSubstituicao);

            $julgamentoSubstituicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return JulgamentoRecursoSubstituicaoTO::newInstance($julgamentoSubstituicao);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a query com os joins dos relacionamentos
     *
     * @param $idPedidoSubstituicao
     * @return JulgamentoRecursoSubstituicao
     */
    public function findPorPedidoSubstituicao($idPedidoSubstituicao)
    {
        try {
            $query = $this->getQueryJulgamentoRecursoSubstituicao();

            $query->andWhere('pedidoSubstituicaoChapa.id = :id');
            $query->setParameter('id', $idPedidoSubstituicao);

            return $query->getQuery()->getSingleResult();
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Julgamento Substituiçao
     *
     * @param int $idCalendario
     * @param bool $addPedidoSubstituicao
     * @return JulgamentoSubstituicaoTO[]|null
     * @throws \Exception
     */
    public function getPorCalendario(int $idCalendario)
    {
        try {
            $query = $this->getQueryJulgamentoRecursoSubstituicao(true);

            $query->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundaria');
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");
            $query->addSelect("atividadeSecundaria");

            $this->addRelacoesPedidoSubstituicaoChapa($query);

            $query->andWhere('calendario.id = :idCalendario');
            $query->setParameter('idCalendario', $idCalendario);

            $julgamentosRecursoSubstituicao = $query->getQuery()->getArrayResult();

            return array_map(function ($julgamentoRecursoSubstituicao) {
                return JulgamentoRecursoSubstituicaoTO::newInstance($julgamentoRecursoSubstituicao);
            }, $julgamentosRecursoSubstituicao);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a query com os joins dos relacionamentos
     *
     * @return QueryBuilder
     */
    private function getQueryJulgamentoRecursoSubstituicao($addRelacoesSelect = false): QueryBuilder
    {
        $query = $this->createQueryBuilder('julgamentoRecursoSubstituicao')
            ->innerJoin('julgamentoRecursoSubstituicao.statusJulgamentoSubstituicao', 'statusJulgamento')
            ->innerJoin('julgamentoRecursoSubstituicao.usuario', 'usuario')
            ->innerJoin('julgamentoRecursoSubstituicao.recursoSubstituicao', 'recursoSubstituicao')
            ->innerJoin('recursoSubstituicao.julgamentoSubstituicao', 'julgamentoSubstituicao')
            ->innerJoin('julgamentoSubstituicao.pedidoSubstituicaoChapa', 'pedidoSubstituicaoChapa')
            ->innerJoin('pedidoSubstituicaoChapa.chapaEleicao', 'chapaEleicao')
            ->addSelect('statusJulgamento')
            ->addSelect('usuario');

        if ($addRelacoesSelect) {
            $query->addSelect("recursoSubstituicao");
            $query->addSelect("julgamentoSubstituicao");
            $query->addSelect("pedidoSubstituicaoChapa");
            $query->addSelect("chapaEleicao");
        }

        $query->where('chapaEleicao.excluido = false');

        return $query;
    }

    /**
     * Método auxiliar adiciona relações do pedido de substituição chapa
     *
     * @param QueryBuilder $query
     */
    private function addRelacoesPedidoSubstituicaoChapa(QueryBuilder $query): void
    {
        $query->innerJoin("chapaEleicao.tipoCandidatura", "tipoCandidatura")
            ->innerJoin('pedidoSubstituicaoChapa.statusSubstituicaoChapa', 'statusSubstituicaoChapa')
            ->innerJoin('pedidoSubstituicaoChapa.membrosChapaSubstituicao', 'membrosChapaSubstituicao')
            ->leftJoin("chapaEleicao.chapaEleicaoStatus", "chapaEleicaoStatus")
            ->leftJoin("chapaEleicaoStatus.statusChapa", "statusChapa")
            ->addSelect("tipoCandidatura")
            ->addSelect('statusSubstituicaoChapa')
            ->addSelect('membrosChapaSubstituicao')
            ->addSelect("chapaEleicaoStatus")
            ->addSelect("statusChapa");

        $query->leftJoin('membrosChapaSubstituicao.membroChapaSubstituido', 'membroChapaSubstituido')
            ->leftJoin("membroChapaSubstituido.tipoMembroChapa", "tipoMembroChapaSubstituido")
            ->leftJoin("membroChapaSubstituido.tipoParticipacaoChapa", "tipoParticipacaoChapaSubstituido")
            ->leftJoin("membroChapaSubstituido.statusParticipacaoChapa", "statusParticipacaoChapaSubstituido")
            ->leftJoin("membroChapaSubstituido.statusValidacaoMembroChapa", "statusValidacaoMembroChapaSubstituido")
            ->leftJoin("membroChapaSubstituido.pendencias", "pendenciasSubstituido")
            ->leftJoin("pendenciasSubstituido.tipoPendencia", "tipoPendenciaSubstituido")
            ->leftJoin("membroChapaSubstituido.profissional", "profissionalSubstituido")
            ->leftJoin("profissionalSubstituido.pessoa", "pessoaSubstituido")
            ->addSelect('membroChapaSubstituido')
            ->addSelect('tipoMembroChapaSubstituido')
            ->addSelect("tipoParticipacaoChapaSubstituido")
            ->addSelect("statusParticipacaoChapaSubstituido")
            ->addSelect('statusValidacaoMembroChapaSubstituido')
            ->addSelect('pendenciasSubstituido')
            ->addSelect('tipoPendenciaSubstituido')
            ->addSelect('profissionalSubstituido')
            ->addSelect('pessoaSubstituido');

        $query->leftJoin('membrosChapaSubstituicao.membroChapaSubstituto', 'membroChapaSubstituto')
            ->leftJoin("membroChapaSubstituto.tipoMembroChapa", "tipoMembroChapaSubstituto")
            ->leftJoin("membroChapaSubstituto.tipoParticipacaoChapa", "tipoParticipacaoChapaSubstituto")
            ->leftJoin("membroChapaSubstituto.statusParticipacaoChapa", "statusParticipacaoChapaSubstituto")
            ->leftJoin("membroChapaSubstituto.statusValidacaoMembroChapa", "statusValidacaoMembroChapaSubstituto")
            ->leftJoin("membroChapaSubstituto.pendencias", "pendenciasSubstituto")
            ->leftJoin("pendenciasSubstituto.tipoPendencia", "tipoPendenciaSubstituto")
            ->leftJoin("membroChapaSubstituto.profissional", "profissionalSubstituto")
            ->leftJoin("profissionalSubstituto.pessoa", "pessoaSubstituto")
            ->addSelect('membroChapaSubstituto')
            ->addSelect('tipoMembroChapaSubstituto')
            ->addSelect('tipoParticipacaoChapaSubstituto')
            ->addSelect('statusParticipacaoChapaSubstituto')
            ->addSelect('statusValidacaoMembroChapaSubstituto')
            ->addSelect('pendenciasSubstituto')
            ->addSelect('tipoPendenciaSubstituto')
            ->addSelect('profissionalSubstituto')
            ->addSelect('pessoaSubstituto');
    }
}
