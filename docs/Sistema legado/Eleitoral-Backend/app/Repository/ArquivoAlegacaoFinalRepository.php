<?php

namespace App\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NoResultException;
use App\Entities\ArquivoAlegacaoFinal;
use App\Entities\AlegacaoFinal;
/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'AlegacaoFinal'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ArquivoAlegacaoFinalRepository extends AbstractRepository
{
    
    /**
     * Retorna o arquivo da alegação final conforme o id informado.
     *
     * @param $id
     * @return \App\Entities\ArquivoAlegacaoFinal|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('arquivoAlegacaoFinal');
            $query->innerJoin("arquivoAlegacaoFinal.alegacaoFinal", "alegacaoFinal")->addSelect('alegacaoFinal');
            $query->where("arquivoAlegacaoFinal.id = :id");
            $query->setParameter("id", $id);
        
            $arquivo = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
     
            return ArquivoAlegacaoFinal::newInstance($arquivo);
        } catch (NoResultException $e) {
            return null;
        }
    }
    
}