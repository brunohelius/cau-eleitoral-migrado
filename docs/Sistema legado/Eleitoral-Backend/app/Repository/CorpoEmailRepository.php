<?php
/*
 * CorpoEmailRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\To\CorpoEmailFiltroTO;
use App\To\CorpoEmailTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'CorpoEmail'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class CorpoEmailRepository extends AbstractRepository
{

    /**
     * Recupera os corpos de emails cadastrados.
     *
     * @return mixed
     */
    public function getCorposEmail()
    {
        $query = $this->createQueryBuilder('corpoEmail');
        $query->leftJoin("corpoEmail.cabecalhoEmail", "cabecalhoEmail")->addSelect('cabecalhoEmail');
        $query->leftJoin("corpoEmail.emailsAtividadeSecundaria", "emailsAtividadeSecundaria")
            ->addSelect('emailsAtividadeSecundaria');
        $query->leftJoin("emailsAtividadeSecundaria.atividadeSecundaria", "atividadeSecundaria")
            ->addSelect('atividadeSecundaria');
        $query->orderBy('corpoEmail.id', 'desc');

        return array_map(function ($corpoEmail) {
            return CorpoEmailTO::newInstance($corpoEmail);
        }, $query->getQuery()->getArrayResult());
    }

    /**
     * Recupera o corpo do email de acordo com o 'id' informado.
     *
     * @param $id
     * @return CorpoEmailTO
     * @throws NonUniqueResultException
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder('corpoEmail');
            $query->leftJoin("corpoEmail.cabecalhoEmail", "cabecalhoEmail")->addSelect('cabecalhoEmail');
            $query->leftJoin("corpoEmail.emailsAtividadeSecundaria", "emailsAtividadeSecundaria")
                ->addSelect('emailsAtividadeSecundaria');
            $query->leftJoin("emailsAtividadeSecundaria.atividadeSecundaria", "atividadeSecundaria")
                ->addSelect('atividadeSecundaria');
            $query->leftJoin("emailsAtividadeSecundaria.emailsTipos", "emailsTipos")
                ->addSelect("emailsTipos");
            $query->leftJoin('atividadeSecundaria.atividadePrincipalCalendario', 'atividadePrincipal')
                ->addSelect('atividadePrincipal');

            $query->where('corpoEmail.id = :id');
            $query->setParameter('id', $id);

            $corpoEmail = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
            return CorpoEmailTO::newInstance($corpoEmail);
        } catch (NoResultException $e) {
            return null;
        }
    }
        
    /**
     * Recupera os corpos de e-mails de acordo com os filtros informados.
     *
     * @param CorpoEmailFiltroTO $corpoEmailFiltroTO
     * @return array
     */
    public function getCorposEmailPorFiltro(CorpoEmailFiltroTO $corpoEmailFiltroTO)
    {
        $query = $this->createQueryBuilder('corpoEmail');
        $query->leftJoin("corpoEmail.cabecalhoEmail", "cabecalhoEmail")->addSelect('cabecalhoEmail');
        $query->leftJoin("corpoEmail.emailsAtividadeSecundaria", "emailsAtividadeSecundaria")
            ->addSelect('emailsAtividadeSecundaria');
        $query->leftJoin("emailsAtividadeSecundaria.atividadeSecundaria", "atividadeSecundaria")
            ->addSelect('atividadeSecundaria');
        $query->orderBy('corpoEmail.id', 'desc');
        $query->where('1 = 1');

        if (!empty($corpoEmailFiltroTO->getCorposEmail())) {
            $query->andWhere('corpoEmail.id IN (:idsCorpoEmail)');
            $query->setParameter('idsCorpoEmail', $corpoEmailFiltroTO->getIdsCorposEmail());
        }

        if (!empty($corpoEmailFiltroTO->getAtividadeSecundarias())) {
            $query->innerJoin("corpoEmail.emailsAtividadeSecundaria", "emailsAtividadeSecundaria2");
            $query->andWhere('emailsAtividadeSecundaria2.id IN (:idsEmailsAtividadesSecundarias)');
            $query->setParameter(
                'idsEmailsAtividadesSecundarias',
                $corpoEmailFiltroTO->getIdsEmailsAtividadesSecundarias()
            );
        }

        if (!empty($corpoEmailFiltroTO->getAtivo())) {
            $query->andWhere('corpoEmail.ativo IN (:ativos)');
            $query->setParameter('ativos', $corpoEmailFiltroTO->getStatusAtivoInativoFiltro());
        }

        return array_map(function ($corpoEmail) {
            return CorpoEmailTO::newInstance($corpoEmail);
        }, $query->getQuery()->getArrayResult());
    }

    /**
     * Recupera a atividade secundária do calendário de acordo com o 'id' informado.
     *
     * @param $id
     * @return mixed|null
     * @throws NonUniqueResultException
     */
    public function getEmailsPorAtividadeSecundaria($id)
    {
        $query = $this->createQueryBuilder('corpoEmail');
        $query->innerJoin("corpoEmail.emailsAtividadeSecundaria", "emailsAtividadeSecundaria");
        $query->innerJoin("emailsAtividadeSecundaria.atividadeSecundaria", "atividadeSecundaria");
        $query->leftJoin("corpoEmail.cabecalhoEmail", "cabecalhoEmail")->addSelect("cabecalhoEmail");
        $query->leftJoin("cabecalhoEmail.cabecalhoEmailUfs", "uf");

        $query->where("corpoEmail.ativo = true");
        $query->andWhere("atividadeSecundaria.id = :id");
        $query->setParameter("id", $id);

        return $query->getQuery()->getResult();
    }

}
