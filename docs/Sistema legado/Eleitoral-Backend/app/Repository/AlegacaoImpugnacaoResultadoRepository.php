<?php

/*
 * ImpugnacaoResultadoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Config\Constants;
use App\To\AlegacaoImpugnacaoFiltroTO;
use Doctrine\ORM\AbstractQuery;
use App\To\AlegacaoImpugnacaoResultadoTO;
use Doctrine\ORM\NonUniqueResultException;
use App\Entities\AlegacaoImpugnacaoResultado;
use Doctrine\ORM\NoResultException;


/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'AlegacaoImpugnacaoResultadoRepository'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class AlegacaoImpugnacaoResultadoRepository extends AbstractRepository
{

    /**
     * Retorna a impugnação de resultado a partir do id.
     */
    public function getPorIdImpugnacao($idImpugnacao) {
        $query = $this->createQueryBuilder('alegacaoImpugnacaoResultado')
            ->addSelect('alegacaoImpugnacaoResultado')
            ->leftJoin('alegacaoImpugnacaoResultado.impugnacaoResultado', 'impugnacaoResultado')
            ->addSelect('impugnacaoResultado')
            ->innerJoin('alegacaoImpugnacaoResultado.profissional', 'profissional')
            ->addSelect('profissional')
            ->innerJoin('profissional.pessoa', 'pessoa')
            ->addSelect('pessoa')
            ->where('impugnacaoResultado.id = :id')
            ->setParameter('id', $idImpugnacao);

        $alegacao = $query->getQuery()->getArrayResult();
        return array_map(static function($alegacao) {
            return AlegacaoImpugnacaoResultado::newInstance($alegacao);
        }, $alegacao);
    }


    /**
     * Retorna a quantidade de Pedidos de Impugnacao de resultado agrupados para cada UF
     *
     * @param int $idCalendario
     * @return int|mixed
     */
    public function getTotalAlegacaoImpugnacaoPorImpugnacao(int $idImpugnacao = null)
    {
        try {

            $query = $this->createQueryBuilder('alegacao')
                ->select('COUNT(alegacao.id)')
                ->innerJoin('alegacao.impugnacaoResultado', 'impugnacaoResultado');

            $query->where('impugnacaoResultado.id = :idImpugnacao')
                ->setParameter('idImpugnacao', $idImpugnacao);

            $consulta = $query->getQuery();

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e){
            return null;
        }
    }

    /**
     * Buscar Alegações de acordo com os dados do filtro informado.
     *
     * @param AlegacaoImpugnacaoFiltroTO $filtroTO
     * @return array
     */
    public function getPorFiltro(AlegacaoImpugnacaoFiltroTO $filtroTO)
    {
        try {
            $query = $this->createQueryBuilder('alegacaoImpugnacaoResultado')
                ->addSelect('alegacaoImpugnacaoResultado')
                ->leftJoin('alegacaoImpugnacaoResultado.impugnacaoResultado', 'impugnacaoResultado')
                ->addSelect('impugnacaoResultado')
                ->innerJoin('alegacaoImpugnacaoResultado.profissional', 'profissionalAlegacao')
                ->addSelect('profissionalAlegacao')
                ->innerJoin('profissionalAlegacao.pessoa', 'pessoaAlegacao')
                ->addSelect('pessoaAlegacao');

            if(!empty($filtroTO->getIdCalendarios())) {
                $query->andWhere('impugnacaoResultado.calendario in (:idCalendarios)')
                    ->setParameter('idCalendarios', $filtroTO->getIdCalendarios());
            }

            if(!empty($filtroTO->getIdProfissionais())) {
                $query->andWhere('profissionalAlegacao.id in (:idProfissionais)')
                    ->setParameter('idProfissionais', $filtroTO->getIdProfissionais());
            }

            if(!empty($filtroTO->getIdImpugnacoes())) {
                $query->andWhere('impugnacaoResultado.id in (:impugnacaoResultado)')
                 ->setParameter('impugnacaoResultado', $filtroTO->getIdImpugnacoes());
            }

            $alegacao = $query->getQuery()->getArrayResult();
            return array_map(static function($alegacao) {
                return AlegacaoImpugnacaoResultado::newInstance($alegacao);
            }, $alegacao);

        } catch(NoResultException $e) {
            return [];
        }
    }
}