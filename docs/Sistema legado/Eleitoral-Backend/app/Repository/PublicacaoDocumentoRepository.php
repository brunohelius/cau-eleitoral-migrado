<?php

namespace App\Repository;

use App\Exceptions\NegocioException;
use Doctrine\ORM\AbstractQuery;
use App\To\PublicacaoDocumentoTO;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'PublicacaoDocumento'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class PublicacaoDocumentoRepository extends AbstractRepository
{

    /**
     * Recupera a publicação de documento de acordo com o 'id' informado.
     *
     * @param $id
     * @return PublicacaoDocumentoTO
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder("publicacaoDocumento");
            $query->join('publicacaoDocumento.documentoComissaoMembro', 'documentoComissaoMembro')
                ->addSelect('documentoComissaoMembro');
            $query->where("publicacaoDocumento.id = :idPublicacaoDocumento");
            $query->setParameter("idPublicacaoDocumento", $id);

            $documentoComissaoMembro = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return PublicacaoDocumentoTO::newInstance($documentoComissaoMembro);
        } catch (NoResultException $e) {
            return PublicacaoDocumentoTO::newInstance();
        }
    }

    /**
     * Recupera o total de publicações referente a publicação da comissão membro.
     *
     * @param $idDocumentoComissao
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getTotalPublicacoesComissaoMembro($idDocumentoComissao)
    {
        $query = $this->createQueryBuilder("publicacaoDocumento");
        $query->select('COUNT(publicacaoDocumento.id)');
        $query->join('publicacaoDocumento.documentoComissaoMembro', 'documentoComissaoMembro');
        $query->where("documentoComissaoMembro.id = :idDocumentoComissao");
        $query->setParameter("idDocumentoComissao", $idDocumentoComissao);
        return $query->getQuery()->getSingleScalarResult();
    }

}
