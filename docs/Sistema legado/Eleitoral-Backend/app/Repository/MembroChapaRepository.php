<?php

namespace App\Repository;

use App\Config\Constants;
use App\Entities\MembroChapa;
use App\To\MembroChapaFiltroTO;
use App\Util\Utils;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Exception;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'MembroChapa'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class MembroChapaRepository extends AbstractRepository
{

    /**
     * Retorna a chapa da eleição conforme o id informado.
     *
     * @param $id
     * @return MembroChapa|null
     * @throws NonUniqueResultException
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder("membroChapa");
            $this->addRelacoesConsulta($query);

            $query->leftJoin("membroChapa.suplente", "suplente")->addSelect("suplente");

            $query->where('chapaEleicao.excluido = false AND membroChapa.id = :idMembroChapa');
            $query->setParameter('idMembroChapa', $id);

            $membroChapa = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return MembroChapa::newInstance($membroChapa);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a chapa da eleição conforme o id informado.
     *
     * @param $id
     * @return MembroChapa|null
     * @throws NonUniqueResultException
     */
    public function getMembrosSemFoto()
    {
        try {
            $query = $this->createQueryBuilder("membroChapa");
            $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");
            $query->innerJoin('membroChapa.statusParticipacaoChapa', 'statusParticipacaoChapa')
                ->addSelect('statusParticipacaoChapa');

            $query->where('chapaEleicao.excluido = false');
            $query->andWhere("membroChapa.nomeArquivoFoto IS NULL OR membroChapa.nomeArquivoFoto = ''");

            $query->andWhere('statusParticipacaoChapa.id = :statusParticipacaoChapa');
            $query->setParameter('statusParticipacaoChapa', Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO);

            $this->addCondicaoSituacaoMembroAtual($query);

            return $query->getQuery()->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna os membros confirmados e suas chapas
     *
     * @param $id
     * @return MembroChapa|null
     * @throws NonUniqueResultException
     */
    public function getMembrosConfirmados()
    {
        try {
            $query = $this->createQueryBuilder("membroChapa");
            $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");
            $query->innerJoin('membroChapa.statusParticipacaoChapa', 'statusParticipacaoChapa')
                ->addSelect('statusParticipacaoChapa');

            $query->where('chapaEleicao.excluido = false');
            $query->andWhere("membroChapa.sinteseCurriculo LIKE '%<img%'");

            $query->andWhere('statusParticipacaoChapa.id = :statusParticipacaoChapa');
            $query->setParameter('statusParticipacaoChapa', Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO);

            $this->addCondicaoSituacaoMembroAtual($query);

            return $query->getQuery()->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna os membros de uma chapa eleição conforme o id do profissional informado.
     *
     * @param $idChapaEleicao
     * @param $idProfissional
     *
     * @return MembroChapa|null
     * @throws Exception
     */
    public function getMembroChapaPorProfissional($idChapaEleicao, $idProfissional, $idsSituacoesMembroChapa)
    {
        try {
            $query = $this->createQueryBuilder("membroChapa");
            $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao")
                ->addSelect("chapaEleicao");
            $query->innerJoin("membroChapa.tipoMembroChapa", "tipoMembroChapa")
                ->addSelect("tipoMembroChapa");
            $query->innerJoin("membroChapa.tipoParticipacaoChapa", "tipoParticipacaoChapa")
                ->addSelect("tipoParticipacaoChapa");
            $query->innerJoin("membroChapa.statusParticipacaoChapa", "statusParticipacaoChapa")
                ->addSelect("statusParticipacaoChapa");

            $query->where("chapaEleicao.id = :idChapaEleicao");
            $query->andWhere("membroChapa.profissional = :idProfissional");
            $query->setParameter('idChapaEleicao', $idChapaEleicao);
            $query->setParameter('idProfissional', $idProfissional);

            $query->andWhere("membroChapa.situacaoMembroChapa in (:situacoes)");
            $query->setParameter('situacoes', $idsSituacoesMembroChapa);

            $query->setMaxResults(1);

            $membroChapa = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return MembroChapa::newInstance($membroChapa);
        } catch (NoResultException | NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Realiza a busca pelos membros do mesmo tipo e ordem pelo id da chapa e do profissional
     *
     * @param $idChapaEleicao
     * @param $idProfissional
     * @return array|null
     */
    public function getMembrosTitularESuplentePorChapaIdProfissional($idChapaEleicao, $idProfissional)
    {
        try {
            $query = $this->createQueryBuilder("membroChapa");
            $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao")
                ->addSelect("chapaEleicao");
            $query->innerJoin("membroChapa.tipoMembroChapa", "tipoMembroChapa")
                ->addSelect("tipoMembroChapa");
            $query->innerJoin("membroChapa.tipoParticipacaoChapa", "tipoParticipacaoChapa")
                ->addSelect("tipoParticipacaoChapa");
            $query->innerJoin("membroChapa.statusParticipacaoChapa", "statusParticipacaoChapa")
                ->addSelect("statusParticipacaoChapa");
            $this->addJoinProfissional($query, 'membroChapa');
            $query->leftJoin("membroChapa.statusValidacaoMembroChapa", "statusValidacaoMembroChapa")
                ->addSelect("statusValidacaoMembroChapa");
            $query->leftJoin("membroChapa.pendencias", "pendencias")->addSelect("pendencias");
            $query->leftJoin("pendencias.tipoPendencia", "tipoPendencia")->addSelect("tipoPendencia");

            $query->where("chapaEleicao.id = :idChapaEleicao");

            $queryTipoMembro = $this->getSubQueryPorProfissional(
                'tipoMembroChapaTipoMembro.id',
                'TipoMembro'
            );
            $query->andWhere("tipoMembroChapa.id = ({$queryTipoMembro})");

            $queryNmOrdem = $this->getSubQueryPorProfissional(
                'membroChapaNumeroOrdem.numeroOrdem',
                'NumeroOrdem'
            );
            $query->andWhere("membroChapa.numeroOrdem = ({$queryNmOrdem})");

            $query->setParameter('idChapaEleicao', $idChapaEleicao);
            $query->setParameter('idProfissional', $idProfissional);

            $situacoes = [Constants::ST_MEMBRO_CHAPA_CADASTRADO, Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO];
            $query->setParameter('situacoes', $situacoes, Connection::PARAM_INT_ARRAY);

            $this->addCondicaoSituacaoMembroAtual($query);

            $query->orderBy("tipoParticipacaoChapa.id", 'ASC');

            $membrosChapa = $query->getQuery()->getArrayResult();
            return array_map(function ($dadosMembro) {
                return MembroChapa::newInstance($dadosMembro);
            }, $membrosChapa);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna o valor da coluna de um membro chapa por profissional
     *
     * @param $column
     * @param $alias
     * @return QueryBuilder
     */
    private function getSubQueryPorProfissional($select, $alias)
    {
        $aliasMembroChapa = "membroChapa" . $alias;
        $aliasTipoMembroChapa = "tipoMembroChapa" . $alias;

        $query = $this->createQueryBuilder($aliasMembroChapa);
        $query->select($select);
        $query->innerJoin("{$aliasMembroChapa}.tipoMembroChapa", $aliasTipoMembroChapa);
        $query->where("{$aliasMembroChapa}.chapaEleicao = :idChapaEleicao");
        $query->andWhere("{$aliasMembroChapa}.profissional = :idProfissional");
        $query->andWhere("({$aliasMembroChapa} IS NULL OR {$aliasMembroChapa}.situacaoMembroChapa IN (:situacoes))");

        return $query;
    }

    /**
     * Retorna o membro de uma chapa eleição com status "A Confirmar" conforme filtro e id do profissional informado.
     *
     * @param $idCalendario
     * @param int $idMembroChapa
     * @param int $idProfissional
     *
     * @return MembroChapa
     * @throws NonUniqueResultException
     */
    public function getMembroChapaAConfirmarPorProfissional($idCalendario, int $idMembroChapa, int $idProfissional)
    {
        try {
            $query = $this->createQueryBuilder("membroChapa");
            $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");
            $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");

            $query->where('chapaEleicao.excluido = false');

            $query->andWhere("calendario.id = :idCalendario");
            $query->setParameter('idCalendario', $idCalendario);

            $query->andWhere('membroChapa.id = :idMembroChapa');
            $query->setParameter('idMembroChapa', $idMembroChapa);

            $query->andWhere('membroChapa.profissional = :idProfissional');
            $query->setParameter('idProfissional', $idProfissional);

            $query->andWhere('membroChapa.statusParticipacaoChapa = :statusParticAConfirmar');
            $query->setParameter('statusParticAConfirmar', Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna os membros de chapas eleição com status "A Confirmar" conforme id do profissional informado.
     *
     * @param int $idCalendario
     * @param int $idProfissional
     * @param int $idSituacaoMrmbroChapa
     * @return array|null
     */
    public function getMembrosChapaAConfirmarPorProfissional($idCalendario, $idProfissional, $idsSituacaoMrmbroChapa)
    {
        $query = $this->createQueryBuilder("membroChapa");
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");
        $query->innerJoin("membroChapa.statusParticipacaoChapa", "statusParticipacaoChapa");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
        $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
        $query->innerJoin("atividadePrincipal.calendario", "calendario");

        $query->where('chapaEleicao.excluido = false');

        $query->andWhere("calendario.id = :idCalendario");
        $query->setParameter('idCalendario', $idCalendario);

        $query->andWhere("membroChapa.profissional = :idProfissional");
        $query->setParameter('idProfissional', $idProfissional);

        $query->andWhere('statusParticipacaoChapa.id = :statusParticChapaAConfirmar');
        $query->setParameter('statusParticChapaAConfirmar', Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR);

        $query->andWhere("membroChapa.situacaoMembroChapa IN (1, 3)");

        return $query->getQuery()->getResult();
    }

    /**
     * Retorna os membros de chapas eleição com status "A Confirmar" conforme id do profissional informado.
     *
     * @param int $idCalendario
     * @param int $idProfissional
     * @param int $idSituacaoMrmbroChapa
     * @return array|null
     */
    public function getMembrosChapaAConfirmarDeSubstituicaoJulgFinalPorChapa($idChapaEleicao, $idsSituacaoMrmbroChapa)
    {
        $query = $this->createQueryBuilder("membroChapa");
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");
        $query->innerJoin("membroChapa.statusParticipacaoChapa", "statusParticipacaoChapa");
        $query->innerJoin("membroChapa.membroSubstituicaoJulgamentoFinal", "membroSubstituicaoJulgamentoFinal");

        $query->where('chapaEleicao.excluido = false');

        $query->andWhere("chapaEleicao.id = :idChapaEleicao");
        $query->setParameter('idChapaEleicao', $idChapaEleicao);

        $query->andWhere('statusParticipacaoChapa.id = :statusParticChapaAConfirmar');
        $query->setParameter('statusParticChapaAConfirmar', Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR);

        $query->andWhere("membroChapa.situacaoMembroChapa IN (:situacoes)");
        $query->setParameter('situacoes', $idsSituacaoMrmbroChapa);

        return $query->getQuery()->getResult();
    }

    /**
     * Retorna o membro de uma chapa eleição com status "A Confirmar" conforme filtro e id do profissional informado.
     *
     * @return array|null
     * @throws Exception
     */
    public function getMembrosChapaAConfirmarDiasLimitesConvite()
    {
        $whereDiasLimites = sprintf('(atividadeSecundariaConvite.dataFim - %d) = :now',
            Constants::QUANTIDADE_DIAS_LIMITE_ALERTA_CONVITES_A_CONFIRMAR);

        $query = $this->createQueryBuilder("membroChapa");
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");
        $query->innerJoin("membroChapa.statusParticipacaoChapa", "statusParticipacaoChapa");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
        $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
        $query->innerJoin("atividadePrincipal.atividadesSecundarias", "atividadeSecundariaConvite");
        $this->addJoinProfissional($query, 'membroChapa');
        $query->where('chapaEleicao.excluido = false');

        $query->andWhere("atividadeSecundariaConvite.nivel = :nivelSecundariaConvite");
        $query->setParameter('nivelSecundariaConvite', 2);

        $query->andWhere($whereDiasLimites);
        $query->setParameter('now', Utils::getDataHoraZero());

        //todo: corrigir: passar parâmetro da eleição
        $query->andWhere('statusParticipacaoChapa.id = :statusParticAConfirmar');
        $query->setParameter('statusParticAConfirmar', Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR);

        $this->addCondicaoSituacaoMembroAtual($query);

        return $query->getQuery()->getResult();
    }

    /**
     * Retorna o membro de uma chapa eleição com status "A Confirmar" conforme filtro e id do profissional informado.
     *
     * @param $nivelPrincipal
     * @param $nivelSecundaria
     * @param $idSituacaoMembro
     * @return array|null
     */
    public function getMembrosChapaAConfirmarPrazoExpiradoPorNivelAtividade(
        $nivelPrincipal,
        $nivelSecundaria,
        $idSituacaoMembro,
        $dataFim
    ) {
        $query = $this->getQueryMembrosChapaAConfirmarPrazoExpirado(
            $nivelPrincipal, [$nivelSecundaria], $idSituacaoMembro, $dataFim
        );

        return $query->getQuery()->getResult();
    }

    /**
     *
     *
     * @param $nivelPrincipal
     * @param $nivelSecundaria
     * @param $idSituacaoMembro
     * @return array|null
     */
    public function getMembrosChapaAConfirmarPrazoExpiradoPorImpugnacao(
        $nivelPrincipal,
        $nivelSecundaria,
        $idSituacaoMembro,
        $dataFim
    ) {
        $query = $this->getQueryMembrosChapaAConfirmarPrazoExpirado(
            $nivelPrincipal, [$nivelSecundaria], $idSituacaoMembro, $dataFim
        );

        $query->innerJoin("membroChapa.substituicaoImpugnacao", "substituicaoImpugnacao");

        return $query->getQuery()->getResult();
    }

    /**
     *
     *
     * @param $niveisPrincipal
     * @param $niveisSecundaria
     * @param $idSituacaoMembro
     * @return array|null
     */
    public function getMembrosChapaAConfirmarPrazoExpiradoPorSubstituicao(
        $nivelPrincipal,
        $niveisSecundaria,
        $idSituacaoMembro,
        $dataFim
    ) {
        $query = $this->getQueryMembrosChapaAConfirmarPrazoExpirado(
            $nivelPrincipal, $niveisSecundaria, $idSituacaoMembro, $dataFim
        );

        $query->innerJoin("membroChapa.membroChapaSubstituicao", "membroChapaSubstituicao");

        return $query->getQuery()->getResult();
    }

    /**
     *
     *
     * @param $nivelPrincipal
     * @param $niveisSecundaria
     * @param $idSituacaoMembro
     * @return array|null
     */
    public function getMembrosChapaAConfirmarPrazoExpiradoPorSubstituicaoFinal(
        $nivelPrincipal,
        $niveisSecundaria,
        $idSituacaoMembro,
        $dataFim
    ) {
        $query = $this->getQueryMembrosChapaAConfirmarPrazoExpirado(
            $nivelPrincipal, $niveisSecundaria, $idSituacaoMembro, $dataFim
        );

        $query->innerJoin("membroChapa.membroSubstituicaoJulgamentoFinal", "membroSubstituicaoJulgamentoFinal");

        return $query->getQuery()->getResult();
    }

    /**
     * Retorna o membro de uma chapa eleição com status "A Confirmar" conforme filtro e id do profissional informado.
     *
     * @return array|null
     * @throws Exception
     */
    public function getMembrosChapaAConfirmarCalendarioExpirado()
    {
        $query = $this->createQueryBuilder("membroChapa");
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");
        $query->innerJoin("membroChapa.statusParticipacaoChapa", "statusParticipacaoChapa");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
        $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
        $query->innerJoin("atividadePrincipal.calendario", "calendario");
        $query->innerJoin("calendario.atividadesPrincipais", "atividadesPrincipaisSubstituicao");
        $query->innerJoin("atividadePrincipalSubstituicao.atividadesSecundarias", "atividadeSecundariaSubstituicao");
        $query->innerJoin("atividadePrincipal.atividadesSecundarias", "atividadeSecundariaConvite");

        $query->where('chapaEleicao.excluido = false AND statusParticipacaoChapa.id = :statusParticAConfirmar');
        $query->setParameter('statusParticAConfirmar', Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR);

        $query->andWhere(
            $query->expr()->orX(
                $query->expr()->andX(
                    $query->expr()->eq("atividadeSecundariaConvite.nivel", ":nivelSecundariaConvite"),
                    $query->expr()->lt("atividadeSecundariaConvite.dataFimm", ":nowDataFim"),
                    $query->expr()->eq("membroChapa.situacaoMembroChapa", ":situacaoCadastrado")
                ),
                $query->expr()->andX(
                    $query->expr()->eq("atividadesPrincipaisSubstituicao.nivel", ":nivelPrincipalSubstituicao"),
                    $query->expr()->eq("atividadeSecundariaSubstituicao.nivel", ":nivelSecundariaSubstituicao"),
                    $query->expr()->lt("atividadeSecundariaSubstituicao.dataFimm", ":nowDataFim"),
                    $query->expr()->eq("membroChapa.situacaoMembroChapa", ":situacaoSubstitutoAndamento")
                )
            )
        );

        $query->setParameter('nivelSecundariaConvite', 2);
        $query->setParameter('nivelSecundariaConvite', 3);
        $query->setParameter('nivelSecundariaConvite', 8);
        $query->setParameter('nowDataFim', Utils::getDataHoraZero());
        $query->setParameter('situacaoCadastrado', Constants::ST_MEMBRO_CHAPA_CADASTRADO);
        $query->setParameter('situacaoSubstitutoAndamento', Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO);

        return $query->getQuery()->getResult();
    }

    /**
     * Retorna os membros de uma chapa eleicao conforme os filtros informados
     *
     * @param MembroChapaFiltroTO $membroChapaFiltroTO
     *
     * @return array|null
     */
    public function getMembrosPorFiltro(MembroChapaFiltroTO $membroChapaFiltroTO, $limit = null)
    {
        $query = $this->createQueryBuilder("membroChapa");
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao")
            ->addSelect("chapaEleicao");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria")
            ->addSelect("atividadeSecundaria");
        $query->innerJoin("membroChapa.tipoMembroChapa", "tipoMembroChapa")
            ->addSelect('tipoMembroChapa');
        $query->innerJoin("membroChapa.tipoParticipacaoChapa", "tipoParticipacaoChapa")
            ->addSelect('tipoParticipacaoChapa');
        $query->innerJoin("membroChapa.statusParticipacaoChapa", "statusParticipacaoChapa")
            ->addSelect('statusParticipacaoChapa');
        $query->innerJoin("membroChapa.statusValidacaoMembroChapa", "statusValidacaoMembroChapa")
            ->addSelect('statusValidacaoMembroChapa');
        $query->innerJoin("membroChapa.situacaoMembroChapa", "situacaoMembroChapa")
            ->addSelect("situacaoMembroChapa");
        $this->addJoinProfissional($query, 'membroChapa', 'Titular');
        $query->leftJoin("membroChapa.suplente", "suplente")
            ->addSelect("suplente");
        $this->addJoinProfissional($query, 'membroChapa');
        $query->where('1 = 1');

        if (!empty($membroChapaFiltroTO->getIdChapaEleicao())) {
            $query->andWhere("chapaEleicao.id = :idChapaEleicao");
            $query->setParameter('idChapaEleicao', $membroChapaFiltroTO->getIdChapaEleicao());
        }

        if (!empty($membroChapaFiltroTO->getNumeroOrdem())) {
            $query->andWhere('membroChapa.numeroOrdem = :numeroOrdem');
            $query->setParameter('numeroOrdem', $membroChapaFiltroTO->getNumeroOrdem());
        }

        if (!is_null($membroChapaFiltroTO->getSituacaoResponsavel())) {
            $query->andWhere('membroChapa.situacaoResponsavel = :situacaoResponsavel');
            $query->setParameter('situacaoResponsavel', $membroChapaFiltroTO->getSituacaoResponsavel());
        }

        if (!empty($membroChapaFiltroTO->getIdTipoMembro())) {
            $query->andWhere('tipoMembroChapa.id = :idTipoMembro');
            $query->setParameter('idTipoMembro', $membroChapaFiltroTO->getIdTipoMembro());
        }

        if (!empty($membroChapaFiltroTO->getIdTipoParticipacaoChapa())) {
            $query->andWhere('tipoParticipacaoChapa.id = :idTipoParticipacaoChapa');
            $query->setParameter('idTipoParticipacaoChapa', $membroChapaFiltroTO->getIdTipoParticipacaoChapa());
        }

        if (!empty($membroChapaFiltroTO->getIdStatusParticipacaoChapa())) {
            $query->andWhere('statusParticipacaoChapa.id = :idStatusParticipacaoChapa');
            $query->setParameter('idStatusParticipacaoChapa', $membroChapaFiltroTO->getIdStatusParticipacaoChapa());
        }

        if (!empty($membroChapaFiltroTO->getIdCalendario())) {
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");

            $query->andWhere('atividadePrincipal.calendario = :idCalendario');
            $query->setParameter('idCalendario', $membroChapaFiltroTO->getIdCalendario());
        }

        if (!empty($membroChapaFiltroTO->getIdCauUf())) {
            if ($membroChapaFiltroTO->getIdCauUf() == Constants::IES_ID) {
                $query->andWhere('chapaEleicao.tipoCandidatura = :tipoCandidatura');
            }
            else {
                $query->andWhere('chapaEleicao.filial = :idCauUf');
                $query->setParameter('idCauUf', $membroChapaFiltroTO->getIdCauUf());
                $query->andWhere('chapaEleicao.tipoCandidatura != :tipoCandidatura');
            }
            $query->setParameter('tipoCandidatura', 2);
        }

        if ($membroChapaFiltroTO->isIncluirSuplenteConsulta()) {
            $query->leftJoin("suplente.chapaEleicao", "chapaEleicaoSuplente")
                ->addSelect("chapaEleicaoSuplente");
            $query->leftJoin("chapaEleicaoSuplente.atividadeSecundariaCalendario", "atividadeSecundariaSuplente")
                ->addSelect("atividadeSecundariaSuplente");
            $query->leftJoin("suplente.tipoMembroChapa", "tipoMembroChapaSuplente")
                ->addSelect('tipoMembroChapaSuplente');
            $query->leftJoin("suplente.tipoParticipacaoChapa", "tipoParticipacaoChapaSuplente")
                ->addSelect('tipoParticipacaoChapaSuplente');
            $query->leftJoin("suplente.statusParticipacaoChapa", "statusParticipacaoChapaSuplente")
                ->addSelect('statusParticipacaoChapaSuplente');
            $this->addLeftJoinProfissional($query, 'suplente', 'Suplente');
        }

        $this->addCondicaoSituacaoMembroAtual($query);

        if (!empty($limit)) {
            $query->setMaxResults($limit);
        }

        $arrayMembrosChapa = $query->getQuery()->getArrayResult();

        return array_map(function ($dadosMembroChapa) {
            return MembroChapa::newInstance($dadosMembroChapa);
        }, $arrayMembrosChapa);
    }

    /**
     * Retorna os membros de uma chapa eleicao conforme os filtros informados
     *
     * @param MembroChapaFiltroTO $membroChapaFiltroTO
     *
     * @return array|null
     */
    public function getMembrosResponsaveisPorCalendarioAndTipoCandidaturaAndCauUF($idCalendario, $idTipoCandidatura, $idCauUf = null)
    {
        $query = $this->createQueryBuilder("membroChapa");
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao")
            ->addSelect("chapaEleicao");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria")
            ->addSelect("atividadeSecundaria");
        $query->innerJoin("membroChapa.tipoMembroChapa", "tipoMembroChapa")
            ->addSelect('tipoMembroChapa');
        $query->innerJoin("membroChapa.tipoParticipacaoChapa", "tipoParticipacaoChapa")
            ->addSelect('tipoParticipacaoChapa');
        $query->innerJoin("membroChapa.statusParticipacaoChapa", "statusParticipacaoChapa")
            ->addSelect('statusParticipacaoChapa');
        $query->innerJoin("membroChapa.statusValidacaoMembroChapa", "statusValidacaoMembroChapa")
            ->addSelect('statusValidacaoMembroChapa');
        $query->innerJoin("membroChapa.situacaoMembroChapa", "situacaoMembroChapa")
            ->addSelect("situacaoMembroChapa");
        $this->addJoinProfissional($query, 'membroChapa', 'Titular');
        $query->leftJoin("membroChapa.suplente", "suplente")
            ->addSelect("suplente");
        $query->where('1 = 1');

        $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
        $query->andWhere('atividadePrincipal.calendario = :idCalendario');
        $query->setParameter('idCalendario', $idCalendario);

        $query->andWhere('chapaEleicao.tipoCandidatura = :tipoCandidatura');
        $query->setParameter('tipoCandidatura', $idTipoCandidatura);

        $query->andWhere('membroChapa.situacaoResponsavel = :situacaoResponsavel');
        $query->setParameter('situacaoResponsavel', true);

        $query->andWhere('statusParticipacaoChapa.id = :statusParticChapa');
        $query->setParameter('statusParticChapa', Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO);

        if (!empty($idCauUf)) {
            $query->andWhere('chapaEleicao.filial = :idCauUf');
            $query->setParameter('idCauUf', $idCauUf);
        }

        $this->addCondicaoSituacaoMembroAtual($query);

        $arrayMembrosChapa = $query->getQuery()->getArrayResult();

        return array_map(function ($dadosMembroChapa) {
            return MembroChapa::newInstance($dadosMembroChapa);
        }, $arrayMembrosChapa);
    }

    /**
     * Retorna os membros responsaveis de chapas confirmadas de acordo com o id do calendário e uf.
     *
     * @param integer $idCalendario
     * @param integer $tipoCandidatura
     * @param integer|null $idCauUf
     *
     * @return array|null
     */
    public function getMembrosResponsaveisChapasCalendarioCauUf($idCalendario, $tipoCandidatura, $idCauUf)
    {
        $query = $this->createQueryBuilder("membroChapa");
        $query->select([
            'chapaEleicao.idCauUf',
            'profissional.id idProfissional',
            'chapaEleicao.id idChapaEleicao'
        ]);
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");
        $query->innerJoin("membroChapa.profissional", "profissional");
        $query->innerJoin("membroChapa.tipoParticipacaoChapa", "tipoParticipacaoChapa");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
        $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
        $query->innerJoin("atividadePrincipal.calendario", "calendario");
        $query->where("chapaEleicao.excluido = false AND calendario.id = :idCalendario");
        $query->andWhere('chapaEleicao.tipoCandidatura = :tipoCandidatura');
        $query->andWhere('chapaEleicao.idEtapa > :etapaMembrosChapaIncluida');
        $query->andWhere('membroChapa.situacaoResponsavel = :membroResponsavel');
        $query->setParameter('idCalendario', $idCalendario);
        $query->setParameter('tipoCandidatura', $tipoCandidatura);
        $query->setParameter('membroResponsavel', Constants::MEMBRO_CHAPA_RESPONSAVEL);
        $query->setParameter('etapaMembrosChapaIncluida', Constants::ETAPA_MEMBROS_CHAPA_INCLUIDA);
        $query->orderBy('membroChapa.numeroOrdem');
        $query->addOrderBy('tipoParticipacaoChapa.id');

        if (null !== $idCauUf) {
            $query->andWhere('chapaEleicao.idCauUf = :idCauUf');
            $query->setParameter('idCauUf', $idCauUf);
        }

        $this->addCondicaoSituacaoMembroAtual($query);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Retorna Lista de membros responsáveis da chapa.
     *
     * @param $idChapa
     * @return array
     */
    public function getIdsMembrosResponsaveisPorChapa($idChapa)
    {
        $query = $this->createQueryBuilder("membroChapa");
        $query->select([
            'membroChapa.id',
        ]);
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");

        $query->where("chapaEleicao.excluido = false ");
        $query->andWhere('chapaEleicao.id = :idChapa');
        $query->andWhere('membroChapa.situacaoResponsavel = :membroResponsavel');

        $query->setParameter('membroResponsavel', Constants::MEMBRO_CHAPA_RESPONSAVEL);
        $query->setParameter('idChapa', $idChapa);

        $this->addCondicaoSituacaoMembroAtual($query);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Retorna primeiro responsavel de chapa com nome.
     *
     * @param $idChapa
     * @return string|null
     */
    public function getPrimeiroResponsavelPorChapa($idChapa)
    {
        $query = $this->createQueryBuilder("membroChapa");
        $query->select([
            'profissional.nome',
        ]);
        $query->innerJoin("membroChapa.profissional", "profissional");
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");

        $query->where("chapaEleicao.excluido = false ");
        $query->andWhere('chapaEleicao.id = :idChapa');
        $query->andWhere('membroChapa.situacaoResponsavel = :membroResponsavel');

        $query->setParameter('membroResponsavel', Constants::MEMBRO_CHAPA_RESPONSAVEL);
        $query->setParameter('idChapa', $idChapa);

        $this->addCondicaoSituacaoMembroAtual($query);

        $query->orderBy('membroChapa.numeroOrdem', 'ASC');

        $query->setMaxResults(1);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Retorna os membro chapa da eleição vigente, de acordo com o id do profissional e uf.
     * Considera apenas os membros que não rejeitaram o convite.
     *
     * @param $idCalendario
     * @param integer $idProfissioanl
     * @param integer|null $idCauUf
     *
     * @param null $idsSituacoes
     * @return array|null
     */
    public function getMembrosChapaPorCalendarioProfissioal(
        $idCalendario,
        $idProfissioanl,
        $idCauUf,
        $idsSituacoes = null
    ) {
        try {
            $query = $this->createQueryBuilder("membroChapa");
            $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao")->addSelect("chapaEleicao");
            $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");
            $query->innerJoin('membroChapa.statusParticipacaoChapa', 'statusParticipacaoChapa')
                ->addSelect('statusParticipacaoChapa');

            $query->where("chapaEleicao.excluido = false AND membroChapa.profissional = :idProfissional");
            $query->setParameter('idProfissional', $idProfissioanl);

            $query->andWhere('calendario.id = :idCalendario');
            $query->setParameter('idCalendario', $idCalendario);

            $query->andWhere('statusParticipacaoChapa.id IN (:IdsStatusParticipacaoChapa)');
            $query->setParameter('IdsStatusParticipacaoChapa', [
                Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR,
                Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
            ]);

            $query->andWhere('(chapaEleicao.tipoCandidatura = :idTipoCandidaturaIES OR (chapaEleicao.tipoCandidatura = :idTipoCandidaturaUF AND chapaEleicao.idCauUf = :idCauUf))');
            $query->setParameter('idTipoCandidaturaIES', Constants::TIPO_CANDIDATURA_IES);
            $query->setParameter('idTipoCandidaturaUF', Constants::TIPO_CANDIDATURA_CONSELHEIROS_UF_BR);
            $query->setParameter('idCauUf', $idCauUf);

            if (empty($idsSituacoes)) {
                $this->addCondicaoSituacaoMembroAtual($query);
            } else {
                $query->andWhere("membroChapa.situacaoMembroChapa in (:idsSituacoes)");
                $query->setParameter("idsSituacoes", $idsSituacoes);
            }

            $arrayMembrosChapa = $query->getQuery()->getArrayResult();

            return array_map(function ($dadosMembroChapa) {
                return MembroChapa::newInstance($dadosMembroChapa);
            }, $arrayMembrosChapa);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna os membro chapa da eleição vigente, de acordo com o id do profissional e uf.
     * Considera apenas os membros que não rejeitaram o convite.
     *
     * @param $idCalendario
     * @param integer $idProfissioanl
     *
     * @return MembroChapa|null
     */
    public function getMembroChapaAtualPorCalendarioProfissioal($idCalendario, $idProfissioanl)
    {
        try {
            $query = $this->createQueryBuilder("membroChapa");
            $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao")->addSelect("chapaEleicao");
            $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");
            $query->innerJoin('membroChapa.statusParticipacaoChapa', 'statusParticipacaoChapa')
                ->addSelect('statusParticipacaoChapa');

            $query->where("chapaEleicao.excluido = false AND membroChapa.profissional = :idProfissional");
            $query->setParameter('idProfissional', $idProfissioanl);

            $query->andWhere('calendario.id = :idCalendario');
            $query->setParameter('idCalendario', $idCalendario);

            $query->andWhere('statusParticipacaoChapa.id = :statusParticipacaoChapa');
            $query->setParameter('statusParticipacaoChapa', Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO);

            $this->addCondicaoSituacaoMembroAtual($query);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna os membro chapa do calendário informado, de acordo com o id do profissional.
     * Considera apenas os membros que confirmaram participação.
     *
     * @param $idCalendario
     * @param integer $idProfissioanl
     *
     * @return MembroChapa|null
     * @throws Exception
     */
    public function getMembroConfirmadoPorCalendarioProfissioal($idCalendario, $idProfissioanl)
    {
        try {
            $query = $this->createQueryBuilder("membroChapa");

            $this->addRelacoesConsulta($query);

            $query->innerJoin("chapaEleicao.tipoCandidatura", "tipoCandidatura")->addSelect("tipoCandidatura");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");
            $query->leftJoin("chapaEleicao.chapaEleicaoStatus", "chapaEleicaoStatus")
                ->addSelect("chapaEleicaoStatus");
            $query->leftJoin("chapaEleicaoStatus.statusChapa", "statusChapa")
                ->addSelect("statusChapa");

            $query->where("chapaEleicao.excluido = false AND membroChapa.profissional = :idProfissional");
            $query->setParameter('idProfissional', $idProfissioanl);

            $query->andWhere('calendario.id = :idCalendario');
            $query->setParameter('idCalendario', $idCalendario);

            $query->andWhere('statusParticipacaoChapa.id = :isStatusParticipacaoChapa');
            $query->setParameter(
                'isStatusParticipacaoChapa', Constants::STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO
            );

            $this->addCondicaoSituacaoMembroAtual($query);

            $query->getQuery()->setMaxResults(1);

            $membroChapa = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return MembroChapa::newInstance($membroChapa);
        } catch (NoResultException | NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Retorna todos os convites para chapa que o usuário informado recebeu.
     *
     * @param integer $idProfissional
     * @param int $idCalendario
     * @return array|null
     */
    public function getConvitesUsuario(int $idProfissional, int $idCalendario)
    {
        $query = $this->getQueryConvite($idProfissional, $idCalendario);

        $this->addCondicaoSituacaoMembroAtual($query);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Retorna todos os convites para chapa que o usuário informado recebeu.
     *
     * @param integer $idProfissional
     * @param int $idCalendario
     * @return array|null
     */
    public function getConvitesUsuarioSubstituicaoImpugnacao(int $idProfissional, int $idCalendario)
    {
        $query = $this->getQueryConvite($idProfissional, $idCalendario);

        $query->innerJoin("membroChapa.substituicaoImpugnacao", "substituicaoImpugnacao");
        $query->innerJoin("substituicaoImpugnacao.pedidoImpugnacao", "pedidoImpugnacao");

        $query->andWhere('membroChapa.situacaoMembroChapa = :situacaoMembroSubstitutoAndamento');

        $condPrcedente = 'pedidoImpugnacao.statusPedidoImpugnacao = :statusProcedente';
        $condRecursoProcedente = 'pedidoImpugnacao.statusPedidoImpugnacao = :statusRecursoProcedente';
        $query->andWhere("{$condPrcedente} OR {$condRecursoProcedente}");

        $query->setParameter('situacaoMembroSubstitutoAndamento', Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO);
        $query->setParameter('statusProcedente', Constants::STATUS_IMPUGNACAO_PROCEDENTE);
        $query->setParameter('statusRecursoProcedente', Constants::STATUS_IMPUGNACAO_RECURSO_PROCEDENTE);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Retorna todos os convites para chapa que o usuário informado recebeu.
     *
     * @param integer $idProfissional
     * @param int $idCalendario
     * @return array|null
     */
    public function getConvitesUsuarioPedidoSubstituicao(int $idProfissional, int $idCalendario, $idStatusPedido)
    {
        $query = $this->getQueryConvite($idProfissional, $idCalendario);

        $query->innerJoin("membroChapa.membroChapaSubstituicao", "membroChapaSubstituicao");
        $query->innerJoin("membroChapaSubstituicao.pedidoSubstituicaoChapa", "pedidoSubstituicaoChapa");

        $query->andWhere('membroChapa.situacaoMembroChapa = :situacaoMembroSubstitutoAndamento');

        $query->andWhere("pedidoSubstituicaoChapa.statusSubstituicaoChapa = :statusDeferido");

        $query->setParameter('situacaoMembroSubstitutoAndamento', Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO);
        $query->setParameter('statusDeferido', $idStatusPedido);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Retorna todos os convites para chapa que o usuário informado recebeu.
     *
     * @param integer $idProfissional
     * @param int $idCalendario
     * @return array|null
     */
    public function getConvitesUsuarioSubstituicaoJulgFinal(int $idProfissional, int $idCalendario)
    {
        $query = $this->getQueryConvite($idProfissional, $idCalendario);

        $query->innerJoin("membroChapa.membroSubstituicaoJulgamentoFinal", "membroSubstituicaoJulgamentoFinal");

        $query->andWhere('membroChapa.situacaoMembroChapa = :situacaoMembroSubstitutoAndamento');

        $query->setParameter('situacaoMembroSubstitutoAndamento', Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Retorna total de convite para participar de uma chapa pelo status de participacao
     *
     * @param integer $idProfissional
     * @param integer $idAtividadeSecundaria
     * @param integer $idStatusParticipacao
     *
     * @return integer
     * @throws NonUniqueResultException
     */
    public function totalConvitePorStatusParticipacao($idProfissional, $idAtividadeSecundaria, $idStatusParticipacao)
    {
        $query = $this->createQueryBuilder('membroChapa');
        $query->select(" COUNT( membroChapa.id ) ");
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");
        $query->innerJoin("membroChapa.statusParticipacaoChapa", "statusParticipacaoChapa");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundariaCalendario");

        $query->where("chapaEleicao.excluido = false");

        $query->andWhere("membroChapa.profissional = :idProfissional");
        $query->setParameter("idProfissional", $idProfissional);

        $query->andWhere("atividadeSecundariaCalendario.id = :idAtividadeSecundaria");
        $query->setParameter("idAtividadeSecundaria", $idAtividadeSecundaria);

        if (!empty($idStatusParticipacao)) {
            $query->andWhere("statusParticipacaoChapa.id = :idStatusParticipacaoChapa");
            $query->setParameter("idStatusParticipacaoChapa", $idStatusParticipacao);
        }

        $this->addCondicaoSituacaoMembroAtual($query);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Retorna total de convite para participar de uma chapa pelo id da chapa e pelo status de participacao
     *
     * @param integer $idChapaEleicao
     * @param integer $idStatusParticipacao
     *
     * @return integer
     * @throws NonUniqueResultException
     */
    public function totalConvitesChapaPorStatusParticipacao($idChapaEleicao, $idStatusParticipacao)
    {
        $query = $this->createQueryBuilder('membroChapa');
        $query->select(" COUNT( membroChapa.id ) ");
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");
        $query->innerJoin("membroChapa.statusParticipacaoChapa", "statusParticipacaoChapa");

        $query->where("chapaEleicao.id = :idChapaEleicao");
        $query->setParameter("idChapaEleicao", $idChapaEleicao);

        $query->andWhere("statusParticipacaoChapa.id = :idStatusParticipacaoChapa");
        $query->setParameter("idStatusParticipacaoChapa", $idStatusParticipacao);

        $this->addCondicaoSituacaoMembroAtual($query);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Retorna total de membros incluídos em uma chapa
     *
     * @param integer $idChapaEleicao
     * @param bool $ignorarStatusParticipacaoRejeitado
     *
     * @return integer
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function totalMembrosChapa($idChapaEleicao, $ignorarStatusParticipacaoRejeitado = false)
    {
        $query = $this->createQueryBuilder('membroChapa');
        $query->select(" COUNT( membroChapa.id ) ");
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");

        $query->where("chapaEleicao.id = :idChapaEleicao");
        $query->setParameter("idChapaEleicao", $idChapaEleicao);

        if (!empty($ignorarStatusParticipacaoRejeitado)) {
            $query->innerJoin("membroChapa.statusParticipacaoChapa", "statusParticipacaoChapa");
            $query->andWhere("statusParticipacaoChapa.id <> :idStatusParticipacaoChapa");
            $query->setParameter("idStatusParticipacaoChapa", Constants::STATUS_PARTICIPACAO_MEMBRO_REJEITADO);
        }

        $this->addCondicaoSituacaoMembroAtual($query);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Retorna total de membros responsáveis de uma chapa
     *
     * @param integer $idChapaEleicao
     * @param null $idTipoMembro
     * @param null $idTipoParticipacao
     * @param null $numeroOrdem
     *
     * @return integer
     * @throws NonUniqueResultException
     */
    public function totalMembrosResponsaveisChapa(
        $idChapaEleicao,
        $idTipoMembro = null,
        $idTipoParticipacao = null,
        $numeroOrdem = null
    ) {
        $query = $this->createQueryBuilder('membroChapa');
        $query->select(" COUNT( membroChapa.id ) ");
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");

        $query->where("chapaEleicao.id = :idChapaEleicao");
        $query->setParameter("idChapaEleicao", $idChapaEleicao);

        if (!empty($idTipoMembro)) {
            $query->andWhere("membroChapa.tipoMembroChapa = :idTipoMembroChapa");
            $query->setParameter("idTipoMembroChapa", $idTipoMembro);
        }

        if (!empty($idTipoParticipacao)) {
            $query->andWhere("membroChapa.tipoParticipacaoChapa = :idTipoParticipacaoChapa");
            $query->setParameter("idTipoParticipacaoChapa", $idTipoParticipacao);
        }

        if (!empty($numeroOrdem)) {
            $query->andWhere("membroChapa.numeroOrdem = :numeroOrdem");
            $query->setParameter("numeroOrdem", $numeroOrdem);
        }

        $query->andWhere("membroChapa.situacaoResponsavel = :situacaoResponsavel");
        $query->setParameter("situacaoResponsavel", true);

        $this->addCondicaoSituacaoMembroAtual($query);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Recupera o total de resposta de declaração referente a uma atividade principal
     *
     * @param integer $idAtividadePrincipal
     * @return mixed
     */
    public function getQtdRespostaDeclaracaoPorAtividadePrincipal($idAtividadePrincipal)
    {
        try {
            $query = $this->createQueryBuilder("membroChapa");
            $query->select('COUNT( membroChapa.id )');
            $query->join('membroChapa.chapaEleicao', 'chapaEleicao');
            $query->join('chapaEleicao.atividadeSecundariaCalendario', 'atividadeSecundaria');
            $query->join('atividadeSecundaria.atividadePrincipalCalendario', 'atividadePrincipal');

            $query->where("atividadePrincipal.id = :idAtividadePrincipal");
            $query->setParameter("idAtividadePrincipal", $idAtividadePrincipal);

            $query->andWhere("membroChapa.situacaoRespostaDeclaracao = :situacao");
            $query->setParameter('situacao', true);

            $this->addCondicaoSituacaoMembroAtual($query);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Retorna total de membros com representatividade incluídos em uma chapa
     *
     * @param integer $idChapaEleicao
     * @param bool $ignorarStatusParticipacaoRejeitado
     *
     * @return integer
     * @throws NonUniqueResultException
     */
    public function totalMembrosRepresentatividadeChapa($idChapaEleicao, $ignorarStatusParticipacaoRejeitado = false): int
    {
        $query = $this->createQueryBuilder('membroChapa');
        $query->select(" COUNT(DISTINCT membroChapa.id) ");
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");
        $query->innerJoin("membroChapa.respostaDeclaracaoRepresentatividade", "respostaDeclaracaoRepresentatividade");

        $query->where("chapaEleicao.id = :idChapaEleicao");
        $query->setParameter("idChapaEleicao", $idChapaEleicao);

        if (!empty($ignorarStatusParticipacaoRejeitado)) {
            $query->innerJoin("membroChapa.statusParticipacaoChapa", "statusParticipacaoChapa");
            $query->andWhere("statusParticipacaoChapa.id <> :idStatusParticipacaoChapa");
            $query->setParameter("idStatusParticipacaoChapa", Constants::STATUS_PARTICIPACAO_MEMBRO_REJEITADO);
        }

        $this->addCondicaoSituacaoMembroAtual($query);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param QueryBuilder $query
     * @throws Exception
     */
    private function addCondicaoPeriodoVigenciaConvite(QueryBuilder $query): void
    {
        $query->innerJoin(
            "atividadePrincipal.atividadesSecundarias",
            "atividadeSecundariaConvite"
        );

        $query->andWhere("atividadeSecundariaConvite.nivel = :nivelSecundariaConvite");
        $query->setParameter('nivelSecundariaConvite', 2);
        $query->andWhere("atividadeSecundariaConvite.dataInicio <= :nowDataInicio");
        $query->setParameter('nowDataInicio', Utils::getDataHoraZero());
        $query->andWhere("atividadeSecundariaConvite.dataFim >= :nowDataFim");
        $query->setParameter('nowDataFim', Utils::getDataHoraZero());
    }

    /**
     * @param QueryBuilder $query
     */
    private function addCondicaoSituacaoMembroAtual(QueryBuilder $query): void
    {
        $condStMembroCadastrado = "membroChapa.situacaoMembroChapa = :situacaoMembroCadastrado";
        $condStMembroSubsDeferido = "membroChapa.situacaoMembroChapa = :situacaoMembroSubstitutoDeferido";

        $query->andWhere("{$condStMembroCadastrado} OR {$condStMembroSubsDeferido}");

        $query->setParameter('situacaoMembroCadastrado', Constants::ST_MEMBRO_CHAPA_CADASTRADO);
        $query->setParameter(
            'situacaoMembroSubstitutoDeferido',
            Constants::ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO
        );
    }

    /**
     * @param QueryBuilder $query
     */
    private function addRelacoesConsulta(QueryBuilder $query): void
    {
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao")->addSelect("chapaEleicao");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria")
            ->addSelect("atividadeSecundaria");
        $query->innerJoin("membroChapa.tipoMembroChapa", "tipoMembroChapa")
            ->addSelect("tipoMembroChapa");
        $query->innerJoin("membroChapa.tipoParticipacaoChapa", "tipoParticipacaoChapa")
            ->addSelect("tipoParticipacaoChapa");
        $query->innerJoin("membroChapa.statusParticipacaoChapa", "statusParticipacaoChapa")
            ->addSelect("statusParticipacaoChapa");
        $query->innerJoin("membroChapa.statusValidacaoMembroChapa", "statusValidacaoMembroChapa")
            ->addSelect("statusValidacaoMembroChapa");
        $query->innerJoin("membroChapa.situacaoMembroChapa", "situacaoMembroChapa")
            ->addSelect("situacaoMembroChapa");
        $this->addJoinProfissional($query, 'membroChapa');
        $query->leftJoin("membroChapa.pendencias", "pendencias")->addSelect("pendencias");
        $query->leftJoin("pendencias.tipoPendencia", "tipoPendencia")->addSelect("tipoPendencia");
    }

    /**
     * Método auxiliar para adicionar join para profissional
     *
     * @param QueryBuilder $query
     * @param string $nameAliasMembro
     * @param string $posFixoAlias
     */
    private function addJoinProfissional(
        QueryBuilder $query,
        $nameAliasMembro = 'membroChapa',
        $posFixoAlias = ''
    ): void {
        $query->innerJoin("{$nameAliasMembro}.profissional", "profissional{$posFixoAlias}")
            ->addSelect("profissional{$posFixoAlias}");
        $query->innerJoin("profissional{$posFixoAlias}.pessoa", "pessoa{$posFixoAlias}")
            ->addSelect("pessoa{$posFixoAlias}");
    }

    /**
     * Método auxiliar para adicionar join para profissional
     *
     * @param QueryBuilder $query
     * @param string $nameAliasMembro
     * @param string $posFixoAlias
     */
    private function addLeftJoinProfissional(
        QueryBuilder $query,
        $nameAliasMembro = 'membroChapa',
        $posFixoAlias = ''
    ): void {
        $query->leftJoin("{$nameAliasMembro}.profissional", "profissional{$posFixoAlias}")
            ->addSelect("profissional{$posFixoAlias}");
        $query->leftJoin("profissional{$posFixoAlias}.pessoa", "pessoa{$posFixoAlias}")
            ->addSelect("pessoa{$posFixoAlias}");
    }

    /**
     * Retorna responsáveis pela chapa conforme o id informado.
     *
     * @param integer $idChapa
     * @return mixed
     */
    public function getResponsaveisChapaPorIdChapa($idChapa)
    {
        try {
            $query = $this->createQueryBuilder('membroChapa');
            $query->leftJoin("membroChapa.chapaEleicao", "chapaEleicao");
            $this->addJoinProfissional($query, 'membroChapa');

            $query->where('chapaEleicao.excluido = false');
            $query->andWhere("membroChapa.situacaoResponsavel = true");
            $query->andWhere("chapaEleicao.id = :idChapa");
            $query->setParameter("idChapa", $idChapa);

            return $query->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryConvite(int $idProfissional, int $idCalendario): QueryBuilder
    {
        $query = $this->createQueryBuilder("membroChapa");
        $query->select([
            'membroChapa.numeroOrdem',
            'membroChapa.id idMembroChapa',
            'chapaEleicao.id idChapaEleicao',
            'atividadeSecundaria.id idAtividadeSecundaria',
            'atividadePrincipal.id idAtividadePrincipal',
            'chapaEleicao.descricaoPlataforma',
            'tipoCandidatura.id idTipoCandidatura',
            'chapaEleicao.idProfissionalInclusao idProfissional',
            'tipoParticipacaoChapa.descricao tipoParticChapa',
        ]);
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
        $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
        $query->innerJoin("atividadePrincipal.calendario", "calendario");
        $query->innerJoin("membroChapa.tipoParticipacaoChapa", "tipoParticipacaoChapa");
        $query->innerJoin("membroChapa.statusParticipacaoChapa", "statusParticipacaoChapa");
        $query->innerJoin("chapaEleicao.tipoCandidatura", "tipoCandidatura");

        $query->where('chapaEleicao.excluido = false');

        $query->andWhere('calendario.id = :idCalendario');
        $query->andWhere('membroChapa.profissional = :idUsuario');
        $query->andWhere('statusParticipacaoChapa.id = :statusParticChapaAConfirmar');
        $query->andWhere('chapaEleicao.idEtapa = :etapaChapaConfirmada');
        $query->setParameter('idCalendario', $idCalendario);
        $query->setParameter('idUsuario', $idProfissional);
        $query->setParameter('statusParticChapaAConfirmar', Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR);
        $query->setParameter('etapaChapaConfirmada', Constants::ETAPA_CRIACAO_CHAPA_CONFIRMADA);

        return $query;
    }

    /**
     * @param $nivelPrincipal
     * @param $niveisSecundaria
     * @param $idSituacaoMembro
     * @return QueryBuilder
     */
    private function getQueryMembrosChapaAConfirmarPrazoExpirado(
        $nivelPrincipal,
        $niveisSecundaria,
        $idSituacaoMembro,
        $dataFim
    ): QueryBuilder {
        $query = $this->createQueryBuilder("membroChapa");
        $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");
        $query->innerJoin("membroChapa.statusParticipacaoChapa", "statusParticipacaoChapa");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
        $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
        $query->innerJoin("atividadePrincipal.calendario", "calendario");
        $query->innerJoin("calendario.atividadesPrincipais", "atividadesPrincipais");
        $query->innerJoin("atividadesPrincipais.atividadesSecundarias", "atividadesSecundarias");

        $query->where('chapaEleicao.excluido = false');

        $query->andWhere(
            $query->expr()->andX(
                $query->expr()->eq("statusParticipacaoChapa.id", ":statusPartic"),
                $query->expr()->eq("atividadesPrincipais.nivel", ":nivelPrincipal"),
                $query->expr()->in("atividadesSecundarias.nivel", ":nivelSecundaria"),
                $query->expr()->eq("membroChapa.situacaoMembroChapa", ":situacaoMembro"),
                $query->expr()->eq("atividadesSecundarias.dataFim", ":nowDataFim")
            )
        );

        $query->setParameter('nivelPrincipal', $nivelPrincipal);
        $query->setParameter('nivelSecundaria', $niveisSecundaria);
        $query->setParameter('situacaoMembro', $idSituacaoMembro);
        $query->setParameter('nowDataFim', $dataFim);
        $query->setParameter('statusPartic', Constants::STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR);
        return $query;
    }
}
