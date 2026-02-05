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
use App\Entities\ImpugnacaoResultado;
use App\Entities\JulgamentoRecursoImpugResultado;
use App\To\ImpugnacaoResultadoTO;
use App\To\QuantidadePedidoImpugnacaoResultadoPorUfTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'impugnacaoResultado'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ImpugnacaoResultadoRepository extends AbstractRepository
{
    /**
     * Retorna a quantidade de Pedidos de Impugnacao de resultado agrupados para cada UF
     *
     * @param int $idCalendario
     * @return QuantidadePedidoImpugnacaoResultadoPorUfTO[]
     */
    public function getQuantidadeImpugnacaoResultadoParaCadaUf(int $idCalendario = null, $idsCauUfs = null, $idProfissionalImpugnante = null )
    {
        $query = $this->createQueryBuilder('impugnacaoResultado')
            ->select('COUNT(impugnacaoResultado.id) as quantidadePedidos')
            ->addSelect('filial.id as idCauUf')
            ->addSelect('filial.prefixo as siglaUf')
            ->addSelect('calendario.id as idCalendario')
            ->innerJoin('impugnacaoResultado.calendario', 'calendario')
            ->leftJoin('impugnacaoResultado.cauBR', 'filial');

        if(!empty($idCalendario)) {
            $query->where('impugnacaoResultado.calendario = :idCalendario')
                ->setParameter('idCalendario', $idCalendario);
        }
        else {
            $query->where('calendario.ativo = true');
            $query->andWhere('calendario.excluido = false');
        }

        if (!empty($idsCauUfs)) {
            $query->andWhere('filial.id in (:idsCauUfs)');
            $query->setParameter('idsCauUfs', $idsCauUfs);
        }

        if(!empty($idProfissionalImpugnante)){
            $query->andWhere('impugnacaoResultado.profissional = :idprofissional');
            $query->setParameter('idprofissional', $idProfissionalImpugnante);
        }

        $query->groupBy('filial.id')
        ->addGroupBy('calendario.id')
        ->addOrderBy('filial.prefixo', "ASC");

        $dados = $query->getQuery()->getResult();

        return array_map(function ($item)  {
            return QuantidadePedidoImpugnacaoResultadoPorUfTO::newInstance($item);
        }, $dados);
    }

    /**
     * Retorna a quantidade de Pedidos de Impugnacao de resultado agrupados para cada UF
     *
     * @param int $idCalendario
     * @param int|null $idCauBr
     * @param array|null $idsProfissional
     * @return int|mixed
     */
    public function getTotalImpugnacaoResultadoPorCalendario(
        int $idCalendario = null,
        int $idCauBr = null,
        array $idsProfissional = null
    ) {
        try {
            $query = $this->createQueryBuilder('impugnacaoResultado')
                ->select('COUNT(impugnacaoResultado.id)')
                ->innerJoin('impugnacaoResultado.calendario', 'calendario');

            $query->where('impugnacaoResultado.calendario = :idCalendario')
                ->setParameter('idCalendario', $idCalendario);

            if (!empty($idCauBr)) {
                $query->andWhere('impugnacaoResultado.cauBR = :idCauBr');
                $query->setParameter('idCauBr', $idCauBr);
            } else if($idCauBr == Constants::ID_IES_ELEITORAL){
                $query->andWhere('impugnacaoResultado.cauBR IS NULL');
            }

            if (!empty($idsProfissional)) {
                $query->andWhere('impugnacaoResultado.profissional in (:profissionais)');
                $query->setParameter('profissionais', $idsProfissional);
            }

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e){
            return null;
        }
    }

    /**
     * Retorna o resultado da impugnacao de acordo com a uf e profissional
     */
    public function getPorUfeProfissional($uf = null, $profissonais = false){
        $query = $this->createQueryBuilder('impugnacaoResultado')
            ->addSelect('impugnacaoResultado')
            ->innerJoin('impugnacaoResultado.calendario', 'calendario')
            ->addSelect('calendario')
            ->innerJoin('impugnacaoResultado.status', 'status')
            ->addSelect('status')
            ->innerJoin('impugnacaoResultado.profissional', 'profissional')
            ->addSelect('profissional')
            ->leftJoin('impugnacaoResultado.cauBR', 'filial')
            ->addSelect('filial');

        if (!empty($uf)) {
            $query->where('impugnacaoResultado.cauBR = :uf')
                ->setParameter('uf', $uf);
        } else if($uf == Constants::ID_IES_ELEITORAL){
            $query->where('impugnacaoResultado.cauBR IS NULL');
        }

        $query->orderBy('impugnacaoResultado.numero' ,'ASC');

        if (!empty($profissonais)) {
            $query->andWhere('impugnacaoResultado.profissional in (:profissionais)')
            ->setParameter('profissionais', $profissonais);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Retorna o resultado da impugnacao de acordo com a uf e calendario.
     */
    public function getPorUfeCalendario($uf = null, $calendarios = null){
        $query = $this->createQueryBuilder('impugnacaoResultado')
            ->addSelect('impugnacaoResultado')
            ->innerJoin('impugnacaoResultado.calendario', 'calendario')
            ->addSelect('calendario')
            ->innerJoin('impugnacaoResultado.status', 'status')
            ->addSelect('status')
            ->innerJoin('impugnacaoResultado.profissional', 'profissional')
            ->addSelect('profissional')
            ->leftJoin('impugnacaoResultado.cauBR', 'filial')
            ->addSelect('filial');

        if($uf == 0) {
            $query->where('impugnacaoResultado.cauBR IS NULL');
        } else if(!empty($uf)){
            $query->where('impugnacaoResultado.cauBR = :uf')->setParameter('uf', $uf);
        }

        $query->orderBy('impugnacaoResultado.numero' ,'ASC');

        if (!empty($calendarios)) {
            $query->andWhere('impugnacaoResultado.calendario in (:calendarios)')
                ->setParameter('calendarios', $calendarios);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Retorna a impugnação de resultado a partir do id.
     *
     * @param $id
     * @return ImpugnacaoResultadoTO
     */
    public function getImpugnacaoPorId($id) {
        $query = $this->createQueryBuilder('impugnacaoResultado')
            ->addSelect('impugnacaoResultado')
            ->innerJoin('impugnacaoResultado.calendario', 'calendario')
            ->addSelect('calendario')
            ->innerJoin('impugnacaoResultado.profissional', 'profissional')
            ->addSelect('profissional')
            ->innerJoin('profissional.pessoa', 'pessoa')
            ->addSelect('pessoa')
            ->leftJoin('impugnacaoResultado.cauBR', 'filial')
            ->addSelect('filial')
            ->leftJoin('impugnacaoResultado.julgamentoAlegacao', 'julgamentoAlegacao')
            ->addSelect('julgamentoAlegacao')
            ->leftJoin('julgamentoAlegacao.statusJulgamentoAlegacaoResultado', 'statusJulgamentoAlegacaoResultado')
            ->addSelect('statusJulgamentoAlegacaoResultado')
            ->leftJoin('impugnacaoResultado.alegacoes', 'alegacoes')
            ->addSelect('alegacoes')
            ->leftJoin('impugnacaoResultado.julgamentoRecurso', 'julgamentoRecurso')
            ->addSelect('julgamentoRecurso')
            ->where('impugnacaoResultado.id = :id')
            ->setParameter('id', $id);

        $impugnacao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

        return ImpugnacaoResultadoTO::newInstance($impugnacao);
    }


    /**
     * Retorna a impugnação de resultado a partir do id.
     *
     * @param $idCalendario
     * @param bool $isRecursoCadastrado
     * @param null $idStatusJulgamentoAlegacaoResultado
     * @param bool $isApenasIES
     * @return ImpugnacaoResultadoTO
     */
    public function getImpugnacoesComJulgamentoAlegacaoPorCalendario($idCalendario, $isRecursoCadastrado = false,
                                                                     $idStatusJulgamentoAlegacaoResultado = null,
                                                                     $isApenasIES = null)
    {
        $query = $this->createQueryBuilder('impugnacaoResultado')
            ->innerJoin('impugnacaoResultado.calendario', 'calendario')
            ->innerJoin('impugnacaoResultado.julgamentoAlegacao', 'julgamentoAlegacao')
            ->leftJoin('julgamentoAlegacao.recursosJulgamentoAlegacaoImpugResultado', 'recursos')
            ->where('calendario.id = :id')
            ->setParameter('id', $idCalendario);

        if (!is_null($isRecursoCadastrado)) {
            if ($isRecursoCadastrado) {
                $query->andWhere('recursos IS NOT NULL');
            } else {
                $query->andWhere('recursos IS NULL');
            }
        }

        if (!empty($idStatusJulgamentoAlegacaoResultado)) {
            $query->innerJoin('julgamentoAlegacao.statusJulgamentoAlegacaoResultado', 'statusJulgamentoAlegacaoResultado');
            $query->andWhere('statusJulgamentoAlegacaoResultado.id = :statusJulgamentoAlegacaoResultado');
            $query->setParameter('statusJulgamentoAlegacaoResultado', $idStatusJulgamentoAlegacaoResultado);
        }

        if(!is_null($isApenasIES)) {
            if ($isApenasIES) {
                $query->andWhere('impugnacaoResultado.cauBR IS NULL');
            } else {
                $query->andWhere('impugnacaoResultado.cauBR IS NOT NULL');
            }
        }

        return $query->getQuery()->getResult();
    }
}
