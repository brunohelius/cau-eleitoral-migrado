<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 30/08/2019
 * Time: 10:39
 */

namespace App\Repository;

use App\Config\Constants;
use App\Entities\AtividadeSecundariaCalendario;
use App\Entities\Calendario;
use App\To\EmailAtividadeSemParametrizacaoTO;
use App\Util\Utils;
use DateTime;
use Doctrine\DBAL\Driver\Statement;
use Exception;
use Illuminate\Support\Facades\Date;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'HistoricoCalendario'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class AtividadeSecundariaCalendarioRepository extends AbstractRepository
{

    /**
     * Recupera a atividade secundária do calendário de acordo com o 'id' informado.
     *
     * @param $id
     * @return AtividadeSecundariaCalendario
     *
     * @throws NonUniqueResultException
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('atividadeSecundaria');
            $query->leftJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario")->addSelect("atividadePrincipalCalendario");
            $query->leftJoin("atividadePrincipalCalendario.calendario", "calendario")->addSelect("calendario");
            $query->leftJoin("calendario.eleicao", "eleicao")->addSelect("eleicao");
            $query->leftJoin("atividadeSecundaria.informacaoComissaoMembro", "informacaoComissaoMembro")->addSelect("informacaoComissaoMembro");
            $query->leftJoin("calendario.cauUf", "cauUf")->addSelect("cauUf");
            $query->where("atividadeSecundaria.id = :id");
            $query->setParameter("id", $id);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Buscar Atividade secundária por calendário.
     * @param $idCalendario
     * @param integer $nivelPrincipal
     * @param integer $nivelSecundaria
     * @return array|mixed|Statement|NULL|NULL
     */
    public function getPorCalendario($idCalendario, $nivelPrincipal, $nivelSecundaria){
        try {
            $query = $this->createQueryBuilder('atividadeSecundaria');
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario")->addSelect("atividadePrincipalCalendario");
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario")->addSelect("calendario");
            $query->where("calendario.id = :idCalendario");
            $query->andWhere("atividadePrincipalCalendario.nivel = :nivelPrincipal");
            $query->andWhere("atividadeSecundaria.nivel = :nivelSecundaria");
            $query->setParameters([':idCalendario' => $idCalendario,':nivelPrincipal' => $nivelPrincipal, ':nivelSecundaria' => $nivelSecundaria ]);

            return $query->getQuery()->getSingleResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Buscar Atividades secundárias por calendário.
     * @param $idCalendario
     * @param integer $nivelPrincipal
     * @param integer $nivelSecundaria
     * @return array|mixed|Statement|NULL|NULL
     * @throws NonUniqueResultException
     */
    public function getAtividadesPorCalendario($idCalendario, $nivelPrincipal = null, $nivelSecundaria = null){
        try {
            $query = $this->createQueryBuilder('atividadeSecundaria');
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal")
                ->addSelect("atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario")
                ->addSelect("calendario");

            $query->where("calendario.id = :idCalendario");
            $query->setParameter(':idCalendario', $idCalendario );

            if(!empty($nivelPrincipal)) {
                $query->andWhere("atividadePrincipal.nivel = :nivelPrincipal");
                $query->setParameter(':nivelPrincipal', $nivelPrincipal);
            }

            if(!empty($nivelSecundaria)) {
                $query->andWhere("atividadeSecundaria.nivel = :nivelSecundaria");
                $query->setParameter(':nivelSecundaria', $nivelSecundaria);
            }

            return $query->getQuery()->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Buscar Atividade secundária por calendário.
     * @param $idCalendario
     * @param integer $nivelPrincipal
     * @param integer $nivelSecundaria
     * @return array|mixed|Statement|NULL|NULL
     * @throws NonUniqueResultException
     */
    public function getPorAtividadeSecundaria($idAtividadeSecundaria, $nivelPrincipal, $nivelSecundaria){
        try {
            $query = $this->createQueryBuilder('atividadeSecundaria');
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario")
                ->addSelect("atividadePrincipalCalendario");
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario")
                ->addSelect("calendario");
            $query->innerJoin("calendario.atividadesPrincipais","atividadesPrincipais");
            $query->innerJoin("atividadesPrincipais.atividadesSecundarias","atividadesSecundarias");

            $query->where("atividadesSecundarias.id = :idAtividadeSecundaria");
            $query->andWhere("atividadePrincipalCalendario.nivel = :nivelPrincipal");
            $query->andWhere("atividadeSecundaria.nivel = :nivelSecundaria");
            $query->setParameters([
                ':idAtividadeSecundaria' => $idAtividadeSecundaria,
                ':nivelPrincipal' => $nivelPrincipal,
                ':nivelSecundaria' => $nivelSecundaria
            ]);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Buscar Atividade secundária por calendário.
     * @param $idCalendario
     * @param integer $nivelPrincipal
     * @param integer $nivelSecundaria
     * @return array|mixed|Statement|NULL|NULL
     * @throws NonUniqueResultException
     */
    public function getPorChapaEleicao($idChapaELeicao, $nivelPrincipal, $nivelSecundaria){
        try {
            $query = $this->createQueryBuilder('atividadeSecundaria');
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario")
                ->addSelect("atividadePrincipalCalendario");
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario")
                ->addSelect("calendario");
            $query->innerJoin("calendario.atividadesPrincipais","atividadesPrincipais");
            $query->innerJoin("atividadesPrincipais.atividadesSecundarias","atividadesSecundarias");
            $query->innerJoin("atividadesSecundarias.chapasEleicao","chapasEleicao");

            $query->where("chapasEleicao.id = :idChapaELeicao");
            $query->andWhere("atividadePrincipalCalendario.nivel = :nivelPrincipal");
            $query->andWhere("atividadeSecundaria.nivel = :nivelSecundaria");
            $query->setParameters([
                ':idChapaELeicao' => $idChapaELeicao,
                ':nivelPrincipal' => $nivelPrincipal,
                ':nivelSecundaria' => $nivelSecundaria
            ]);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Busca Atividade Secundária por data de início e data de fim.
     * @param null $dataInicio
     * @param null $dataFim
     * @param null $nivelPrincipal
     * @param null $nivelSecundaria
     * @return mixed
     */
    public function getAtividadesSecundariasPorVigencia(
        $dataInicio = null,
        $dataFim = null,
        $nivelPrincipal = null,
        $nivelSecundaria = null
    ) {


        $query = $this->createQueryBuilder('atividadeSecundaria');
        $query->leftJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario")->addSelect("atividadePrincipalCalendario");
        $query->leftJoin("atividadePrincipalCalendario.calendario", "calendario")->addSelect("calendario");
        $query->leftJoin("calendario.situacoes", "situacoes");
        $query->leftJoin("situacoes.situacaoCalendario", "situacaoCalendario");

        $subQuerySituacaoAtual = $this->getSubQueryCalendarioSituacaoAtual();
        $query->where("situacoes.id = $subQuerySituacaoAtual");

        $query->andWhere("situacaoCalendario.id = :idSituacaoCalendario");
        $query->setParameter('idSituacaoCalendario', Constants::SITUACAO_CALENDARIO_CONCLUIDO);

        $query->andWhere('calendario.ativo = :ativo');
        $query->setParameter('ativo', true);

        if (!empty($dataInicio)) {
            $query->andWhere('atividadeSecundaria.dataInicio = :dataInicio');
            $query->setParameter('dataInicio', $dataInicio);
        }

        if (!empty($dataFim)) {
            $query->andWhere('atividadeSecundaria.dataFim = :dataFim');
            $query->setParameter('dataFim', $dataFim);
        }

        if (!empty($nivelPrincipal)) {
            $query->andWhere('atividadePrincipalCalendario.nivel = :nivelPrincipal');
            $query->setParameter('nivelPrincipal', $nivelPrincipal);
        }

        if (!empty($nivelSecundaria)) {
            $query->andWhere('atividadeSecundaria.nivel = :nivelSecundaria');
            $query->setParameter('nivelSecundaria', $nivelSecundaria);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Recupera a listagem de emails das subatividades para as informaçãoes não parametrizadas e que se
     * encontram dentro do prazo de vencimento.
     *
     * @param DateTime $dataFim
     *
     * @return array
     * @throws Exception
     */
    public function getAtividadesSemParametrizacaoInicialPorDataFim(DateTime $dataFim)
    {
        $dql = $this->getSubQueryIdResponsavelConclusaoCalendario();

        $query = $this->createQueryBuilder("atividadeSecundaria");
        $query->select("atividadeSecundaria.id, " . $dql);
        $query->join("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
        $query->join("atividadePrincipal.calendario", "calendario");
        $query->leftJoin("atividadeSecundaria.informacaoComissaoMembro", "informacaoComissaoMembro");
        $query->leftJoin("informacaoComissaoMembro.documentoComissaoMembro", "documentoComissaoMembro");
        $query->leftJoin("calendario.situacoes", "situacoes");
        $query->leftJoin("situacoes.situacaoCalendario", "situacaoCalendario");

        $query->where("atividadePrincipal.nivel = :nivel");
        $query->setParameter("nivel", 1);

        $query->andWhere("atividadeSecundaria.nivel = :nivelSecundaria");
        $query->setParameter("nivelSecundaria", 1);

        $query->andWhere("atividadeSecundaria.dataFim = :dataFim");
        $query->setParameter("dataFim", $dataFim->format('Y-m-d'));

        $subQuerySituacaoAtual = $this->getSubQueryCalendarioSituacaoAtual();
        $query->andWhere("situacoes.id = $subQuerySituacaoAtual");

        $query->andWhere("situacaoCalendario.id = :idSituacaoCalendario");
        $query->setParameter('idSituacaoCalendario', Constants::SITUACAO_CALENDARIO_CONCLUIDO);

        $query->setParameter("acaoConcluir", Constants::ACAO_CALENDARIO_CONCLUIR);

        $query->andWhere("informacaoComissaoMembro.id IS NULL OR documentoComissaoMembro.id IS NULL");

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Retorna a atividade secundária por nível do nível 1 da atividade principal
     * baseada no id do membro da comissão
     *
     * @param $idMembroComissao
     * @param $nivel
     * @return int
     * @throws NonUniqueResultException
     */
    public function getIdAtividadeSecundariaPorNivelMembroComissao($idMembroComissao, $nivel)
    {
        try {
            $query = $this->createQueryBuilder("atividadeSecundaria");
            $query->select("atividadeSecundaria_email.id");
            $query->innerJoin("atividadeSecundaria.informacaoComissaoMembro", "informacaoComissaoMembro");
            $query->innerJoin("informacaoComissaoMembro.membrosComissao", "membrosComissao");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario");
            $query->innerJoin("atividadePrincipalCalendario.atividadesSecundarias","atividadeSecundaria_email");
            $query->Where("atividadeSecundaria_email.nivel = :nivel");
            $query->setParameter("nivel", $nivel);
            $query->andWhere("membrosComissao = :idMembroComissao");
            $query->setParameter("idMembroComissao", $idMembroComissao);

            return $query->getQuery()->getSingleScalarResult();

        } catch(\Doctrine\ORM\NoResultException $e) {
            return 0;
        }
    }

    /**
     * Retorna o id da atividade secundária por atividade principal e nível
     *
     * @param $idAtividadePrincipal
     * @param $nivel
     * @return int|null
     * @throws NonUniqueResultException
     */
    public function getIdAtividadeSecundariaPorAtividadePrincipalENivel($idAtividadePrincipal, $nivel)
    {
        try {
            $query = $this->createQueryBuilder("atividadeSecundaria");
            $query->select("atividadeSecundaria.id");
            $query->Where("atividadeSecundaria.nivel = :nivel");
            $query->setParameter("nivel", $nivel);
            $query->andWhere("atividadeSecundaria.atividadePrincipalCalendario = :idAtividadePrincipal");
            $query->setParameter("idAtividadePrincipal", $idAtividadePrincipal);

            return $query->getQuery()->getSingleScalarResult();

        } catch(NonUniqueResultException|NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna uma lista de Atividades Secundárias até calendário e eleição dado 2 níveis de busca
     * e Para eleicoes que estejam vigentes e ativas.
     *
     * @param int $nivelPrincipal
     * @param int $nivelSecundaria
     *
     * @return AtividadeSecundariaCalendario
     * @throws NonUniqueResultException
     */
    public function getAtividadeSecundariaAtivaPorNivel($nivelPrincipal, $nivelSecundaria, $isApenasVigenciaCalendario = false)
    {
        try {
            $query = $this->createQueryBuilder("atividadeSecundaria")->addSelect("atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario")->addSelect("atividadePrincipalCalendario");
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario")->addSelect("calendario");
            $query->innerJoin("calendario.eleicao", "eleicao")->addSelect("eleicao");

            $query->leftJoin("calendario.situacoes", "situacoes");
            $query->leftJoin("situacoes.situacaoCalendario", "situacaoCalendario");

            $query->Where("atividadePrincipalCalendario.nivel = :nivelPrinc");
            $query->andWhere("atividadeSecundaria.nivel = :nivelSec");
            if(!$isApenasVigenciaCalendario) {
                $query->andWhere("atividadeSecundaria.dataInicio <= :dataAtual");
                $query->andWhere("atividadeSecundaria.dataFim >= :dataAtual");
            }
            $query->andWhere("calendario.dataInicioVigencia <= :dataAtual");
            $query->andWhere("calendario.dataFimVigencia >= :dataAtual");
            $query->andWhere("calendario.ativo = :ativo");
            $query->andWhere("calendario.excluido = :excluido");

            $query->andWhere("situacoes.id in {$this->getSubQueryCalendarioSituacaoAtual()}");
            $query->andWhere("situacaoCalendario.id = :idSituacaoConcluido");
            $query->setParameter("idSituacaoConcluido", Constants::SITUACAO_CALENDARIO_CONCLUIDO);


            $query->setParameter("nivelPrinc", $nivelPrincipal);
            $query->setParameter("nivelSec", $nivelSecundaria);
            $query->setParameter("dataAtual", Utils::getData());
            $query->setParameter("ativo", true);
            $query->setParameter("excluido", false);
            $query->orderBy('calendario.id', 'DESC');
            $query->setMaxResults(1);

            $atividadeSecundaria = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return AtividadeSecundariaCalendario::newInstance($atividadeSecundaria);

        }catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Recupera a Atividade Secundaria de acordo com o Calendario e os Niveis informados.
     *
     * @param $nivelPrincipal
     * @param $nivelSecundaria
     * @param $idCalendario
     * @return AtividadeSecundariaCalendario|null
     * @throws NonUniqueResultException
     */
    public function getAtividadeSecundariaPorCalendario($nivelPrincipal, $nivelSecundaria, $idCalendario)
    {
        try {
            $query = $this->createQueryBuilder("atividadeSecundaria")->addSelect("atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario")->addSelect("atividadePrincipalCalendario");
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario")->addSelect("calendario");
            $query->innerJoin("calendario.eleicao", "eleicao")->addSelect("eleicao");
            $query->Where("atividadePrincipalCalendario.nivel = :nivelPrinc");
            $query->andWhere("atividadeSecundaria.nivel = :nivelSec");
            $query->andWhere("calendario.id = :idCalendario");

            $query->setParameter("nivelPrinc", $nivelPrincipal);
            $query->setParameter("nivelSec", $nivelSecundaria);
            $query->setParameter("idCalendario", $idCalendario);

            $atividadeSecundaria = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return AtividadeSecundariaCalendario::newInstance($atividadeSecundaria);

        }catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Buscar Atividade secundária por calendário.
     * @param integer $nivelPrincipal
     * @param integer $nivelSecundaria
     * @return array|mixed|Statement|NULL|NULL
     * @throws NonUniqueResultException
     */
    public function getAtividadeSecundariaPorNiveis($nivelPrincipal, $nivelSecundaria){
        try {
            $query = $this->createQueryBuilder('atividadeSecundaria');
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipalCalendario")
                ->addSelect("atividadePrincipalCalendario");
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario")
                ->addSelect("calendario");
            $query->innerJoin("calendario.atividadesPrincipais","atividadesPrincipais");
            $query->innerJoin("atividadesPrincipais.atividadesSecundarias","atividadesSecundarias");

            $query->where("atividadePrincipalCalendario.nivel = :nivelPrincipal");
            $query->andWhere("atividadeSecundaria.nivel = :nivelSecundaria");
            $query->setParameters([
                ':nivelPrincipal' => $nivelPrincipal,
                ':nivelSecundaria' => $nivelSecundaria
            ]);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * DQL para seleção de situação atual de calendário.
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
     * DQL para seleção do responsável pela conclusão do calendário
     * @return string
     */
    private function getSubQueryIdResponsavelConclusaoCalendario(): string
    {
        $dql = " (SELECT historico.responsavel FROM App\Entities\HistoricoCalendario historico";
        $dql .= " WHERE historico.id IN ";
        $dql .= " (SELECT MAX (historico2.id) FROM App\Entities\HistoricoCalendario historico2 ";
        $dql .= " JOIN historico2.calendario as calendario2 ";
        $dql .= " WHERE calendario2.id = calendario.id ";
        $dql .= " AND historico2.acao = :acaoConcluir )";
        $dql .= " ) as idResponsavel";
        return $dql;
    }


}
