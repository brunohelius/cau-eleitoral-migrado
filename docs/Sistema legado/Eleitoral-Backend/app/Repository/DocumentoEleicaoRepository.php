<?php
/*
 * DocumentoEleicaoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\DocumentoEleicao;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'DocumentoEleicao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class DocumentoEleicaoRepository extends AbstractRepository
{
    /**
     * Retorna uma lista de instâncias de 'DocumentoEleicao' conforme o id da eleição informada.
     *
     * @param integer $idCalendario
     * @return array|null
     */
    public function getDocumentosEleicaoPorCalendario($idCalendario)
    {
        $query = $this->createQueryBuilder("documentoEleicao");
        $query->innerJoin("documentoEleicao.eleicao", "eleicao");
        $query->innerJoin("eleicao.calendario", "calendario");
        $query->where("calendario.id = :idCalendario");
        $query->setParameter("idCalendario", $idCalendario);
        $query->orderBy("documentoEleicao.sequencial");

        return $query->getQuery()->getResult();
    }

    /**
     * Retorna o último sequencial da eleição informada.
     *
     * @param integer $idEleicao
     * @return integer
     * @throws NonUniqueResultException
     */
    public function getUltimaSequenciaPorEleicao($idEleicao)
    {
        try {
            $query = $this->createQueryBuilder("documentoEleicao");
            $query->select("MAX(documentoEleicao.sequencial)");
            $query->innerJoin("documentoEleicao.eleicao", "eleicao");
            $query->where("eleicao.id = :idEleicao");
            $query->setParameter("idEleicao", $idEleicao);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     * Retorna o 'Documento' da Eleição conforme o id informado.
     *
     * @param $id
     * @return array|null
     * @throws NonUniqueResultException
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('documentoEleicao');
            $query->where("documentoEleicao.id = :id");
            $query->setParameter("id", $id);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}