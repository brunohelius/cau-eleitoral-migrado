<?php
/*
 * ParecerFinalRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use App\Config\Constants;
use App\Entities\ParecerFinal;
use Doctrine\ORM\AbstractQuery;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'ParecerFinal'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ParecerFinalRepository extends AbstractRepository
{
    /**
     * Retorna o parecer final de uma denúncia.
     *
     * @param integer $idDenuncia
     * @return mixed|Statement|array|NULL|array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorIdDenuncia($idDenuncia)
    {
        try{
            $query = $this->createQueryBuilder('parecerFinal');
            $query->innerJoin('parecerFinal.encaminhamentoDenuncia', 'encaminhamentoDenuncia');
            $query->innerJoin('encaminhamentoDenuncia.denuncia', 'denuncia');
            $query->where("denuncia.id = :idDenuncia");
            $query->setParameter(':idDenuncia', $idDenuncia);

            return $query->getQuery()->getSingleResult();
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Busca Parecer Final utilizando id de Encaminhamento denúncia.
     *
     * @param $idEncaminhamento
     * @return ParecerFinal
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorEncaminhamento($idEncaminhamento)
    {
        try{
            $query = $this->createQueryBuilder("parecerFinal");
            $query->join("parecerFinal.encaminhamentoDenuncia", "encaminhamentoDenuncia")->addSelect("encaminhamentoDenuncia");
            $query->leftJoin("encaminhamentoDenuncia.arquivoEncaminhamento", "arquivoEncaminhamento")->addSelect("arquivoEncaminhamento");
            $query->where("encaminhamentoDenuncia.id = :idEncaminhamento");
            $query->setParameters(["idEncaminhamento" => $idEncaminhamento]);

            return $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_OBJECT);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }
}
