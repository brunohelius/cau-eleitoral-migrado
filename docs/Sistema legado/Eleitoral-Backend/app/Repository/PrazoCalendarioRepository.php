<?php
/*
 * PrazoCalendarioRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;


use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use phpDocumentor\Reflection\Types\Integer;
use App\Entities\PrazoCalendario;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'PrazoCalendario'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class PrazoCalendarioRepository extends AbstractRepository
{

    /**
     * Retorna o total de prazos vinculados ao calendário informado.
     *
     * @param $idCalendario
     * @return integer
     * @throws NonUniqueResultException
     */
    public function getTotalPrazosPorCalendario($idCalendario)
    {
        try {
            $query = $this->createQueryBuilder("prazoCalendario");
            $query->select("COUNT(prazoCalendario.id)");
            $query->innerJoin("prazoCalendario.atividadePrincipal", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");

            $query->where("calendario.id = :idCalendario");
            $query->setParameter("idCalendario", $idCalendario);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
    
    /**
     * Retorna prazo calendário vinculados a atividade principal.
     * @param Integer $idAtividadePrincipal
     * @return array| null
     */
    public function getPrazosPorAtividadePrincipal($idAtividadePrincipal){
        try {
            $query = $this->createQueryBuilder("prazoCalendario");
            $query->innerJoin("prazoCalendario.atividadePrincipal", "atividadePrincipal");   
            
            $query->where("atividadePrincipal.id = :idAtividadePrincipal");
            $query->setParameter(":idAtividadePrincipal", $idAtividadePrincipal);
            
            return array_map(function($prazo){
                return PrazoCalendario::newInstance($prazo);
            }, $query->getQuery()->getArrayResult());
        } catch (NoResultException $e) {
            return null;
        }
    }

}
