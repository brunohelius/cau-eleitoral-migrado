<?php

namespace App\Repository;

use App\Entities\TipoMembroChapa;
use App\Entities\TipoParticipacaoChapa;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'TipoParticipacaoChapa'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class TipoParticipacaoChapaRepository extends AbstractRepository
{
    /**
     * Retorna o tipo participação chapa conforme o id informado.
     *
     * @param $id
     *
     * @return TipoParticipacaoChapa|null
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('tipoParticipacao');

            $query->where("tipoParticipacao.id = :id");
            $query->setParameter("id", $id);

            $tipoParticipacaoChapa = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return TipoParticipacaoChapa::newInstance($tipoParticipacaoChapa);
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}