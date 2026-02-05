<?php

namespace App\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'AlegacaoFinal'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class AlegacaoFinalRepository extends AbstractRepository
{
    
    /**
     * Retorna 'ImpedimentoSuspeicao' relacionada a um pedido de emcaminhamento.
     *
     * @param $idEmcaminhamento
     * @return mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorEncaminhamento($idEncaminhamento)
    {        
        try {
            $query = $this->createQueryBuilder("alegacaoFinal");
            $query->leftJoin("alegacaoFinal.arquivosAlegacaoFinal", "arquivosAlegacaoFinal")->addSelect('arquivosAlegacaoFinal');
            $query->where("alegacaoFinal.encaminhamentoDenuncia = :idEncaminhamento");
            $query->setParameter('idEncaminhamento', $idEncaminhamento);
            
            return $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
        } catch (NoResultException $e) {
            return null;
        }
    }
    
}