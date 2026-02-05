<?php
/*
 * ArquivoJulgamentoRecursoDenunciaRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\ArquivoJulgamentoRecursoDenuncia;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'ArquivoJulgamentoRecursoDenuncia'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ArquivoJulgamentoRecursoDenunciaRepository extends AbstractRepository
{

    /**
     * Retorna o arquivo do julgamento conforme o id informado.
     *
     * @param $id
     * @return array|null
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('arquivoJulgamentoRecurso');
            $query->innerJoin('arquivoJulgamentoRecurso.julgamentoRecursoDenuncia', 'julgamentoRecursoDenuncia')
                  ->addSelect('julgamentoRecursoDenuncia');
            $query->where('arquivoJulgamentoRecurso.id = :id');
            $query->setParameter("id", $id);
            $query->getDQL();
            $arquivo = $query->getQuery()->getArrayResult();

            return array_map(static function ($dadosArquivo) {
                return ArquivoJulgamentoRecursoDenuncia::newInstance($dadosArquivo);
            }, $arquivo);
        } catch (NoResultException $e) {
            return null;
        }
    }

}
