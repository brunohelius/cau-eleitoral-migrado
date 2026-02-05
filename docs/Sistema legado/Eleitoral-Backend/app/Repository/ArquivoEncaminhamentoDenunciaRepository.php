<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 16/09/2019
 * Time: 15:56
 */

namespace App\Repository;

use App\Entities\ArquivoEncaminhamentoDenuncia;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'ArquivoEncaminhamentoDenuncia'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ArquivoEncaminhamentoDenunciaRepository extends AbstractRepository
{
  /**
     * Retorna os arquivos do encaminhamento da denuncia conforme o id informado.
     *
     * @param $id
     * @return \App\Entities\ArquivoEncaminhamentoDenuncia|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('arquivoEncaminhamentoDenuncia');
            $query->innerJoin("arquivoEncaminhamentoDenuncia.encaminhamentoDenuncia", "encaminhamentoDenuncia")->addSelect('encaminhamentoDenuncia');
            $query->innerJoin("encaminhamentoDenuncia.denuncia", "denuncia")->addSelect('denuncia');
            $query->leftJoin("encaminhamentoDenuncia.parecerFinal", "parecerFinal")->addSelect('parecerFinal');
            $query->where("arquivoEncaminhamentoDenuncia.id = :id");
            $query->setParameter("id", $id);
            
            $arquivo = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            
            return ArquivoEncaminhamentoDenuncia::newInstance($arquivo);
        } catch (NoResultException $e) {
            return null;
        }
    }
}
