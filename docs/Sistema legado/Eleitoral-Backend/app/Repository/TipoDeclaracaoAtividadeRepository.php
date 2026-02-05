<?php


namespace App\Repository;

use App\Entities\EmailAtividadeSecundaria;
use App\Entities\TipoDeclaracaoAtividade;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'TipoDeclaracaoAtividade'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class TipoDeclaracaoAtividadeRepository extends AbstractRepository
{

    /**
     * Retorna os tipos de declaração da atividade secundária
     *
     * @param array $ids
     * @return array|null
     */
    public function getTiposDeclaracaoPorIds($ids){
        try {
            $query = $this->createQueryBuilder('tipoDeclaracaoAtividade');

            $query->where(" tipoDeclaracaoAtividade.id IN (:id)");
            $query->setParameter(':id', $ids);

            $arrayDados = $query->getQuery()->getArrayResult();

            return array_map(static function($dadosTipoDeclaracao){
                return  TipoDeclaracaoAtividade::newInstance($dadosTipoDeclaracao);
            }, $arrayDados);
        } catch (NoResultException $e) {
            return null;
        }
    }

}