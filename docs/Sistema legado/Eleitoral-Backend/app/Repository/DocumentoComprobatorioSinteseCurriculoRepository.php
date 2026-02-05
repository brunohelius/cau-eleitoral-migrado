<?php

namespace App\Repository;

use App\Entities\DocumentoComprobatorioSinteseCurriculo;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade
 * 'DocumentoComprobatorioSinteseCurriculo'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class DocumentoComprobatorioSinteseCurriculoRepository extends AbstractRepository
{
    /**
     * Retorna um documento comprobatório do membro chapa de acrodo com o id informada.
     *
     * @param int $idDocumento
     *
     * @return DocumentoComprobatorioSinteseCurriculo|null
     */
    public function getPorId(int $idDocumento)
    {
        try {
            $query = $this->createQueryBuilder("documentoComprobatorio");
            $query->innerJoin("documentoComprobatorio.membroChapa", "membroChapa")
                ->addSelect("membroChapa");
            $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao")
                ->addSelect("chapaEleicao");

            $query->where("documentoComprobatorio.id = :idDocumentoComprobatorio");
            $query->setParameter("idDocumentoComprobatorio", $idDocumento);

            $dadosDcoumentoComprob = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return DocumentoComprobatorioSinteseCurriculo::newInstance($dadosDcoumentoComprob);
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            return null;
        }

    }

    /**
     * Retorna uma lista documento comprobatório do membro chapa de acrodo com o id do membro chapa informado.
     *
     * @param int $idDocumento
     *
     * @return array|null
     */
    public function getPorMembro(int $idMembroChapa)
    {
        $query = $this->createQueryBuilder("documentoComprobatorio");
        $query->innerJoin("documentoComprobatorio.membroChapa", "membroChapa")
            ->addSelect("membroChapa");
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao")
            ->addSelect("chapaEleicao");

        $query->where("membroChapa.id = :idMembroChapa");
        $query->setParameter("idMembroChapa", $idMembroChapa);

        $arrayDcoumentoComprob = $query->getQuery()->getArrayResult();

        return array_map(static function ($dadosDcoumentoComprob) {
            return DocumentoComprobatorioSinteseCurriculo::newInstance($dadosDcoumentoComprob);
        }, $arrayDcoumentoComprob);
    }
}
