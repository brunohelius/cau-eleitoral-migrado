<?php
/*
 * CabecalhoEmailRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\To\CabecalhoEmailFiltroTO;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use App\Entities\CabecalhoEmail;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'CabecalhoEmail'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class CabecalhoEmailRepository extends AbstractRepository
{

    /**
     * Retorna dados de Cabeçalho E-mail.
     *
     * @param integer $idCabecalhoEmail
     * @return mixed|Statement|array|NULL|array
     * @throws NonUniqueResultException
     */
    public function getPorId($idCabecalhoEmail)
    {
        $query = $this->createQueryBuilder('cabecalhoEmail');
        $query->leftJoin('cabecalhoEmail.cabecalhoEmailUfs', 'cabecalhoEmailUfs')->addSelect('cabecalhoEmailUfs');
        $query->leftJoin('cabecalhoEmailUfs.uf', 'uf')->addSelect('uf');
        $query->leftJoin('cabecalhoEmail.corpoEmails', 'corpoEmails')->addSelect('corpoEmails');
        $query->where("cabecalhoEmail.id = :id");
        $query->setParameter(':id', $idCabecalhoEmail);

        try{            
            return $query->getQuery()->getSingleResult( );
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna quantidade total de E-mais vinculados ao cabeçalho.
     * @param integer $idCabecalhoEmail
     *
     * @return integer
     * @throws NonUniqueResultException
     */
    public function getTotalCorpoEmailVinculado($idCabecalhoEmail)
    {
        $query = $this->createQueryBuilder('cabecalhoEmail');
        $query->innerJoin('cabecalhoEmail.corpoEmails', 'corpoEmails');
        $query->select("COUNT(cabecalhoEmail.id)");
        $query->where("cabecalhoEmail.id = :id");
        $query->setParameter(':id', $idCabecalhoEmail);
        
        return $query->getQuery()->getSingleScalarResult(); 
    }
    
    
    /**
     * Busca de cabecalhos de E-mail com Filtro
     * 
     * @param CabecalhoEmailFiltroTO $cabecalhoEmailFiltroTO
     * @return array
     */
    public function getCabecalhoEmailPorFiltro(CabecalhoEmailFiltroTO $cabecalhoEmailFiltroTO)
    {
        $query = $this->createQueryBuilder('cabecalhoEmail');        
        $query->leftJoin('cabecalhoEmail.cabecalhoEmailUfs', 'cabecalhoEmailUfs')->addSelect('cabecalhoEmailUfs');
        $query->leftJoin('cabecalhoEmailUfs.uf', 'uf')->addSelect('uf');

        $query->orderBy("cabecalhoEmail.id", 'desc');
        
        if (!empty($cabecalhoEmailFiltroTO->getUfs())) {
            $query->leftJoin('cabecalhoEmail.cabecalhoEmailUfs', 'cabecalhoEmailUfs2');
            $query->leftJoin('cabecalhoEmailUfs2.uf', 'uf2');
            $query->andWhere('uf2.id IN (:cabecalhoEmailUfs)');
            $query->setParameter(':cabecalhoEmailUfs', $cabecalhoEmailFiltroTO->getUfs());
        }
        
        if (!empty($cabecalhoEmailFiltroTO->getIdsCabecalhosEmail())) {
            $query->andWhere("cabecalhoEmail.id IN (:ids)");
            $query->setParameter(':ids', $cabecalhoEmailFiltroTO->getIdsCabecalhosEmail());
        }

        if (!empty($cabecalhoEmailFiltroTO->isAtivo()) || $cabecalhoEmailFiltroTO->isAtivo() === false ) {
            if($cabecalhoEmailFiltroTO->isAtivo()){
                $query->andWhere( "(cabecalhoEmail.isCabecalhoAtivo = true OR cabecalhoEmail.isRodapeAtivo = true)");
            } else{
                $query->andWhere( "(cabecalhoEmail.isCabecalhoAtivo = false and cabecalhoEmail.isRodapeAtivo = false)");
            }            
        }

        $dadosCabecalhoEmail = $query->getQuery()->getArrayResult();

        return array_map(function ($data) {
            return CabecalhoEmail::newInstance($data);
        }, $dadosCabecalhoEmail);
    }

}
