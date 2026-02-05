<?php
/*
 * DenunciaOutroRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'DenunciaOutro'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class DenunciaOutroRepository extends AbstractRepository
{

    /**
     * Retorna as informações do denunciante
     *
     * @param integer $idDenuncia
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getDadosDenuncia($idDenuncia)
    {
        $sqlSituacao = " INNER JOIN ( ";

        $sqlSituacao .= " SELECT max(tds.id_denuncia_situacao), tds.id_denuncia, tds.id_situacao_denuncia ";
        $sqlSituacao .= " FROM eleitoral.tb_denuncia_situacao tds ";
        $sqlSituacao .= " INNER JOIN eleitoral.tb_denuncia etd ON tds.id_denuncia = etd.id_denuncia ";
        $sqlSituacao .= " INNER JOIN eleitoral.tb_denuncia_outro subTdo ON etd.id_denuncia = subTdo.id_denuncia ";
        $sqlSituacao .= " WHERE tds.id_denuncia = " . $idDenuncia;
        $sqlSituacao .= " GROUP BY tds.id_denuncia_situacao, tds.id_denuncia, tds.id_situacao_denuncia ";
        $sqlSituacao .= " limit 1 ";
        $sqlSituacao .= " ) sit ON td.id_denuncia IN (sit.id_denuncia) ";

        $sql  = " SELECT td.id_denuncia, denunciante.nome as nome_denunciante, tp.email, td.ds_fatos, ";
        $sql .= "  '-' as nome_denunciado, denunciante.registro_nacional, td.id_cau_uf, tsd.id_situacao_denuncia, td.dt_denuncia, td.sq_denuncia as numero_sequencial, ";
        $sql .= " case when tf.prefixo IS null then 'IES' else tf.prefixo end as prefixo ";
        $sql .= " FROM  eleitoral.tb_denuncia td ";
        $sql .= " INNER JOIN PUBLIC.tb_pessoa tp ON td.id_pessoa = tp.id ";
        $sql .= " INNER JOIN PUBLIC.tb_profissional denunciante ON denunciante.pessoa_id = tp.id ";
        $sql .= " INNER JOIN eleitoral.tb_denuncia_outro tdo ON td.id_denuncia = tdo.id_denuncia ";
        $sql .= " LEFT JOIN PUBLIC.tb_filial tf ON td.id_cau_uf = tf.id ";
        $sql .= $sqlSituacao;
        $sql .= " INNER JOIN eleitoral.tb_situacao_denuncia tsd ON tsd.id_situacao_denuncia = sit.id_situacao_denuncia ";
        $sql .= " WHERE  td.id_denuncia = " . $idDenuncia;
        $sql .= " ORDER  BY td.id_denuncia ";

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

}
