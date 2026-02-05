<?php
/*
 * ArquivoDenunciaAdmitidaRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\Denuncia;
use App\Entities\ArquivoDenuncia;
use App\Entities\ArquivoDenunciaAdmitida;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;


/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'ArquivoDenunciaAdmitida'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ArquivoDenunciaAdmitidaRepository extends AbstractRepository
{
    
    /**
     * Retorna os arquivos a denuncia conforme o id informado.
     *
     * @param $id
     * @return \App\Entities\ArquivoDenunciaAdmitida|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('arquivoDenunciaAdmitida');
            $query->innerJoin("arquivoDenunciaAdmitida.denunciaAdmitida", "denunciaAdmitida")->addSelect('denunciaAdmitida');
            $query->innerJoin("denunciaAdmitida.denuncia", "denuncia")->addSelect('denuncia');
            $query->where("arquivoDenunciaAdmitida.id = :id");
            $query->setParameter("id", $id);
            
            $arquivo = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            
            return ArquivoDenunciaAdmitida::newInstance($arquivo);
        } catch (NoResultException $e) {
            return null;
        }
    }

}