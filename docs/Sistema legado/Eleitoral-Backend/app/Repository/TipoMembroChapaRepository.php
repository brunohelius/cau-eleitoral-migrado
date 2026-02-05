<?php

namespace App\Repository;

use App\Entities\StatusParticipacaoChapa;
use App\Entities\TipoMembroChapa;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'TipoMembroChapa'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class TipoMembroChapaRepository extends AbstractRepository
{
    /**
     * Retorna o tipo membro chapa conforme o id informado.
     *
     * @param $id
     *
     * @return TipoMembroChapa|null
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('tipoMembro');

            $query->where("tipoMembro.id = :id");
            $query->setParameter("id", $id);

            $tipoMembroChapa = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return TipoMembroChapa::newInstance($tipoMembroChapa);
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}