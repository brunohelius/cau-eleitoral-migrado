<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 07/10/2019
 * Time: 16:40
 */

namespace App\Repository;

use App\Entities\MembroComissao;
use App\Entities\MembroComissaoSituacao;
use App\Util\Utils;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use App\Config\Constants;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'MembroComissao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class MembroComissaoRepository extends AbstractRepository
{
    /**
     * Retorna os membros da comissão dado um id de informação de comissão e id cau uf.
     *
     * @param $idInformacaoComissao
     * @param $idCauUf
     * @param $limit
     * @return array|null
     */
    public function getPorInformacaoComissao($idInformacaoComissao, $idCauUf = null, $limit = null)
    {
        $query = $this->createQueryBuilder('membroComissao');
        $query->innerJoin("membroComissao.informacaoComissaoMembro", "informacaoComissaoMembro")->addSelect("informacaoComissaoMembro");
        $query->innerJoin("membroComissao.membroComissaoSituacao", "membroComissaoSituacao")->addSelect("membroComissaoSituacao");
        $query->leftJoin("membroComissaoSituacao.situacaoMembroComissao", "situacaoMembroComissao")->addSelect("situacaoMembroComissao");
        $query->leftJoin("membroComissao.tipoParticipacao", "tipoParticipacao")->addSelect("tipoParticipacao");
        $query->leftJoin("membroComissao.membroSubstituto", "membroSubstituto")->addSelect("membroSubstituto");

        $query->leftJoin("membroSubstituto.tipoParticipacao", "tipoParticipacaoSubstituto")->addSelect("tipoParticipacaoSubstituto");
        $query->leftJoin("membroSubstituto.membroComissaoSituacao", "membroComissaoSituacaoSubstituto")->addSelect("membroComissaoSituacaoSubstituto");
        $query->leftJoin("membroComissaoSituacaoSubstituto.situacaoMembroComissao", "situacaoMembroComissaoSubstituto")->addSelect("situacaoMembroComissaoSubstituto");

        $query->leftJoin("membroComissao.profissionalEntity", "profissionalEntity")->addSelect("profissionalEntity");
        $query->leftJoin("membroSubstituto.profissionalEntity", "profissionalSubstitutoEntity")->addSelect("profissionalSubstitutoEntity");

        $query->where("informacaoComissaoMembro.id = :id");
        $query->andWhere("membroComissao.excluido = :excluido ");

        if (!empty($idCauUf)) {
            $query->andWhere("membroComissao.idCauUf = :idCauUf");
            $query->setParameter("idCauUf", $idCauUf);
        }

        $query->orderBy('membroComissao.idCauUf', 'ASC');
        $query->setParameter("id", $idInformacaoComissao);
        $query->setParameter("excluido", false);

        if (!empty($limit)) {
            $query->setMaxResults($limit);
        }

        $retorno = [];
        $membrosArray = $query->getQuery()->getArrayResult();
        if (!empty($membrosArray)) {
            $retorno = array_map(function ($membroComissao) {
                return MembroComissao::newInstance($membroComissao);
            }, $membrosArray);
        }

        return $retorno;
    }

    /**
     * Retorna a quantidade de membros para cada CAU UF.
     *
     * @param null $idCauUf
     * @param null $idInformacaoComissao
     * @return array|null
     */
    public function getTotalMembrosPorCauUf($idCauUf = null, $idInformacaoComissao = null)
    {
        try {
            $query = $this->createQueryBuilder("membroComissao");
            $query->select("membroComissao.idCauUf");
            $query->addSelect("COUNT(membroComissao.id) as quantidade");

            if (!empty($idInformacaoComissao)) {
                $query->innerJoin("membroComissao.informacaoComissaoMembro", "informacaoComissaoMembro");
            }

            $query->where("membroComissao.excluido = :excluido");
            $query->setParameter("excluido", false);

            if (!empty($idInformacaoComissao)) {
                $query->andWhere("informacaoComissaoMembro.id = :id");
                $query->setParameter("id", $idInformacaoComissao);
            }

            if (!empty($idCauUf)) {
                $query->andWhere("membroComissao.idCauUf = :idCauUf");
                $query->setParameter("idCauUf", $idCauUf);
            }
            $query->setParameter("excluido", false);
            $query->groupBy("membroComissao.idCauUf");

            return $query->getQuery()->getArrayResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Exclui logicamente todos os membros de uma comissão por id informacao e cau uf
     *
     * @param $idInformacaoComissao
     * @param $idCauUf
     * @return mixed
     */
    public function deleteMembrosPorInformacaoCauUf($idInformacaoComissao, $idCauUf)
    {
        $query = $this->createQueryBuilder('membroComissao');
        $q = $query->update('App\Entities\MembroComissao', 'membroComissao')
            ->set("membroComissao.excluido", $query->expr()->literal('t'))
            ->where('membroComissao.idCauUf = :idCauUf ')
            ->andWhere('membroComissao.informacaoComissaoMembro = :id ')
            ->setParameter("idCauUf", $idCauUf)
            ->setParameter("id", $idInformacaoComissao)
            ->getQuery();

        return $q->execute();
    }

    /**
     * Retorna a quantidade de membros que tem relação com a declaração informada.
     *
     * @param integer $idModulo
     *
     * @return integer
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountMembrosPorDeclaracao($idDeclaracao)
    {
        try {
            $query = $this->createQueryBuilder("membroComissao");
            $query->select("COUNT(membroComissao.id)");
            $query->where("membroComissao.idDeclaracao = :idDeclaracao");
            $query->setParameter("idDeclaracao", $idDeclaracao);

            return $query->getQuery()->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return 0;
        }
    }

    /**
     * Recupera as lista de membros de uma comissão pela eleição informada.
     *
     * @param $idEleicao
     * @return array
     */
    public function getMembrosPorEleicao($idEleicao)
    {
        $query = $this->createQueryBuilder("membroComissao");
        $query->innerJoin("membroComissao.informacaoComissaoMembro", "informacaoComissaoMembro");
        $query->innerJoin("informacaoComissaoMembro.atividadeSecundaria", "atividadeSecundaria");
        $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario");
        $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario");
        $query->innerJoin("calendario.eleicao", "eleicao");
        $query->where("eleicao.id = :idEleicao");
        $query->setParameter("idEleicao", $idEleicao);

        return $query->getQuery()->getResult();
    }

    /**
     * Retorna os membros da comissão que estão com o aceite do convite para membro de comissão pendente
     * @return array
     * @throws Exception
     */
    public function getMembrosComissaoConvitePendente($idAtividadePrincipal)
    {

        $dataNotificacao = Utils::getDataHoraZero();

        $query = $this->createQueryBuilder("membroComissao");
        $query->select(" DISTINCT atividadePrincipalCalendario.id id_ativ_principal, membroComissao.pessoa");
        $query->addSelect("membroComissao.idCauUf,situacaoMembroComissao.id sit, membroComissao.id id_membro");
        $query->addSelect("atividadeSecundaria_convite.dataInicio, atividadeSecundaria_convite.dataFim");

        $query->innerJoin("membroComissao.informacaoComissaoMembro", "informacaoComissaoMembro");
        $query->innerJoin("informacaoComissaoMembro.atividadeSecundaria", "atividadeSecundaria");
        $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario");
        $query->innerJoin("atividadePrincipalCalendario.atividadesSecundarias", "atividadeSecundaria_convite");
        $query->innerJoin("membroComissao.membroComissaoSituacao", "membroComissaoSituacao");
        $query->innerJoin("membroComissaoSituacao.situacaoMembroComissao", "situacaoMembroComissao");

        $query->where("atividadeSecundaria_convite.nivel = 4");
        $query->andWhere("membroComissaoSituacao.id in (" . $this->getSubQuerySituacaoAtualMembro() . ')');
        $query->andWhere("situacaoMembroComissao.id = :situacao");
        $query->andWhere("atividadeSecundaria_convite.dataFim -1  = :dataNotificacao");
        $query->setParameter("situacao", Constants::SITUACAO_MEMBRO_ACONFIRMAR);
        $query->andWhere("atividadePrincipalCalendario.id = :idAtividadePrincipal");
        $query->setParameter("idAtividadePrincipal", $idAtividadePrincipal);
        $query->setParameter("dataNotificacao", $dataNotificacao);
        $query->orderBy("atividadePrincipalCalendario.id");

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Método que retorna DQL para subconsulta onde retorna
     * a ultima situação do membro da comissão eleitoral
     *
     * @return string
     */
    public function getSubQuerySituacaoAtualMembro()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select("situacao.id");
        $query->from(MembroComissaoSituacao::class, "situacao");
        $query->where("situacao.membroComissao = membroComissao.id");
        $query->andWhere("situacao.data = (" . $this->getSubqueryMaxDataSituacaoMembro() . ")");

        return $query->getDQL();
    }

    /**
     * Retorna DQL oara subQuery da situação atual do membro
     * @return string
     */
    public function getSubqueryMaxDataSituacaoMembro()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select("max(situacao2.data)");
        $query->from(MembroComissaoSituacao::class, "situacao2");
        $query->where("situacao2.membroComissao = membroComissao.id");
        return $query->getDQL();
    }

    /**
     * Retorna o membro da comissão dado um determinado id.
     *
     * @param $idMembroComissao
     * @return \App\Entities\MembroComissao|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorId($idMembroComissao)
    {
        try {
            $query = $this->createQueryBuilder('membroComissao');
            $query->leftJoin("membroComissao.tipoParticipacao", "tipoParticipacao")->addSelect("tipoParticipacao");
            $query->where("membroComissao.id = :id");
            $query->setParameter("id", $idMembroComissao);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a lista de Membros dado um determinado membro da comissão
     *
     * @param $id
     * @return array|null
     */
    public function getListaMembrosPorMembro($id)
    {
        try {
            $query = $this->getQueryMembroComissao(true);
            $query->innerJoin("membroComissao.informacaoComissaoMembro",
                "informacaoComissaoMembro")->addSelect("informacaoComissaoMembro");
            $query->where("membroComissao.excluido = :excluido");
            $query->andWhere("tipoParticipacao.id in (:idsTipoParticipacao)");

            $subquery = $this->createQueryBuilder('membroComissao2');
            $subquery->select("MAX(informacaoComissaoMembro2.id)");
            $subquery->innerJoin("membroComissao2.informacaoComissaoMembro", "informacaoComissaoMembro2");
            $subquery->where("membroComissao2.excluido = :excluido AND membroComissao2.pessoa = :id");

            $query->andWhere("membroComissao.informacaoComissaoMembro IN ({$subquery})");

            $query->orderBy("tipoParticipacao.id", "ASC");
            $query->addOrderBy("membroComissao.id", "ASC");

            $parametros = [];
            $parametros['excluido'] = false;
            $parametros['id'] = $id;
            $parametros['idsTipoParticipacao'] = [
                Constants::TIPO_PARTICIPACAO_COORDENADOR,
                Constants::TIPO_PARTICIPACAO_COORDENADOR_ADJUNTO,
                Constants::TIPO_PARTICIPACAO_MEMBRO
            ];

            $query->setParameters($parametros);

            $membros = $query->getQuery()->getArrayResult();
            return array_map(function ($data) {
                return MembroComissao::newInstance($data);
            }, $membros);

        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna os dados da informacao da comissao por um membro (idPessoa)
     *
     * @param $idPessoa
     * @return array|null
     */
    public function getInformacaoPorMembro($idPessoa)
    {
        try {
            $dql = "SELECT membroComissao, informacaoComissaoMembro, atividadeSecundaria, atividadePrincipalCalendario, calendario, eleicao ";
            $dql .= "FROM App\Entities\MembroComissao membroComissao  ";
            $dql .= "INNER JOIN membroComissao.informacaoComissaoMembro informacaoComissaoMembro ";
            $dql .= "INNER JOIN informacaoComissaoMembro.atividadeSecundaria atividadeSecundaria ";
            $dql .= "INNER JOIN atividadeSecundaria.atividadePrincipalCalendario atividadePrincipalCalendario ";
            $dql .= "INNER JOIN atividadePrincipalCalendario.calendario calendario ";
            $dql .= "INNER JOIN calendario.eleicao eleicao ";

            $dql .= " WHERE membroComissao.pessoa = :id ";
            $dql .= " AND calendario.ativo = :ativo ";
            $dql .= " AND membroComissao.excluido = :excluido ";
            $dql .= " AND calendario.dataFimVigencia > :dataAtual ";
            $dql .= " ORDER BY informacaoComissaoMembro.id DESC ";

            $parametros = [];
            $parametros['excluido'] = false;
            $parametros['ativo'] = true;
            $parametros['id'] = $idPessoa;
            $parametros['dataAtual'] = Utils::getData();

            $query = $this->getEntityManager()->createQuery($dql);
            $query->setParameters($parametros);

            $membros = $query->getArrayResult();
            return array_map(function ($data) {
                return MembroComissao::newInstance($data);
            }, $membros);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retorna os membros da comissão dado um filtro.
     *
     * @param $filtroTO
     * @return array|null
     */
    public function getPorFiltro($filtroTO)
    {
        $query = $this->getQueryMembroComissao(true);
        $query->innerJoin("membroComissao.informacaoComissaoMembro", "informacaoComissaoMembro")->addSelect("informacaoComissaoMembro");
        $query->leftJoin("informacaoComissaoMembro.atividadeSecundaria", "atividadeSecundaria");
        $query->leftJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario");
        $query->leftJoin("atividadePrincipalCalendario.calendario", "calendario");
        $query->leftJoin("calendario.eleicao", "eleicao");

        $query->where("membroComissao.excluido = :excluido ");

        if (!empty($filtroTO->idCauUf)) {

            if ($filtroTO->idCauUf == Constants::COMISSAO_MEMBRO_CAU_BR_ID or $filtroTO->idCauUf == Constants::IES_ID) {
                $query->andWhere("membroComissao.idCauUf IN (:idCauCEN, :idCauIES)");
                $query->setParameter("idCauCEN", Constants::COMISSAO_MEMBRO_CAU_BR_ID);
                $query->setParameter("idCauIES", Constants::IES_ID);
            } else {
                $query->andWhere("membroComissao.idCauUf = :idCauUf");
                $query->setParameter("idCauUf", $filtroTO->idCauUf);
            }
        }

        if (!empty($filtroTO->ano)) {
            $query->andWhere("eleicao.ano = :ano");
            $query->setParameter("ano", $filtroTO->ano);
        }

        if (!empty($filtroTO->idEleicao)) {
            $query->andWhere("eleicao.id = :idEleicao");
            $query->setParameter("idEleicao", $filtroTO->idEleicao);
        }

        if (!empty($filtroTO->idInformacaoComissao)) {
            $query->andWhere("informacaoComissaoMembro.id = :id");
            $query->setParameter("id", $filtroTO->idInformacaoComissao);
        }

        if (!empty($filtroTO->tipoParticipacao)) {
            $query->andWhere("tipoParticipacao.id IN (:idTipoParticipacao)");

            $query->andWhere("calendario.dataInicioVigencia <= :dataAtual");
            $query->andWhere("calendario.dataFimVigencia >= :dataAtual");
            $query->andWhere("calendario.ativo = :ativo");

            if(!is_array($filtroTO->tipoParticipacao)) {
                $filtroTO->tipoParticipacao = [$filtroTO->tipoParticipacao];
            }
            $query->setParameter("dataAtual", Utils::getData());
            $query->setParameter("idTipoParticipacao", $filtroTO->tipoParticipacao);
            $query->setParameter("ativo", true);
        }

        if (!empty($filtroTO->idProfissional)) {
            $query->andWhere("membroComissao.pessoa = :idProfissional");
            $query->setParameter("idProfissional", $filtroTO->idProfissional);
        }

        $query->orderBy('membroComissao.idCauUf', 'ASC');
        $query->addOrderBy('tipoParticipacao.id', 'ASC');
        $query->addOrderBy('membroComissao.id', 'ASC');
        $query->setParameter("excluido", false);

        $retorno = [];
        $membrosArray = $query->getQuery()->getArrayResult();
        if (!empty($membrosArray)) {
            $retorno = array_map(function ($membroComissao) {
                return MembroComissao::newInstance($membroComissao);
            }, $membrosArray);
        }

        return $retorno;
    }

    /**
     * Retorna as UFs que possuem comissão
     */
    public function getUfsComissao()
    {
        try {
            $dql = "SELECT DISTINCT membroComissao.idCauUf ";
            $dql .= "FROM App\Entities\MembroComissao membroComissao ";
            $dql .= "INNER JOIN membroComissao.informacaoComissaoMembro informacaoComissaoMembro ";
            $dql .= "INNER JOIN informacaoComissaoMembro.atividadeSecundaria atividadeSecundaria ";
            $dql .= "INNER JOIN atividadeSecundaria.atividadePrincipalCalendario atividadePrincipalCalendario ";
            $dql .= "INNER JOIN atividadePrincipalCalendario.calendario calendario ";
            $dql .= "WHERE membroComissao.excluido = :excluido ";
            $dql .= " AND calendario.dataInicioVigencia <= :dataAtual ";
            $dql .= " AND calendario.dataFimVigencia >= :dataAtual ";

            $parametros = [];
            $parametros['excluido'] = false;
            $parametros['dataAtual'] = Utils::getData();

            $query = $this->getEntityManager()->createQuery($dql);
            $query->setParameters($parametros);

            return $query->getArrayResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Recupera o total de resposta de declaração referente a uma atividade principal nivel 1
     *
     * @param integer $idAtividadePrincipal
     * @return mixed
     */
    public function getQtdRespostaDeclaracaoPorAtividadePrincipal($idAtividadePrincipal)
    {
        try {
            $query = $this->createQueryBuilder("membroComissao");
            $query->select('COUNT( membroComissao.id )');
            $query->innerJoin("membroComissao.informacaoComissaoMembro", "informacaoComissaoMembro");
            $query->innerJoin("informacaoComissaoMembro.atividadeSecundaria", "atividadeSecundaria");
            $query->leftJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");

            $query->where("atividadePrincipal.id = :idAtividadePrincipal");
            $query->setParameter("idAtividadePrincipal", $idAtividadePrincipal);

            $query->andWhere("membroComissao.sitRespostaDeclaracao = :situacao");
            $query->setParameter('situacao', true);

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Verifica se o profissional e membro de comissao na UF informada
     *
     * @param int $idCalendario
     * @param int $idProfissional
     * @return MembroComissao
     */
    public function getPorCalendarioAndProfissional(int $idCalendario, int $idProfissional)
    {
        try {
            $query = $this->createQueryBuilder('membroComissao');
            $query->innerJoin("membroComissao.informacaoComissaoMembro", "informacaoComissaoMembro");
            $query->innerJoin("informacaoComissaoMembro.atividadeSecundaria", "atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario");
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario");
            $query->innerJoin("calendario.eleicao", "eleicao");
            $query->where("membroComissao.excluido = false ");

            $query->andWhere("membroComissao.pessoa = :idProfissional");
            $query->setParameter("idProfissional", $idProfissional);

            $query->andWhere('calendario.id = :idCalendario');
            $query->setParameter('idCalendario', $idCalendario);

            $dados = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_OBJECT);
            return $dados;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Verifica se o profissional e membro de comissao na UF informada
     *
     * @param int $idCalendario
     * @param int $idProfissional
     * @return MembroComissao
     */
    public function getPorChapaAndProfissional(int $idChapa, int $idProfissional)
    {
        try{
            $query = $this->createQueryBuilder('membroComissao');
            $query->innerJoin("membroComissao.informacaoComissaoMembro", "informacaoComissaoMembro");
            $query->innerJoin("informacaoComissaoMembro.atividadeSecundaria", "atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario");
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario");
            $query->innerJoin("calendario.eleicao", "eleicao");

            $query->where("membroComissao.pessoa = :idProfissional");
            $query->setParameter("idProfissional", $idProfissional);

            $dados = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_OBJECT);
            return $dados;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Verifica se o profissional e membro de comissao na UF informada
     *
     * @param int $idCalendario
     * @param int $idProfissional
     * @return array|null
     */
    public function getPorCalendariosAndProfissional(array $idsCalendario, int $idProfissional)
    {
        try {
            $query = $this->createQueryBuilder('membroComissao');
            $query->innerJoin("membroComissao.informacaoComissaoMembro", "informacaoComissaoMembro");
            $query->innerJoin("informacaoComissaoMembro.atividadeSecundaria", "atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario");
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario");
            $query->innerJoin("calendario.eleicao", "eleicao");
            $query->where("membroComissao.excluido = false ");

            $query->andWhere("membroComissao.pessoa = :idProfissional");
            $query->setParameter("idProfissional", $idProfissional);

            $query->andWhere('calendario.id IN (:idCalendario)');
            $query->setParameter('idCalendario', $idsCalendario);

            $dados = $query->getQuery()->getResult(AbstractQuery::HYDRATE_OBJECT);
            return $dados;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Consulta os membros da comissão (coordenadores) de acordo com id da atividade secundária (independente do nível)
     *
     * @param int $idAtividadeSecundaria
     * @param int $idCauUf
     * @return MembroComissao
     */
    public function getCoordenadoresPorAtividadeSecundaria(int $idAtividadeSecundaria, $idCauUf)
    {
        try {
            $query = $this->createQueryBuilder('membroComissao');
            $query->innerJoin("membroComissao.informacaoComissaoMembro", "informacaoComissaoMembro");
            $query->innerJoin("informacaoComissaoMembro.atividadeSecundaria", "atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario");
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario");
            $query->innerJoin("calendario.atividadesPrincipais", "atividadesPrincipais");
            $query->innerJoin("atividadesPrincipais.atividadesSecundarias", "atividadesSecundarias");
            $query->innerJoin("calendario.eleicao", "eleicao");
            $query->innerJoin("membroComissao.membroComissaoSituacao", "membroComissaoSituacao");
            $query->innerJoin("membroComissaoSituacao.situacaoMembroComissao", "situacaoMembroComissao");
            $query->where("membroComissao.excluido = false ");

            $query->andWhere('atividadesSecundarias.id = :idAtvSecundaria');
            $query->setParameter('idAtvSecundaria', $idAtividadeSecundaria);

            $query->andWhere('membroComissao.idCauUf = :idCauUf');
            $query->setParameter('idCauUf', $idCauUf);

            $query->andWhere('membroComissao.tipoParticipacao in (:idTipoPartcipacao)');
            $query->setParameter('idTipoPartcipacao', [
                Constants::TIPO_PARTICIPACAO_COORDENADOR,
                Constants::TIPO_PARTICIPACAO_COORDENADOR_ADJUNTO,
                Constants::TIPO_PARTICIPACAO_COORDENADOR_SUBSTITUTO
            ]);

            $query->andWhere("membroComissaoSituacao.id in (" . $this->getSubQuerySituacaoAtualMembro() . ')');
            $query->andWhere("situacaoMembroComissao.id = :situacao");
            $query->setParameter("situacao", Constants::SITUACAO_MEMBRO_CONFIRMADO);

            return $query->getQuery()->getResult();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Consulta os membros da comissão(membro e substituto) de acordo com id da atividade secundária (independente do nível)
     *
     * @param int $idAtividadeSecundaria
     * @param int $idCauUf
     * @return MembroComissao
     */
    public function getMembrosComissaoPorAtividadeSecundaria(int $idAtividadeSecundaria, $idCauUf)
    {
        try {
            $query = $this->createQueryBuilder('membroComissao');
            $query->innerJoin("membroComissao.informacaoComissaoMembro", "informacaoComissaoMembro");
            $query->innerJoin("informacaoComissaoMembro.atividadeSecundaria", "atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario");
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario");
            $query->innerJoin("calendario.atividadesPrincipais", "atividadesPrincipais");
            $query->innerJoin("atividadesPrincipais.atividadesSecundarias", "atividadesSecundarias");
            $query->innerJoin("calendario.eleicao", "eleicao");
            $query->innerJoin("membroComissao.membroComissaoSituacao", "membroComissaoSituacao");
            $query->innerJoin("membroComissaoSituacao.situacaoMembroComissao", "situacaoMembroComissao");
            $query->where("membroComissao.excluido = false ");

            $query->andWhere('atividadesSecundarias.id = :idAtvSecundaria');
            $query->setParameter('idAtvSecundaria', $idAtividadeSecundaria);

            $query->andWhere('membroComissao.idCauUf = :idCauUf');
            $query->setParameter('idCauUf', $idCauUf);

            $query->andWhere('membroComissao.tipoParticipacao in (:idTipoPartcipacao)');
            $query->setParameter('idTipoPartcipacao', [
                Constants::TIPO_PARTICIPACAO_MEMBRO,
                Constants::TIPO_PARTICIPACAO_SUBSTITUTO
            ]);

            $query->andWhere("membroComissaoSituacao.id in (" . $this->getSubQuerySituacaoAtualMembro() . ')');
            $query->andWhere("situacaoMembroComissao.id = :situacao");
            $query->setParameter("situacao", Constants::SITUACAO_MEMBRO_CONFIRMADO);

            return $query->getQuery()->getResult();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryMembroComissao($addProfissional = false): \Doctrine\ORM\QueryBuilder
    {
        $query = $this->createQueryBuilder('membroComissao');
        $query->innerJoin("membroComissao.membroComissaoSituacao",
            "membroComissaoSituacao")->addSelect("membroComissaoSituacao");
        $query->innerJoin("membroComissaoSituacao.situacaoMembroComissao",
            "situacaoMembroComissao")->addSelect("situacaoMembroComissao");
        $query->innerJoin("membroComissao.tipoParticipacao", "tipoParticipacao")->addSelect("tipoParticipacao");
        if($addProfissional) {
            $query->innerJoin("membroComissao.profissionalEntity", "profissional")->addSelect("profissional");
            $query->innerJoin("profissional.pessoa", "pessoa")->addSelect("pessoa");
        }

        $query->leftJoin("membroComissao.membroSubstituto", "membroSubstituto")->addSelect("membroSubstituto");
        $query->leftJoin("membroSubstituto.tipoParticipacao",
            "tipoParticipacaoSubstituto")->addSelect("tipoParticipacaoSubstituto");
        $query->leftJoin("membroSubstituto.membroComissaoSituacao",
            "membroComissaoSituacaoSubstituto")->addSelect("membroComissaoSituacaoSubstituto");
        $query->leftJoin("membroComissaoSituacaoSubstituto.situacaoMembroComissao",
            "situacaoMembroComissaoSubstituto")->addSelect("situacaoMembroComissaoSubstituto");
        if($addProfissional) {
            $query->leftJoin("membroSubstituto.profissionalEntity",
                "profissionalSubstituto")->addSelect("profissionalSubstituto");
            $query->leftJoin("profissionalSubstituto.pessoa", "pessoaSubstituto")->addSelect("pessoaSubstituto");
        }
        return $query;
    }

    /**
     * Recupera o membro comissao de acordo com o id Profissiona e a AtividadeSecundaria
     *
     * @param $idProfissional
     * @param $idAtividadeSecundaria
     * @param $isCoordenador
     * @return mixed
     */
    public function getMembroComissaoPorProfissionalEAtividadeSecundaria(
        $idProfissional,
        $idAtividadeSecundaria
    )
    {
        $query = $this->createQueryBuilder('membroComissao');
        $query->select("distinct membroComissao.idCauUf, tipoParticipacao.id as idTipoParticipacao, tipoParticipacao.descricao");
        $query->innerJoin("membroComissao.tipoParticipacao", "tipoParticipacao");
        $query->innerJoin('membroComissao.membroComissaoSituacao', 'ms');
        $query->innerJoin('ms.situacaoMembroComissao', 's');
        $query->innerJoin("membroComissao.informacaoComissaoMembro", "informacaoComissaoMembro");
        $query->innerJoin("informacaoComissaoMembro.atividadeSecundaria", "atividadeSecundaria");

        $query->where("membroComissao.pessoa = :idProfissional ");
        $query->andWhere('atividadeSecundaria.id = :idAtvSecundaria');
        $query->andWhere('membroComissao.excluido = :excluido');
        $query->andWhere('s.id = :situacao');


        $query->setParameter('idProfissional', $idProfissional);
        $query->setParameter('idAtvSecundaria', $idAtividadeSecundaria);
        $query->setParameter('excluido', false);
        $query->setParameter('situacao', Constants::SITUACAO_MEMBRO_CONFIRMADO);


        return $query->getQuery()->getResult();
    }

    /**
     * @param $idCauUf
     * @param array $ignorarPessoas
     * @return MembroComissao[]
     */
    public function getMembrosAtivosPorIdCauUf($idCauUf, $ignorarPessoas = [])
    {

        $qb = $this->createQueryBuilder('mc')
            ->join('mc.membroComissaoSituacao', 'ms')
            ->join('ms.situacaoMembroComissao', 's')
            ->andWhere('mc.idCauUf = :idCauUf')
            ->andWhere('mc.excluido = false')
            ->andWhere('s.id IN (:situacao)')
            ->setParameters([
                'idCauUf' => $idCauUf,
                'situacao' =>[
                    Constants::TIPO_PARTICIPACAO_COORDENADOR,
                    Constants::TIPO_PARTICIPACAO_COORDENADOR_SUBSTITUTO,
                    Constants::TIPO_PARTICIPACAO_COORDENADOR_ADJUNTO,
                    Constants::TIPO_PARTICIPACAO_MEMBRO,
                    Constants::TIPO_PARTICIPACAO_SUBSTITUTO
                 ]
            ]);

        if ($ignorarPessoas) {
            $qb->andWhere('mc.pessoa not in(:pessoas)')
                ->setParameter('pessoas', $ignorarPessoas);
        }

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @param MembroComissao|int $membroComissao
     * @param $idCauUf
     * @param array $ignorarPessoas
     * @return bool
     * @throws
     */
    public function isMembroAtivoIdCauUf($membroComissao, $idCauUf, $ignorarPessoas = [])
    {
        $qb = $this->createQueryBuilder('mc')
            ->select('count(1)')
            ->andWhere('mc.id = :id')
            ->andWhere('mc.idCauUf = :idCauUf')
            ->setParameters([
                'id' => $membroComissao,
                'idCauUf' => $idCauUf,
            ]);


        if ($ignorarPessoas) {
            $qb->andWhere('mc.pessoa not in(:pessoas)')
                ->setParameter('pessoas', $ignorarPessoas);
        }

        return (bool)$qb->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param $idProfissional
     * @param $idCauUf
     * @return bool
     */
    public function isCoordenador($idProfissional, $idCauUf)
    {
        $query = $this->createQueryBuilder('mc')
            ->select('mc.id')
            ->join('mc.tipoParticipacao', 'tp')
            ->join('mc.membroComissaoSituacao', 'ms')
            ->join('ms.situacaoMembroComissao', 's')
            ->andWhere('mc.profissionalEntity = :profissional')
            ->andWhere('tp.id in (:tipo_participacao)')
            ->andWhere('mc.idCauUf = :idCauUf')
            ->andWhere('s.id = :situacao')
            ->andWhere()
            ->setParameters([
                'tipo_participacao' => [
                    Constants::TIPO_PARTICIPACAO_COORDENADOR,
                    Constants::TIPO_PARTICIPACAO_COORDENADOR_ADJUNTO,
                    Constants::TIPO_PARTICIPACAO_COORDENADOR_SUBSTITUTO
                ],
                'profissional' => $idProfissional,
                'idCauUf' => $idCauUf,
                'situacao' => Constants::SITUACAO_MEMBRO_CONFIRMADO
            ]);

        $result =  $query->getQuery()->getResult();

        return empty($result) ? false : true;
    }

    /**
     * Consulta os membros da comissão (coordenadores) de acordo com id da atividade secundária (independente do nível)
     *
     * @param int $idAtividadeSecundaria
     * @param int $idCauUf
     * @return MembroComissao
     */
    public function getCoordenadoresPorFiltro($params)
    {
        try {
            $query = $this->createQueryBuilder('membroComissao');
            $query->innerJoin("membroComissao.informacaoComissaoMembro", "informacaoComissaoMembro");
            $query->innerJoin("informacaoComissaoMembro.atividadeSecundaria", "atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario");
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario");
            $query->innerJoin("calendario.atividadesPrincipais", "atividadesPrincipais");
            $query->innerJoin("atividadesPrincipais.atividadesSecundarias", "atividadesSecundarias");
            $query->innerJoin("calendario.eleicao", "eleicao");
            $query->innerJoin("membroComissao.membroComissaoSituacao", "membroComissaoSituacao");
            $query->innerJoin("membroComissaoSituacao.situacaoMembroComissao", "situacaoMembroComissao");
            $query->where("membroComissao.excluido = false ");

            $query->andWhere('membroComissao.tipoParticipacao in (:idTipoPartcipacao)');
            $query->setParameter('idTipoPartcipacao', [
                Constants::TIPO_PARTICIPACAO_COORDENADOR,
                Constants::TIPO_PARTICIPACAO_COORDENADOR_ADJUNTO,
                Constants::TIPO_PARTICIPACAO_COORDENADOR_SUBSTITUTO
            ]);

            $query->andWhere("membroComissaoSituacao.id in (" . $this->getSubQuerySituacaoAtualMembro() . ')');
            $query->andWhere("situacaoMembroComissao.id = :situacao");
            $query->setParameter("situacao", Constants::SITUACAO_MEMBRO_CONFIRMADO);

            if(!empty($params['idCauUf'])){
                $query->andWhere('membroComissao.idCauUf = :idCauUf');
                $query->setParameter('idCauUf', $params['idCauUf']);
            }

            return $query->getQuery()->getResult();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Recupera o membro comissao de acordo com o id Profissiona e a AtividadeSecundaria
     *
     * @param $idProfissional
     * @param null $idCauUf
     * @param null $idInformacaoComissaoMembro
     * @return mixed
     */
    public function getMembroComissaoPorProfissionalAndIdInformacaoComissaoMembroAndIdCauUf(
        $idProfissional,
        $idCauUf = null,
        $idInformacaoComissaoMembro = null
    )
    {
        $query = $this->createQueryBuilder('membroComissao');
        $query->innerJoin("membroComissao.tipoParticipacao", "tipoParticipacao")->addSelect("tipoParticipacao");
        $query->innerJoin("membroComissao.informacaoComissaoMembro", "informacaoComissaoMembro");
        $query->innerJoin("membroComissao.membroComissaoSituacao", "membroComissaoSituacao")->addSelect("membroComissaoSituacao");
        $query->innerJoin("membroComissaoSituacao.situacaoMembroComissao", "situacaoMembroComissao")->addSelect("situacaoMembroComissao");

        $query->where("membroComissao.pessoa = :idProfissional ");

        if(!empty($idInformacaoComissaoMembro)) {
            $query->andWhere("informacaoComissaoMembro.id = :informacaoComissaoMembro ")
                ->setParameter('informacaoComissaoMembro', $idInformacaoComissaoMembro);
        }

        if(!empty($idCauUf)) {
            $query->andWhere('membroComissao.idCauUf = :idCauUf')
                ->setParameter('idCauUf', $idCauUf);
        }

        $query->andWhere('situacaoMembroComissao.id = :situacaoMembroComissao');
        $query->andWhere('tipoParticipacao.id in (:tipoParticipacao)');
        $query->andWhere('membroComissao.excluido = :excluido');

        $query->setParameter('idProfissional', $idProfissional);
        $query->setParameter('situacaoMembroComissao', Constants::SITUACAO_MEMBRO_CONFIRMADO);
        $query->setParameter('tipoParticipacao', [Constants::TIPO_PARTICIPACAO_COORDENADOR_SUBSTITUTO,
            Constants::TIPO_PARTICIPACAO_SUBSTITUTO], \Doctrine\DBAL\Connection::PARAM_INT_ARRAY);
        $query->setParameter('excluido', false);

        return $query->getQuery()->getResult();
    }

    /**
     * Recupera o membro comissao de acordo com o id Profissiona e a AtividadeSecundaria
     *
     * @param $idProfissional
     * @return int
     * @throws NonUniqueResultException
     */
    public function getFilialMembroComissaoAtual(
        $idProfissional
    )
    {
        $query = $this->createQueryBuilder('membroComissao');
        $query->select("membroComissao.idCauUf");

        $query->where("membroComissao.pessoa = :idProfissional ");
        $query->andWhere('membroComissao.excluido = :excluido');

        $subquery = $this->createQueryBuilder('membroComissao2');
        $subquery->select("MAX(membroComissao2.id)");
        $subquery->where("membroComissao2.excluido = :excluido AND membroComissao2.pessoa = :idProfissional");

        $query->andWhere("membroComissao.id = ({$subquery})");

        $query->setParameter('idProfissional', $idProfissional);
        $query->setParameter('excluido', false);

        return $query->getQuery()->getSingleScalarResult();
    }
}
