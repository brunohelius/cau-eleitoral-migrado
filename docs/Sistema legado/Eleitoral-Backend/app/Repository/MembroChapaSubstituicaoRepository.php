<?php

namespace App\Repository;

use App\Entities\MembroChapaSubstituicao;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'MembroChapaSubstituicao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class MembroChapaSubstituicaoRepository extends AbstractRepository
{

    /**
     * Método consulta todos os membros substituição de acordo com o calendário e status
     *
     * @param $idCalendario
     * @param array|null $idsStatusPedidoSubstituicao
     * @return MembroChapaSubstituicao[]
     */
    public function getPorCalendario($idCalendario, $idsStatusPedidoSubstituicao = null)
    {
        $query = $this->createQueryBuilder("membroChapaSubstituicao");
        $query->innerJoin("membroChapaSubstituicao.pedidoSubstituicaoChapa", "pedidoSubstituicaoChapa");
        $query->innerJoin("pedidoSubstituicaoChapa.chapaEleicao", "chapaEleicao");
        $query->innerJoin("chapaEleicao.atividadeSecundariaCalendario", "atividadeSecundaria");
        $query->innerJoin("atividadeSecundaria.atividadePrincipalCalendario", "atividadePrincipal");
        $query->innerJoin("pedidoSubstituicaoChapa.statusSubstituicaoChapa", "statusSubstituicaoChapa");

        $query->where("atividadePrincipal.calendario = :idCalendario");
        $query->setParameter("idCalendario", $idCalendario);

        if (!empty($idsStatusPedidoSubstituicao)) {
            $query->andWhere("statusSubstituicaoChapa.id IN (:idsStatus)");
            $query->setParameter("idsStatus", $idsStatusPedidoSubstituicao);
        }

        return $query->getQuery()->getResult();
    }

}
