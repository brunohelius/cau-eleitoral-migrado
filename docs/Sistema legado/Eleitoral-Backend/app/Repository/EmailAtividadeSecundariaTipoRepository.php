<?php

namespace App\Repository;

use App\Entities\EmailAtividadeSecundariaTipo;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'EmailAtividadeSecundariaTipo'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class EmailAtividadeSecundariaTipoRepository extends AbstractRepository
{
    /**
     * Busca de EmailAtividadeSecundariaTipo por id E-mail Atividade secundária e id Tipo de E-mail Atividade Secundária.
     *
     * @param integer $idEmail
     * @param integer $idTipo
     * @return mixed|Statement|array|NULL|NULL
     * @throws NonUniqueResultException
     */
    public function getPorEmailAtividadeSecundariaAndTipoEmail($idEmail, $idTipo = null)
    {
        try {
            $query = $this->createQueryBuilder('emailAtividadeSecundariaTipo');
            $query->innerJoin('emailAtividadeSecundariaTipo.emailAtividadeSecundaria',
                'emailAtividadeSecundaria')->addSelect('emailAtividadeSecundaria');
            $query->innerJoin('emailAtividadeSecundariaTipo.tipoEmail', 'tipoEmail')->addSelect('tipoEmail');
            $query->where('emailAtividadeSecundaria.id = :idEmail');
            $query->andWhere('tipoEmail.id = :idTipo');
            $query->setParameters(['idEmail' => $idEmail, 'idTipo' => $idTipo]);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna os EmailAtividadeSecundariaTipo por id Atividade secundária
     *
     * @param integer $idAtividadeSecundaria
     * @return array|NULL
     */
    public function getPorAtividadeSecundaria($idAtividadeSecundaria)
    {
        try {
            $query = $this->createQueryBuilder('emailAtividadeSecundariaTipo');
            $query->innerJoin(
                'emailAtividadeSecundariaTipo.emailAtividadeSecundaria',
                'emailAtividadeSecundaria'
            )->addSelect('emailAtividadeSecundaria');
            $query->innerJoin(
                'emailAtividadeSecundaria.atividadeSecundaria',
                'atividadeSecundaria'
            );
            $query->innerJoin(
                'emailAtividadeSecundariaTipo.tipoEmail',
                'tipoEmail'
            )->addSelect('tipoEmail');
            $query->innerJoin("emailAtividadeSecundaria.corpoEmail", "corpoEmail")
                ->addSelect("corpoEmail");
            $query->leftJoin("corpoEmail.cabecalhoEmail", "cabecalhoEmail")
                ->addSelect("cabecalhoEmail");

            $query->where('atividadeSecundaria.id = :idAtividadeSecundaria');
            $query->setParameter('idAtividadeSecundaria', $idAtividadeSecundaria);

            $emailsAtvidadeSecundariaTipo = $query->getQuery()->getArrayResult();

            return array_map(static function ($dadosEmailAtividadeSecundarioTipo) {
                return EmailAtividadeSecundariaTipo::newInstance($dadosEmailAtividadeSecundarioTipo);
            }, $emailsAtvidadeSecundariaTipo);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Busca de EmailAtividadeSecundariaTipo por id Atividade secundária e id Tipo de E-mail Atividade Secundária.
     *
     * @param integer $idAtividadeSecundaria
     * @param integer $idTipo
     * @return mixed|Statement|array|NULL
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorAtividadeSecundariaAndTipoEmail($idAtividadeSecundaria, $idTipo)
    {
        try {
            $query = $this->createQueryBuilder('emailAtividadeSecundariaTipo');
            $query->innerJoin('emailAtividadeSecundariaTipo.emailAtividadeSecundaria', 'emailAtividadeSec')
                ->addSelect('emailAtividadeSec');
            $query->innerJoin('emailAtividadeSec.atividadeSecundaria', 'atividadeSecundaria')
                ->addSelect('atividadeSecundaria');
            $query->innerJoin('emailAtividadeSecundariaTipo.tipoEmail', 'tipoEmail')
                ->addSelect('tipoEmail');

            $query->where('atividadeSecundaria.id = :idAtividadeSecundaria');
            $query->setParameter("idAtividadeSecundaria", $idAtividadeSecundaria);

            $query->andWhere('tipoEmail.id = :idTipo');
            $query->setParameter("idTipo", $idTipo);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}

