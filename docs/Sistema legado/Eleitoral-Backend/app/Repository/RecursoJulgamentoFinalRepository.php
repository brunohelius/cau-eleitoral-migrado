<?php
/*
 * UfRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\RecursoJulgamentoFinal;
use App\To\JulgamentoFinalTO;
use App\To\RecursoJulgamentoFinalTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'RecursoJulgamentoFinal'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class RecursoJulgamentoFinalRepository extends AbstractRepository
{
    /**
     * Retorna a quantidade de Julgamento Impugnação
     *
     * @param int $idChapaEleicao
     * @return RecursoJulgamentoFinalTO
     * @throws \Exception
     */
    public function getPorChapaEleicao(int $idChapaEleicao)
    {
        try {
            $query = $this->getQueryRecursoJulgamentoFinal();

            $query->andWhere('chapaEleicao.id = :id');
            $query->setParameter('id', $idChapaEleicao);

            $query->orderBy('indicacoes.id', 'ASC');

            $test = $query->getQuery()->getSQL();
            $julgamento = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return RecursoJulgamentoFinalTO::newInstance($julgamento);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Julgamento Impugnação
     *
     * @param int $idChapaEleicao
     * @return RecursoJulgamentoFinal
     * @throws \Exception
     */
    public function findPorChapaEleicao(int $idChapaEleicao)
    {
        try {
            $query = $this->createQueryBuilder('recursoJulgamentoFinal')
                ->innerJoin('recursoJulgamentoFinal.julgamentoFinal', 'julgamentoFinal');

            $query->leftJoin('julgamentoFinal.chapaEleicao', 'chapaEleicao');

            $query->andWhere('chapaEleicao.id = :id');
            $query->setParameter('id', $idChapaEleicao);

            return $query->getQuery()->getSingleResult();
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Julgamento Impugnação
     *
     * @param int $idRecursoJulgamentoFinal
     * @return RecursoJulgamentoFinalTO
     * @throws \Exception
     */
    public function getRecursoJulgamentoFinalPorId(int $idRecursoJulgamentoFinal)
    {
        try {
            $query = $this->getQueryRecursoJulgamentoFinal();

            $query->andWhere('recursoJulgamentoFinal.id = :id');
            $query->setParameter('id', $idRecursoJulgamentoFinal);

            $query->orderBy('indicacoes.id', 'ASC');

            $test = $query->getQuery()->getSQL();
            $julgamento = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return RecursoJulgamentoFinalTO::newInstance($julgamento);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a query com os joins dos relacionamentos
     *
     * @return QueryBuilder
     */
    private function getQueryRecursoJulgamentoFinal(): QueryBuilder
    {
        $query = $this->createQueryBuilder('recursoJulgamentoFinal')
            ->innerJoin('recursoJulgamentoFinal.julgamentoFinal', 'julgamentoFinal')
            ->innerJoin('recursoJulgamentoFinal.profissional', 'profissionalResponsavel')
            ->addSelect('profissionalResponsavel')
            ->addSelect('julgamentoFinal');

        $query
            ->leftJoin('julgamentoFinal.usuario', 'usuario')
            ->leftJoin('julgamentoFinal.chapaEleicao', 'chapaEleicao')
            ->leftJoin('julgamentoFinal.statusJulgamentoFinal', 'statusJulgamentoFinal')
            ->addSelect('statusJulgamentoFinal')
            ->addSelect('chapaEleicao');

        $query
            ->leftJoin('recursoJulgamentoFinal.recursosIndicacao', 'indicacoes')
            ->leftJoin('indicacoes.indicacaoJulgamentoFinal', 'indicacaoJulgamentoFinal')
            ->leftJoin('indicacaoJulgamentoFinal.tipoParticipacaoChapa', 'tipoParticipacaoChapa')
            ->leftJoin('indicacaoJulgamentoFinal.membroChapa', 'membroChapa')
            ->leftJoin('membroChapa.profissional', 'profissional')
            ->addSelect('indicacaoJulgamentoFinal')
            ->addSelect('membroChapa')
            ->addSelect('profissional')
            ->addSelect('tipoParticipacaoChapa')
            ->addSelect('indicacoes');

        $query->where('chapaEleicao.excluido = false');
        return $query;
    }
}
