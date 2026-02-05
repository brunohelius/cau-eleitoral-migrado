<?php

namespace App\Repository;


use App\Config\Constants;

/**
 * Class RecursoJulgamentoAdmissibilidadeRepository
 * @package App\Repository
 */
class RecursoJulgamentoAdmissibilidadeRepository extends AbstractRepository
{


    public function getJulgamentoSemRecursoPrazoVencido()
    {
        try {
            $sql = "SELECT ja.id_julg_admissibilidade,
                           ja.id_denuncia,
                           ds.id_situacao_denuncia,
                           ja.dt_criacao
                    FROM eleitoral.tb_julgamento_admissibilidade ja
                    
                             inner join (select max(id_denuncia_situacao) id_denuncia_situacao, id_denuncia
                                         from eleitoral.TB_DENUNCIA_SITUACAO
                    
                                         group by id_denuncia) tmp on tmp.id_denuncia = ja.id_denuncia
                             inner join eleitoral.tb_denuncia_situacao ds on ds.id_denuncia_situacao = tmp.id_denuncia_situacao
                    
                    WHERE ja.id_julg_admissibilidade NOT IN (
                        SELECT id_recurso_julgamento
                        FROM eleitoral.tb_recurso_julgamentoadmissibilidade
                    )
                      AND (ja.dt_criacao + interval '3' day) < CURRENT_DATE
                      AND ds.id_situacao_denuncia = " . Constants::SITUACAO_DENUNCIA_EM_RECURSO;

            $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (NoResultException $e) {
            echo null;
        }
    }
}