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

use App\Entities\JulgamentoFinal;
use App\To\JulgamentoFinalTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'JulgamentoFinal'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoFinalRepository extends AbstractRepository
{

    /**
     * Retorna julgamento final por id.
     *
     * @param $id
     * @return JulgamentoFinal|null
     */
    public function getPorIdChapa($idChapa)
    {
        try{
            $query = $this->getQueryJulgamentoFinal(false);
            $query->leftJoin("julgamentoFinal.recursoJulgamentoFinal", "recursoJulgamentoFinal")->addSelect("recursoJulgamentoFinal");
            $query->join("julgamentoFinal.substituicoesJulgamentoFinal", "substituicoesJulgamentoFinal")->addSelect("substituicoesJulgamentoFinal");
            $query->andWhere('chapaEleicao.id = :id');
            $query->setParameter('id', $idChapa);
            $query->addOrderBy('julgamentoFinal.id', 'DESC');

            return $query->getQuery()->getResult();
        }
        catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Julgamento Impugnação
     *
     * @param int $idChapaEleicao
     * @return JulgamentoFinalTO
     */
    public function getPorChapaEleicao(int $idChapaEleicao)
    {
        try {
            $query = $this->getQueryJulgamentoFinal(true);

            $queryUltimoJulgamento = $this->getQueryUltimoJulgamentoFinal();
            $queryUltimoJulgamento->andWhere('chapaEleicaoQueryUltimoJulgamentoFinal.id = :id');

            $query->andWhere($query->expr()->in('julgamentoFinal.id', $queryUltimoJulgamento->getDQL()));

            $query->setParameter('id', $idChapaEleicao);

            $query->orderBy('julgamentoFinal.id','DESC');
            $query->addOrderBy('indicacoes.id', 'ASC');

            $julgamento = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return JulgamentoFinalTO::newInstance($julgamento);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Buscar histórico de retificações do julgamento.
     *
     * @param int $idChapaEleicao
     */
    public function getRetificacoesPorChapaEleicao(int $idChapaEleicao)
    {
        try {
            $query = $this->getQueryJulgamentoFinal(true);

            $query->andWhere('chapaEleicao.id = :id');
            $query->setParameter('id', $idChapaEleicao);

            $query->orderBy('julgamentoFinal.id','ASC');
            $query->addOrderBy('indicacoes.id', 'ASC');

            $retificacoes = $query->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

            return array_map( function ($retificacao) {
                return JulgamentoFinalTO::newInstance($retificacao);
            }, $retificacoes );
        } catch (NoResultException $e) {
            return null;
        }

    }

    /**
     * Retorna a quantidade de Julgamento Impugnação
     *
     * @param $idCalendario
     * @return JulgamentoFinal[]|null
     */
    public function getPorCalendario($idCalendario)
    {
        try {
            $query = $this->getQueryJulgamentoFinal(false);

            $query->innerJoin('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundaria');
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");

            $query->andWhere('calendario.id = :id');
            $query->setParameter('id', $idCalendario);

            return $query->getQuery()->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Julgamento Impugnação
     *
     * @param int $idChapaEleicao
     * @return JulgamentoFinalTO
     */
    public function getidJulgamentoPorChapaEleicao(int $idChapaEleicao)
    {
        try {
            $query = $this->createQueryBuilder('julgamentoFinal');
            $query->select('julgamentoFinal.id');
            $query->where('julgamentoFinal.chapaEleicao = :idChapa');
            $query->setParameter('idChapa', $idChapaEleicao);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException| NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a query com os joins dos relacionamentos
     *
     * @param bool $addIndicacoes
     * @return QueryBuilder
     */
    private function getQueryJulgamentoFinal($addIndicacoes = false): QueryBuilder
    {
        $query = $this->createQueryBuilder('julgamentoFinal')
            ->innerJoin('julgamentoFinal.statusJulgamentoFinal', 'statusJulgamento')
            ->innerJoin('julgamentoFinal.usuario', 'usuario')
            ->innerJoin('julgamentoFinal.chapaEleicao', 'chapaEleicao')
            ->addSelect('statusJulgamento')
            ->addSelect('usuario');

        if ($addIndicacoes) {
            $query->leftJoin('julgamentoFinal.indicacoes', 'indicacoes')
                ->leftJoin('indicacoes.tipoParticipacaoChapa', 'tipoParticipacaoIndicacao')
                ->leftJoin('indicacoes.membroChapa', 'membroChapa')
                ->leftJoin('membroChapa.profissional', 'profissional')
                ->leftJoin('profissional.pessoa', 'pessoa')
                ->leftJoin('membroChapa.tipoParticipacaoChapa', 'tipoParticipacaoChapa')
                ->leftJoin('membroChapa.statusParticipacaoChapa', 'statusParticipacaoChapa')
                ->leftJoin("membroChapa.statusValidacaoMembroChapa", "statusValidacaoMembroChapa")
                ->leftJoin("membroChapa.pendencias", "pendencias")
                ->leftJoin("pendencias.tipoPendencia", "tipoPendencia")
                ->addSelect('indicacoes')
                ->addSelect('tipoParticipacaoIndicacao')
                ->addSelect('membroChapa')
                ->addSelect('profissional')
                ->addSelect('pessoa')
                ->addSelect('tipoParticipacaoChapa')
                ->addSelect('statusParticipacaoChapa')
                ->addSelect("statusValidacaoMembroChapa")
                ->addSelect("pendencias")
                ->addSelect("tipoPendencia");
        }


        $query->where('chapaEleicao.excluido = false');

        return $query;
    }

    /**
     * Retorna query para seleção do ultimo julgamento.
     *
     * @return QueryBuilder
     */
    private function getQueryUltimoJulgamentoFinal() {
        $query = $this->createQueryBuilder('julgamentoFinalQueryUltimoJulgamentoFinal')
            ->innerJoin('julgamentoFinalQueryUltimoJulgamentoFinal.chapaEleicao', 'chapaEleicaoQueryUltimoJulgamentoFinal');
        $query->select('MAX(julgamentoFinalQueryUltimoJulgamentoFinal.id)');

        $query->where('chapaEleicaoQueryUltimoJulgamentoFinal.excluido = false');
        return $query;
    }


}
