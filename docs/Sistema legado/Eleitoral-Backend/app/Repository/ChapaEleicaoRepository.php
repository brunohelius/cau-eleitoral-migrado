<?php

namespace App\Repository;

use App\Config\Constants;
use App\Entities\ChapaEleicao;
use App\Entities\ChapaEleicaoStatus;
use App\Entities\MembroChapa;
use App\To\ChapaEleicaoExtratoFiltroTO;
use App\To\ChapaEleicaoTO;
use App\Util\Utils;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Exception;
use stdClass;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'ChapaEleicao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ChapaEleicaoRepository extends AbstractRepository
{

    /**
     * Retorna a chapa da eleição conforme o id informado.
     *
     * @param $id
     * @param bool $addMembrosRetorno
     * @param bool $addRelacoesConsulta
     * @param bool $addFilial
     * @return ChapaEleicao|null
     * @throws Exception
     */
    public function getPorId($id, bool $addMembrosRetorno = false, bool $addRelacoesConsulta = true, $addFilial = false)
    {
        try {
            $query = $this->createQueryBuilder('chapaEleicao');

            if ($addRelacoesConsulta) {
                $this->addRelacoesConsulta($query);
            }

            if ($addFilial) {
                $query->innerJoin("chapaEleicao.filial", 'filial')->addSelect("filial");
            }

            $query->where("chapaEleicao.excluido = false AND chapaEleicao.id = :id");
            $query->setParameter("id", $id);

            if ($addMembrosRetorno) {
                $this->addMembrosConsulta($query);
                $this->addCondicaoMembroChapaAtual($query);
            }

            $chapaEleicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return ChapaEleicao::newInstance($chapaEleicao);
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a chapa da eleição conforme o id informado.
     *
     * @param $idPedidoImpugnacao
     * @param bool $addMembrosRetorno
     * @param bool $addRelacoesConsulta
     * @return ChapaEleicao|null
     * @throws Exception
     */
    public function getPorPedidoImpugnacao($idPedidoImpugnacao)
    {
        try {
            $query = $this->createQueryBuilder('chapaEleicao');

            $this->addRelacoesConsulta($query);

            $query->leftJoin('chapaEleicao.membrosChapa', 'membrosChapa');
            $query->leftJoin('membrosChapa.pedidoImpugnacao', 'pedidoImpugnacao');

            $query->where("chapaEleicao.excluido = false AND pedidoImpugnacao.id = :id");
            $query->setParameter("id", $idPedidoImpugnacao);

            $query->setMaxResults(1);

            $chapaEleicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return ChapaEleicao::newInstance($chapaEleicao);
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Retorna as chapas da eleição conforme o id calendpario, o id da CAU UF e status da chapa.
     *
     * @param ChapaEleicaoExtratoFiltroTO $chapaEleicaoExtratoFiltroTO
     *
     * @return array|null
     */
    public function getChapasExtratoPorFiltro(ChapaEleicaoExtratoFiltroTO $chapaEleicaoExtratoFiltroTO) {
        try {
            $query = $this->createQueryBuilder('chapaEleicao');
            if (empty($idStatusChapa)) {
                $query->innerJoin(
                    "chapaEleicao.atividadeSecundariaCalendario",
                    "atividadeSecundariaCalendario"
                );
                $query->innerJoin(
                    "atividadeSecundariaCalendario.atividadePrincipalCalendario",
                    "atividadePrincipal"
                );
            }
            $query->innerJoin("chapaEleicao.tipoCandidatura", "tipoCandidatura")
                ->addSelect("tipoCandidatura");
            $query->leftJoin("chapaEleicao.chapaEleicaoStatus", "chapaEleicaoStatusConfirmado");
            $query->leftJoin("chapaEleicao.chapaEleicaoStatus", "chapaEleicaoStatusAtual")->addSelect("chapaEleicaoStatusAtual");
            $query->leftJoin("chapaEleicaoStatusAtual.statusChapa", "statusChapaAtual")->addSelect('statusChapaAtual');
            $query->leftJoin("chapaEleicao.redesSociaisChapa", "redesSociaisChapa")
                ->addSelect("redesSociaisChapa");
            $query->leftJoin("redesSociaisChapa.tipoRedeSocial", "tipoRedeSocial")
                ->addSelect("tipoRedeSocial");
            $query->leftJoin('chapaEleicao.membrosChapa', 'membrosChapa')->addSelect('membrosChapa');
            $query->leftJoin('membrosChapa.tipoMembroChapa', 'tipoMembroChapa')
                ->addSelect('tipoMembroChapa');
            $query->leftJoin('membrosChapa.tipoParticipacaoChapa', 'tipoParticipacaoChapa')
                ->addSelect('tipoParticipacaoChapa');
            $query->leftJoin('membrosChapa.respostaDeclaracaoRepresentatividade', 'respostaDeclaracaoRepresentatividade')
                ->addSelect('respostaDeclaracaoRepresentatividade');
            $this->addLeftJoinProfissional($query, 'membrosChapa');
            $query->innerJoin("chapaEleicao.filial", "filial")->addSelect('filial');

            $query->where("chapaEleicao.excluido = false");

            if (!empty($chapaEleicaoExtratoFiltroTO->getIdStatus())) {
                $subQueryIdsChapas = $this->getSubQueryIdsChapasEleicaoPorCalendarioEStatus('PorStatus');
                if(!empty($chapaEleicaoExtratoFiltroTO->getIdCauUf())) {
                    $subQueryIdsChapas->andWhere("chapaEleicaoPorStatus.idCauUf = :idCauUf");
                    $subQueryIdsChapas->setParameter('idCauUf', $chapaEleicaoExtratoFiltroTO->getIdCauUf());
                }

                $query->andWhere("chapaEleicao.id in (" . $subQueryIdsChapas . ")");
                $query->setParameter('idStatusChapa', $chapaEleicaoExtratoFiltroTO->getIdStatus());
            } else {
                $query->andWhere('atividadePrincipal.calendario = :idCalendario');
            }

            $subQueryStatus = $this->getSubQueryStatusChapaConfirmarCriacao('SubQueryConfirmado');
            $query->andWhere("chapaEleicaoStatusConfirmado.id in (" . $subQueryStatus . ")");

            $subQueryStatusAtual = $this->getSubQueryChapaEleicaoStatusAtual("SubQueryAtual");
            $query->andWhere("chapaEleicaoStatusAtual.id in (" . $subQueryStatusAtual . ")");

            $query->setParameter('idCalendario', $chapaEleicaoExtratoFiltroTO->getIdCalendario());

            if(!empty($chapaEleicaoExtratoFiltroTO->getIdCauUf())) {
                $query->andWhere("chapaEleicao.idCauUf = :idCauUf");
                $query->setParameter('idCauUf', $chapaEleicaoExtratoFiltroTO->getIdCauUf());
            }

            if(!empty($chapaEleicaoExtratoFiltroTO->getIdTipoCandidatura())){
                $query->andWhere("tipoCandidatura.id = :idTipoCandidatura");
                $query->setParameter('idTipoCandidatura', $chapaEleicaoExtratoFiltroTO->getIdTipoCandidatura());
            }

            $this->addCondicaoMembroChapaAtual($query);

            $query->orderBy('membrosChapa.numeroOrdem', 'ASC');
            $query->addOrderBy("statusChapaAtual.id", 'DESC');
            $query->addOrderBy("chapaEleicao.numeroChapa", "ASC");
            $query->addOrderBy("chapaEleicaoStatusConfirmado.id", "ASC");

            $chapasEleicao = $query->getQuery()->getArrayResult();

            return array_map(function ($chapaEleicao) {
                return ChapaEleicao::newInstance($chapaEleicao);
            }, $chapasEleicao);
        } catch (NoResultException $e) {
            return null;
        }
    }


    /**
     * Retorna chapas de acordo com o id do calendário.
     *
     * @param integer $idCalendario
     * @param bool $isAddQuantidadeChapaConcluida
     * @return array|null
     */
    public function getQtdChapasCalendario($idCalendario, $isAddQuantidadeChapaConcluida = true)
    {
        $subQueryQuantidadeChapasPendentes = $this->getSubQueryQuantidadeChapasPendentes();

        $select = [
            'chapaEleicao.idCauUf',
            'COUNT(chapaEleicao.id) quantidadeTotalChapas',
            'tipoCandidatura.id idTipoCandidatura',
            "($subQueryQuantidadeChapasPendentes) quantidadeChapasPendentes"
        ];
        if ($isAddQuantidadeChapaConcluida) {
            $subQueryQuantidadeChapasConcluidas = $this->getSubQueryQuantidadeChapasConcluidas();
            array_push($select, "($subQueryQuantidadeChapasConcluidas) quantidadeChapasConcluidas");
        }

        $query = $this->createQueryBuilder("chapaEleicao");
        $query->select($select);
        $query->innerJoin("chapaEleicao.tipoCandidatura", "tipoCandidatura");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
        $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
        $query->innerJoin("atividadePrincipal.calendario", "calendario");
        $query->where('chapaEleicao.excluido <> true');

        $query->andWhere("calendario.id = :idCalendario");
        $query->setParameter('idCalendario', $idCalendario);

        $query->andWhere('chapaEleicao.idEtapa > :etapaMembrosChapaIncluida');
        $query->setParameter('etapaMembrosChapaIncluida', Constants::ETAPA_MEMBROS_CHAPA_INCLUIDA);

        $query->groupBy("chapaEleicao.idCauUf, tipoCandidatura.id");

        $query->setParameter('statusChapaPendente', Constants::SITUACAO_CHAPA_PENDENTE);

        if ($isAddQuantidadeChapaConcluida) {
            $query->setParameter('statusChapaConcluida', Constants::SITUACAO_CHAPA_CONCLUIDA);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Recupera a chapa eleição de acordo com id da atividade e profissional.
     *
     * @param $idAtividadeSeundaria
     * @param $idProfissioal
     * @param $idCauUf
     * @return ChapaEleicao|null
     * @throws Exception
     */
    public function getChapaEleicaoPorAtividadeProfissional($idAtividadeSeundaria,  $idProfissioal, $idCauUf)
    {
        $query = $this->createQueryBuilder("chapaEleicao");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");

        $query->where('chapaEleicao.excluido = false AND chapaEleicao.idCauUf = :idCauUf');
        $query->setParameter('idCauUf', $idCauUf);

        $query->andWhere('chapaEleicao.idProfissionalInclusao = :idProfissionalInclusao');
        $query->setParameter('idProfissionalInclusao', $idProfissioal);

        $query->andWhere('atividadeSecundaria.id = :idAtividadeSecundaria');
        $query->setParameter('idAtividadeSecundaria', $idAtividadeSeundaria);

        $query->setMaxResults(1);

        try {
            $chapaEleicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return ChapaEleicao::newInstance($chapaEleicao);
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Recupera a chapa eleição de acordo com o id e uf do profisional
     *
     * @param integer $idProfissional
     * @param integer $idCauUf
     * @param array|null $inIdEtapa
     * @return ChapaEleicao|null
     * @throws Exception
     */
    public function getChapaEleicaoPorResponsavelInclusao($idProfissional, $idCauUf, array $inIdEtapa = null)
    {
        $query = $this->createQueryBuilder("chapaEleicao");

        $this->addRelacoesConsulta($query);

        $this->addMembrosConsulta($query);

        $query->where('chapaEleicao.excluido = false AND chapaEleicao.idCauUf = :idCauUf');
        $query->setParameter('idCauUf', $idCauUf);

        if(!empty($inIdEtapa)) {
            $query->andWhere('chapaEleicao.idEtapa in (:idsEtapa)');
            $query->setParameter('idsEtapa', $inIdEtapa);
        }

        $query->andWhere('chapaEleicao.idProfissionalInclusao = :idProfissionalInclusao');
        $query->setParameter('idProfissionalInclusao', $idProfissional);

        $this->addCondicaoPeriodoVigenteEleicao($query);
        $this->addCondicaoMembroChapaAtual($query);

        try {
            $chapaEleicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return ChapaEleicao::newInstance($chapaEleicao);
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Recupera a chapa eleição de acordo com o id calendario e uf do profisional
     *
     * @param integer $idProfissional
     * @param integer $idCauUf
     * @return ChapaEleicao|null
     * @throws Exception
     */
    public function getChapaEleicaoPorCalendarioEResponsavel($idCalendario, $idProfissionalResponsavel)
    {
        $query = $this->createQueryBuilder("chapaEleicao");

        $this->addRelacoesConsulta($query);

        $query->innerJoin("atividadePrincipal.calendario", "calendario");
        $query->innerJoin("chapaEleicao.membrosChapa", "membrosChapa");

        $query = $this->atribuirCondicoesResponsavelPorCalendario($idCalendario, $idProfissionalResponsavel, $query);

        $query->setMaxResults(1);

        try {
            $chapaEleicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return ChapaEleicao::newInstance($chapaEleicao);
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Recupera a chapa eleição de acordo com o id chapa e uf do profisional
     *
     * @param integer $idProfissionalResponsavel
     * @param integer $idChapa
     * @return ChapaEleicao|null
     * @throws Exception
     */
    public function getIdChapaEleicaoPorChapaEResponsavel($idChapa, $idProfissionalResponsavel)
    {
        $query = $this->createQueryBuilder("chapaEleicao");
        $query->select("chapaEleicao.id");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundariaCalendario");
        $query->innerJoin("atividadeSecundariaCalendario.atividadePrincipalCalendario", "atividadePrincipal");
        $query->innerJoin("atividadePrincipal.calendario", "calendario");
        $query->innerJoin("chapaEleicao.membrosChapa", "membrosChapa");

        $query = $this->atribuirCondicoesResponsavelPorChapa($idChapa, $idProfissionalResponsavel, $query);

        try {
            return $query->getQuery()->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Recupera a chapa eleição de acordo com o id e uf do profisional
     *
     * @param integer $idProfissional
     * @param integer $idCauUf
     * @return integer|null
     * @throws Exception
     */
    public function getIdChapaEleicaoPorCalendarioEResponsavel($idCalendario, $idProfissionalResponsavel)
    {
        $query = $this->createQueryBuilder("chapaEleicao");
        $query->select("chapaEleicao.id");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundariaCalendario");
        $query->innerJoin("atividadeSecundariaCalendario.atividadePrincipalCalendario", "atividadePrincipal");
        $query->innerJoin("atividadePrincipal.calendario", "calendario");
        $query->innerJoin("chapaEleicao.membrosChapa", "membrosChapa");

        $query = $this->atribuirCondicoesResponsavelPorCalendario($idCalendario, $idProfissionalResponsavel, $query);

        try {
            return $query->getQuery()->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Recupera o ano da eleição pelo id da chapa eleição informada.
     *
     * @return mixed
     * @throws Exception
     */
    public function getAnoEleicaoChapa($idChapa)
    {
        $query = $this->createQueryBuilder('chapaEleicao');
        $query->select('eleicao.ano');

        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
        $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
        $query->innerJoin("atividadePrincipal.calendario", "calendario");
        $query->innerJoin("calendario.eleicao", "eleicao");

        $query->where('chapaEleicao.excluido = false AND chapaEleicao.id = :idChapa');
        $query->setParameter('idChapa', $idChapa);

        $query->setMaxResults(1);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Recupera as chapas da eleição que esteja dentro do período vigente da eleição.
     *
     * @return mixed
     * @throws Exception
     */
    public function getChapasConfirmadasPeriodoVigente()
    {
        $query = $this->createQueryBuilder('chapaEleicao');

        $this->addRelacoesConsulta($query);

        $query->where('chapaEleicao.excluido = false AND chapaEleicao.idEtapa = :idEtapa');
        $query->setParameter('idEtapa', Constants::ETAPA_CRIACAO_CHAPA_CONFIRMADA);

        $this->addCondicaoPeriodoVigenteEleicao($query);

        $arrayChapas = $query->getQuery()->getArrayResult();

        return array_map(static function ($dadosChapa) {
            return ChapaEleicao::newInstance($dadosChapa);
        }, $arrayChapas);
    }

    /**
     * Recupera as chapas da eleição pela data fim da atividade secundária
     *
     * @return mixed
     * @throws Exception
     */
    public function getChapasPorDataFimAtividadeSecundaria(DateTime $dataFimAtividadeSecundaria)
    {
        $query = $this->createQueryBuilder('chapaEleicao');

        $this->addRelacoesConsulta($query);

        $query->where('chapaEleicao.excluido = false AND atividadeSecundariaCalendario.dataFim = :dataFim');
        $query->setParameter('dataFim', $dataFimAtividadeSecundaria->format('Y-m-d'));

        $arrayChapas = $query->getQuery()->getArrayResult();

        return array_map(static function ($dadosChapa) {
            return ChapaEleicao::newInstance($dadosChapa);
        }, $arrayChapas);
    }

    /**
     * Recupera as chapas da eleição que não foram confirmadas a criação após o fim da atividade secundária.
     *
     * @return mixed
     * @throws Exception
     */
    public function getChapasNaoConfirmadasForaPeriodoVigente()
    {
        $query = $this->createQueryBuilder('chapaEleicao');

        $this->addRelacoesConsulta($query);

        $query->where('chapaEleicao.excluido = false AND chapaEleicao.idEtapa <> :idEtapa');
        $query->setParameter('idEtapa', Constants::ETAPA_CRIACAO_CHAPA_CONFIRMADA);

        $query->andWhere('atividadeSecundariaCalendario.dataFim < :now');
        $query->setParameter('now', Utils::getData());

        $arrayChapas = $query->getQuery()->getArrayResult();

        return array_map(static function ($dadosChapa) {
            return ChapaEleicao::newInstance($dadosChapa);
        }, $arrayChapas);
    }

    /**
     * Recupera o total de resposta de declarações referente a uma atividade secundária
     *
     * @param integer $idAtividadeSecundaria
     * @return mixed
     */
    public function getQtdRespostaDeclaracaoPorAtividadeSecundaria($idAtividadeSecundaria)
    {
        try {
            $query = $this->createQueryBuilder("chapaEleicao");
            $query->select('COUNT( chapaEleicao.id )');
            $query->join('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundariaCalendario');

            $query->where('chapaEleicao.excluido = false');
            $query->andWhere("atividadeSecundariaCalendario.id = :idAtividadeSecundariaCalendario");
            $query->setParameter("idAtividadeSecundariaCalendario", $idAtividadeSecundaria);

            $query->andWhere("chapaEleicao.situacaoRespostaDeclaracao = :situacao");
            $query->setParameter('situacao', true);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade dos membros de chapas confirmadas de acordo com o id do calendário e uf.
     *
     * @param integer $idCalendario
     * @param integer $tipoCandidatura
     * @param integer|null $idCauUf
     *
     * @return array|null
     */
    public function getQuantidadeMembrosChapasCalendarioCauUf($idCalendario, $tipoCandidatura, $idCauUf)
    {
        $subQueryChapaEleicaoStatus = $this->getSubQueryStatusChapaAtual("Atual");

        $query = $this->getQueryPorCalendario();
        $query->select([
            'chapaEleicao.idCauUf',
            'chapaEleicao.numeroChapa',
            'chapaEleicao.id idChapaEleicao',
            //"($subQueryChapaEleicaoStatus) idStatusChapa",
            '(' . $this->getSubQueryTotalMembrosConfirmados() . ') quantidadeMembrosConfirmados',
        ]);

        $query->where("chapaEleicao.excluido = false and calendario.id = :idCalendario");
        $query->andWhere('chapaEleicao.tipoCandidatura = :tipoCandidatura');
        $query->andWhere('chapaEleicao.idEtapa > :etapaMembrosChapaIncluida');
        $query->setParameter('idCalendario', $idCalendario);
        $query->setParameter('tipoCandidatura', $tipoCandidatura);
        $query->setParameter('etapaMembrosChapaIncluida', Constants::ETAPA_MEMBROS_CHAPA_INCLUIDA);
        $query->groupBy('idChapaEleicao');

        $query->setParameter(
            "idStatusParticipacaoChapa",
            Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
        );

        if (null !== $idCauUf) {
            $query->andWhere('chapaEleicao.idCauUf = :idCauUf');
            $query->setParameter('idCauUf', $idCauUf);
        }

        $situacoes = [Constants::ST_MEMBRO_CHAPA_CADASTRADO, Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO];
        $query->setParameter('situacoes', $situacoes, Connection::PARAM_INT_ARRAY);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Retorna todas as UFs que possuem chapas vigentes
     *
     * @return array|null
     * @throws Exception
     */
    public function getUfsDeChapas()
    {
        try {
            $dql = "SELECT (CASE WHEN chapaEleicao.tipoCandidatura = 2 THEN 1000 ELSE chapaEleicao.idCauUf END) as idCauUf ";
            $dql .= "FROM App\Entities\ChapaEleicao chapaEleicao ";
            $dql .= "INNER JOIN chapaEleicao.atividadeSecundariaCalendario atividadeSecundaria ";
            $dql .= "INNER JOIN atividadeSecundaria.atividadePrincipalCalendario atividadePrincipalCalendario ";
            $dql .= "INNER JOIN atividadePrincipalCalendario.calendario calendario ";
            $dql .= "LEFT JOIN chapaEleicao.tipoCandidatura tipoCandidatura ";
            $dql .= "WHERE chapaEleicao.excluido = :excluido ";
            $dql .= " AND calendario.dataInicioVigencia <= :dataAtual ";
            $dql .= " AND calendario.dataFimVigencia >= :dataAtual ";
            $dql .= " AND calendario.excluido = :excluido ";
            $dql .= " AND chapaEleicao.numeroChapa is not null ";
            $dql .= " AND calendario.ativo = :ativo ";
            $dql .= " GROUP BY chapaEleicao.idCauUf, chapaEleicao.tipoCandidatura ";
            $dql .= " ORDER BY chapaEleicao.idCauUf ";

            $parametros = [];
            $parametros['excluido'] = false;
            $parametros['dataAtual'] = Utils::getData();
            $parametros['ativo'] = true;

            $query = $this->getEntityManager()->createQuery($dql);
            $query->setParameters($parametros);

            return $query->getArrayResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retornando as chapas ativas por id cau uf
     *
     * @param int $idCauUf
     */
    public function getPorCauUf($idCauUf)
    {
        try{
            $query = $this->createQueryBuilder("chapaEleicao")->addSelect('chapaEleicao');
            $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");

            if ($idCauUf == Constants::IES_ID) {
                $query->where("chapaEleicao.tipoCandidatura = :tipoCand");
            }
            else {
                $query->where("chapaEleicao.idCauUf = :idCauUf");
                $query->setParameter('idCauUf', $idCauUf);
                $query->andWhere('chapaEleicao.tipoCandidatura != :tipoCand');
            }
            $query->andWhere("chapaEleicao.excluido = :excluido");
            $query->andWhere('calendario.excluido = :excluido');
            $query->andWhere('calendario.ativo = :ativo');

            $query->andWhere('calendario.dataInicioVigencia <= :dataHoje');
            $query->andWhere('calendario.dataFimVigencia >= :dataHoje');

            $query->andWhere('chapaEleicao.numeroChapa IS NOT NULL');

            $query->setParameter('excluido', false);
            $query->setParameter('ativo', true);
            $query->setParameter('dataHoje', Utils::getData());
            $query->setParameter('tipoCand', 2);

            $resultado = $query->getQuery()->getArrayResult();

            return array_map(function ($data) {
                return ChapaEleicao::newInstance($data);
            }, $resultado);

        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Retorna as chapas confirmadas de acordo com o id do calendário, tipo de candidatura e cau uf.
     *
     * @param integer $idCalendario
     * @param integer $tipoCandidatura
     * @param bool $isAPenasComJulgamentoFinal
     * @param integer|null $idCauUf
     *
     * @return array|null
     */
    public function getChapasEleicaoPorCalendarioTpCandidaturaAndCauUf(
        $idCalendario,
        $tipoCandidatura,
        $isAPenasComJulgamentoFinal = false,
        $idCauUf = null
    ) {
        $query = $this->getQueryPorCalendario();
        $query->innerJoin("chapaEleicao.tipoCandidatura", "tipoCandidatura")
            ->addSelect("tipoCandidatura");
        $query->innerJoin("chapaEleicao.statusChapaJulgamentoFinal", "statusChapaJulgamentoFinal")
            ->addSelect("statusChapaJulgamentoFinal");
        $query->innerJoin("chapaEleicao.filial", "filial")->addSelect('filial');
        $query->leftJoin("chapaEleicao.membrosChapa", "membrosChapa")->addSelect('membrosChapa');
        $query->leftJoin("membrosChapa.profissional", "profissional")->addSelect('profissional');
        $query->leftJoin("membrosChapa.statusParticipacaoChapa", "statusParticipacaoChapa")
            ->addSelect('statusParticipacaoChapa');
        $query->leftJoin("membrosChapa.statusValidacaoMembroChapa", "statusValidacaoMembroChapa")
            ->addSelect('statusValidacaoMembroChapa');
        $query->leftJoin("chapaEleicao.chapaEleicaoStatus", "chapaEleicaoStatus")
            ->addSelect("chapaEleicaoStatus");
        $query->leftJoin("chapaEleicaoStatus.statusChapa", "statusChapa")
            ->addSelect("statusChapa");

        if ($isAPenasComJulgamentoFinal) {
            $query->innerJoin("chapaEleicao.julgamentoFinal", 'julgamentoFinal');
        }

        $query->where("chapaEleicao.excluido = false and calendario.id = :idCalendario");
        $query->setParameter('idCalendario', $idCalendario);

        $query->andWhere('chapaEleicao.tipoCandidatura = :tipoCandidatura');
        $query->setParameter('tipoCandidatura', $tipoCandidatura);

        $query->andWhere('chapaEleicao.idEtapa > :etapaMembrosChapaIncluida');
        $query->setParameter('etapaMembrosChapaIncluida', Constants::ETAPA_MEMBROS_CHAPA_INCLUIDA);

        if (null !== $idCauUf) {
            $query->andWhere('chapaEleicao.idCauUf = :idCauUf');
            $query->setParameter('idCauUf', $idCauUf);
        }

        $this->addCondicaoMembroChapaAtual($query);

        $query->orderBy("chapaEleicao.numeroChapa", 'ASC');
        $query->addOrderBy("chapaEleicao.id", 'ASC');

        $chapas = $query->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

        return array_map(function ($chapa) {
            return ChapaEleicao::newInstance($chapa);
        }, $chapas);
    }

    /**
     * Retorna as chapas confirmadas de acordo com o id do calendário, tipo de candidatura e cau uf.
     *
     * @param integer $idCalendario
     * @return array|null
     */
    public function getChapasEleicaoPorCalendarioSemJulgamento($idCalendario)
    {
        $query = $this->getQueryPorCalendario();
        $query->leftJoin('chapaEleicao.julgamentoFinal', 'julgamentoFinal');

        $query->where("chapaEleicao.excluido = false");

        $query->andWhere('calendario.id = :idCalendario');
        $query->setParameter('idCalendario', $idCalendario);

        $query->andWhere('chapaEleicao.idEtapa > :etapaMembrosChapaIncluida');
        $query->setParameter('etapaMembrosChapaIncluida', Constants::ETAPA_MEMBROS_CHAPA_INCLUIDA);

        $query->andWhere('julgamentoFinal.id IS NULL');

        return $query->getQuery()->getResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getSubQueryStatusChapaConfirmarCriacao($posfixoAlias)
    {
        $aliasChapaEleicaoStatus = "chapaEleicaoStatus" . $posfixoAlias;

        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select("MIN({$aliasChapaEleicaoStatus}.id)");
        $query->from(ChapaEleicaoStatus::class, $aliasChapaEleicaoStatus);
        $query->where("{$aliasChapaEleicaoStatus}.chapaEleicao = chapaEleicao.id");

        return $query;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getSubQueryStatusChapaAtual($posfixoAlias)
    {
        $aliasChapaEleicaoStatus = "chapaEleicaoStatus" . $posfixoAlias;
        $aliasStatusChapa = "statusChapa" . $posfixoAlias;

        $subQueryChapaEleicaoStatusAtual = $this->getSubQueryChapaEleicaoStatusAtual($posfixoAlias . "02");

        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select("{$aliasStatusChapa}.id");
        $query->from(ChapaEleicaoStatus::class, $aliasChapaEleicaoStatus);
        $query->join("{$aliasChapaEleicaoStatus}.statusChapa", $aliasStatusChapa);
        $query->where("{$aliasChapaEleicaoStatus}.chapaEleicao = chapaEleicao.id");
        $query->andWhere("{$aliasChapaEleicaoStatus}.id = (" . $subQueryChapaEleicaoStatusAtual . ")");

        return $query;
    }

    /**
     * @return string
     */
    private function getSubQueryTotalMembrosConfirmados()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->from(MembroChapa::class, "membroChapaMC");
        $query->select("COUNT(membroChapaMC.id)");
        $query->where("membroChapaMC.chapaEleicao = chapaEleicao.id");
        $query->andWhere("membroChapaMC.statusParticipacaoChapa = :idStatusParticipacaoChapa");
        $query->andWhere('membroChapaMC.situacaoMembroChapa IN (:situacoes)');

        return $query->getQuery()->getDQL();
    }

    private function addRelacoesConsulta(QueryBuilder $query)
    {
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundariaCalendario")
            ->addSelect("atividadeSecundariaCalendario");
        $query->innerJoin("atividadeSecundariaCalendario.atividadePrincipalCalendario", "atividadePrincipal")
            ->addSelect("atividadePrincipal");
        $query->innerJoin("chapaEleicao.tipoCandidatura", "tipoCandidatura")
            ->addSelect("tipoCandidatura");
        $query->innerJoin("chapaEleicao.statusChapaJulgamentoFinal", "statusChapaJulgamentoFinal")
            ->addSelect("statusChapaJulgamentoFinal");
        $query->leftJoin("chapaEleicao.chapaEleicaoStatus", "chapaEleicaoStatus")
            ->addSelect("chapaEleicaoStatus");
        $query->leftJoin("chapaEleicaoStatus.statusChapa", "statusChapa")
            ->addSelect("statusChapa");
        $query->leftJoin("chapaEleicaoStatus.tipoAlteracao", "tipoAlteracao")
            ->addSelect("tipoAlteracao");
        $query->leftJoin("chapaEleicao.redesSociaisChapa", "redesSociaisChapa")
            ->addSelect("redesSociaisChapa");
        $query->leftJoin("redesSociaisChapa.tipoRedeSocial", "tipoRedeSocial")
            ->addSelect("tipoRedeSocial");
    }

    private function addMembrosConsulta(QueryBuilder $query)
    {
        $query->leftJoin('chapaEleicao.membrosChapa', 'membrosChapa')->addSelect('membrosChapa');
        $query->leftJoin('membrosChapa.tipoParticipacaoChapa', 'tipoParticipacaoChapa')
            ->addSelect('tipoParticipacaoChapa');
        $query->leftJoin('membrosChapa.statusParticipacaoChapa', 'statusParticipacaoChapa')
            ->addSelect('statusParticipacaoChapa');
        $query->leftJoin("membrosChapa.statusValidacaoMembroChapa", "statusValidacaoMembroChapa")
            ->addSelect("statusValidacaoMembroChapa");
        $query->leftJoin("membrosChapa.pendencias", "pendencias")->addSelect("pendencias");
        $query->leftJoin("pendencias.tipoPendencia", "tipoPendencia")->addSelect("tipoPendencia");
        $query->leftJoin('membrosChapa.tipoMembroChapa', 'tipoMembroChapa')->addSelect('tipoMembroChapa');
        $query->leftJoin('membrosChapa.respostaDeclaracaoRepresentatividade', 'respostaDeclaracaoRepresentatividade')
            ->addSelect('respostaDeclaracaoRepresentatividade');
        $query->leftJoin('respostaDeclaracaoRepresentatividade.itemDeclaracao', 'itemDeclaracao')
            ->addSelect('itemDeclaracao');
        $this->addLeftJoinProfissional($query, 'membrosChapa');
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getSubQueryIdsChapasEleicaoPorCalendarioEStatus($posfixoAlias)
    {
        $aliasChapaEleicao = "chapaEleicao" . $posfixoAlias;
        $aliasAtvPrincipal = "atividadePrincipal" . $posfixoAlias;
        $aliasAtvSecundaria = "atividadeSecundariaCalendario" . $posfixoAlias;

        $subqueryChapaStatusAtual = $this->getSubQueryChapaEleicaoStatusAtual("PorStatus");

        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select("{$aliasChapaEleicao}.id");
        $query->from(ChapaEleicaoStatus::class, "chapaEleicaoStatus");
        $query->innerJoin("chapaEleicaoStatus.chapaEleicao", $aliasChapaEleicao);
        $query->innerJoin("{$aliasChapaEleicao}.atividadeSecundariaCalendario", $aliasAtvSecundaria);
        $query->innerJoin("{$aliasAtvSecundaria}.atividadePrincipalCalendario", $aliasAtvPrincipal);
        $query->where("{$aliasAtvPrincipal}.calendario = :idCalendario");
        $query->andWhere('chapaEleicaoStatus.id IN (' . $subqueryChapaStatusAtual . ')');
        $query->andWhere('chapaEleicaoStatus.statusChapa = :idStatusChapa');

        return $query;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getSubQueryQuantidadeChapasPendentes()
    {
        $subqueryChapaStatusAtual = $this->getSubQueryChapaEleicaoStatusAtual("Pendente");
        $subqueryChapaStatusAtual->where("chapaEleicaoStatusPendente.chapaEleicao = chapaEleicao2.id");

        $subqueryIdsChapa = $this->getSubQueryIdsChapaEleicaoStatusCalendario('Pendente02');

        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select("count(chapaEleicaoStatus.id)");
        $query->from(ChapaEleicaoStatus::class, "chapaEleicaoStatus");
        $query->innerJoin("chapaEleicaoStatus.chapaEleicao", "chapaEleicao2");
        $query->where("chapaEleicaoStatus.chapaEleicao in (" . $subqueryIdsChapa . ")");
        $query->andWhere('chapaEleicaoStatus.id IN (' . $subqueryChapaStatusAtual . ')');
        $query->andWhere('chapaEleicaoStatus.statusChapa = :statusChapaPendente');

        return $query;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getSubQueryQuantidadeChapasConcluidas()
    {
        $subqueryChapaStatusAtual = $this->getSubQueryChapaEleicaoStatusAtual("Concluida");
        $subqueryChapaStatusAtual->where("chapaEleicaoStatusConcluida.chapaEleicao = chapaEleicao3.id");

        $subqueryIdsChapa = $this->getSubQueryIdsChapaEleicaoStatusCalendario('Concluida02');

        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select("count(chapaEleicaoStatus2.id)");
        $query->from(ChapaEleicaoStatus::class, "chapaEleicaoStatus2");
        $query->innerJoin("chapaEleicaoStatus2.chapaEleicao", "chapaEleicao3");
        $query->where("chapaEleicaoStatus2.chapaEleicao in (" . $subqueryIdsChapa . ")");
        $query->andWhere('chapaEleicaoStatus2.id IN (' . $subqueryChapaStatusAtual . ')');
        $query->andWhere('chapaEleicaoStatus2.statusChapa = :statusChapaConcluida');

        return $query;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getSubQueryChapaEleicaoStatusAtual($posfixoAlias)
    {
        $aliasChapaEleicaoStatus = "chapaEleicaoStatus" . $posfixoAlias;

        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select("MAX({$aliasChapaEleicaoStatus}.id)");
        $query->from(ChapaEleicaoStatus::class, $aliasChapaEleicaoStatus);
        $query->where("{$aliasChapaEleicaoStatus}.chapaEleicao = chapaEleicao.id");

        return $query;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getSubQueryIdsChapaEleicaoStatusCalendario($posfixoAlias)
    {
        $aliasChapaEleicao = "chapaEleicao" . $posfixoAlias;
        $aliasAtvPrincipal = "atividadePrincipal" . $posfixoAlias;
        $aliasAtvSecundaria = "atividadeSecundaria" . $posfixoAlias;

        $query = $this->createQueryBuilder($aliasChapaEleicao);
        $query->innerJoin("{$aliasChapaEleicao}.atividadeSecundariaCalendario", $aliasAtvSecundaria);
        $query->innerJoin("{$aliasAtvSecundaria}.atividadePrincipalCalendario", $aliasAtvPrincipal);
        $query->where("{$aliasAtvPrincipal}.calendario = :idCalendario");
        $query->andWhere("{$aliasChapaEleicao}.excluido <> true");
        $query->andWhere("{$aliasChapaEleicao}.tipoCandidatura = tipoCandidatura.id");
        $query->andWhere("{$aliasChapaEleicao}.idEtapa > :etapaMembrosChapaIncluida");
        $query->andWhere("{$aliasChapaEleicao}.idCauUf = chapaEleicao.idCauUf");

        return $query;
    }

    /**
     * @param QueryBuilder $query
     * @throws Exception
     */
    private function addCondicaoPeriodoVigenteEleicao(QueryBuilder $query): void
    {
        $query->andWhere('atividadeSecundariaCalendario.dataInicio <= :nowInicio');
        $query->setParameter('nowInicio', Utils::getDataHoraZero());

        $query->andWhere('atividadeSecundariaCalendario.dataFim >= :nowFim');
        $query->setParameter('nowFim', Utils::getDataHoraZero());
    }

    /**
     * Retorna a quantidade de chapa criadas com o número e CAU UF informados
     *
     * @param ChapaEleicao $chapaEleicao
     * @param int $numeroChapa
     * @return integer
     */
    public function getCountChapaEleicaoPorNumeroChapaAndCauUf($chapaEleicao, $numeroChapa)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('idCauUf', $chapaEleicao->getIdCauUf()))
            ->andWhere(Criteria::expr()->eq('numeroChapa', $numeroChapa))
            ->andWhere(Criteria::expr()->eq('atividadeSecundariaCalendario', $chapaEleicao->getAtividadeSecundariaCalendario()))
            ->andWhere(Criteria::expr()->eq('tipoCandidatura', $chapaEleicao->getTipoCandidatura()))
            ->andWhere(Criteria::expr()->eq('excluido', false))
            ->andWhere(Criteria::expr()->neq('id', $chapaEleicao->getId()));

        return $this->matching($criteria)->count();
    }

    /**
     * Retorna a quantidade de chapa criadas com o número para uma eleição.
     *
     * @param ChapaEleicao $chapaEleicao
     * @param int $numeroChapa
     * @return integer
     */
    public function getCountChapaEleicaoPorNumeroChapa($chapaEleicao, $numeroChapa)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('numeroChapa', $numeroChapa))
            ->andWhere(Criteria::expr()->eq('atividadeSecundariaCalendario', $chapaEleicao->getAtividadeSecundariaCalendario()))
            ->andWhere(Criteria::expr()->eq('tipoCandidatura', $chapaEleicao->getTipoCandidatura()))
            ->andWhere(Criteria::expr()->eq('excluido', false))
            ->andWhere(Criteria::expr()->neq('id', $chapaEleicao->getId()));

        return $this->matching($criteria)->count();
    }

    /**
     * Método auxiliar que adiciona condição para buscar apenas membros atuais
     * que esteja na situação de cadastrodo ou subsituido deferido
     *
     * @param QueryBuilder $query
     * @return void
     */
    private function addCondicaoMembroChapaAtual(QueryBuilder &$query): void
    {
        $situacoes = [Constants::ST_MEMBRO_CHAPA_CADASTRADO, Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO];
        $query->andWhere('(membrosChapa IS NULL OR membrosChapa.situacaoMembroChapa IN (:situacoes))');
        $query->setParameter('situacoes', $situacoes, Connection::PARAM_INT_ARRAY);
    }

    /**
     * @param $idCalendario
     * @param $idProfissionalResponsavel
     * @param QueryBuilder $query
     * @return QueryBuilder
     */
    public function atribuirCondicoesResponsavelPorCalendario($idCalendario, $idProfissionalResponsavel, QueryBuilder $query): QueryBuilder
    {
        $query->where('chapaEleicao.excluido = false');

        $query->andWhere("calendario.id = :idCalendario");
        $query->setParameter('idCalendario', $idCalendario);

        $query->andWhere("membrosChapa.profissional = :idProfissional");
        $query->setParameter('idProfissional', $idProfissionalResponsavel);

        $query->andWhere("membrosChapa.situacaoResponsavel = :responsavel");
        $query->setParameter("responsavel", true);

        $query->andWhere("membrosChapa.statusParticipacaoChapa = :idStatusParticipacaoChapa");
        $query->setParameter("idStatusParticipacaoChapa", Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO);
        $this->addCondicaoMembroChapaAtual($query);
        return $query;
    }

    /**
     * @param $idCalendario
     * @param $idProfissionalResponsavel
     * @param QueryBuilder $query
     * @return QueryBuilder
     */
    public function atribuirCondicoesResponsavelPorChapa($idChapa, $idProfissionalResponsavel, QueryBuilder $query): QueryBuilder
    {
        $query->where('chapaEleicao.excluido = false');

        $query->andWhere("chapaEleicao.id = :idChapa");
        $query->setParameter('idChapa', $idChapa);

        $query->andWhere("membrosChapa.profissional = :idProfissional");
        $query->setParameter('idProfissional', $idProfissionalResponsavel);

        $query->andWhere("membrosChapa.situacaoResponsavel = :responsavel");
        $query->setParameter("responsavel", true);

        $query->andWhere("membrosChapa.statusParticipacaoChapa = :idStatusParticipacaoChapa");
        $query->setParameter("idStatusParticipacaoChapa", Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO);
        $this->addCondicaoMembroChapaAtual($query);
        return $query;
    }

    /**
     * Método auxiliar para adicionar join para profissional
     *
     * @param QueryBuilder $query
     * @param string $nameAliasMembro
     */
    private function addLeftJoinProfissional(QueryBuilder $query, $nameAliasMembro = 'membroChapa'): void
    {
        $query->leftJoin("{$nameAliasMembro}.profissional", 'profissional')
            ->addSelect('profissional');
        $query->leftJoin('profissional.pessoa', 'pessoa')
            ->addSelect('pessoa');
    }

    /**
     * @param bool $isRetornarAtividadeCalendario
     * @return QueryBuilder
     */
    public function getQueryPorCalendario(bool $isRetornarAtividadeCalendario = false): QueryBuilder
    {
        $query = $this->createQueryBuilder("chapaEleicao");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
        $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
        $query->innerJoin("atividadePrincipal.calendario", "calendario");

        if ($isRetornarAtividadeCalendario) {
            $query->addSelect("atividadeSecundaria")->addSelect("atividadePrincipal")->addSelect("calendario");
        }
        return $query;
    }

    /**
     * Retorna as chapas por UF
     *
     * @param $idCalendario
     * @param $idCauUf
     * @return ChapaEleicao[]
     */
    public function getChapasPorUf($idCalendario, $idCauUf = null)
    {

        $query = $this->getQueryPorCalendario(true)
            ->innerJoin("calendario.eleicao", "eleicao")->addSelect("eleicao")
            ->innerJoin('chapaEleicao.tipoCandidatura', 'tipoCandidatura')->addSelect('tipoCandidatura')
            ->innerJoin('chapaEleicao.filial', 'filial')->addSelect('filial')
            ->leftJoin("chapaEleicao.chapaEleicaoStatus", "chapaEleicaoStatusAtual")->addSelect("chapaEleicaoStatusAtual")
            ->leftJoin("chapaEleicaoStatusAtual.statusChapa", "statusChapaAtual")->addSelect('statusChapaAtual')
            ->leftJoin('chapaEleicao.membrosChapa', 'membrosChapa')->addSelect('membrosChapa')
            ->leftJoin('membrosChapa.tipoParticipacaoChapa', 'tipoParticipacaoChapa')->addSelect('tipoParticipacaoChapa')
            ->leftJoin('membrosChapa.statusParticipacaoChapa', 'statusParticipacaoChapa')->addSelect('statusParticipacaoChapa')
            ->leftJoin('membrosChapa.profissional', 'profissional')->addSelect('profissional')
            ->leftJoin('membrosChapa.pendencias', 'pendencias')->addSelect('pendencias')
            ->leftJoin("pendencias.tipoPendencia", "tipoPendencia")->addSelect('tipoPendencia')
            ->leftJoin('membrosChapa.tipoMembroChapa', 'tipoMembroChapa')->addSelect('tipoMembroChapa')
            ->addOrderBy('chapaEleicao.numeroChapa', 'ASC')
            ->addOrderBy('membrosChapa.numeroOrdem', 'ASC')
            ->addOrderBy("tipoParticipacaoChapa.id", 'ASC');

        $situacoes = [Constants::ST_MEMBRO_CHAPA_CADASTRADO, Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO];
        $query->where('membrosChapa.situacaoMembroChapa IN (:situacoes)');
        $query->setParameter('situacoes', $situacoes, Connection::PARAM_INT_ARRAY);

        $query->andWhere('chapaEleicao.excluido = false');

        $subQueryStatusAtual = $this->getSubQueryChapaEleicaoStatusAtual("SubQueryAtual");
        $query->andWhere("chapaEleicaoStatusAtual.id in (" . $subQueryStatusAtual . ")");

        if(!empty($idCalendario)){
            $query->andWhere('calendario.id = :calendario')
                ->setParameter(':calendario', $idCalendario);
        }

        if(empty($idCauUf)){
            $query->andWhere('chapaEleicao.tipoCandidatura = 2');
        }
        else {
            $query->andWhere('chapaEleicao.tipoCandidatura = 1');
            $query->andWhere('filial.id = :filial')
                ->setParameter(':filial', $idCauUf);
        }

        $sql = $query->getQuery()->getSQL();

        $resultado = $query->getQuery()->getArrayResult();

        return array_map(function ($data) {
            return ChapaEleicao::newInstance($data);
        }, $resultado);
    }

    public function getxportarXMLorCSV($params)
    {
        $query = $this->getQueryPorCalendario(true)
            ->innerJoin("calendario.eleicao", "eleicao")->addSelect("eleicao")
            ->innerJoin("eleicao.tipoProcesso", "tipoProcesso")->addSelect("tipoProcesso")
            ->innerJoin('chapaEleicao.statusChapaJulgamentoFinal', 'statusChapaJulgamentoFinal')->addSelect("statusChapaJulgamentoFinal")
            ->leftJoin('chapaEleicao.filial', 'filial')->addSelect('filial')
            ->leftJoin('chapaEleicao.tipoCandidatura', 'tipoCandidatura')->addSelect('tipoCandidatura')
            ->leftJoin('chapaEleicao.redesSociaisChapa', 'redesSociaisChapa')->addSelect('redesSociaisChapa')
            ->leftJoin('redesSociaisChapa.tipoRedeSocial', 'tipoRedeSocial')->addSelect('tipoRedeSocial')
            ->leftJoin('chapaEleicao.membrosChapa', 'membrosChapa')->addSelect('membrosChapa')
            ->leftJoin('membrosChapa.tipoParticipacaoChapa', 'tipoParticipacaoChapa')->addSelect('tipoParticipacaoChapa')
            ->leftJoin('membrosChapa.profissional', 'profissional')->addSelect('profissional')
            ->leftJoin('membrosChapa.tipoMembroChapa', 'tipoMembroChapa')->addSelect('tipoMembroChapa')
            ->leftJoin('membrosChapa.respostaDeclaracaoRepresentatividade', 'respostaDeclaracaoRepresentatividade')->addSelect('respostaDeclaracaoRepresentatividade')
            ->addOrderBy('chapaEleicao.tipoCandidatura', 'asc')
            ->addOrderBy('filial.prefixo', 'asc')
            ->addOrderBy('chapaEleicao.numeroChapa', 'asc')
            ->addOrderBy('membrosChapa.numeroOrdem', 'asc')
            ->addOrderBy("tipoParticipacaoChapa.id", 'ASC')
            ->addOrderBy("tipoRedeSocial.id", 'ASC');

        if(!empty($params['calendario'])){
            $query->andWhere('calendario.id = :calendario')
                ->setParameter(':calendario', $params['calendario']);
        }

        if($params['statusChapaJulgamentoFinal'] == 0) {
            $statusJulgamentoChapa = [Constants::ID_STATUS_PEDIDO_CHAPA_DEFERIDO,
                Constants::ID_STATUS_PEDIDO_CHAPA_INDEFERIDO];
            $query->andWhere('statusChapaJulgamentoFinal.id in (:statusChapaJulgamentoFinal)');
            $query->setParameter('statusChapaJulgamentoFinal', $statusJulgamentoChapa);
        }
        else{
            $query->andWhere('statusChapaJulgamentoFinal.id = :statusChapaJulgamentoFinal');
            $query->setParameter('statusChapaJulgamentoFinal', $params['statusChapaJulgamentoFinal']);
        }

        $situacoes = [Constants::ST_MEMBRO_CHAPA_CADASTRADO, Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO];
        $query->andWhere('membrosChapa.situacaoMembroChapa IN (:situacoes)');
        $query->setParameter('situacoes', $situacoes, Connection::PARAM_INT_ARRAY);

        $resultado = $query->getQuery()->getArrayResult();

        return array_map(function ($data) {
            return ChapaEleicao::newInstance($data);
        }, $resultado);
    }

    /**
     * Retorna a chapa da eleição conforme o id informado.
     *
     * @param $id
     * @param bool $addMembrosRetorno
     * @param bool $addRelacoesConsulta
     * @param bool $addFilial
     * @return ChapaEleicao|null
     * @throws Exception
     */
    public function getPlataformaAndMeiosPropagandaPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('chapaEleicao');
            $query->innerJoin("chapaEleicao.tipoCandidatura", "tipoCandidatura")
                ->addSelect("tipoCandidatura");
            $query->leftJoin("chapaEleicao.redesSociaisChapa", "redesSociaisChapa")
                ->addSelect("redesSociaisChapa");
            $query->leftJoin("redesSociaisChapa.tipoRedeSocial", "tipoRedeSocial")
                ->addSelect("tipoRedeSocial");

            $query->where("chapaEleicao.excluido = false AND chapaEleicao.id = :id");
            $query->setParameter("id", $id);

            $chapaEleicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return ChapaEleicao::newInstance($chapaEleicao);
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function getxportarCSVTre($idCalendario, $idCauUf = null)
    {

        $query = $this->createQueryBuilder("chapaEleicao");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
        $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
        $query->innerJoin("atividadePrincipal.calendario", "calendario");
        $query->addSelect("atividadeSecundaria")->addSelect("atividadePrincipal")->addSelect("calendario");

        $query->innerJoin("calendario.eleicao", "eleicao")->addSelect("eleicao");
        $query->innerJoin("eleicao.tipoProcesso", "tipoProcesso")->addSelect("tipoProcesso");
        $query->innerJoin('chapaEleicao.statusChapaJulgamentoFinal', 'statusChapaJulgamentoFinal')->addSelect("statusChapaJulgamentoFinal");
        $query->leftJoin('chapaEleicao.filial', 'filial')->addSelect('filial');
        $query->leftJoin('chapaEleicao.tipoCandidatura', 'tipoCandidatura')->addSelect('tipoCandidatura');
        $query->leftJoin('chapaEleicao.redesSociaisChapa', 'redesSociaisChapa')->addSelect('redesSociaisChapa');
        $query->leftJoin('redesSociaisChapa.tipoRedeSocial', 'tipoRedeSocial')->addSelect('tipoRedeSocial');
        $query->leftJoin('chapaEleicao.membrosChapa', 'membrosChapa')->addSelect('membrosChapa');
        $query->leftJoin('membrosChapa.tipoParticipacaoChapa', 'tipoParticipacaoChapa')->addSelect('tipoParticipacaoChapa');
        $query->leftJoin('membrosChapa.profissional', 'profissional')->addSelect('profissional');
        $query->leftJoin('membrosChapa.tipoMembroChapa', 'tipoMembroChapa')->addSelect('tipoMembroChapa');
        $query->addOrderBy('chapaEleicao.numeroChapa', 'asc');
        $query->addOrderBy('membrosChapa.numeroOrdem', 'asc');
        $query->addOrderBy('chapaEleicao.tipoCandidatura', 'asc');
        $query->addOrderBy('filial.prefixo', 'asc');
        $query->addOrderBy("tipoParticipacaoChapa.id", 'ASC');
        $query->addOrderBy("tipoRedeSocial.id", 'ASC');

        $query->andWhere('calendario.id = :calendario')
            ->setParameter(':calendario', $idCalendario);

        $query->andWhere('membrosChapa.situacaoMembroChapa <> 5');

        $query->andWhere('filial.id = :filial')
            ->setParameter(':filial', $idCauUf);

        $query->andWhere('statusChapaJulgamentoFinal.id = :statusChapaJulgamentoFinal')
            ->setParameter('statusChapaJulgamentoFinal', Constants::ID_STATUS_PEDIDO_CHAPA_DEFERIDO);
        $resultado = $query->getQuery()->getArrayResult();

        return array_map(function ($data) {
            return ChapaEleicao::newInstance($data);
        }, $resultado);
    }

    public function getPorIdTre($id, bool $addMembrosRetorno = false, bool $addRelacoesConsulta = true, $addFilial = false)
    {
        try {
            $query = $this->createQueryBuilder('chapaEleicao');

            if ($addRelacoesConsulta) {
                $this->addRelacoesConsulta($query);
            }

            if ($addFilial) {
                $query->innerJoin("chapaEleicao.filial", 'filial')->addSelect("filial");
            }

            $query->where("chapaEleicao.excluido = false AND chapaEleicao.id = :id");
            $query->setParameter("id", $id);

            if ($addMembrosRetorno) {
                $this->addMembrosConsultaTre($query);
                $this->addCondicaoMembroChapaAtual($query);
            }
            
            $query->addOrderBy('membrosChapa.numeroOrdem', 'asc');
            $query->addOrderBy('tipoParticipacaoChapa.id', 'asc');
            $chapaEleicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return ChapaEleicao::newInstance($chapaEleicao);
        } catch (NoResultException $e) {
            return null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    private function addMembrosConsultaTre(QueryBuilder $query)
    {
        $query->leftJoin('chapaEleicao.membrosChapa', 'membrosChapa')->addSelect('membrosChapa');
        $query->leftJoin('membrosChapa.tipoParticipacaoChapa', 'tipoParticipacaoChapa')
            ->addSelect('tipoParticipacaoChapa');
        $query->leftJoin('membrosChapa.statusParticipacaoChapa', 'statusParticipacaoChapa')
            ->addSelect('statusParticipacaoChapa');
        $query->leftJoin("membrosChapa.statusValidacaoMembroChapa", "statusValidacaoMembroChapa")
            ->addSelect("statusValidacaoMembroChapa");
        $query->leftJoin("membrosChapa.pendencias", "pendencias")->addSelect("pendencias");
        $query->leftJoin("pendencias.tipoPendencia", "tipoPendencia")->addSelect("tipoPendencia");
        $query->leftJoin('membrosChapa.tipoMembroChapa', 'tipoMembroChapa')->addSelect('tipoMembroChapa');
        $query->leftJoin('membrosChapa.respostaDeclaracaoRepresentatividade', 'respostaDeclaracaoRepresentatividade')
            ->addSelect('respostaDeclaracaoRepresentatividade');
        $query->leftJoin('respostaDeclaracaoRepresentatividade.itemDeclaracao', 'itemDeclaracao')
            ->addSelect('itemDeclaracao');
        $this->addLeftJoinProfissional($query, 'membrosChapa');
    }

}
