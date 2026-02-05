<?php

namespace App\Repository;

use App\Entities\ChapaEleicao;
use App\Entities\StatusParticipacaoChapa;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'StatusParticipacaoChapa'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class StatusParticipacaoChapaRepository extends AbstractRepository
{
    /**
     * Retorna o status participação chapa conforme o id informado.
     *
     * @param $id
     *
     * @return StatusParticipacaoChapa|null
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('statusParticipacaoChapa');

            $query->where("statusParticipacaoChapa.id = :id");
            $query->setParameter("id", $id);

            $status = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return StatusParticipacaoChapa::newInstance($status);
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}