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
 * Repositório responsável por encapsular as implementações de persistência da entidade 'JulgamentoSegundaInstanciaSubstituicao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class JulgamentoSegundaInstanciaSubstituicaoRepository extends AbstractRepository
{
    /**
     * Retorna julgamento final por id.
     *
     * @param $idJulgamentoFinal
     * @return JulgamentoSegundaInstanciaSubstituicao|null
     * @throws NonUniqueResultException
     */
    public function getPorJulgamentoFinal($idJulgamentoFinal)
    {
        try{
            $query = $query = $this->createQueryBuilder('julgamentoSegundaInstanciaSubstituicao');
            
            $query->leftJoin("julgamentoSegundaInstanciaSubstituicao.statusJulgamentoFinal", "statusJulgamentoFinal")->addSelect("statusJulgamentoFinal");
            
            $query->leftJoin("julgamentoSegundaInstanciaSubstituicao.indicacoes", "indicacoes")->addSelect("indicacoes")
                ->leftJoin('indicacoes.tipoParticipacaoChapa', 'tipoParticipacaoIndicacao')->addSelect('tipoParticipacaoIndicacao')
                ->leftJoin('indicacoes.membroChapa', 'membroChapa')->addSelect('membroChapa')
                ->leftJoin('membroChapa.profissional', 'profissional')->addSelect('profissional')
                ->leftJoin('membroChapa.tipoParticipacaoChapa', 'tipoParticipacaoChapa')->addSelect('tipoParticipacaoChapa');

            $query
                ->leftJoin("julgamentoSegundaInstanciaSubstituicao.substituicaoJulgamentoFinal", "substituicaoJulgamentoFinal")->addSelect("substituicaoJulgamentoFinal")
                ->leftJoin("substituicaoJulgamentoFinal.julgamentoFinal", "julgamentoFinal")->addSelect("julgamentoFinal")
                ->leftJoin('substituicaoJulgamentoFinal.membroSubstituicaoJulgamentoFinal', 'membroSubstituicaoJulgamentoFinal')->addSelect('membroSubstituicaoJulgamentoFinal')
                ->leftJoin('membroSubstituicaoJulgamentoFinal.indicacaoJulgamentoFinal', 'indicacaoJulgamentoFinal')->addSelect('indicacaoJulgamentoFinal')
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
     * @param $idChapa
     * @return JulgamentoSegundaInstanciaSubstituicao|null
     * @throws NonUniqueResultException
     */
    public function getUltimoPorChapa($idChapa)
    {
        try{
            $query = $query = $this->createQueryBuilder('julgamentoSegundaInstanciaSubstituicao');

            $query->leftJoin("julgamentoSegundaInstanciaSubstituicao.substituicaoJulgamentoFinal", "substituicaoJulgamentoFinal")
                ->leftJoin("substituicaoJulgamentoFinal.julgamentoFinal", "julgamentoFinal")
                ->join("julgamentoFinal.chapaEleicao", "chapaEleicao");

            $query->andWhere('chapaEleicao.id = :id');
            $query->setParameter('id', $idChapa);

            $query->orderBy("julgamentoSegundaInstanciaSubstituicao.dataCadastro", "DESC");
            $query->setMaxResults(1);

            return $query->getQuery()->getSingleResult();
        }
        catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Buscar Substituição do Julgamento em Segunda Instância por id chapa.
     *
     * @param $idChapa
     * @return mixed|null
     */
    public function getPorChapa($idChapa) {
        try {
            $query = $this->createQueryBuilder('julgamentoSegundaInstanciaSubstituicao');
            $query->innerJoin("julgamentoSegundaInstanciaSubstituicao.substituicaoJulgamentoFinal", "substituicaoJulgamentoFinal");
            $query->innerJoin("substituicaoJulgamentoFinal.julgamentoFinal", "julgamentoFinalChapa");
            $query->innerJoin("julgamentoFinalChapa.chapaEleicao", "chapaEleicaoFinal");

            $query->where("chapaEleicaoFinal.id = :idChapa");
            $query->setParameters(["idChapa" => $idChapa]);

            $queryUltimoJulgamento = $this->getSubQueryUltimoJulgametnoSubstituicao();
            $query->andWhere("julgamentoSegundaInstanciaSubstituicao.id in ($queryUltimoJulgamento)");

            $query->orderBy('julgamentoSegundaInstanciaSubstituicao.dataCadastro', 'ASC');

           return $query->getQuery()->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Buscar Substituição do Julgamento em Segunda Instância por id chapa.
     *
     * @param $idSubstituicao
     * @return mixed|null
     */
    public function getPorSubstituicao($idSubstituicao) {
        try {
            $query = $this->createQueryBuilder('julgamentoSegundaInstanciaSubstituicao');
            $query->innerJoin("julgamentoSegundaInstanciaSubstituicao.substituicaoJulgamentoFinal", "substituicao");

            $query->where("substituicao.id = :id");
            $query->setParameters(["id" => $idSubstituicao]);

            $query->orderBy('julgamentoSegundaInstanciaSubstituicao.dataCadastro', 'ASC');

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
    public function getidJulgamentoSegundaInstanciaSubstituicaoPorChapaEleicao($idChapaEleicao)
    {
        try {
            $query = $this->createQueryBuilder('julgamentoSegundaInstanciaSubstituicao');

            $query->innerJoin('julgamentoSegundaInstanciaSubstituicao.substituicaoJulgamentoFinal', 'substituicaoJulgamentoFinal')->addSelect('substituicaoJulgamentoFinal');
            $query->innerJoin('substituicaoJulgamentoFinal.julgamentoFinal', 'julgamentoFinal')->addSelect('julgamentoFinal');
            $query->innerJoin('julgamentoFinal.chapaEleicao', 'chapaEleicao')->addSelect('chapaEleicao');

            $query->select('julgamentoSegundaInstanciaSubstituicao.id');
            $query->where('chapaEleicao.id = :idChapa');
            $query->setParameter('idChapa', $idChapaEleicao);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException| NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna o ID da segunda instancia de Acordo com a Substituicao informado.
     *
     * @param $idSubstituicao
     * @return int|mixed
     */
    public function getIdJulgamentoPorSubstituicaoFinal($idSubstituicao)
    {
        try {
            $query = $this->createQueryBuilder('julgamentoSegundaInstanciaSubstituicao');
            $query->innerJoin(
                'julgamentoSegundaInstanciaSubstituicao.substituicaoJulgamentoFinal',
                'substituicaoJulgamentoFinal'
            );

            $query->select('julgamentoSegundaInstanciaSubstituicao.id');
            $query->where('substituicaoJulgamentoFinal.id = :id');
            $query->setParameter('id', $idSubstituicao);

            $query->orderBy('julgamentoSegundaInstanciaSubstituicao.id', 'ASC');
            $query->setMaxResults(1);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException| NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna o ID da segunda instancia de Acordo com a Substituicao informado.
     *
     * @param $idSubstituicao
     * @return JulgamentoSegundaInstanciaSubstituicao
     */
    public function getPorSubstituicaoSegundaInstancia($idSubstituicao)
    {
        try {
            $query = $this->createQueryBuilder('julgamentoSegundaInstanciaSubstituicao');

            $query->innerJoin('julgamentoSegundaInstanciaSubstituicao.substituicaoJulgamentoFinal', 'substituicaoJulgamentoFinal')->addSelect('substituicaoJulgamentoFinal');

            $query->leftJoin("julgamentoSegundaInstanciaSubstituicao.statusJulgamentoFinal", "statusJulgamentoFinal")->addSelect("statusJulgamentoFinal");
            $query->leftJoin("julgamentoSegundaInstanciaSubstituicao.indicacoes", "indicacoes")->addSelect("indicacoes");
            $query->leftJoin('indicacoes.tipoParticipacaoChapa', 'tipoParticipacaoIndicacao')->addSelect('tipoParticipacaoIndicacao');
            $query->leftJoin('indicacoes.membroChapa', 'membroChapa')->addSelect('membroChapa');
            $query->leftJoin('membroChapa.profissional', 'profissional')->addSelect('profissional');
            $query->leftJoin('membroChapa.tipoParticipacaoChapa', 'tipoParticipacaoChapa')->addSelect('tipoParticipacaoChapa');
            $query->leftJoin("substituicaoJulgamentoFinal.julgamentoFinal", "julgamentoFinal")->addSelect("julgamentoFinal");
            $query->leftJoin("julgamentoFinal.chapaEleicao", "chapaEleicao")->addSelect("chapaEleicao");
            $query->leftJoin("chapaEleicao.tipoCandidatura", "tipoCandidatura")->addSelect("tipoCandidatura");

            $query->where('julgamentoSegundaInstanciaSubstituicao.id = :id');
            $query->setParameter('id', $idSubstituicao);

            return $query->getQuery()->getSingleResult();
        } catch (NonUniqueResultException| NoResultException $e) {
            return null;
        }
    }

    /**
     * @return QueryBuilder
     */
    private function getSubQueryUltimoJulgametnoSubstituicao(): QueryBuilder
    {
        $queryUltimoJulgamento = $this->createQueryBuilder('julgamentoSegundaInstanciaSubstituicao2');
        $queryUltimoJulgamento->select('MAX(julgamentoSegundaInstanciaSubstituicao2.id)');
        $queryUltimoJulgamento->innerJoin("julgamentoSegundaInstanciaSubstituicao2.substituicaoJulgamentoFinal",
            "substituicaoJulgamentoFinal2");
        $queryUltimoJulgamento->where('substituicaoJulgamentoFinal2.id = substituicaoJulgamentoFinal.id');
        return $queryUltimoJulgamento;
    }
}
