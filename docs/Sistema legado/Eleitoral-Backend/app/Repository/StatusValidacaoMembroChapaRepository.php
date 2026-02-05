<?php

namespace App\Repository;

use App\Entities\StatusValidacaoMembroChapa;
use App\Entities\TipoParticipacaoChapa;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'StatusValidacaoMembroChapa'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class StatusValidacaoMembroChapaRepository extends AbstractRepository
{
    /**
     * Retorna o status validação membro chapa conforme o id informado.
     *
     * @param $id
     *
     * @return StatusValidacaoMembroChapa|null
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('statusValidacao');

            $query->where("statusValidacao.id = :id");
            $query->setParameter("id", $id);

            $statusValidacao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return StatusValidacaoMembroChapa::newInstance($statusValidacao);
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}