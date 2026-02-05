<?php
namespace App\Repository;

use App\Entities\EmailAtividadeSecundaria;
use App\Entities\TipoEmailAtividadeSecundaria;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'EmailAtividadeSecundaria'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class EmailAtividadeSecundariaRepository extends AbstractRepository
{

    /**
     * Retorna lista de CorpoEmail relacionados a uma determinada AtividadePrincipal.
     *
     * @param integer $idAtividadeSecundaria
     * @return mixed|Statement|array|NULL
     */
    public function getEmailAtividadeSecundariaPorAtividadeSecundaria($idAtividadeSecundaria)
    {
        try {
            $query = $this->createQueryBuilder('emailAtividadeSecundaria');
            $query->innerJoin('emailAtividadeSecundaria.corpoEmail', 'corpoEmail')->addSelect("corpoEmail");
            $query->leftJoin("corpoEmail.cabecalhoEmail", "cabecalhoEmail")->addSelect("cabecalhoEmail");
            $query->leftJoin('emailAtividadeSecundaria.emailsTipos', 'emailsTipos')->addSelect('emailsTipos');
            $query->leftJoin('emailsTipos.tipoEmail', 'tipoEmail')->addSelect('tipoEmail');
            $query->innerJoin('emailAtividadeSecundaria.atividadeSecundaria', 'atividadeSecundaria');        

            $query->where("corpoEmail.ativo = true");
            $query->where("atividadeSecundaria.id = :id");
            $query->setParameter("id", $idAtividadeSecundaria);

            return array_map(function ($email) {
                return EmailAtividadeSecundaria::newInstance($email);
            }, $query->getQuery()->getArrayResult());
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna EmailAtividadeSecundaria por id.
     *
     * @param integer $id
     * @return \app\Entities\EmailAtividadeSecundaria|NULL
     * @throws NonUniqueResultException
     */
    public function getEmailAtividadeSecundariaPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('emailAtividadeSecundaria');
            $query->innerJoin('emailAtividadeSecundaria.corpoEmail', 'corpoEmail')
                ->addSelect("corpoEmail");
            $query->innerJoin('emailAtividadeSecundaria.atividadeSecundaria', 'atividadeSecundaria')
                ->addSelect('atividadeSecundaria');
            $query->leftJoin('emailAtividadeSecundaria.emailsTipos', 'emailsTipos')->addSelect('emailsTipos');
            $query->leftJoin('emailsTipos.tipoEmail', 'tipoEmail')->addSelect('tipoEmail');
            $query->leftJoin("corpoEmail.cabecalhoEmail", "cabecalhoEmail")->addSelect("cabecalhoEmail");
            $query->where("emailAtividadeSecundaria.id = :id");
            $query->setParameter("id", $id);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna EmailAtividadeSecundaria por id de Corpo de E-mail e id de Atividade Secundária.
     *
     * @param integer $idCorpoEmail
     * @return EmailAtividadeSecundaria|mixed|Statement|array|NULL
     * @throws NonUniqueResultException
     */
    public function getEmailAtividadeSecundariaPorCorpoEmail($idCorpoEmail, $idAtividadeSecundaria){
        try{
            $query = $this->createQueryBuilder('emailAtividadeSecundaria');
            $query->innerJoin('emailAtividadeSecundaria.corpoEmail', 'corpoEmail')->addSelect("corpoEmail");
            $query->innerJoin('emailAtividadeSecundaria.atividadeSecundaria', 'atividadeSecundaria');
            $query->where('corpoEmail.id = :idCorpoEmail');
            $query->andWhere('atividadeSecundaria.id = :idAtividadeSecundaria');
            $query->setParameter(':idCorpoEmail', $idCorpoEmail);
            $query->setParameter(':idAtividadeSecundaria', $idAtividadeSecundaria);
            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
    
    /**
     * Retorna EmailAtividadeSecundaria por id de Corpo de E-mail.
     *
     * @param integer $idCorpoEmail
     * @return mixed|Statement|array|NULL
     */
    public function getEmailsAtividadeSecundariaPorCorpoEmail($idCorpoEmail){
        $query = $this->createQueryBuilder('emailAtividadeSecundaria');
        $query->innerJoin('emailAtividadeSecundaria.corpoEmail', 'corpoEmail')->addSelect("corpoEmail");
        $query->innerJoin('emailAtividadeSecundaria.atividadeSecundaria', 'atividadeSecundaria');
        $query->where('corpoEmail.id = :idCorpoEmail');
        $query->setParameter(':idCorpoEmail', $idCorpoEmail);
        return $query->getQuery()->getResult();
    }

    /**
     * Retorna tipo de e-mail por atividade secundária.
     * @param integer $idAtividadeSecundaria
     * @param integer $idTipo
     * @return \app\Entities\EmailAtividadeSecundaria|NULL
     * @throws NonUniqueResultException
     */
    public function getEmailAtividadeSecundariaPorTipo($idAtividadeSecundaria, $idTipo){
        try {
            $query = $this->createQueryBuilder('emailAtividadeSecundaria');
            $query->innerJoin('emailAtividadeSecundaria.corpoEmail', 'corpoEmail')->addSelect("corpoEmail");
            $query->leftJoin("corpoEmail.cabecalhoEmail", "cabecalhoEmail")->addSelect("cabecalhoEmail");
            $query->innerJoin('emailAtividadeSecundaria.emailsTipos', 'emailsTipos')->addSelect('emailsTipos');
            $query->innerJoin('emailsTipos.tipoEmail', 'tipoEmail')->addSelect('tipoEmail');
            $query->innerJoin('emailAtividadeSecundaria.atividadeSecundaria', 'atividadeSecundaria');
            $query->where("atividadeSecundaria.id = :idAtividadeSecundaria");
            $query->andWhere('tipoEmail.id = :idTipo');
            $query->setParameters(['idAtividadeSecundaria' => $idAtividadeSecundaria, 'idTipo' => $idTipo]);
                        
            return EmailAtividadeSecundaria::newInstance($query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY));
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna tipo de e-mail por atividade secundária.
     * @param integer $idEmailAtividadeSecundaria
     * @return array|NULL
     */
    public function getIdsTipoEmailPorEmilAtividadeSecundaria($idEmailAtividadeSecundaria){
        try {
            $query = $this->createQueryBuilder('emailAtividadeSecundaria');
            $query->select('emailsTipos.id');
            $query->innerJoin('emailAtividadeSecundaria.emailsTipos', 'emailsTipos');
            $query->where("emailAtividadeSecundaria.id = :idEmailAtividadeSecundaria");
            $query->setParameters(['idEmailAtividadeSecundaria' => $idEmailAtividadeSecundaria]);

            return $query->getQuery()->getScalarResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna lista de E-mails que corresponda a data de início ou data de fim da atividade secundária especificada e cujo calendário atual seja igual a concluído.
     * @param integer $idTipoEmailsAtividadeSecundaria
     * @param string $dataInicio
     * @param string $dataFim
     * @return array|NULL
     */
    public function getEmailPorData($idTipoEmailsAtividadeSecundaria, $dataInicio, $dataFim){
        try {
            $query = $this->createQueryBuilder('emailAtividadeSecundaria');
            $query->innerJoin('emailAtividadeSecundaria.emailsTipos', 'emailsTipos')->addSelect('emailsTipos');
            $query->innerJoin('emailsTipos.tipoEmail', 'tipoEmail')->addSelect('tipoEmail');
            $query->innerJoin('emailAtividadeSecundaria.atividadeSecundaria', 'atividadeSecundaria')->addSelect('atividadeSecundaria');
            $query->innerJoin('atividadeSecundaria.atividadePrincipalCalendario', 'atividadePrincipal')->addSelect('atividadePrincipal');
            $query->innerJoin('atividadePrincipal.calendario', 'calendario');
            $query->leftJoin("calendario.situacoes", "situacoes");
            $query->leftJoin("situacoes.situacaoCalendario", "situacaoCalendario");

            $query->where("calendario.id = " . $this->getSubQueryCalendarioSituacaoAtual());
            $query->andWhere('tipoEmail.id = :idTipo');            
            $query->setParameter('idTipo',$idTipoEmailsAtividadeSecundaria);
            
            if(!empty($dataInicio)){
                $query->andWhere('atividadeSecundaria.dataInicio = :dataInicio');
                $query->setParameter('dataInicio', $dataInicio);
            }
            if(!empty($dataFim)){
                $query->andWhere('atividadeSecundaria.dataFim = :dataFim');
                $query->setParameter('dataInicio', $dataFim);
            }
           
            $queryResult = $query->getQuery()->getArrayResult();
            if(empty($queryResult)){
                throw (new NoResultException());
            }
            
            return array_map(function($email){
                return EmailAtividadeSecundaria::newInstance($email);
            }, $queryResult);
            
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna tipo de emailAtividadeSecundaria por id.
     *
     * @param integer $id
     * @return mixed|Statement|array|NULL
     * @throws NonUniqueResultException
     */
    public function getTipoEmailsAtividadeSecundariaPorId($id)
    {
        try {
            $queryBuider = $this->_em->createQueryBuilder();
            $query = $queryBuider->select('tipoEmail');
            $query->from(TipoEmailAtividadeSecundaria::class, 'tipoEmail');
            $query->where(" tipoEmail.id = :id");
            $query->setParameter(':id', $id);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna tipo de emailAtividadeSecundaria por ids.
     *
     * @param $ids
     * @return mixed|Statement|array|NULL
     */
    public function getTipoEmailsAtividadeSecundariaPorIds($ids)
    {
        try {
            $queryBuider = $this->_em->createQueryBuilder();
            $query = $queryBuider->select('tipoEmail');
            $query->from(TipoEmailAtividadeSecundaria::class, 'tipoEmail');
            $query->where(" tipoEmail.id IN (:id)");
            $query->setParameter(':id', $ids);

            $query->orderBy("tipoEmail.id", "ASC");
            
            return $query->getQuery()->getArrayResult();
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
     * Retorna e-mail pelo o id informado.
     *
     * @param integer $id
     * @return \app\Entities\EmailAtividadeSecundaria|NULL
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getEmailAtividadeSecundaria($id)
    {
        try {
            $query = $this->createQueryBuilder("emailAtividadeSecundaria");
            $query->where("emailAtividadeSecundaria.id = :id");
            $query->setParameter("id", $id);

            return $query->getQuery()->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}

