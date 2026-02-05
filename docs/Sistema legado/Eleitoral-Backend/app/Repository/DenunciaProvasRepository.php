<?php
/*
 * DenunciaProvasRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Config\Constants;
use App\Entities\ArquivoDenunciaProvas;
use App\Entities\Denuncia;
use App\Entities\DenunciaMembroChapa;
use App\Entities\DenunciaMembroComissao;
use App\Entities\DenunciaSituacao;
use App\Entities\Filial;
use App\Entities\HistoricoDenuncia;
use App\Entities\Pessoa;
use App\Entities\Profissional;
use App\Entities\TipoDenuncia;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'Denuncias Provas'.
 *
 * Class DenunciaProvasRepository
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class DenunciaProvasRepository extends AbstractRepository
{
    public function getDenunciaProvasPorEncaminhamento($idEncaminhamento)
    {
        try {
            $query = $this->createQueryBuilder('denunciaProvas');
            $query->leftJoin('denunciaProvas.arquivosDenunciaProvas', 'ArquivoDenunciaProvas')->addSelect('ArquivoDenunciaProvas');
            $query->where("denunciaProvas.encaminhamentoDenuncia = :id");
            $query->setParameter("id", $idEncaminhamento);

            //return $query->getQuery()->getArrayResult();
            return $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
        } catch (NoResultException $e) {
            return null;
        }
    }
}
