<?php
/*
 * ArquivoJulgamentoDenunciaRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\ArquivoJulgamentoDenuncia;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'ArquivoJulgamentoDenuncia'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ArquivoJulgamentoDenunciaRepository extends AbstractRepository
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
            $query = $this->createQueryBuilder('arquivoJulgamento');
            $query->innerJoin('arquivoJulgamento.julgamentoDenuncia', 'julgamentoDenuncia')
                ->addSelect('julgamentoDenuncia');
            $query->innerJoin('julgamentoDenuncia.denuncia', 'denuncia')
                ->addSelect('denuncia');

            $query->where('arquivoJulgamento.id = :id');
            $query->setParameter("id", $id);

            $arquivo = $query->getQuery()->getArrayResult();
            return array_map(static function ($dadosArquivo) {
                return ArquivoJulgamentoDenuncia::newInstance($dadosArquivo);
            }, $arquivo);
        } catch (NoResultException $e) {
            return null;
        }
    }
}
