<?php

namespace App\Repository;

use App\Entities\Denuncia;

/**
 * Class JulgamentoAdmissibilidadeRepository
 * @package App\Repository
 */
class JulgamentoAdmissibilidadeRepository extends AbstractRepository
{
    /**
     * @param Denuncia|int $denuncia
     * @return bool
     * @throws
     */
    public function existeJulgamento($denuncia)
    {
        return (bool) $this->createQueryBuilder('d')
            ->select('count(1)')
            ->andWhere('d.denuncia = :denuncia')
            ->setParameter('denuncia', $denuncia)
            ->getQuery()
            ->getSingleScalarResult();
    }
}