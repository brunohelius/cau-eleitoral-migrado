<?php
/*
 * RecursoDenunciaRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\JulgamentoRecursoDenuncia;
use App\Entities\RecursoDenuncia;
use App\To\JulgamentoRecursoImpugnacaoTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'RecursoDenuncia'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class RecursoDenunciaRepository extends AbstractRepository
{

    /**
     * Retorna o recurso da Denuncia de acordo com 'idDenuncia' informado.
     *
     * @param integer $idDenuncia
     *
     * @param         $idProfissionalLogado
     *
     * @return RecursoDenuncia|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function getRecursoPorDenunciaAndProfissionalLogado(
        $idDenuncia,
        $idProfissionalLogado
    ) {
        try {
            $dql  = 'SELECT recursoDenuncia, contrarrazao ';
            $dql .= 'FROM App\Entities\RecursoDenuncia recursoDenuncia ';
            $dql .= 'LEFT JOIN recursoDenuncia.profissional profissionalRecurso ';
            $dql .= 'LEFT JOIN recursoDenuncia.contrarrazao contrarrazao ';
            $dql .= 'LEFT JOIN contrarrazao.profissional profissionalContrarrazao ';

            $dql .= 'WHERE recursoDenuncia.recursoDenuncia IS NULL ';
            $dql .= 'AND recursoDenuncia.denuncia = :idDenuncia ';
            $dql .= 'AND recursoDenuncia.profissional != :idProfissionalLogado ';

            $dql .= 'ORDER BY recursoDenuncia.id';

            $query = $this->getEntityManager()->createQuery($dql);
            $query->setParameter('idDenuncia', $idDenuncia);
            $query->setParameter('idProfissionalLogado', $idProfissionalLogado);

            $recurso =  $query->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return RecursoDenuncia::newInstance($recurso);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna os recursos da Denuncia de acordo com 'idDenuncia' informado.
     *
     * @param \stdClass $filtro
     * @return RecursoDenuncia[]|null
     */
    public function getRecursosPorFiltro(\stdClass $filtro)
    {
        $query = $this->createQueryBuilder('recursoDenuncia');
        $query->innerJoin('recursoDenuncia.profissional', 'profissionalRecurso')
              ->addSelect('profissionalRecurso');
        $query->leftJoin('recursoDenuncia.denuncia', 'denunciaRecurso')
              ->addSelect('denunciaRecurso');
        $query->leftJoin('denunciaRecurso.julgamentoDenuncia', 'julgamentoDenuncia')
              ->addSelect('julgamentoDenuncia');
        $query->leftJoin('recursoDenuncia.arquivos', 'arquivosRecursoDenuncia')
              ->addSelect('arquivosRecursoDenuncia');
        $query->leftJoin('recursoDenuncia.contrarrazao', 'contrarrazao')
              ->addSelect('contrarrazao');
        $query->leftJoin('contrarrazao.profissional', 'profissionalContrarrazao')
              ->addSelect('profissionalContrarrazao');
        $query->leftJoin('contrarrazao.arquivos', 'arquivosContrarrazao')
              ->addSelect('arquivosContrarrazao');

        $query->leftJoin('recursoDenuncia.julgamentoRecursoDenunciado', 'julgamentoRecursoDenunciado')
              ->addSelect('julgamentoRecursoDenunciado');
        $query->leftJoin('julgamentoRecursoDenunciado.tipoJulgamentoDenunciado', 'tipoJulgamentoDenunciadoRecurso')
              ->addSelect('tipoJulgamentoDenunciadoRecurso');
        $query->leftJoin('julgamentoRecursoDenunciado.tipoJulgamentoDenunciante', 'tipoJulgamentoDenuncianteRecursoDenunciado')
              ->addSelect('tipoJulgamentoDenuncianteRecursoDenunciado');
        $query->leftJoin('julgamentoRecursoDenunciado.tipoSentencaJulgamento', 'tipoSentencaJulgamentoRecursoDenunciado')
              ->addSelect('tipoSentencaJulgamentoRecursoDenunciado');
        $query->leftJoin('julgamentoRecursoDenunciado.arquivosJulgamentoRecursoDenuncia', 'arquivosJulgamentoRecursoDenunciado')
              ->addSelect('arquivosJulgamentoRecursoDenunciado');
        $query->leftJoin('julgamentoRecursoDenunciado.retificacaoJulgamento', 'retificacaoJulgamentoDenunciado')
              ->addSelect('retificacaoJulgamentoDenunciado');
        $query->leftJoin('retificacaoJulgamentoDenunciado.usuario', 'usuarioRetificacaoJulgamentoDenunciado')
              ->addSelect('usuarioRetificacaoJulgamentoDenunciado');

        $query->leftJoin('recursoDenuncia.julgamentoRecursoDenunciante', 'julgamentoRecursoDenunciante')
              ->addSelect('julgamentoRecursoDenunciante');
        $query->leftJoin('julgamentoRecursoDenunciante.tipoJulgamentoDenunciado', 'tipoJulgamentoDenunciadoRecursoDenunciante')
              ->addSelect('tipoJulgamentoDenunciadoRecursoDenunciante');
        $query->leftJoin('julgamentoRecursoDenunciante.tipoJulgamentoDenunciante', 'tipoJulgamentoRecursoDenunciante')
              ->addSelect('tipoJulgamentoRecursoDenunciante');
        $query->leftJoin('julgamentoRecursoDenunciante.tipoSentencaJulgamento', 'tipoSentencaJulgamentoRecursoDenunciante')
              ->addSelect('tipoSentencaJulgamentoRecursoDenunciante');
        $query->leftJoin('julgamentoRecursoDenunciante.arquivosJulgamentoRecursoDenuncia', 'arquivosJulgamentoRecursoDenunciante')
              ->addSelect('arquivosJulgamentoRecursoDenunciante');
        $query->leftJoin('julgamentoRecursoDenunciante.retificacaoJulgamento', 'retificacaoJulgamentoDenunciante')
              ->addSelect('retificacaoJulgamentoDenunciante');
        $query->leftJoin('retificacaoJulgamentoDenunciante.usuario', 'usuarioRetificacaoJulgamentoDenunciante')
              ->addSelect('usuarioRetificacaoJulgamentoDenunciante');

        $query->where('1 = 1');

        if (!empty($filtro->idDenuncia)) {
            $query->andWhere('recursoDenuncia.denuncia = :idDenuncia');
            $query->setParameter(':idDenuncia', $filtro->idDenuncia);
        }

        if (!empty($filtro->idRecursosOcultos)) {
            $query->andWhere('recursoDenuncia.id NOT IN(:idRecursosOcultos)');
            $query->setParameter(':idRecursosOcultos', $filtro->idRecursosOcultos);
        }

        $query->orderBy('recursoDenuncia.id');

        $recursosDenuncia = $query->getQuery()->getArrayResult();
        return array_map(static function ($recursoDenuncia) {
            return RecursoDenuncia::newInstance($recursoDenuncia);
        }, $recursosDenuncia);
    }

    /**
     * Retorna os registros de recursos
     * @param $tpRecConDen
     * @param $idDenuncia
     * @return array|int|string
     */
    public function hasRecurso($tpRecConDen, $idDenuncia)
    {
        $dql  = "SELECT rd ";
        $dql .= "FROM App\Entities\RecursoDenuncia rd ";
        $dql .= "WHERE rd.tipoRecursoContrarrazaoDenuncia IN(:tipo) ";
        $dql .= "AND rd.denuncia =:id ";
        $dql .= "AND rd.recursoDenuncia  IS NULL";
        $query = $this->getEntityManager()->createQuery($dql);

        $parametros['tipo'] = $tpRecConDen;

        $query->setParameters($parametros);
        $query->setParameter('id', $idDenuncia);
        return $query->getArrayResult();
    }

    /**
     * Retorna os registros de recursos
     * @param $tpRecConDen
     * @param $idDenuncia
     * @return array|int|string
     */
    public function getRecursoDenunciaPorTipo($tpRecConDen, $idDenuncia)
    {
        $dql  = "SELECT rd ";
        $dql .= "FROM App\Entities\RecursoDenuncia rd ";
        $dql .= "WHERE rd.tipoRecursoContrarrazaoDenuncia =:tipo ";
        $dql .= "AND rd.denuncia =:id ";
        $dql .= "AND rd.recursoDenuncia  IS NULL";
        $query = $this->getEntityManager()->createQuery($dql);

        $parametros['tipo'] = $tpRecConDen;

        $query->setParameters($parametros);
        $query->setParameter('id', $idDenuncia);
        return $query->getArrayResult();
    }
}