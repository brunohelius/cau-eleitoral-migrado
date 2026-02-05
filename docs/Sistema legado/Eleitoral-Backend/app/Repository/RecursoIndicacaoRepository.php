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

use App\To\RecursoIndicacaoTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'RecursoIndicacao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class RecursoIndicacaoRepository extends AbstractRepository
{

    /**
     * Retorna a quantidade de Julgamento Impugnação
     *
     * @param int $idChapaEleicao
     * @return RecursoIndicacaoTO
     * @throws \Exception
     */
    public function getPorChapaEleicao(int $idChapaEleicao)
    {
        try {
            $query = $this->getQueryRecursoJulgamentoFinal();

            $query->andWhere('chapaEleicao.id = :id');
            $query->setParameter('id', $idChapaEleicao);

            $query->orderBy('indicacaoJulgamentoFinal.id', 'ASC');

            $test = $query->getQuery()->getSQL();
            $julgamento = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return RecursoIndicacaoTO::newInstance($julgamento);
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
        $query = $this->createQueryBuilder('recursoIndicacao')
            ->innerJoin('recursoIndicacao.indicacaoJulgamentoFinal', 'indicacaoJulgamentoFinal')
            ->innerJoin('recursoIndicacao.recursoJulgamentoFinal', 'recursoJulgamentoFinal')
            ->innerJoin('recursoJulgamentoFinal.julgamentoFinal', 'julgamentoFinal')
            ->innerJoin('recursoJulgamentoFinal.profissional', 'profissionalResponsavel')
            ->addSelect('profissionalResponsavel')
            ->addSelect('recursoJulgamentoFinal')
            ->addSelect('indicacaoJulgamentoFinal')
            ->addSelect('julgamentoFinal');

        $query
            ->leftJoin('julgamentoFinal.statusJulgamentoFinal', 'statusJulgamento')
            ->leftJoin('julgamentoFinal.usuario', 'usuario')
            ->leftJoin('julgamentoFinal.chapaEleicao', 'chapaEleicao')
            ->leftJoin('indicacaoJulgamentoFinal.tipoParticipacaoChapa', 'tipoParticipacaoIndicacao')
            ->leftJoin('indicacaoJulgamentoFinal.membroChapa', 'membroChapa')
            ->leftJoin('membroChapa.profissional', 'profissional')
            ->addSelect('membroChapa')
            ->addSelect('profissional')
            ->addSelect('tipoParticipacaoIndicacao')
            ->addSelect('chapaEleicao');

        $query->where('chapaEleicao.excluido = false');
        return $query;
    }
}
