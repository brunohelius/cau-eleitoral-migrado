<?php
/*
 * ArquivoDenunciaRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\ArquivoDenuncia;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'ArquivoDenuncia'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ArquivoDenunciaRepository extends AbstractRepository
{
    /**
     * Retorna o arquivo da denúncia conforme o id informado.
     *
     * @param $id
     * @return array|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('arquivoDenuncia');
            $query->innerJoin("arquivoDenuncia.denuncia", "denuncia")->addSelect('denuncia');
            $query->where("arquivoDenuncia.id = :id");
            $query->setParameter("id", $id);
            $arquivo = $query->getQuery()->getArrayResult();

            return array_map(function ($dadosArquivo) {
                return ArquivoDenuncia::newInstance($dadosArquivo);
            }, $arquivo);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna o arquivo a denuncia conforme o id informado.
     *
     * @param $id
     * @return array|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getArquivoPorDenuncia($id)
    {
        try {
            $query = $this->createQueryBuilder('arquivoDenuncia');
            $query->innerJoin("arquivoDenuncia.denuncia", "denuncia")->addSelect('denuncia');
            $query->where("denuncia.id = :id");
            $query->setParameter("id", $id);

            $arquivo = $query->getQuery()->getArrayResult();

            return array_map(function ($dadosArquivo) {
                return ArquivoDenuncia::newInstance($dadosArquivo);
            }, $arquivo);
        } catch (NoResultException $e) {
            return null;
        }
    }
}