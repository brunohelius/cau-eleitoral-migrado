<?php

namespace App\Repository;

use App\Config\Constants;
use App\Entities\MembroChapa;
use App\To\RecursoImpugnacaoResultadoTO;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'RecursoImpugnacaoResultado'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class RecursoImpugnacaoResultadoRepository extends AbstractRepository
{
    /**
     * @param $idImpugnacao
     * @param $idTipoRecurso
     * @return int|mixed|string|null
     * Retorna o total de Recurso do Julgamento da Alegação por impugnação e tipo de recurso.
     */
    public function getTotalPorPedidoImpugnacaoAndTipoRecurso($idImpugnacao, $idTipoRecurso)
    {
        try {

            $query = $this->createQueryBuilder('recursoImpugnacaoResultado')
                ->select('COUNT(recursoImpugnacaoResultado.id)')
                ->innerJoin('recursoImpugnacaoResultado.julgamentoAlegacaoImpugResultado', 'julgamentoAlegacaoImpugResultado')
                ->innerJoin('julgamentoAlegacaoImpugResultado.impugnacaoResultado', 'impugnacaoResultado');

            $query->where('impugnacaoResultado.id = :idImpugnacao')
                ->setParameter('idImpugnacao', $idImpugnacao);

            $query->andWhere('recursoImpugnacaoResultado.tipoRecursoImpugnacaoResultado = :idTipoRecurso');
            $query->setParameter('idTipoRecurso', $idTipoRecurso);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e){
            return null;
        }
    }

    public function getPorPedidoImpugnacao($idImpugnacao, $idTipoRecurso)
    {
        try {

            $query = $this->createQueryBuilder('recursoImpugnacaoResultado')
                ->innerJoin('recursoImpugnacaoResultado.julgamentoAlegacaoImpugResultado', 'julgamentoAlegacaoImpugResultado')
                ->innerJoin('julgamentoAlegacaoImpugResultado.impugnacaoResultado', 'impugnacaoResultado')
                ->innerJoin('recursoImpugnacaoResultado.profissional', 'profissional')
                ->leftJoin('recursoImpugnacaoResultado.contrarrazoesRecursoImpugnacaoResultado', 'contrarrazoesRecursoImpugnacaoResultado')
                ->leftJoin('contrarrazoesRecursoImpugnacaoResultado.profissional', 'profissionalContrarrazao')
                ->addSelect('contrarrazoesRecursoImpugnacaoResultado')
                ->addSelect('profissional')
                ->addSelect('profissionalContrarrazao');

            $query->where('impugnacaoResultado.id = :idImpugnacao')
                ->setParameter('idImpugnacao', $idImpugnacao);

            $query->andWhere('recursoImpugnacaoResultado.tipoRecursoImpugnacaoResultado = :idTipoRecurso');
            $query->setParameter('idTipoRecurso', $idTipoRecurso);

            $query->orderBy('recursoImpugnacaoResultado.numero', 'ASC');
            $recursos = $query->getQuery()->getArrayResult();
            return array_map(static function ($recurso) {
                return RecursoImpugnacaoResultadoTO::newInstance($recurso);
            }, $recursos);
        } catch (NoResultException $e){
            return null;
        }
    }

    /**
     * @param $idImpugnacao
     * @param $idChapa
     * @param null $idTipoRecuro
     * @return RecursoImpugnacaoResultadoTO[]|array|null
     */
    public function getRecursoPorImpugnacaoEChapa($idImpugnacao, $idChapa, $idTipoRecuro = null)
    {
        try {
            $query = $this->createQueryBuilder('recursoImpugnacaoResultado')
                ->innerJoin('recursoImpugnacaoResultado.julgamentoAlegacaoImpugResultado', 'julgamentoAlegacaoImpugResultado')
                ->innerJoin('julgamentoAlegacaoImpugResultado.impugnacaoResultado', 'impugnacaoResultado')
                ->innerJoin('recursoImpugnacaoResultado.profissional', 'profissional');

            $subquery = $this->getSubQueryMembroResponsavelChapa();

            $query->where('impugnacaoResultado.id = :id')->setParameter('id', $idImpugnacao);
            $query->andWhere("profissional.id in ({$subquery})");

            if (!empty($idTipoRecuro)) {
                $query->andWhere('recursoImpugnacaoResultado.tipoRecursoImpugnacaoResultado = :idTipoRecurso');
                $query->setParameter('idTipoRecurso', $idTipoRecuro);
            }

            $query->setParameter('idChapa', $idChapa);
            $query->setParameter('situacoesMembroChapa', Constants::$situacaoMembroAtual);
            $query->setParameter('idStatusParticipacao', Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO);

            $recursos = $query->getQuery()->getArrayResult();
            return array_map(static function ($recurso) {
                return RecursoImpugnacaoResultadoTO::newInstance($recurso);
            }, $recursos);
        } catch (NoResultException $e){
            return null;
        }
    }

    /**
     * Retorna o ultimo numero inserido para o recurso de um julgamento
     *
     * @param int $idjulgamentoAlegacaoImpugResultado
     * @return int|mixed
     */
    public function getUltimoNumeroPorJulgamentoAlegacaoImpugnacaoResultadoAndTipoRecurso(int $idjulgamentoAlegacaoImpugResultado,
                                                                                          int $idTipoRecursoImpugnacaoResultado)
    {
        try {
            $query = $this->createQueryBuilder('recursoImpugnacaoResultado')
                ->select('MAX(recursoImpugnacaoResultado.numero)')
                ->innerJoin('recursoImpugnacaoResultado.julgamentoAlegacaoImpugResultado', 'julgamentoAlegacaoImpugResultado')
                ->innerJoin('recursoImpugnacaoResultado.tipoRecursoImpugnacaoResultado', 'tipoRecursoImpugnacaoResultado')
                ->where('julgamentoAlegacaoImpugResultado.id = :idjulgamentoAlegacaoImpugResultado')
                ->setParameter('idjulgamentoAlegacaoImpugResultado', $idjulgamentoAlegacaoImpugResultado)
                ->andWhere('tipoRecursoImpugnacaoResultado.id = :idTipoRecursoImpugnacaoResultado')
                ->setParameter('idTipoRecursoImpugnacaoResultado', $idTipoRecursoImpugnacaoResultado);


            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e){
            return null;
        }
    }

    /**
     * Método auxiliar para pegar s ids dos profissionais responsáveis chapa
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getSubQueryMembroResponsavelChapa(): \Doctrine\ORM\QueryBuilder
    {
        $subquery = $this->getEntityManager()->createQueryBuilder();
        $subquery->select('profissionalMembroChapa.id');
        $subquery->from(MembroChapa::class, 'membroChapa');
        $subquery->innerJoin('membroChapa.chapaEleicao', 'chapaEleicao');
        $subquery->innerJoin('membroChapa.profissional', 'profissionalMembroChapa');
        $subquery->where('chapaEleicao.id = :idChapa');
        $subquery->andWhere('profissionalMembroChapa.id = profissional.id');
        $subquery->andWhere('membroChapa.situacaoResponsavel = true');
        $subquery->andWhere('membroChapa.statusParticipacaoChapa = :idStatusParticipacao');
        $subquery->andWhere('membroChapa.situacaoMembroChapa in (:situacoesMembroChapa)');
        return $subquery;
    }
}
