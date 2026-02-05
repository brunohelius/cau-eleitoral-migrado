<?php
/*
 * CalendarioRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\Calendario;
use App\Entities\PedidoImpugnacao;
use App\Entities\PedidoSubstituicaoChapa;
use App\To\AtividadePrincipalCalendarioTO;
use App\To\CalendarioPublicacaoComissaoEleitoralFiltroTO;
use App\To\CalendarioTO;
use App\To\CalendarioFiltroTO;
use App\To\DocumentoComissaoMembroTO;
use App\To\EleicaoTO;
use App\To\NumeroMembroTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Exception;
use App\Config\Constants;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'Calendario'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class CalendarioRepository extends AbstractRepository
{
    /**
     * Retorna todos os anos onde há calendários eleitorais
     *
     * @return array
     */
    public function getAnos()
    {
        $dql = " SELECT DISTINCT eleicao.ano ano ";
        $dql .= " FROM App\Entities\Calendario calendario ";
        $dql .= " INNER JOIN calendario.eleicao eleicao ";
        $dql .= " WHERE calendario.ativo = :ativo  ";
        $dql .= " AND calendario.excluido = :excluido ";

        $parametros['ativo'] = true;
        $parametros['excluido'] = false;

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        $arrayAnos = $query->getArrayResult();

        return array_map(function ($dadosAnos) {
            return CalendarioTO::newInstance($dadosAnos);
        }, $arrayAnos);
    }

    /**
     * Retorna todos os anos onde há calendários eleitorais por Filtro
     *
     * @return array
     */
    public function getAnosPorFiltro(CalendarioFiltroTO $filtroTO)
    {
        $dql = " SELECT DISTINCT eleicao.ano ano ";
        $dql .= " FROM App\Entities\Calendario calendario ";
        $dql .= " INNER JOIN calendario.eleicao  eleicao  ";
        $dql .= " INNER JOIN calendario.situacoes  calendarioSituacao  ";
        $dql .= " INNER JOIN calendarioSituacao.situacaoCalendario situacaoCalendario ";
        $dql .= " WHERE calendario.excluido = :excluido ";

        $parametros['excluido'] = false;

        if (!empty($filtroTO->getSituacoes())) {
            $dql .= " AND ( situacaoCalendario.id IN (:situacoes))  ";
            $parametros['situacoes'] = $filtroTO->getSituacoes();
        }

        if (!empty($filtroTO->isListaChapas())) {
            $dql .= " AND (" . $this->getSubQueryQuantidadeChapasVinculadasConfirmadas() . " > 0) ";
            $parametros['etapaMembrosChapaIncluida'] = Constants::ETAPA_MEMBROS_CHAPA_INCLUIDA;
        }

        $dql .= " ORDER BY eleicao.ano DESC ";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        $arrayAnos = $query->getArrayResult();

        return array_map(function ($dadosAnos) {
            return EleicaoTO::newInstance($dadosAnos);
        }, $arrayAnos);
    }

    /**
     * Retorna todos os anos onde há calendários eleitorais por Filtro
     *
     * @return array
     */
    public function getPorProfissionalMembroComissao($idProfissional)
    {
        try {
            $query = $this->createQueryBuilder('calendario');
            $query->innerJoin("calendario.eleicao", "eleicao")->addSelect("eleicao");
            $query->innerJoin("eleicao.tipoProcesso", "tipoProcesso")->addSelect("tipoProcesso");
            $query->leftJoin("calendario.situacoes", "situacoes");
            $query->leftJoin("situacoes.situacaoCalendario", "situacaoCalendario");

            $query->innerJoin("calendario.atividadesPrincipais", "atividadesPrincipais");
            $query->innerJoin("atividadesPrincipais.atividadesSecundarias", "atividadesSecundarias");
            $query->innerJoin("atividadesSecundarias.informacaoComissaoMembro", "informacaoComissaoMembro");
            $query->innerJoin("informacaoComissaoMembro.membrosComissao", "membrosComissao");

            $query->where("calendario.excluido = false and calendario.ativo = true");

            $query->andWhere("situacoes.id = {$this->getSubQueryCalendarioSituacaoAtual()}");
            $query->andWhere("situacaoCalendario.id = :idSituacaoConcluido");
            $query->setParameter("idSituacaoConcluido", Constants::SITUACAO_CALENDARIO_CONCLUIDO);

            $query->andWhere("membrosComissao.pessoa = :idProfissional");
            $query->setParameter("idProfissional", $idProfissional);

            $query->orderBy("eleicao.ano", "ASC");
            $query->addOrderBy("eleicao.sequenciaAno", "ASC");

            $arrayCalendarios = $query->getQuery()->getArrayResult();

            return array_map(function ($dadosCalendario) {
                return CalendarioTO::newInstance($dadosCalendario);
            }, $arrayCalendarios);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna quantidade de calendários pela situação.
     * @param integer $idSituacao
     * @return mixed
     * @throws Exception
     */
    public function getTotalCalendariosPorSituacao($idSituacao)
    {
        $dql = "SELECT COUNT(calendario.id) ";
        $dql .= " FROM App\Entities\Calendario calendario ";
        $dql .= " INNER JOIN calendario.eleicao eleicao ";
        $dql .= " INNER JOIN calendario.situacoes calendarioSituacao ";
        $dql .= " INNER JOIN calendarioSituacao.situacaoCalendario situacaoCalendario ";

        $dql .= " WHERE calendario.ativo = :ativo  ";
        $dql .= " AND calendario.excluido = :excluido ";
        $dql .= " AND situacaoCalendario.id = :idSituacao ";

        $parametros['ativo'] = true;
        $parametros['excluido'] = false;
        $parametros['idSituacao'] = $idSituacao;

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        return $query->getSingleScalarResult();
    }

    /**
     * Retorna lista de anos com eleições concluidas.
     */
    public function getCalendariosConcluidosAnos()
    {
        $dql = " SELECT DISTINCT eleicao.ano ano ";
        $dql .= " FROM App\Entities\Calendario calendario ";
        $dql .= " INNER JOIN calendario.eleicao eleicao ";
        $dql .= " INNER JOIN calendario.situacoes  calendarioSituacao  ";
        $dql .= " INNER JOIN calendarioSituacao.situacaoCalendario situacaoCalendario ";
        $dql .= " WHERE calendario.ativo = :ativo  ";
        $dql .= " AND calendario.excluido = :excluido ";
        $dql .= " AND situacaoCalendario.id = " . Constants::SITUACAO_CALENDARIO_CONCLUIDO;

        $parametros['ativo'] = true;
        $parametros['excluido'] = false;

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);
        $arrayAnos = $query->getArrayResult();

        return array_map(function ($dadosAnos) {
            return CalendarioTO::newInstance($dadosAnos);
        }, $arrayAnos);
    }

    /**
     * Retorna as eleicoes para todos os anos
     *
     * @return array
     */
    public function getEleicoes($ano = null, $recuperaExcluido = true)
    {
        $dql = " SELECT DISTINCT eleicao.ano ano, eleicao.sequenciaAno sequenciaAno, calendario.id id,  ";
        $dql .= " tipoProcesso.id AS idTipoProcesso, ";
        $dql .= " tipoProcesso.descricao AS descricaoTipoProcesso ";
        $dql .= " FROM App\Entities\Calendario calendario ";
        $dql .= " INNER JOIN calendario.eleicao eleicao ";
        $dql .= " INNER JOIN eleicao.tipoProcesso tipoProcesso ";
        $dql .= " WHERE 1 = 1 ";
        $parametros = [];

        if (!$recuperaExcluido) {
            $dql .= " AND calendario.ativo = :ativo  ";
            $dql .= " AND calendario.excluido = :excluido ";

            $parametros['ativo'] = true;
            $parametros['excluido'] = false;
        }

        //Caso seja informado o ano, filtra e ordena pela maior sequencia.
        if (!empty($ano)) {
            $dql .= " AND eleicao.ano = :ano ";
            $parametros['ano'] = $ano;
        }
        $dql .= " ORDER BY eleicao.sequenciaAno DESC ";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        $arrayEleicoes = $query->getArrayResult();

        return array_map(function ($dadosEleicoes) {
            return CalendarioTO::newInstance($dadosEleicoes);
        }, $arrayEleicoes);
    }

    /**
     * Retorna calendários por vigência.
     *
     * @param $dataAtual
     * @return Calendario[]|array
     * @throws Exception
     */
    public function getCalendariosVigentes($dataAtual) {
        $query = $this->createQueryBuilder('calendario');
        $query->innerJoin("calendario.eleicao", "eleicao")->addSelect("eleicao");
        $query->innerJoin("eleicao.tipoProcesso", "tipoProcesso")->addSelect("tipoProcesso");
        $query->leftJoin("calendario.situacoes", "situacoes");
        $query->innerJoin('calendario.cauUf', 'cauUf');

        $query->where('calendario.ativo = :ativo');
        $query->andWhere('calendario.excluido = :excluido');

        if(!empty($dataAtual)) {
            $query->andWhere('calendario.dataInicioVigencia <= :dataAtual');
            $query->andWhere('calendario.dataFimVigencia >= :dataAtual');
        }

        $query->setParameters([
            'ativo' => true,
            'excluido' => false,
            'dataAtual' => $dataAtual
        ]);

        return array_map(function ($data) {
            return Calendario::newInstance($data);
        }, $query->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY));
    }

    /**
     * Retorna o calendario conforme o id informado.
     *
     * @param $id
     * @return Calendario|null
     * @throws Exception
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('calendario');
            $query->innerJoin("calendario.eleicao", "eleicao")->addSelect("eleicao");
            $query->leftJoin("calendario.situacoes", "situacoes")->addSelect("situacoes");
            $query->leftJoin("calendario.cauUf", "cauUf")->addSelect("cauUf");
            $query->leftJoin("situacoes.situacaoCalendario", "situacaoCalendario")
                ->addSelect("situacaoCalendario")
                ->orderBy('situacoes.data', 'ASC');
            $query->innerJoin("eleicao.tipoProcesso", "tipoProcesso")->addSelect("tipoProcesso");
            $query->leftJoin("calendario.atividadesPrincipais", "atividadesPrincipais")
                ->addSelect("atividadesPrincipais")->orderBy('atividadesPrincipais.nivel', 'ASC');
            $query->leftJoin("atividadesPrincipais.prazos", "prazo")->addSelect("prazo");
            $query->leftJoin("atividadesPrincipais.atividadesSecundarias", "atividadesSecundarias")
                ->addSelect("atividadesSecundarias")->addOrderBy('atividadesSecundarias.nivel', 'ASC');
            $query->leftJoin("calendario.arquivos", "arquivos")->addSelect("arquivos");

            $query->where("calendario.id = :id");
            $query->setParameter("id", $id);

            $query->getQuery()->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);

            return Calendario::newInstance($query->getQuery()->getArrayResult()[0]);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna os calendários conforme o filtro de pesquisa informado.
     *
     * @param CalendarioFiltroTO $filtroTO
     * @return array
     */
    public function getCalendariosPorFiltro(CalendarioFiltroTO $filtroTO)
    {
        $dql = " SELECT calendario.id as id, ";
        $dql .= "  eleicao.id as idEleicao, ";
        $dql .= "  eleicao.ano as ano, ";
        $dql .= "  eleicao.sequenciaAno as sequenciaAno, ";
        $dql .= " tipoProcesso.id AS idTipoProcesso, ";
        $dql .= " tipoProcesso.descricao AS descricaoTipoProcesso, ";
        $dql .= " calendario.ativo as ativo, ";
        $dql .= " calendario.dataInicioVigencia as dataInicioVigencia, ";
        $dql .= " calendario.dataFimVigencia as dataFimVigencia, ";
        $dql .= " situacaoCalendarioJoin.id as idSituacao ,  ";
        $dql .= " situacaoCalendarioJoin.descricao as descricaoSituacao ,";
        $dql .= " situacaoEleicao.id as idSituacaoEleicao ,";
        $dql .= " situacaoEleicao.descricao as descricaoSituacaoEleicao ";
        $dql .= " , " . $this->getSubQueryNomeArquivoRecente();
        $dql .= " , " . $this->getSubQueryIdArquivoRecente();
        $dql .= " FROM App\Entities\Calendario calendario ";
        $dql .= " INNER JOIN calendario.eleicao eleicao";
        $dql .= " INNER JOIN eleicao.tipoProcesso tipoProcesso";
        $dql .= " INNER JOIN eleicao.situacoes situacoesEleicao";
        $dql .= " INNER JOIN situacoesEleicao.situacaoEleicao situacaoEleicao";

        $dql .= " INNER JOIN calendario.situacoes  calendarioSituacaoJoin  ";
        $dql .= " INNER JOIN calendarioSituacaoJoin.situacaoCalendario situacaoCalendarioJoin ";

        $dql .= " WHERE calendario.excluido = :excluido  AND calendarioSituacaoJoin.id = " . $this->getSubQueryCalendarioSituacaoAtual();
        $dql .= " AND situacoesEleicao.id = " . $this->getSubQueryEleicaoSituacaoAtual();

        $parametros = [];

        if (!empty($filtroTO->getIdTipoProcesso())) {
            $dql .= " AND tipoProcesso.id = :idTipoProcesso ";
            $parametros['idTipoProcesso'] = $filtroTO->getIdTipoProcesso();
        }
        if (!empty($filtroTO->getIdsCalendariosEleicao())) {
            $dql .= " AND calendario.id IN (:sequenciasEleicoes) ";
            $parametros['sequenciasEleicoes'] = $filtroTO->getIdsCalendariosEleicao();
        }
        if (!empty($filtroTO->getAnos())) {
            $dql .= " AND ( eleicao.ano IN (:anosCalendario) )  ";
            $parametros['anosCalendario'] = $filtroTO->getAnos();
        }
        if (!empty($filtroTO->getSituacoes())) {
            $dql .= " AND ( situacaoCalendarioJoin.id IN (:situacoes))  ";
            $parametros['situacoes'] = $filtroTO->getSituacoes();
        }
        if (!empty($filtroTO->isListaChapas())) {
            $dql .= " AND (" . $this->getSubQueryQuantidadeChapasVinculadasConfirmadas() . " > 0) ";
            $parametros['etapaMembrosChapaIncluida'] = Constants::ETAPA_MEMBROS_CHAPA_INCLUIDA;
        }

        if (!empty($filtroTO->isListaPedidosSubstituicaoChapa())) {
            $dql .= " AND ((" . $this->getSubQueryQuantidadePedidosSubstituicaoChapas() . ") > 0) ";
        }

        if (!empty($filtroTO->isListaPedidosImpugnacao())) {
            $dql .= " AND ((" . $this->getSubQueryQuantidadePedidosImpugnacao() . ") > 0) ";
        }

        if (
            !empty($filtroTO->isListaChapas())
            || !empty($filtroTO->isListaPedidosSubstituicaoChapa())
            || !empty($filtroTO->isListaPedidosImpugnacao())
            || !empty($filtroTO->isListaDenuncias())
            || !empty($filtroTO->isListaPedidosImpugnacaoResultado())
        ) {
            $dql .= " AND calendario.ativo = true ";
            $dql .= " AND situacaoCalendarioJoin.id = :idSituacaoConcluido ";
            $parametros['idSituacaoConcluido'] = Constants::SITUACAO_CALENDARIO_CONCLUIDO;
        }

        $parametros['excluido'] = false;

        $dql .= " ORDER BY eleicao.ano ASC, eleicao.sequenciaAno ASC ";
        $query = $this->getEntityManager()->createQuery($dql);

        $query->setParameters($parametros);

        $arrayCalendarios = $query->getArrayResult();
        return array_map(function ($dadosCalendario) {
            return CalendarioTO::newInstance($dadosCalendario);
        }, $arrayCalendarios);
    }

    /**
     * Retorna as atividades principais do calendario conforme o Filtros Informado informado.
     *
     * @param AtividadePrincipalCalendarioTO $filtroTO
     * @return array|null
     */
    public function getAtividadePrincipalPorCalendarioComFiltro($idCalendario, $filtroTO)
    {
        $query = $this->createQueryBuilder('calendario');
        $query->innerJoin('calendario.eleicao', 'eleicao')->addSelect("eleicao");
        $query->leftJoin("calendario.atividadesPrincipais",
            "atividadePrincipais")->addSelect("atividadePrincipais")->addOrderBy("atividadePrincipais.nivel", "ASC");
        $query->leftJoin('atividadePrincipais.atividadesSecundarias', 'atividadesSecundarias')
            ->addSelect('atividadesSecundarias')
            ->orderBy("atividadesSecundarias.id", "ASC");

        $query->where("(atividadePrincipais.nivel = 1 AND atividadesSecundarias.nivel = 1)");

        if (!empty($idCalendario)) {
            $query->andWhere("calendario.id = (:id_calendario)");
            $query->setParameter("id_calendario", $idCalendario);
        }

        if (!empty($filtroTO->getDataInicio())) {
            $query->andWhere("atividadePrincipais.dataInicio >= :dataInicio");
            $query->andWhere("atividadesSecundarias.dataInicio >= :dataInicio");
            $query->setParameter("dataInicio", $filtroTO->getDataInicio());
        }

        if (!empty($filtroTO->getDataFim())) {
            $query->andWhere("atividadePrincipais.dataFim <= :dataFim");
            $query->andWhere("atividadesSecundarias.dataFim <= :dataFim");
            $query->setParameter("dataFim", $filtroTO->getDataFim());
        }

        $atividadePrincipais = $query->getQuery()->getArrayResult();
        return array_map(function ($data) {
            return AtividadePrincipalCalendarioTO::newInstance($data);
        }, $atividadePrincipais);
    }

    /**
     * Retorna o id da situação atual do Calendario
     *
     * @param $idCalendario
     * @return CalendarioTO[]
     */
    public function getSituacaoAtual($idCalendario)
    {
        $dql = " SELECT calendario.id as id, ";
        $dql .= $this->getSubQueryIdSituacaoAtual();
        $dql .= " FROM App\Entities\Calendario calendario ";
        $dql .= " WHERE calendario.ativo = :ativo  ";
        $dql .= " AND calendario.excluido = :excluido ";
        $dql .= " AND calendario.id = :id ";

        $parametros['ativo'] = true;
        $parametros['excluido'] = false;
        $parametros['id'] = $idCalendario;

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        $arrayCalendarios = $query->getArrayResult();
        return array_map(function ($dadosCalendario) {
            return CalendarioTO::newInstance($dadosCalendario);
        }, $arrayCalendarios);
    }

    /**
     * Retorna o calendario conforme o id do Prazo informado.
     *
     * @param $idPrazo
     * @return mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorPrazo($idPrazo)
    {
        try {
            $query = $this->createQueryBuilder('calendario');
            $query->innerJoin("calendario.eleicao", "eleicao");
            $query->leftJoin("calendario.atividadesPrincipais", "atividadesPrincipais");
            $query->leftJoin("atividadesPrincipais.prazos", "prazos");

            $query->where("prazos.id = :id");
            $query->setParameter("id", $idPrazo);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Busca todos os prazos pelo id do Calendário
     *
     * @param $id
     * @return mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPrazosPorCalendario($id)
    {
        try {
            $query = $this->createQueryBuilder('calendario');
            $query->innerJoin("calendario.eleicao", "eleicao");
            $query->leftJoin("calendario.atividadesPrincipais", "atividadesPrincipais");
            $query->leftJoin("atividadesPrincipais.prazos", "prazos");
            $query->leftJoin("calendario.situacoes", "situacoes")->addSelect("situacoes");
            $query->leftJoin("situacoes.situacaoCalendario", "situacaoCalendario")
                ->addSelect("situacaoCalendario")
                ->orderBy('situacoes.data', 'DESC');

            $query->where("calendario.id = :id");
            $query->orderBy('prazos.descricaoNivel', 'ASC');
            $query->setParameter("id", $id);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Recupera a quantidade de membros por UF de acordo com o calendário informado.
     *
     * @param $idCalendario
     * @return mixed|null
     */
    public function getQuantidadeMembrosPorUf($idCalendario)
    {
        $dql = " SELECT cauUf.idCauUf AS idCauUf, ";
        $dql .= " COUNT (membroComissao.id) AS quantidade ";
        $dql .= " FROM App\Entities\MembroComissao membroComissao ";
        $dql .= " JOIN membroComissao.informacaoComissaoMembro informacaoComissaoMembro ";
        $dql .= " JOIN informacaoComissaoMembro.atividadeSecundaria atividadeSecundaria ";
        $dql .= " JOIN atividadeSecundaria.atividadePrincipalCalendario atividadePrincipalCalendario ";
        $dql .= " JOIN atividadePrincipalCalendario.calendario calendario ";
        $dql .= " JOIN calendario.cauUf as cauUf ";
        $dql .= " WHERE cauUf.idCauUf = membroComissao.idCauUf ";
        $dql .= " AND calendario.id = :id ";
        $dql .= " AND membroComissao.excluido = false ";
        $dql .= " AND membroComissao.membroSubstituto IS NOT NULL ";
        $dql .= " GROUP BY cauUf.idCauUf ";

        $parametros['id'] = $idCalendario;

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        $arrayNumeroMembros = $query->getArrayResult();
        return array_map(function ($numeroMembro) {
            return NumeroMembroTO::newInstance($numeroMembro);
        }, $arrayNumeroMembros);
    }

    /**
     * Recupera a listas de calendários para listagem de comissões eleitoraos para publicação.
     *
     * @return array
     */
    public function getCalendariosPublicacaoComissaoEleitoral()
    {
        $dqlSituacaoCalendario = '(SELECT MAX(calendarioSituacaoSub.id) ';
        $dqlSituacaoCalendario .= ' FROM App\Entities\CalendarioSituacao calendarioSituacaoSub ';
        $dqlSituacaoCalendario .= ' WHERE calendarioSituacaoSub.calendario = calendario.id)';

        $dqlComissaoPublicada = '(SELECT COUNT(publicacaoDocumento.id)';
        $dqlComissaoPublicada .= ' FROM App\Entities\PublicacaoDocumento publicacaoDocumento ';
        $dqlComissaoPublicada .= ' WHERE publicacaoDocumento.documentoComissaoMembro = documentoComissaoMembro.id) totalPublicacoes ';

        $dql = " SELECT documentoComissaoMembro.id AS id, ";
        $dql .= " calendario.id AS idCalendario, ";
        $dql .= " eleicao.id AS idEleicao, ";
        $dql .= " eleicao.ano AS ano, ";
        $dql .= " calendario.ativo AS ativo, ";
        $dql .= " tipoProcesso.id AS idTipoProcesso, ";
        $dql .= " tipoProcesso.descricao AS descricaoTipoProcesso, ";
        $dql .= " eleicao.sequenciaAno AS sequenciaAno, ";
        $dql .= " situacaoCalendario.id AS idSituacao , ";
        $dql .= " calendario.dataFimVigencia AS dataFimVigencia, ";
        $dql .= " calendario.dataInicioVigencia AS dataInicioVigencia, ";
        $dql .= " situacaoCalendario.descricao AS descricaoSituacao ";
        $dql .= " , " . $dqlComissaoPublicada;
        $dql .= " , " . $this->getSubQueryNomeArquivoRecente();
        $dql .= " , " . $this->getSubQueryIdArquivoRecente();
        $dql .= " FROM App\Entities\Calendario calendario ";
        $dql .= " INNER JOIN calendario.eleicao eleicao ";
        $dql .= " INNER JOIN eleicao.tipoProcesso tipoProcesso ";

        $dql .= " INNER JOIN calendario.situacoes situacoes ";
        $dql .= " INNER JOIN situacoes.situacaoCalendario situacaoCalendario ";

        $dql .= " INNER JOIN calendario.atividadesPrincipais atividadesPrincipais ";
        $dql .= " INNER JOIN atividadesPrincipais.atividadesSecundarias atividadesSecundarias ";
        $dql .= " INNER JOIN atividadesSecundarias.informacaoComissaoMembro informacaoComissaoMembro ";
        $dql .= " INNER JOIN informacaoComissaoMembro.documentoComissaoMembro documentoComissaoMembro ";
        $dql .= " INNER JOIN informacaoComissaoMembro.membrosComissao membrosComissao";
        $dql .= " WHERE calendario.excluido = :excluido AND situacoes.id = " . $dqlSituacaoCalendario;
        $dql .= " AND situacaoCalendario.id = :situacaoCalendarioConcluido ";
        $dql .= " AND informacaoComissaoMembro.situacaoConcluido = :isInformacaoComissaoConcluida ";

        $dql .= " GROUP BY documentoComissaoMembro.id, calendario.id, eleicao.id, tipoProcesso.id";
        $dql .= ", situacaoCalendario.id";
        $dql .= " ORDER BY eleicao.ano ASC";

        $parametros = [];
        $parametros['excluido'] = false;
        $parametros['isInformacaoComissaoConcluida'] = true;
        $parametros['situacaoCalendarioConcluido'] = Constants::SITUACAO_CALENDARIO_CONCLUIDO;

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        $calendarios = $query->getArrayResult();
        return array_map(function ($data) {
            return DocumentoComissaoMembroTO::newInstance($data);
        }, $calendarios);
    }

    /**
     * Recupera a listas dos anos vinculados aos calendários para listagem de comissões eleitoraos para publicação.
     *
     * @return array
     */
    public function getAnosCalendarioPublicacaoComissaoEleitoral()
    {
        $dqlSituacaoCalendario = '(SELECT MAX(calendarioSituacaoSub.id) ';
        $dqlSituacaoCalendario .= ' FROM App\Entities\CalendarioSituacao calendarioSituacaoSub ';
        $dqlSituacaoCalendario .= ' WHERE calendarioSituacaoSub.calendario = calendario.id)';

        $dql = " SELECT eleicao.ano AS ano ";
        $dql .= " FROM App\Entities\Calendario calendario ";
        $dql .= " INNER JOIN calendario.eleicao eleicao ";
        $dql .= " INNER JOIN eleicao.tipoProcesso tipoProcesso ";

        $dql .= " INNER JOIN calendario.situacoes situacoes ";
        $dql .= " INNER JOIN situacoes.situacaoCalendario situacaoCalendario ";

        $dql .= " INNER JOIN calendario.atividadesPrincipais atividadesPrincipais ";
        $dql .= " INNER JOIN atividadesPrincipais.atividadesSecundarias atividadesSecundarias ";
        $dql .= " INNER JOIN atividadesSecundarias.informacaoComissaoMembro informacaoComissaoMembro ";

        $dql .= " WHERE calendario.excluido = :excluido AND situacoes.id = " . $dqlSituacaoCalendario;
        $dql .= " AND situacaoCalendario.id = :situacaoCalendarioConcluido ";
        $dql .= " AND informacaoComissaoMembro.situacaoConcluido = :isInformacaoComissaoConcluida ";

        $dql .= " GROUP BY eleicao.ano  ORDER BY eleicao.ano ASC";

        $parametros = [];
        $parametros['excluido'] = false;
        $parametros['isInformacaoComissaoConcluida'] = true;
        $parametros['situacaoCalendarioConcluido'] = Constants::SITUACAO_CALENDARIO_CONCLUIDO;

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        $calendarios = $query->getArrayResult();
        return array_map(function ($data) {
            return EleicaoTO::newInstance($data);
        }, $calendarios);
    }

    /**
     * Retorna chapas de acordo com o id do calendário.
     *
     * @param integer $idCalendario
     *
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTotalCalendariosChapa()
    {
        $query = $this->createQueryBuilder("calendario");
        $query->select([
            'COUNT(DISTINCT calendario.id)',
        ]);
        $query->innerJoin("calendario.atividadesPrincipais", "atividadePrincipal");
        $query->innerJoin("atividadePrincipal.atividadesSecundarias", "atividadeSecundaria");
        $query->innerJoin("atividadeSecundaria.chapasEleicao", "chapaEleicao");
        $query->where('chapaEleicao.idEtapa > :etapaMembrosChapaIncluida');
        $query->andWhere('chapaEleicao.excluido <> true');
        $query->setParameter('etapaMembrosChapaIncluida', Constants::ETAPA_MEMBROS_CHAPA_INCLUIDA);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Recupera os calendários disponpiveis para publicação da comissão técnica de acordo com o filtro informado.
     *
     * @param CalendarioPublicacaoComissaoEleitoralFiltroTO $filtro
     * @return array
     */
    public function getCalendarioPublicacaoComissaoEleitoralPorFiltro(
        CalendarioPublicacaoComissaoEleitoralFiltroTO $filtro
    ) {
        $dqlSituacaoCalendario = '(SELECT MAX(calendarioSituacaoSub.id) ';
        $dqlSituacaoCalendario .= ' FROM App\Entities\CalendarioSituacao calendarioSituacaoSub ';
        $dqlSituacaoCalendario .= ' WHERE calendarioSituacaoSub.calendario = calendario.id)';

        $dqlComissaoPublicada = '(SELECT COUNT(publicacaoDoc.id)';
        $dqlComissaoPublicada .= ' FROM App\Entities\PublicacaoDocumento publicacaoDoc ';
        $dqlComissaoPublicada .= ' WHERE publicacaoDoc.documentoComissaoMembro = documentoComissaoMembro.id) totalPublicacoes ';

        $dql = " SELECT calendario.id AS idCalendario, ";
        $dql .= " eleicao.id AS idEleicao, ";
        $dql .= " eleicao.ano AS ano, ";
        $dql .= " calendario.ativo AS ativo, ";
        $dql .= " tipoProcesso.id AS idTipoProcesso, ";
        $dql .= " tipoProcesso.descricao AS descricaoTipoProcesso, ";
        $dql .= " eleicao.sequenciaAno AS sequenciaAno, ";
        $dql .= " situacaoCalendario.id AS idSituacao , ";
        $dql .= " calendario.dataFimVigencia AS dataFimVigencia, ";
        $dql .= " calendario.dataInicioVigencia AS dataInicioVigencia, ";
        $dql .= " situacaoCalendario.descricao AS descricaoSituacao, ";
        $dql .= " documentoComissaoMembro.id AS id ";
        $dql .= " , " . $dqlComissaoPublicada;
        $dql .= " , " . $this->getSubQueryNomeArquivoRecente();
        $dql .= " , " . $this->getSubQueryIdArquivoRecente();
        $dql .= " FROM App\Entities\Calendario calendario ";
        $dql .= " INNER JOIN calendario.eleicao eleicao ";
        $dql .= " INNER JOIN eleicao.tipoProcesso tipoProcesso ";

        $dql .= " INNER JOIN calendario.situacoes situacoes ";
        $dql .= " INNER JOIN situacoes.situacaoCalendario situacaoCalendario ";

        $dql .= " INNER JOIN calendario.atividadesPrincipais atividadesPrincipais ";
        $dql .= " INNER JOIN atividadesPrincipais.atividadesSecundarias atividadesSecundarias ";
        $dql .= " INNER JOIN atividadesSecundarias.informacaoComissaoMembro informacaoComissaoMembro ";
        $dql .= " INNER JOIN informacaoComissaoMembro.documentoComissaoMembro documentoComissaoMembro ";

        $dql .= " WHERE calendario.excluido = :excluido AND situacoes.id = " . $dqlSituacaoCalendario;
        $dql .= " AND situacaoCalendario.id = :situacaoCalendarioConcluido ";
        $dql .= " AND informacaoComissaoMembro.situacaoConcluido = :isInformacaoComissaoConcluida ";

        $parametros = [];
        $parametros['excluido'] = false;
        $parametros['isInformacaoComissaoConcluida'] = true;
        $parametros['situacaoCalendarioConcluido'] = Constants::SITUACAO_CALENDARIO_CONCLUIDO;

        if (!empty($filtro->getAnosEleicao())) {
            $dql .= " AND eleicao.ano IN (:anos) ";
            $parametros['anos'] = $filtro->getAnosEleicao();
        }

        if (!empty($filtro->getEleicoes())) {
            $dql .= " AND eleicao.id IN (:idsEleicoes) ";
            $parametros['idsEleicoes'] = $filtro->getIdsEleicoes();
        }

        if (!empty($filtro->getTiposProcesso())) {
            $dql .= " AND tipoProcesso.id IN (:idsProcesso) ";
            $parametros['idsProcesso'] = $filtro->getIdsProcessos();
        }

        if (
            !empty($filtro->getStatusPublicado())
            && count($filtro->getStatusPublicado()) != 0
            && count($filtro->getStatusPublicado()) != 2
        ) {
            if ($filtro->getStatusPublicado()->first() == 't') {
                $dql .= " AND (SELECT COUNT(publicacaoDocSub.id) FROM App\Entities\PublicacaoDocumento publicacaoDocSub ";
                $dql .= " WHERE publicacaoDocSub.documentoComissaoMembro = documentoComissaoMembro.id) > 0 ";
            }

            if ($filtro->getStatusPublicado()->first() == 'f') {
                $dql .= " AND (SELECT COUNT(publicacaoDocSub.id) FROM App\Entities\PublicacaoDocumento publicacaoDocSub";
                $dql .= " WHERE publicacaoDocSub.documentoComissaoMembro = documentoComissaoMembro.id) = 0 ";
            }
        }

        $dql .= " ORDER BY eleicao.ano ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parametros);

        $calendarios = $query->getArrayResult();
        return array_map(function ($data) {
            return DocumentoComissaoMembroTO::newInstance($data);
        }, $calendarios);
    }

    /**
     * Retorna um calendário por um id de atividade secundaria
     *
     * @param $idAtividadeSecundaria
     * @return mixed|null
     */
    public function getPorAtividadeSecundaria($idAtividadeSecundaria)
    {
        try {
            $query = $this->createQueryBuilder('calendario');
            $query->innerJoin("calendario.eleicao", "eleicao")->addSelect("eleicao");
            $query->leftJoin("calendario.atividadesPrincipais", "atividadesPrincipais");
            $query->leftJoin("atividadesPrincipais.atividadesSecundarias", "atividadesSecundarias");

            $query->where("atividadesSecundarias.id = :id");
            $query->setParameter("id", $idAtividadeSecundaria);

            $dadosEleicao = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return Calendario::newInstance($dadosEleicao);
        } catch (NoResultException $e) {
            return null;
        } catch (Exception $e) {
        }
    }

    /**
     * Método para retornar o trecho do DQL que busca a situação atual
     *
     * @return string
     */
    private function getSubQueryIdSituacaoAtual()
    {
        $dql = " (SELECT situacaoCalendario3.id ";
        $dql .= " FROM App\Entities\CalendarioSituacao calendarioSituacao3 ";
        $dql .= " INNER JOIN calendarioSituacao3.situacaoCalendario situacaoCalendario3 ";
        $dql .= " INNER JOIN calendarioSituacao3.calendario calendario3 ";
        $dql .= " WHERE calendario3.id = calendario.id ";
        $dql .= " AND calendarioSituacao3.data = (select MAX(calendarioSituacao4.data) FROM App\Entities\CalendarioSituacao calendarioSituacao4  ";
        $dql .= " WHERE calendarioSituacao4.calendario = calendario.id )) idSituacao ";

        return $dql;
    }

    /**
     * Método para retornar trecho DQL que busca id do calendário situação.
     *
     * @return string
     */
    private function getSubQueryCalendarioSituacaoAtual()
    {
        $dql = " (SELECT calendarioSituacao5.id";
        $dql .= " FROM App\Entities\CalendarioSituacao calendarioSituacao5 ";
        $dql .= " INNER JOIN calendarioSituacao5.calendario calendario5 ";
        $dql .= " WHERE calendario5.id = calendario.id ";
        $dql .= " AND calendarioSituacao5.data = (select MAX(calendarioSituacao6.data) FROM App\Entities\CalendarioSituacao calendarioSituacao6  ";
        $dql .= " WHERE calendarioSituacao6.calendario = calendario5.id ))  ";
        return $dql;
    }

    /**
     * Método para retornar trecho DQL que busca id do eleicao situação.
     *
     * @return string
     */
    private function getSubQueryEleicaoSituacaoAtual()
    {
        $dql = " (SELECT eleicaoSituacao5.id";
        $dql .= " FROM App\Entities\EleicaoSituacao eleicaoSituacao5 ";
        $dql .= " INNER JOIN eleicaoSituacao5.eleicao eleicao5 ";
        $dql .= " WHERE eleicao5.id = eleicao.id ";
        $dql .= " AND eleicaoSituacao5.data = (select MAX(eleicaoSituacao6.data) FROM App\Entities\EleicaoSituacao eleicaoSituacao6  ";
        $dql .= " WHERE eleicaoSituacao6.eleicao = eleicao5.id ))  ";
        return $dql;
    }

    /**
     * Método para retornar o trecho do DQL que busca o arquivo mais recente
     *
     * @return string
     */
    private function getSubQueryNomeArquivoRecente()
    {
        $dql = " (SELECT arquivoCalendario.nome ";
        $dql .= " FROM  App\Entities\ArquivoCalendario arquivoCalendario ";
        $dql .= " WHERE arquivoCalendario.calendario = calendario.id ";
        $dql .= " AND arquivoCalendario.id = (SELECT MAX(arquivoCalendario2.id) FROM App\Entities\ArquivoCalendario arquivoCalendario2 ";
        $dql .= " WHERE arquivoCalendario2.calendario = calendario.id)) resolucao ";

        return $dql;
    }

    /**
     * Método para retornar o trecho do DQL que busca o id do arquivo mais recente
     *
     * @return string
     */
    private function getSubQueryIdArquivoRecente()
    {
        $dql = " (SELECT arquivoCalendario3.id ";
        $dql .= " FROM  App\Entities\ArquivoCalendario arquivoCalendario3 ";
        $dql .= " WHERE arquivoCalendario3.calendario = calendario.id ";
        $dql .= " AND arquivoCalendario3.id = (SELECT MAX(arquivoCalendario4.id) FROM App\Entities\ArquivoCalendario arquivoCalendario4 ";
        $dql .= " WHERE arquivoCalendario4.calendario = calendario.id)) idResolucao ";

        return $dql;
    }

    /**
     * Método para retornar trecho DQL que busca id do calendário situação.
     *
     * @return string
     */
    private function getSubQueryQuantidadeChapasVinculadasConfirmadas()
    {
        $dql = " (SELECT COUNT(chapaEleicao.id)";
        $dql .= " FROM App\Entities\ChapaEleicao chapaEleicao ";
        $dql .= " INNER JOIN chapaEleicao.atividadeSecundariaCalendario atividadeSecundaria ";
        $dql .= " INNER JOIN atividadeSecundaria.atividadePrincipalCalendario atividadePrincipal ";
        $dql .= " INNER JOIN atividadePrincipal.calendario calendario6 ";
        $dql .= " WHERE calendario6.id = calendario.id ";
        $dql .= " AND chapaEleicao.idEtapa > :etapaMembrosChapaIncluida ";
        $dql .= " AND chapaEleicao.excluido <> true) ";

        return $dql;
    }

    /**
     * Método para retornar trecho DQL que busca id do calendário situação.
     *
     * @return string
     */
    private function getSubQueryQuantidadePedidosSubstituicaoChapas()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('COUNT(pedidoSubstituicaoChapa.id)');
        $query->from(PedidoSubstituicaoChapa::class, 'pedidoSubstituicaoChapa');
        $query->innerJoin('pedidoSubstituicaoChapa.chapaEleicao', 'chapaEleicaoSubQueryPedido');
        $query->innerJoin('chapaEleicaoSubQueryPedido.atividadeSecundariaCalendario',
            'atividadeSecundariaCalendarioSubQueryPedido');
        $query->innerJoin('atividadeSecundariaCalendarioSubQueryPedido.atividadePrincipalCalendario',
            'atividadePrincipalSubQueryPedido');
        $query->innerJoin('atividadePrincipalSubQueryPedido.calendario', 'calendarioSubQueryPedido');
        $query->where("calendarioSubQueryPedido.id = calendario.id");

        return $query->getQuery()->getDQL();
    }

    /**
     * Método para retornar trecho DQL que busca id do calendário situação.
     *
     * @return string
     */
    private function getSubQueryQuantidadePedidosImpugnacao()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('COUNT(pedidoImpugnacao.id)');
        $query->from(PedidoImpugnacao::class, 'pedidoImpugnacao');
        $query->innerJoin('pedidoImpugnacao.membroChapa', 'membroChapa');
        $query->innerJoin('membroChapa.chapaEleicao', 'chapaEleicaoSubQueryPedidoImpugnacao');
        $query->innerJoin('chapaEleicaoSubQueryPedidoImpugnacao.atividadeSecundariaCalendario',
            'atividadeSecundariaCalendarioSubQueryPedidoImpugnacao');
        $query->innerJoin('atividadeSecundariaCalendarioSubQueryPedidoImpugnacao.atividadePrincipalCalendario',
            'atividadePrincipalSubQueryPedidoImpugnacao');
        $query->innerJoin('atividadePrincipalSubQueryPedidoImpugnacao.calendario', 'calendarioSubQueryPedidoImpugnacao');
        $query->where("calendarioSubQueryPedidoImpugnacao.id = calendario.id");

        return $query->getQuery()->getDQL();
    }

    /**
     * Retorna os calendarios por tipo de processo, status e datas de vigencia
     *
     * @param Calendario $calendario
     * @param int $tipoProcesso
     * @return array|null
     */
    public function getPorTipoProcessoPorSituacaoAndDatasVigencia($calendario, $tipoProcesso = Constants::TIPO_PROCESSO_ORDINARIO)
    {
        $query = $this->createQueryBuilder('calendario');
        $query->innerJoin('calendario.eleicao', 'eleicao')->addSelect("eleicao");
        $query->innerJoin('calendario.cauUf', 'cauUf')->addSelect("cauUf");
        $query->innerJoin('eleicao.tipoProcesso', 'tipoProcesso')->addSelect("tipoProcesso");
        $query->leftJoin("calendario.situacoes", "situacoes")->addSelect("situacoes");
        $query->leftJoin("situacoes.situacaoCalendario", "situacaoCalendario");
        $query->andWhere("situacoes.id = {$this->getSubQueryCalendarioSituacaoAtual()}");
        $query->andWhere("situacaoCalendario.id = " . Constants::SITUACAO_CALENDARIO_CONCLUIDO);
        $query->andWhere("tipoProcesso.id = " . $tipoProcesso);
        $query->andWhere("calendario.excluido = false");
        $query->andWhere("calendario.ativo = true");

        if($calendario->getId()) {
            $query->andWhere("calendario.id != " . $calendario->getId());
        }

        $query->andWhere("(calendario.dataInicioVigencia between :dataInicioVigencia AND :dataFimVigencia)
        OR (calendario.dataFimVigencia between :dataInicioVigencia AND :dataFimVigencia)");
        $query->setParameter("dataInicioVigencia", $calendario->getDataInicioVigencia());
        $query->setParameter("dataFimVigencia", $calendario->getDataFimVigencia());


        return $query->getQuery()->getResult();
    }
}
