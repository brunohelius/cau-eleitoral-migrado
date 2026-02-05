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

use App\To\SubstituicaoJulgamentoFinalTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'SubstituicaoJulgamentoFinal'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class SubstituicaoJulgamentoFinalRepository extends AbstractRepository
{
    /**
     * Buscar Substituição Julgamento por id chapa.
     *
     * @param $idChapa
     * @return mixed|null
     */
    public function getPorChapa($idChapa)
    {
        try {
            $query = $this->createQueryBuilder('substituicaoJulgamentoFinal');
            $query->join("substituicaoJulgamentoFinal.julgamentoFinal", "julgamentoFinal");
            $query->join("julgamentoFinal.chapaEleicao", "chapaEleicao");

            $query->where("chapaEleicao.id = :idChapa");
            $query->setParameters(["idChapa" => $idChapa]);

            $query->orderBy("substituicaoJulgamentoFinal.dataCadastro", "ASC");

            return $query->getQuery()->getResult();
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Buscar Substituição Julgamento por id chapa.
     *
     * @param $idChapa
     * @return mixed|null
     */
    public function getTotalPorChapa($idChapa)
    {
        try {
            $query = $this->createQueryBuilder('substituicaoJulgamentoFinal');
            $query->select('COUNT(substituicaoJulgamentoFinal.id)');
            $query->join("substituicaoJulgamentoFinal.julgamentoFinal", "julgamentoFinal");
            $query->join("julgamentoFinal.chapaEleicao", "chapaEleicao");

            $query->where("chapaEleicao.id = :idChapa");
            $query->setParameters(["idChapa" => $idChapa]);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Buscar Substituição Julgamento por id.
     *
     * @param $id
     * @return mixed|null
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('substituicaoJulgamentoFinal');
            $query->leftJoin("substituicaoJulgamentoFinal.profissional", "profissionalSubstituicaoJulgamento")->addSelect("profissionalSubstituicaoJulgamento");
            $query->join("substituicaoJulgamentoFinal.julgamentoFinal", "julgamentoFinal")->addSelect("julgamentoFinal");
            $query->leftJoin("julgamentoFinal.recursoJulgamentoFinal", "recursoJulgamentoFinal")->addSelect("recursoJulgamentoFinal");
            $query->join("julgamentoFinal.chapaEleicao", "chapaEleicao")->addSelect("chapaEleicao");
            $query->leftJoin("julgamentoFinal.statusJulgamentoFinal", "statusJulgamentoFinal")->addSelect("statusJulgamentoFinal");
            $query->join("chapaEleicao.filial", "filial")->addSelect("filial");
            $query->leftJoin("substituicaoJulgamentoFinal.membrosSubstituicaoJulgamentoFinal", "membrosSubstituicaoJulgamentoFinal")->addSelect("membrosSubstituicaoJulgamentoFinal");
            $query->leftJoin("membrosSubstituicaoJulgamentoFinal.indicacaoJulgamentoFinal", "indicacaoJulgamentoFinal")->addSelect("indicacaoJulgamentoFinal");
            $query->leftJoin("chapaEleicao.tipoCandidatura", "tipoCandidatura")->addSelect("tipoCandidatura");
            //$query->leftJoin("substituicaoJulgamentoFinal.membrosSubstituicaoJulgamentoFinal", "membrosSubstituicaoJulgamentoFinal")->addSelect("membrosSubstituicaoJulgamentoFinal");
            //$query->leftJoin("membrosSubstituicaoJulgamentoFinal.indicacaoJulgamentoFinal","indicacaoJulgamentoFinal")->addSelect("indicacaoJulgamentoFinal");
            $query->leftJoin("indicacaoJulgamentoFinal.membroChapa", "membroChapaIndicacao")->addSelect("membroChapaIndicacao");
            $query->leftJoin("membroChapaIndicacao.statusParticipacaoChapa", "statusParticipacaoChapaMembroChapaIndicacao")->addSelect("statusParticipacaoChapaMembroChapaIndicacao");
            $query->leftJoin("membroChapaIndicacao.profissional", "profissionalIndicacao")->addSelect("profissionalIndicacao");
            $query->leftJoin("membroChapaIndicacao.pendencias", "pendenciasIndicacao")->addSelect("pendenciasIndicacao");
            $query->leftJoin('membroChapaIndicacao.statusValidacaoMembroChapa', 'statusValidacaoMembroChapaIndicacao')->addSelect('statusValidacaoMembroChapaIndicacao');
            $query->leftJoin("pendenciasIndicacao.tipoPendencia", "tipoPendenciaIndicacao")->addSelect("tipoPendenciaIndicacao");
            $query->leftJoin("membrosSubstituicaoJulgamentoFinal.membroChapa", "membroChapa")->addSelect("membroChapa");
            $query->leftJoin("membroChapa.statusParticipacaoChapa", "statusParticipacaoChapa")->addSelect("statusParticipacaoChapa");
            $query->leftJoin("membroChapa.statusValidacaoMembroChapa", "statusValidacaoMembroChapa")->addSelect("statusValidacaoMembroChapa");
            $query->leftJoin("membroChapa.profissional", "profissionalMembroSubstituicao")->addSelect("profissionalMembroSubstituicao");
            $query->leftJoin("membroChapa.pendencias", "pendencias")->addSelect("pendencias");
            //$query->leftJoin('membroChapa.statusValidacaoMembroChapa', 'statusValidacaoMembroChapa')->addSelect('statusValidacaoMembroChapa');
            $query->leftJoin("pendencias.tipoPendencia", "tipoPendencia")->addSelect("tipoPendencia");
            $query->leftJoin("substituicaoJulgamentoFinal.julgamentoSegundaInstanciaSubstituicao", "julgamentoSegundaInstanciaSubstituicao")->addSelect("julgamentoSegundaInstanciaSubstituicao");
            $query->leftJoin("julgamentoSegundaInstanciaSubstituicao.recursoSegundoJulgamentoSubstituicao", "recursoSegundoJulgamentoSubstituicao")->addSelect("recursoSegundoJulgamentoSubstituicao");
            $query->leftJoin("recursoSegundoJulgamentoSubstituicao.profissional", "profissionalRecursoSegundoJulgamentoSubstituicao")->addSelect("profissionalRecursoSegundoJulgamentoSubstituicao");
            $query->where("substituicaoJulgamentoFinal.id = :id");
            $query->setParameters(["id" => $id]);

            return $query->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Julgamento Impugnação
     *
     * @param int $idSubstituicaJulgamentoFinal
     * @return SubstituicaoJulgamentoFinalTO|null
     * @throws \Exception
     */
    public function getRecursoJulgamentoFinalPorId(int $idSubstituicaJulgamentoFinal)
    {
        try {
            $query = $this->getQuerySubstituicaoJulgamentoFinal();

            $query->andWhere('substituicaoJulgamentoFinal.id = :id');
            $query->setParameter('id', $idSubstituicaJulgamentoFinal);

            $julgamento = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return SubstituicaoJulgamentoFinalTO::newInstance($julgamento);
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a query com os joins dos relacionamentos
     *
     * @return QueryBuilder
     */
    private function getQuerySubstituicaoJulgamentoFinal(): QueryBuilder
    {
        $query = $this->createQueryBuilder('substituicaoJulgamentoFinal')
            ->innerJoin('substituicaoJulgamentoFinal.julgamentoFinal', 'julgamentoFinal')
            ->innerJoin('substituicaoJulgamentoFinal.profissional', 'profissionalResponsavel')
            ->addSelect('profissionalResponsavel')
            ->addSelect('julgamentoFinal');

        $query
            ->leftJoin('julgamentoFinal.usuario', 'usuario')
            ->leftJoin('julgamentoFinal.chapaEleicao', 'chapaEleicao')
            ->leftJoin('julgamentoFinal.statusJulgamentoFinal', 'statusJulgamentoFinal')
            ->addSelect('statusJulgamentoFinal')
            ->addSelect('chapaEleicao');

        $query->where('chapaEleicao.excluido = false');
        return $query;
    }
}
