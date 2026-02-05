<?php
/*
 * JulgamentoSegundaInstanciaSubstituicaoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\JulgamentoSegundaInstanciaSubstituicao;
use App\To\JulgamentoFinalTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'JulgamentoRecursoPedidoSubstituicaoRepository'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoRecursoPedidoSubstituicaoRepository extends AbstractRepository
{

    /**
     * Retorna último julgamento por id do julgamento final.
     *
     * @param $idJulgamentoFinal
     * @return JulgamentoSegundaInstanciaSubstituicao|null
     * @throws NonUniqueResultException
     */
    public function getUltimoPorJulgamentoFinal($idJulgamentoFinal)
    {
        try{
            $query = $query = $this->createQueryBuilder('julgamentoRecursoPedidoSubst');

            $query->leftJoin("julgamentoRecursoPedidoSubst.recursoSegundoJulgamentoSubstituicao", "recursoSegundoJulgamentoSubstituicao")
                ->leftJoin("recursoSegundoJulgamentoSubstituicao.julgamentoSegundaInstanciaSubstituicao", "julgamentoSegundaInstanciaSubstituicao")
                ->leftJoin("julgamentoSegundaInstanciaSubstituicao.substituicaoJulgamentoFinal", "substituicaoJulgamentoFinal")
                ->leftJoin("substituicaoJulgamentoFinal.julgamentoFinal", "julgamentoFinal");

            $query->andWhere('julgamentoFinal.id = :id');
            $query->setParameter('id', $idJulgamentoFinal);

            $query->orderBy("julgamentoRecursoPedidoSubst.dataCadastro", "DESC");
            $query->setMaxResults(1);

            return $query->getQuery()->getSingleResult();
        }
        catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna último julgamento por id do julgamento final.
     *
     * @param $idChapa
     * @return array|mixed|null
     * @throws NonUniqueResultException
     */
    public function getPorChapa($idChapa)
    {
        try{
            $query = $query = $this->createQueryBuilder('julgamentoRecursoPedidoSubst');

            $query->join("julgamentoRecursoPedidoSubst.recursoSegundoJulgamentoSubstituicao", "recursoSegundoJulgamentoSubstituicao")
                ->join("recursoSegundoJulgamentoSubstituicao.julgamentoSegundaInstanciaSubstituicao", "julgamentoSegundaInstanciaSubstituicao")
                ->join("julgamentoSegundaInstanciaSubstituicao.substituicaoJulgamentoFinal", "substituicaoJulgamentoFinal")
                ->join("substituicaoJulgamentoFinal.julgamentoFinal", "julgamentoFinal")
                ->join("julgamentoFinal.chapaEleicao", "chapaEleicao");;

            $query->andWhere('chapaEleicao.id = :id');
            $query->setParameter('id', $idChapa);

            $queryUltimoJulgamento = $this->getSubQueryUltimoJulgametnoSubstituicao();
            $query->andWhere("julgamentoRecursoPedidoSubst.id in ($queryUltimoJulgamento)");

            $query->orderBy("julgamentoRecursoPedidoSubst.dataCadastro", "ASC");

            return $query->getQuery()->getResult();
        }
        catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Buscar Substituição do Julgamento em Segunda Instância por id chapa.
     *
     * @param $idRecurso
     * @return mixed|null
     */
    public function getPorRecurso($idRecurso) {
        try {
            $query = $this->createQueryBuilder('julgamentoRecursoPedidoSubst');
            $query->innerJoin("julgamentoRecursoPedidoSubst.recursoSegundoJulgamentoSubstituicao", "recurso");

            $query->where("recurso.id = :id");
            $query->setParameters(["id" => $idRecurso]);

            $query->orderBy('julgamentoRecursoPedidoSubst.dataCadastro', 'ASC');

            return $query->getQuery()->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @return QueryBuilder
     */
    private function getSubQueryUltimoJulgametnoSubstituicao(): QueryBuilder
    {
        $queryUltimoJulgamento = $this->createQueryBuilder('julgamentoRecursoPedidoSubst2');
        $queryUltimoJulgamento->select('MAX(julgamentoRecursoPedidoSubst2.id)');
        $queryUltimoJulgamento->innerJoin("julgamentoRecursoPedidoSubst2.recursoSegundoJulgamentoSubstituicao",
            "recursoSegundoJulgamentoSubstituicao2");
        $queryUltimoJulgamento->where('recursoSegundoJulgamentoSubstituicao2.id = recursoSegundoJulgamentoSubstituicao.id');
        return $queryUltimoJulgamento;
    }
}
