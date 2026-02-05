<?php
/*
 * ArquivoRecursoContrarrazaoDenunciaRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\ArquivoRecursoContrarrazaoDenuncia;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'Arquivo Recurso Contrarrazao Denuncia'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ArquivoRecursoContrarrazaoDenunciaRepository extends AbstractRepository
{

    /**
     * Retorna o arquivo da recurso e da contrarrazão conforme o id informado.
     *
     * @param $id
     * @return array|null
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('arquivoRecurso');
            $query->leftJoin('arquivoRecurso.recurso', 'recurso')
                  ->addSelect('recurso');
            $query->leftJoin('recurso.denuncia', 'denunciaRecurso')
                  ->addSelect('denunciaRecurso');

            $query->where('arquivoRecurso.id = :id');
            $query->setParameter("id", $id);

            $arquivo = $query->getQuery()->getArrayResult();
            return array_map(static function ($dadosArquivo) {
                return ArquivoRecursoContrarrazaoDenuncia::newInstance($dadosArquivo);
            }, $arquivo);
        } catch (NoResultException $e) {
            return null;
        }
    }
}
