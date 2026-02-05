<?php


namespace App\Repository;

use App\Entities\DeclaracaoAtividade;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'DeclaracaoAtividade'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class DeclaracaoAtividadeRepository extends AbstractRepository
{

    /**
     * Retorna declaracao por atividade secundária e tipo de declaração
     *
     * @param $idTipoDeclaracaoAtividade
     * @param $idAtividadeSecundaria
     *
     * @return DeclaracaoAtividade|NULL
     * @throws Exception
     */
    public function getDeclaracaoAtividadePorTipoDeclaracao($idTipoDeclaracaoAtividade, $idAtividadeSecundaria)
    {
        try {
            $query = $this->createQueryBuilder('declaracaoAtividade');

            $query->innerJoin('declaracaoAtividade.atividadeSecundaria',
                'atividadeSecundaria')->addSelect("atividadeSecundaria");
            $query->innerJoin("declaracaoAtividade.tipoDeclaracaoAtividade",
                "tipoDeclaracaoAtividade")->addSelect("tipoDeclaracaoAtividade");

            $query->where("atividadeSecundaria.id = :idAtividadeSecundaria");
            $query->setParameter("idAtividadeSecundaria", $idAtividadeSecundaria);

            $query->andWhere("tipoDeclaracaoAtividade.id = :idTipoDeclaracaoAtividade");
            $query->setParameter("idTipoDeclaracaoAtividade", $idTipoDeclaracaoAtividade);

            $dadosDeclaracaoAtividade = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return DeclaracaoAtividade::newInstance($dadosDeclaracaoAtividade);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna declaracao por atividade secundária e tipo de declaração
     *
     * @param $idAtividadeSecundaria
     * @return array|NULL
     */
    public function getDeclaracoesPorAtividadeSecundaria($idAtividadeSecundaria)
    {
        try {
            $query = $this->createQueryBuilder('declaracaoAtividade');

            $query->innerJoin('declaracaoAtividade.atividadeSecundaria', 'atividadeSecundaria')
                ->addSelect("atividadeSecundaria");
            $query->innerJoin("declaracaoAtividade.tipoDeclaracaoAtividade", "tipoDeclaracaoAtividade")
                ->addSelect("tipoDeclaracaoAtividade");

            $query->where("atividadeSecundaria.id = :idAtividadeSecundaria");
            $query->setParameter("idAtividadeSecundaria", $idAtividadeSecundaria);
            $query->orderBy("tipoDeclaracaoAtividade.id", "ASC");

            return array_map(function ($declaracaoAtividade) {
                return DeclaracaoAtividade::newInstance($declaracaoAtividade);
            }, $query->getQuery()->getArrayResult());
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna declaracao por atividade secundária e tipo de declaração
     *
     * @param $idDeclaracao
     * @return integer
     */
    public function getQtdDeclaracoesPorDeclaracao($idDeclaracao)
    {
        try {
            $query = $this->createQueryBuilder('declaracaoAtividade');
            $query->select('count( declaracaoAtividade.id )');

            $query->where("declaracaoAtividade.idDeclaracao = :idDeclaracao");
            $query->setParameter("idDeclaracao", $idDeclaracao);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }
}
