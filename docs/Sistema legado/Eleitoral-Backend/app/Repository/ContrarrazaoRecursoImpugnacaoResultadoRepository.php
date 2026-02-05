<?php

namespace App\Repository;

use App\Config\Constants;
use App\Entities\MembroChapa;
use App\To\ContrarrazaoRecursoImpugnacaoResultadoTO;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'ContrarrazaoRecursoImpugnacaoResultado'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ContrarrazaoRecursoImpugnacaoResultadoRepository extends AbstractRepository
{

    /**
     * Retorna o total de contrarrazões cadastradas por recurso
     *
     * @param int $idRecurso
     * @param int|null $idProfissional
     * @return int|mixed
     */
    public function getTotalPorRecursoAndProfissional($idRecurso, $idProfissional = null)
    {
        try {

            $query = $this->createQueryBuilder('contrarrazaoRecursoImpugnacaoResultado')
                ->select('COUNT(contrarrazaoRecursoImpugnacaoResultado.id)')
                ->innerJoin('contrarrazaoRecursoImpugnacaoResultado.recursoImpugnacaoResultado', 'recursoImpugnacaoResultado');

            $query->where('recursoImpugnacaoResultado.id = :idRecurso')
                ->setParameter('idRecurso', $idRecurso);

            if (!empty($idProfissional)) {
                $query->andWhere('contrarrazaoRecursoImpugnacaoResultado.profissional = :idProfissional');
                $query->setParameter('idProfissional', $idProfissional);
            }

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e){
            return null;
        }
    }

    /**
     * @param $idImpugnacao
     * @param $idChapa
     * @return ContrarrazaoRecursoImpugnacaoResultadoTO[]|array|null
     */
    public function getContrarrazoesPorImpugnacaoEChapa($idImpugnacao, $idChapa, $idTipoRecurso)
    {
        try {
            $query = $this->createQueryBuilder('contrarrazaoRecursoImpugnacaoResultado')
                ->innerJoin('contrarrazaoRecursoImpugnacaoResultado.profissional', 'profissional')
                ->innerJoin('contrarrazaoRecursoImpugnacaoResultado.recursoImpugnacaoResultado', 'recursoImpugnacaoResultado')
                ->innerJoin('recursoImpugnacaoResultado.julgamentoAlegacaoImpugResultado', 'julgamentoAlegacaoImpugResultado')
                ->innerJoin('julgamentoAlegacaoImpugResultado.impugnacaoResultado', 'impugnacaoResultado');

            $subquery = $this->getSubQueryMembroResponsavelChapa();

            $query->where('impugnacaoResultado.id = :id')->setParameter('id', $idImpugnacao);
            $query->andWhere("profissional.id in ({$subquery})");

            $query->andWhere('recursoImpugnacaoResultado.tipoRecursoImpugnacaoResultado = :idTipoRecurso');
            $query->setParameter('idTipoRecurso', $idTipoRecurso);

            $query->setParameter('idChapa', $idChapa);
            $query->setParameter('situacoesMembroChapa', Constants::$situacaoMembroAtual);
            $query->setParameter('idStatusParticipacao', Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO);

            $contrarrazoes = $query->getQuery()->getArrayResult();
            return array_map(static function ($contrarrazao) {
                return ContrarrazaoRecursoImpugnacaoResultadoTO::newInstance($contrarrazao);
            }, $contrarrazoes);
        } catch (NoResultException $e){
            return null;
        }
    }

    /**
     * Método auxiliar para pegar s ids dos profissionais responsáveis chapa
     * @return QueryBuilder
     */
    private function getSubQueryMembroResponsavelChapa()
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
