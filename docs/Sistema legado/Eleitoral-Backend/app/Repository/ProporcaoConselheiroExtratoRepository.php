<?php
/*
 * TipoProcessoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\ProporcaoConselheiroExtrato;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'ProporcaoConselheiroExtrato'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ProporcaoConselheiroExtratoRepository extends AbstractRepository
{
    /**
     * Método que retorna última proporção Extrato Conselheiro por atividade principal independente do nivel
     *
     * @param $idAtividadePrincipal
     * @return ProporcaoConselheiroExtrato|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUltimaPorAtividadePrincipalECauUf($idAtividadePrincipal, $idCauUf)
    {
        try {
            $query = $this->createQueryBuilder('proporcaoConselheiroExtrato');
            $query->innerJoin(
                "proporcaoConselheiroExtrato.historicoExtratoConselheiro", "historicoExtratoConselheiro");
            $query->innerJoin("historicoExtratoConselheiro.atividadeSecundaria", "atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");
            $query->innerJoin("calendario.atividadesPrincipais", "atividadesPrincipais");

            $query->where('atividadesPrincipais.id = :idAtividadePrincipal');
            $query->setParameter('idAtividadePrincipal', $idAtividadePrincipal);

            $query->andWhere('proporcaoConselheiroExtrato.idCauUf = :idCauUf');
            $query->setParameter('idCauUf', $idCauUf);

            $query->orderBy("historicoExtratoConselheiro.dataHistorico", "DESC");
            $query->setMaxResults(1);

            $dadosExtrato = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return ProporcaoConselheiroExtrato::newInstance($dadosExtrato);
        } catch (NoResultException | NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Método que retorna última proporção Historico Extrato Conselheiro por calendario
     *
     * @param $idCalendario
     * @return ProporcaoConselheiroExtrato|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUltimaPorCalendarioECauUf($idCalendario, $idCauUf)
    {
        try {
            $query = $this->createQueryBuilder('proporcaoConselheiroExtrato');
            $query->innerJoin(
                "proporcaoConselheiroExtrato.historicoExtratoConselheiro", "historicoExtratoConselheiro");
            $query->innerJoin("historicoExtratoConselheiro.atividadeSecundaria", "atividadeSecundaria");
            $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
            $query->innerJoin("atividadePrincipal.calendario", "calendario");

            $query->where('calendario.id = :idCalendario');
            $query->setParameter('idCalendario', $idCalendario);

            $query->andWhere('proporcaoConselheiroExtrato.idCauUf = :idCauUf');
            $query->setParameter('idCauUf', $idCauUf);

            $query->orderBy("historicoExtratoConselheiro.dataHistorico", "DESC");
            $query->setMaxResults(1);

            $dadosExtrato = $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

            return ProporcaoConselheiroExtrato::newInstance($dadosExtrato);
        } catch (NoResultException | NonUniqueResultException $e) {
            return null;
        }
    }
}
