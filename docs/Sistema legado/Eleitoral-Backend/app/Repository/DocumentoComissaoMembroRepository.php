<?php

namespace App\Repository;

use App\Exceptions\NegocioException;
use App\To\DocumentoComissaoMembroTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Internal\Hydration\ArrayHydrator;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'DocumentoComissaoMembro'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class DocumentoComissaoMembroRepository extends AbstractRepository
{

    /**
     * Recupera o documento da comissão vinculado ao 'id' informado.
     *
     * @param $id
     * @return mixed|null
     * @throws NonUniqueResultException
     * @throws NegocioException
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder("documentoComissaoMembro");
            $query->leftJoin("documentoComissaoMembro.publicacoesDocumento", "publicacoesDocumento")
                ->addSelect('publicacoesDocumento');
            $query->innerJoin("documentoComissaoMembro.informacaoComissaoMembro", "informacaoComissaoMembro")
                ->addSelect('informacaoComissaoMembro');
            $query->innerJoin("informacaoComissaoMembro.atividadeSecundaria", "atividadeSecundaria")
                ->addSelect('atividadeSecundaria');
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario")
                ->addSelect('atividadePrincipalCalendario');
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario")
                ->addSelect('calendario');
            $query->innerJoin("calendario.eleicao", "eleicao")->addSelect('eleicao');
            $query->orderBy('publicacoesDocumento.id');

            $query->where("documentoComissaoMembro.id = :idDocumentoComissao");
            $query->setParameter("idDocumentoComissao", $id);

            $documentoComissaoMembro = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return DocumentoComissaoMembroTO::newInstance($documentoComissaoMembro);
        } catch (NoResultException $e) {
            return DocumentoComissaoMembroTO::newInstance();
        }
    }

}
