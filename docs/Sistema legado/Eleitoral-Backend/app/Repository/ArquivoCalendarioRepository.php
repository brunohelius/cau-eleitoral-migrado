<?php
/*
 * ArquivoCalendarioRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\ArquivoCalendario;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'ArquivoCalendario'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ArquivoCalendarioRepository extends AbstractRepository
{
    /**
     * Retorna o arquivo do calendario conforme o id informado.
     *
     * @param $id
     * @return array|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('arquivoCalendario');
            $query->innerJoin("arquivoCalendario.calendario", "calendario")->addSelect('calendario');
            $query->where("arquivoCalendario.id = :id");
            $query->setParameter("id", $id);

            $arquivo = $query->getQuery()->getArrayResult();

            return array_map(function ($dadosArquivo) {
                return ArquivoCalendario::newInstance($dadosArquivo);
            }, $arquivo);
        } catch (NoResultException $e) {
            return null;
        }
    }
}