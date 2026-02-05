<?php
/*
 * HistoricoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'Historico'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class HistoricoRepository extends AbstractRepository
{
    /**
     * Retorna o histórico por Tipo e por Filtro
     *
     * @param $tipo
     * @param $filtroTO
     * @return array|null
     */
    public function getPorTipo($tipo, $filtroTO)
    {
        try {
            $query = $this->createQueryBuilder('historico')->addSelect('historico');
            $query->where("historico.tipoHistorico = :tipo");
            $query->andWhere("historico.idReferencia = :idReferencia");

            $query->setParameter("tipo", $tipo);
            $query->setParameter('idReferencia', $filtroTO->idReferencia);

            $query->orderBy('historico.dataHistorico', 'ASC');

            return $query->getQuery()->getArrayResult();
        }catch (NoResultException $e){
            return null;
        }
    }
}
