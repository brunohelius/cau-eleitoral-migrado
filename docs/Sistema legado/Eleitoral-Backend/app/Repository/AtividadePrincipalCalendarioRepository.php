<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 29/08/2019
 * Time: 09:00
 */

namespace App\Repository;

use App\Entities\AtividadePrincipalCalendario;
use App\Entities\HistoricoExtratoConselheiro;
use app\To\AtividadePrincipalFiltroTO;
use Illuminate\Support\Facades\Date;
use App\Config\Constants;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use App\To\AtividadePrincipalCalendarioTO;
use App\Entities\Calendario;
use App\To\CalendarioTO;
use Doctrine\ORM\AbstractQuery;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'HistoricoCalendario'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class AtividadePrincipalCalendarioRepository extends AbstractRepository
{

    /**
     * Retorna as atividades principais do calendario conforme o id informado.
     *
     * @param $idCalendario
     * @return array|null
     */
    public function getPorCalendario($idCalendario)
    {
        try {
            $query = $this->createQueryBuilder('atividadePrincipalCalendario');
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario");
            $query->where("calendario.id = :id");

            $query->setParameter("id", $idCalendario);

            $atividadePrincipal = $query->getQuery()->getArrayResult();

            return array_map(function ($dadosAtividade) {
                return AtividadePrincipalCalendario::newInstance($dadosAtividade);
            }, $atividadePrincipal);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna lista de Atividades Principais de calendários concluídos.
     */
    public function getAtividadePrincipal(){
        $dql = "SELECT  ";
        $dql .= " atividadePrincipalCalendario, ";
        $dql .= " atividadeSecundaria ";

        $dql .= " FROM App\Entities\AtividadePrincipalCalendario atividadePrincipalCalendario ";
        $dql .= " JOIN atividadePrincipalCalendario.atividadesSecundarias atividadeSecundaria ";
        $dql .= " JOIN atividadePrincipalCalendario.calendario as calendario ";

        $subQueryCalendariosConcluidosIds = $this->getSubQueryCalendariosConcluidosIds();
        $dql .= "WHERE calendario.id IN $subQueryCalendariosConcluidosIds";
        $dql .= "ORDER BY calendario.id, atividadePrincipalCalendario.id, atividadeSecundaria.id";

        $query = $this->getEntityManager()->createQuery($dql);

        return $query->getArrayResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * Retorna as atividades principais do calendario conforme o id informado.
     *
     * @param $idCalendario
     * @param AtividadePrincipalFiltroTO $filtroTO
     * @return array|null
     */
    public function getPorCalendarioComFiltro($idCalendario, $filtroTO)
    {
        try {
            $query = $this->createQueryBuilder('atividadePrincipalCalendario');
            $query->innerJoin("atividadePrincipalCalendario.calendario", "calendario");
            $query->innerJoin("atividadePrincipalCalendario.atividadesSecundarias", "atividadesSecundarias")
            ->addSelect('atividadesSecundarias');
            $query->leftJoin('atividadesSecundarias.informacaoComissaoMembro', 'informacaoComissaoMembro')
            ->addSelect('informacaoComissaoMembro');
            $query->leftJoin('informacaoComissaoMembro.documentoComissaoMembro', 'documentoComissaoMembro')
                ->addSelect('documentoComissaoMembro');
            $query->leftJoin('atividadesSecundarias.emailsAtividadeSecundaria', 'emailsAtividadeSecundaria')
                ->addSelect("emailsAtividadeSecundaria");
            $query->leftJoin('emailsAtividadeSecundaria.emailsTipos', 'emailsTipos')
                ->addSelect("emailsTipos");
            $query->leftJoin('emailsTipos.tipoEmail', 'tipoEmail')
                ->addSelect("tipoEmail");

            $query->where("calendario.id = :idCalendario");
            $query->setParameter("idCalendario", $idCalendario);

            $query->orderBy("atividadesSecundarias.id", "ASC");

            if(!empty($filtroTO->getAtividadeSecundariaDataInicio())){
                $query->andWhere("atividadesSecundarias.dataInicio >= :dataInicio");
                $query->setParameter("dataInicio", $filtroTO->getAtividadeSecundariaDataInicio());
            }
            
            if (! empty($filtroTO->getAtividadeSecundariaDataFim())) {
                $query->andWhere("atividadesSecundarias.dataFim <= :dataFim");
                $query->setParameter("dataFim", $filtroTO->getAtividadeSecundariaDataFim());
            }

            $query->orderBy('atividadePrincipalCalendario.nivel', 'ASC');
            $query->addOrderBy('atividadesSecundarias.nivel', 'ASC');

            $atividadesPrincipais = $query->getQuery()->getArrayResult();

            return array_map(static function($atividadePrincipal){
                return AtividadePrincipalCalendario::newInstance($atividadePrincipal);
            }, $atividadesPrincipais);

        } catch (NoResultException $e) {
            return null;
        }
    }
    
    /**
     * Retorna Sub Query para buscar lista de ‘ids’ de calendários concluídos.
     * 
     * @return string
     */
    private function getSubQueryCalendariosConcluidosIds(){
        $dql = " (SELECT calendario5.id";
        $dql .= " FROM App\Entities\CalendarioSituacao calendarioSituacao5 ";
        $dql .= " INNER JOIN calendarioSituacao5.calendario calendario5 ";
        $dql .= " WHERE calendario5.id = calendario.id ";
        $dql .= " AND calendarioSituacao5.data = (select MAX(calendarioSituacao6.data) FROM App\Entities\CalendarioSituacao calendarioSituacao6  ";
        $dql .= " WHERE calendarioSituacao6.calendario = calendario5.id ))  ";
        return $dql;
    }
    
}
