<?php
/*
 * ArquivoDenunciaProvasRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;


use Doctrine\ORM\NoResultException;
use App\Entities\ArquivoDenunciaProvas;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'Arquivo de Denuncias das Provas'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ArquivoDenunciaProvasRepository extends AbstractRepository
{
  /**
     * Retorna o arquivo da denúncia provas conforme o id informado.
     *
     * @param $id
     * @return array|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('arquivoDenunciaProvas');
            $query->innerJoin("arquivoDenunciaProvas.denunciaProvas", "denunciaProvas")->addSelect('denunciaProvas');
            $query->where("arquivoDenunciaProvas.id = :id");
            $query->setParameter("id", $id);
            $arquivo = $query->getQuery()->getArrayResult();

            return array_map(function ($dadosArquivo) {
                return ArquivoDenunciaProvas::newInstance($dadosArquivo);
            }, $arquivo);
        } catch (NoResultException $e) {
            return null;
        }
    }
}
