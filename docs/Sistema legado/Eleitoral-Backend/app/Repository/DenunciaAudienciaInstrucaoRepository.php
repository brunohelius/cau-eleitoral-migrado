<?php
/*
 * DenunciaAudienciaInstrucaoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Config\Constants;
use App\Entities\DenunciaAudienciaInstrucao;
use App\Entities\ArquivoDenunciaAudienciaInstrucao;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'DenunciaAudienciaInstrucaoRepository'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class DenunciaAudienciaInstrucaoRepository extends AbstractRepository
{
    /**
     * Retorna a Audiencia de instrução conforme o id informado.
     *
     * @param $idDenunciaAudienciaInstrucao
     * @return DenunciaAudienciaInstrucao|null
     * @throws DenunciaAudienciaInstrucao
     */
    public function getPorId($idDenunciaAudienciaInstrucao)
    {
        try {
            $query = $this->createQueryBuilder('DenunciaAudienciaInstrucao');
            $query->leftJoin('DenunciaAudienciaInstrucao.arquivosDenunciaAudienciaInstrucao', 'ArquivoDenunciaAudienciaInstrucao')->addSelect('ArquivoDenunciaAudienciaInstrucao');
            $query->where("DenunciaAudienciaInstrucao.id = :id");
            $query->setParameter("id", $idDenunciaAudienciaInstrucao);
            
            $denunciaAudienciaInstrucao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return DenunciaAudienciaInstrucao::newInstance($denunciaAudienciaInstrucao);
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
    
    /**
     * Retorna a Audiencia de instrução conforme o id do encaminhamento.
     *
     * @param $idEncaminhamento
     * @return DenunciaAudienciaInstrucao|null
     * @throws DenunciaAudienciaInstrucao
     */
    public function getPorEncaminhamento($idEncaminhamento)
    {
        try {
            $query = $this->createQueryBuilder('DenunciaAudienciaInstrucao');
            $query->leftJoin('DenunciaAudienciaInstrucao.arquivosDenunciaAudienciaInstrucao', 'ArquivoDenunciaAudienciaInstrucao')->addSelect('ArquivoDenunciaAudienciaInstrucao');
            $query->where("DenunciaAudienciaInstrucao.encaminhamentoDenuncia = :id");
            $query->setParameter("id", $idEncaminhamento);
            
           // $denunciaAudienciaInstrucao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            //return DenunciaAudienciaInstrucao::newInstance($denunciaAudienciaInstrucao);
            
            return $query->getQuery()->getArrayResult();
            
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
    
    
}
