<?php
/*
 * ArquivoDenunciaInadmitidaRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\ArquivoDenuncia;
use App\Entities\ArquivoDenunciaInadmitida;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'ArquivoDenunciaInadmitida'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ArquivoDenunciaInadmitidaRepository extends AbstractRepository
{

    /**
     * Retorna os arquivos a denuncia conforme o id informado.
     *
     * @param $id
     * @return \App\Entities\ArquivoDenunciaInadmitida|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('arquivoDenunciaInadmitida');
            $query->innerJoin("arquivoDenunciaInadmitida.denunciaInadmitida", "denunciaInadmitida")->addSelect('denunciaInadmitida');
            $query->innerJoin("denunciaInadmitida.denuncia", "denuncia")->addSelect('denuncia');
            $query->where("arquivoDenunciaInadmitida.id = :id");
            $query->setParameter("id", $id);

            $arquivo = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return ArquivoDenunciaInadmitida::newInstance($arquivo);
        } catch (NoResultException $e) {
            return null;
        }
    }
}