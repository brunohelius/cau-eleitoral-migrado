<?php

namespace App\Repository;

use App\To\SubstituicaoImpugnacaoTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'SubstituicaoImpugnacao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class SubstituicaoImpugnacaoRepository extends AbstractRepository
{

    /**
     * Retorna o recurso de Julgamento Impugnação
     *
     * @param int $idPedidoImpugnacao
     *
     * @return SubstituicaoImpugnacaoTO
     * @throws \Exception
     */
    public function getPorPedidoImpugnacao($idPedidoImpugnacao)
    {
        try {
            $query = $this->getQuerySubstituicaoImpugnacao();

            $query->andWhere('pedidoImpugnacao.id = :id');
            $query->setParameter('id', $idPedidoImpugnacao);

            $recursoImpugnacao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return SubstituicaoImpugnacaoTO::newInstance($recursoImpugnacao);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a query com os joins dos relacionamentos
     *
     * @return QueryBuilder
     */
    private function getQuerySubstituicaoImpugnacao(): QueryBuilder
    {
        $query = $this->createQueryBuilder('subsituicaoImpugnacao');
        $query->innerJoin('subsituicaoImpugnacao.pedidoImpugnacao', 'pedidoImpugnacao');
        $query->innerJoin('subsituicaoImpugnacao.profissional', 'profissional');
        $query->innerJoin('subsituicaoImpugnacao.membroChapaSubstituto', 'membroChapaSubstituto');
        $query->innerJoin("membroChapaSubstituto.tipoMembroChapa", "tipoMembroChapaSubstituto");
        $query->innerJoin("membroChapaSubstituto.tipoParticipacaoChapa", "tipoParticipacaoChapaSubstituto");
        $query->innerJoin("membroChapaSubstituto.statusParticipacaoChapa", "statusParticipacaoSubstituto");
        $query->innerJoin("membroChapaSubstituto.statusValidacaoMembroChapa", "statusValidacaoMembroSubstituto");
        $query->innerJoin("membroChapaSubstituto.profissional", "profissionalSubstituto");
        $query->innerJoin("profissionalSubstituto.pessoa", "pessoaSubstituto");
        $query->leftJoin("membroChapaSubstituto.pendencias", "pendenciasSubstituto");
        $query->leftJoin("pendenciasSubstituto.tipoPendencia", "tipoPendenciaSubstituto");

        $query->addSelect('profissional');
        $query->addSelect('membroChapaSubstituto');
        $query->addSelect('tipoMembroChapaSubstituto');
        $query->addSelect('tipoParticipacaoChapaSubstituto');
        $query->addSelect('statusParticipacaoSubstituto');
        $query->addSelect('statusValidacaoMembroSubstituto');
        $query->addSelect('profissionalSubstituto');
        $query->addSelect('pessoaSubstituto');
        $query->addSelect('pendenciasSubstituto');
        $query->addSelect('tipoPendenciaSubstituto');

        return $query;
    }
}