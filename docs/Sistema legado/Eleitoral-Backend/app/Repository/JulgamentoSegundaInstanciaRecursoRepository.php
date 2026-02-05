<?php
/*
 * JulgamentoSegundaInstanciaRecursoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\JulgamentoSegundaInstanciaRecurso;
use App\To\JulgamentoSegundaInstanciaRecursoTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'JulgamentoSegundaInstanciaRecurso'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoSegundaInstanciaRecursoRepository extends AbstractRepository
{
    /**
     * Retorna a quantidade de Julgamento Impugnação
     *
     * @param int $idChapaEleicao
     * @return JulgamentoFinalTO
     */
    public function getIdJulgamentoSegundaInstanciaRecursoPorChapaEleicao(int $idChapaEleicao)
    {
        try {
            $query = $this->createQueryBuilder('julgamentoSegundaInstanciaRecurso');

            $query->innerJoin('julgamentoSegundaInstanciaRecurso.recursoJulgamentoFinal', 'recursoJulgamentoFinal')->addSelect('recursoJulgamentoFinal');
            $query->innerJoin('recursoJulgamentoFinal.julgamentoFinal', 'julgamentoFinal')->addSelect('julgamentoFinal');
            $query->innerJoin('julgamentoFinal.chapaEleicao', 'chapaEleicao')->addSelect('chapaEleicao');

            $query->select('julgamentoSegundaInstanciaRecurso.id');
            $query->where('chapaEleicao.id = :idChapa');
            $query->setParameter('idChapa', $idChapaEleicao);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException| NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna o ID do Julgamento de segunda Instancia pelo ID do Recurso Final
     *
     * @param int $idRecursoFinal
     * @return mixed|null
     */
    public function getIdJulgamentoSegundaInstanciaRecursoPorRecursoFinal(int $idRecursoFinal)
    {
        try {
            $query = $this->createQueryBuilder('julgamentoSegundaInstanciaRecurso');

            $query->innerJoin('julgamentoSegundaInstanciaRecurso.recursoJulgamentoFinal', 'recursoJulgamentoFinal')->addSelect('recursoJulgamentoFinal');


            $query->select('julgamentoSegundaInstanciaRecurso.id');
            $query->where('recursoJulgamentoFinal.id = :idRecursoFinal');
            $query->setParameter('idRecursoFinal', $idRecursoFinal);

            $query->orderBy('julgamentoSegundaInstanciaRecurso.id', 'ASC');

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException| NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna julgamento final por id.
     *
     * @param $id
     * @return JulgamentoSegundaInstanciaRecurso|null
     */
    public function getPorJulgamentoFinal($idJulgamentoFinal)
    {
        try{
            $query = $query = $this->createQueryBuilder('julgamentoSegundaInstanciaRecurso');

            $query->leftJoin("julgamentoSegundaInstanciaRecurso.statusJulgamentoFinal", "statusJulgamentoFinal")->addSelect("statusJulgamentoFinal");

            $query->leftJoin("julgamentoSegundaInstanciaRecurso.indicacoes", "indicacoes")->addSelect("indicacoes")
                ->leftJoin('indicacoes.tipoParticipacaoChapa', 'tipoParticipacaoIndicacao')->addSelect('tipoParticipacaoIndicacao')
                ->leftJoin('indicacoes.membroChapa', 'membroChapa')->addSelect('membroChapa')
                ->leftJoin('membroChapa.profissional', 'profissional')->addSelect('profissional')
                ->leftJoin('membroChapa.tipoParticipacaoChapa', 'tipoParticipacaoChapa')->addSelect('tipoParticipacaoChapa');
                //->leftJoin('membroChapa.statusParticipacaoChapa', 'statusParticipacaoChapa')->addSelect('statusParticipacaoChapa')
                //->leftJoin("membroChapa.statusValidacaoMembroChapa", "statusValidacaoMembroChapa")->addSelect("statusValidacaoMembroChapa")
                //->leftJoin("membroChapa.pendencias", "pendencias")->addSelect("pendencias")
                //->leftJoin("pendencias.tipoPendencia", "tipoPendencia")->addSelect("tipoPendencia");

            $query
                ->leftJoin("julgamentoSegundaInstanciaRecurso.recursoJulgamentoFinal", "recursoJulgamentoFinal")->addSelect("recursoJulgamentoFinal")
                ->leftJoin("recursoJulgamentoFinal.julgamentoFinal", "julgamentoFinal")->addSelect("julgamentoFinal")
                ->leftJoin('recursoJulgamentoFinal.recursosIndicacao', 'recursosIndicacao')->addSelect('recursosIndicacao')
                ->leftJoin('recursosIndicacao.indicacaoJulgamentoFinal', 'indicacaoJulgamentoFinal')->addSelect('indicacaoJulgamentoFinal')
                ->leftJoin('indicacaoJulgamentoFinal.tipoParticipacaoChapa', 'recursosTipoParticipacaoChapa')->addSelect('recursosTipoParticipacaoChapa')
                ->leftJoin('indicacaoJulgamentoFinal.membroChapa', 'recursosMembroChapa')->addSelect('recursosMembroChapa')
                ->leftJoin('recursosMembroChapa.profissional', 'recursosProfissional')->addSelect('recursosProfissional');

            $query->andWhere('julgamentoFinal.id = :id');
            $query->setParameter('id', $idJulgamentoFinal);

            return $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
        }
        catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna julgamento final por id.
     *
     * @param $id
     * @return JulgamentoSegundaInstanciaRecurso|null
     */
    public function getUltimoPorJulgamentoFinal($idJulgamentoFinal)
    {
        try{
            $query = $query = $this->createQueryBuilder('julgamentoSegundaInstanciaRecurso');

            $query->innerJoin('julgamentoSegundaInstanciaRecurso.recursoJulgamentoFinal', 'recursoJulgamentoFinal');
            $query->innerJoin('recursoJulgamentoFinal.julgamentoFinal', 'julgamentoFinal');

            $query->andWhere('julgamentoFinal.id = :id');
            $query->setParameter('id', $idJulgamentoFinal);

            $query->orderBy("julgamentoSegundaInstanciaRecurso.dataCadastro", "DESC");
            $query->setMaxResults(1);

            return $query->getQuery()->getSingleResult();
        }
        catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Buscar Recurso do Julgamento em Segunda Instância por id chapa.
     *
     * @param $idChapa
     * @return mixed|null
     */
    public function getPorChapa($idChapa) {
        try {
            $query = $this->createQueryBuilder('julgamentoSegundaInstanciaRecurso');

            $query->innerJoin("julgamentoSegundaInstanciaRecurso.recursoJulgamentoFinal","recursoJulgamentoFinal");
            $query->innerJoin("recursoJulgamentoFinal.julgamentoFinal", "julgamentoFinal");
            $query->innerJoin("julgamentoFinal.chapaEleicao", "chapaEleicao");

            $query->where("chapaEleicao.id = :idChapa");
            $query->setParameters(["idChapa" => $idChapa]);

            $query->orderBy('julgamentoSegundaInstanciaRecurso.dataCadastro', 'DESC');
            $query->setMaxResults(1);

           return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Buscar Recurso do Julgamento em Segunda Instância por id chapa.
     *
     * @param $idRecursoJulgamentoFinal
     * @return mixed|null
     */
    public function getRetificacoes($idRecursoJulgamentoFinal) {
        try {
            $query = $this->getQueryJulgamentoSegundaInstanciaRecurso();

            $query->where("recursoJulgamentoFinal.id = :id");
            $query->setParameters(["id" => $idRecursoJulgamentoFinal]);

            $query->orderBy('julgamentoSegundaInstanciaRecurso.dataCadastro', 'ASC');

            return $query->getQuery()->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function getJulgamentoSegundaInstanciaRecusoPorId($idRecursoSegundaIntancia) {
        try {
            $query = $this->createQueryBuilder('julgamentoSegundaInstanciaRecurso')

                ->leftJoin("julgamentoSegundaInstanciaRecurso.statusJulgamentoFinal", "statusJulgamentoFinal")->addSelect("statusJulgamentoFinal");

            $query->leftJoin("julgamentoSegundaInstanciaRecurso.indicacoes", "indicacoes")->addSelect("indicacoes")
                ->leftJoin('indicacoes.tipoParticipacaoChapa', 'tipoParticipacaoIndicacao')->addSelect('tipoParticipacaoIndicacao')
                ->leftJoin('indicacoes.membroChapa', 'membroChapa')->addSelect('membroChapa')
                ->leftJoin('membroChapa.profissional', 'profissional')->addSelect('profissional')
                ->leftJoin('membroChapa.tipoParticipacaoChapa', 'tipoParticipacaoChapa')->addSelect('tipoParticipacaoChapa');

            $query
                ->leftJoin("julgamentoSegundaInstanciaRecurso.recursoJulgamentoFinal", "recursoJulgamentoFinal")->addSelect("recursoJulgamentoFinal")
                ->leftJoin("recursoJulgamentoFinal.julgamentoFinal", "julgamentoFinal")->addSelect("julgamentoFinal")
                ->leftJoin('recursoJulgamentoFinal.recursosIndicacao', 'recursosIndicacao')->addSelect('recursosIndicacao')
                ->leftJoin('recursosIndicacao.indicacaoJulgamentoFinal', 'indicacaoJulgamentoFinal')->addSelect('indicacaoJulgamentoFinal')
                ->leftJoin('indicacaoJulgamentoFinal.tipoParticipacaoChapa', 'recursosTipoParticipacaoChapa')->addSelect('recursosTipoParticipacaoChapa')
                ->leftJoin('indicacaoJulgamentoFinal.membroChapa', 'recursosMembroChapa')->addSelect('recursosMembroChapa')
                ->leftJoin('recursosMembroChapa.profissional', 'recursosProfissional')->addSelect('recursosProfissional');

            $query
                ->join("julgamentoFinal.chapaEleicao", "chapaEleicao")->addSelect("chapaEleicao")
                ->join("chapaEleicao.filial", "filial")->addSelect("filial");

            $query->where("julgamentoSegundaInstanciaRecurso.id = :idJulgamentoSegundaInstanciaRecurso");
            $query->setParameters(["idJulgamentoSegundaInstanciaRecurso" => $idRecursoSegundaIntancia]);

            return $query->getQuery()->getSingleResult();
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryJulgamentoSegundaInstanciaRecurso(): QueryBuilder
    {
        $query = $this->createQueryBuilder('julgamentoSegundaInstanciaRecurso')
            ->leftJoin("julgamentoSegundaInstanciaRecurso.statusJulgamentoFinal",
                "statusJulgamentoFinal")->addSelect("statusJulgamentoFinal");
        $query->leftJoin('julgamentoSegundaInstanciaRecurso.usuario', 'usuario')->addSelect('usuario');

        $query->leftJoin("julgamentoSegundaInstanciaRecurso.indicacoes", "indicacoes")->addSelect("indicacoes")
            ->leftJoin('indicacoes.tipoParticipacaoChapa',
                'tipoParticipacaoIndicacao')->addSelect('tipoParticipacaoIndicacao')
            ->leftJoin('indicacoes.membroChapa', 'membroChapa')->addSelect('membroChapa')
            ->leftJoin('membroChapa.profissional', 'profissional')->addSelect('profissional')
            ->leftJoin('membroChapa.tipoParticipacaoChapa',
                'tipoParticipacaoChapa')->addSelect('tipoParticipacaoChapa');

        $query
            ->leftJoin("julgamentoSegundaInstanciaRecurso.recursoJulgamentoFinal",
                "recursoJulgamentoFinal")->addSelect("recursoJulgamentoFinal")
            ->leftJoin("recursoJulgamentoFinal.julgamentoFinal", "julgamentoFinal")->addSelect("julgamentoFinal")
            ->leftJoin('recursoJulgamentoFinal.recursosIndicacao', 'recursosIndicacao')->addSelect('recursosIndicacao')
            ->leftJoin('recursosIndicacao.indicacaoJulgamentoFinal',
                'indicacaoJulgamentoFinal')->addSelect('indicacaoJulgamentoFinal')
            ->leftJoin('indicacaoJulgamentoFinal.tipoParticipacaoChapa',
                'recursosTipoParticipacaoChapa')->addSelect('recursosTipoParticipacaoChapa')
            ->leftJoin('indicacaoJulgamentoFinal.membroChapa', 'recursosMembroChapa')->addSelect('recursosMembroChapa')
            ->leftJoin('recursosMembroChapa.profissional', 'recursosProfissional')->addSelect('recursosProfissional');

        $query->join("julgamentoFinal.chapaEleicao", "chapaEleicao")->addSelect("chapaEleicao")
            ->join("chapaEleicao.filial", "filial")->addSelect("filial");
        return $query;
    }
}
