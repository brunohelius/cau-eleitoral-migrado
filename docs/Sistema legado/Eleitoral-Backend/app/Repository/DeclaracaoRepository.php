<?php
/*
 * DeclaracaoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\Declaracao;
use App\To\DeclaracaoFiltroTO;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'Declaracao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class DeclaracaoRepository extends AbstractRepository
{
    /**
     * Retorna a instância do 'Declaracao' conforme o 'id' informado.
     *
     * @param integer $id
     *
     * @return Declaracao|null
     * @throws NonUniqueResultException
     */
    public function getDeclaracao($id)
    {
        try {
            $query = $this->createQueryBuilder("declaracao");
            $query->innerJoin("declaracao.modulo", "modulo")->addSelect("modulo");
            $query->innerJoin("declaracao.itensDeclaracao", "itensDeclaracao")->addSelect("itensDeclaracao");
            $query->where("declaracao.id = :id");
            $query->setParameter("id", $id);

            $query->orderBy("itensDeclaracao.sequencial", 'ASC');

            return $query->getQuery()->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna um lista de nomes de declarações conforme id do módulo informado.
     *
     * @param integer $idModulo
     *
     * @return array|null
     */
    public function getDeclaracoesPorModulo($idModulo)
    {
        $dql =  "SELECT DISTINCT(declaracao.nome) ";
        $dql .= "FROM App\Entities\Declaracao declaracao ";
        $dql .= "INNER JOIN declaracao.modulo modulo ";
        $dql .= "WHERE modulo.id = :idModulo ";
        $dql .= "ORDER BY declaracao.nome ";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("idModulo", $idModulo);
        return $query->getArrayResult();
    }

    /**
     * Retorna uma lista de instâncias de 'Declaracao' conforme o id do módulo informado ou nome da declaração
     * informada, trazendo somente as declarações ativas.
     *
     * @param DeclaracaoFiltroTO $filtroTO
     * @param string $nome
     *
     * @return array|null
     */
    public function getDeclaracoesPorFiltro(DeclaracaoFiltroTO $filtroTO)
    {
        $query = $this->createQueryBuilder("declaracao");
        $query->innerJoin("declaracao.modulo", "modulo")->addSelect("modulo");
        $query->innerJoin("declaracao.itensDeclaracao", "itensDeclaracao")->addSelect("itensDeclaracao");
        $query->where("1 = 1");

        if ($filtroTO->getIdModulo() != null)
        {
            $query->andWhere("modulo.id = :idModulo");
            $query->setParameter("idModulo", $filtroTO->getIdModulo());
        }

        if (!empty($filtroTO->getNome()))
        {
            $query->andWhere("declaracao.nome = :nome");
            $query->setParameter("nome", $filtroTO->getNome());
        }

        if (!empty($filtroTO->getIds()))
        {
            $query->andWhere("declaracao.id IN (:ids)");
            $query->setParameter("ids", $filtroTO->getIds());
        }

        $query->orderBy("declaracao.sequencial");
        $query->addOrderBy("itensDeclaracao.sequencial", 'ASC');

        return $query->getQuery()->getResult();
    }
}
