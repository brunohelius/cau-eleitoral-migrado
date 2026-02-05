<?php
/*
 * DenunciaAdmitidaRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Config\Constants;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use App\Entities\Denuncia;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'DenunciaAdmissibilidade'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class DenunciaAdmissibilidadeRepository extends AbstractRepository
{

    /**
     * @param Denuncia|int $denuncia
     * @return bool
     * @throws
     */
    public function existeAdmissao($denuncia)
    {
        return (bool) $this->createQueryBuilder('a')
            ->select('count(1)')
            ->andWhere('a.denuncia = :denuncia')
            ->setParameter('denuncia', $denuncia)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
