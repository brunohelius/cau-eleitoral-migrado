<?php

namespace App\Repository;

use App\To\JulgamentoSubstituicaoTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'JulgamentoSubstituicao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoSubstituicaoRepository extends AbstractRepository
{

    /**
     * Retorna a quantidade de Julgamento Substituiçao
     *
     * @param int $id
     * @param bool $addPedidoSubstituicao
     * @return JulgamentoSubstituicaoTO
     * @throws \Exception
     */
    public function getPorId(int $id, $addPedidoSubstituicao = false)
    {
        try {
            $query = $this->getQueryJulgamentoSubstituicao($addPedidoSubstituicao);

            $query->andWhere('julgamentoSubstituicao.id = :id');
            $query->setParameter('id', $id);

            $julgamentoSubstituicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return JulgamentoSubstituicaoTO::newInstance($julgamentoSubstituicao);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Julgamento Substituiçao
     *
     * @param int $idPedidoSubstituicao
     * @param bool $addPedidoSubstituicao
     * @return JulgamentoSubstituicaoTO
     * @throws \Exception
     */
    public function getPorPedidosSubstituicao(int $idPedidoSubstituicao, $addPedidoSubstituicao = false)
    {
        try {
            $query = $this->getQueryJulgamentoSubstituicao($addPedidoSubstituicao);

            $query->andWhere('pedidoSubstituicaoChapa.id = :id');
            $query->setParameter('id', $idPedidoSubstituicao);

            $julgamentoSubstituicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return JulgamentoSubstituicaoTO::newInstance($julgamentoSubstituicao);
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
    public function getPorCalendario(int $idCalendario, $addPedidoSubstituicao = false)
    {
        try {
            $query = $this->getQueryJulgamentoSubstituicao($addPedidoSubstituicao);

            $query->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundaria');
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");

            $query->andWhere('calendario.id = :idCalendario');
            $query->setParameter('idCalendario', $idCalendario);

            $julgamentosSubstituicao = $query->getQuery()->getArrayResult();

            return array_map(function ($julgamentoSubstituicao){
                return JulgamentoSubstituicaoTO::newInstance($julgamentoSubstituicao);
            }, $julgamentosSubstituicao);
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
            $query = $this->createQueryBuilder('julgamentoSubstituicao');
            $query->select('calendario.id');
            $query->innerJoin('julgamentoSubstituicao.pedidoSubstituicaoChapa', 'pedidoSubstituicaoChapa');
            $query->innerJoin('pedidoSubstituicaoChapa.chapaEleicao', 'chapaEleicao');
            $query->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundaria');
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");

            $query->where('julgamentoSubstituicao.id = :id');
            $query->setParameter('id', $idJulgamento);

            return $query->getQuery()->getSingleScalarResult();
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
    private function getQueryJulgamentoSubstituicao($addPedidoSubstituicao): QueryBuilder
    {
        $query = $this->createQueryBuilder('julgamentoSubstituicao')
            ->innerJoin('julgamentoSubstituicao.statusJulgamentoSubstituicao', 'statusJulgamento')
            ->innerJoin('julgamentoSubstituicao.usuario', 'usuario')
            ->addSelect('statusJulgamento')
            ->addSelect('usuario');

        if ($addPedidoSubstituicao) {
            $this->addRelacoesPedidoSubstituicaoChapa($query);
        } else {
            $query->innerJoin('julgamentoSubstituicao.pedidoSubstituicaoChapa', 'pedidoSubstituicaoChapa');
            $query->innerJoin('pedidoSubstituicaoChapa.chapaEleicao', 'chapaEleicao');
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
        $query->innerJoin('julgamentoSubstituicao.pedidoSubstituicaoChapa', 'pedidoSubstituicaoChapa')
            ->innerJoin("pedidoSubstituicaoChapa.chapaEleicao", "chapaEleicao")
            ->innerJoin("chapaEleicao.tipoCandidatura", "tipoCandidatura")
            ->innerJoin('pedidoSubstituicaoChapa.statusSubstituicaoChapa', 'statusSubstituicaoChapa')
            ->innerJoin('pedidoSubstituicaoChapa.membrosChapaSubstituicao', 'membrosChapaSubstituicao')
            ->leftJoin("chapaEleicao.chapaEleicaoStatus", "chapaEleicaoStatus")
            ->leftJoin("chapaEleicaoStatus.statusChapa", "statusChapa")
            ->addSelect("pedidoSubstituicaoChapa")
            ->addSelect("chapaEleicao")
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
