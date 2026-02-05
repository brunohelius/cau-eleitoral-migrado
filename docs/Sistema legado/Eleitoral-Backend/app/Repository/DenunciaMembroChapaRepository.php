<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 27/02/2020
 * Time: 14:45
 */

namespace App\Repository;


use App\Entities\DenunciaMembroChapa;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'DenunciaMembroChapa'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class DenunciaMembroChapaRepository extends AbstractRepository
{
    /**
     * Retorna o total de denúncias de chapas por pessoa
     *
     * @param $idPessoa
     * @return array|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDenunciaMembroChapaPorUF($idPessoa)
    {
        try {
            $query = $this->createQueryBuilder('denunciaMembroChapa');
            $query->select('COUNT(denuncia.id) as total_den', 'filial.id as id_cau_uf', 'filial.prefixo', 'filial.descricao');
            $query->innerJoin("denunciaMembroChapa.denuncia", "denuncia");
            $query->innerJoin("denunciaMembroChapa.membroChapa", "membroChapa");
            $query->innerJoin("membroChapa.chapaEleicao", "chapaEleicao");
            $query->innerJoin("chapaEleicao.filial", "filial");
            $query->where("denuncia.pessoa = :id");
            $query->setParameter("id", $idPessoa);
            $query->groupBy('filial.id', 'filial.prefixo', 'filial.descricao');
            return $query->getQuery()->getArrayResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a lista de denúncias por UF e pessoa
     *
     * @param $idPessoa
     * @return DenunciaMembroChapa
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getListaDenunciaMembroChapaPorUF($idPessoa,$idCauUf)
    {
        try {
            $sql = "SELECT td.id_denuncia,
                   td.dt_denuncia,
                   denunciante.nome as nome_denunciante,
                   denunciado.nome as nome_denunciante,
                   tsd.ds_situacao
            FROM   eleitoral.tb_denuncia td
                       INNER JOIN PUBLIC.tb_pessoa tp
                                  ON td.id_pessoa = tp.id
                       INNER JOIN PUBLIC.tb_profissional denunciante
                                  ON denunciante.pessoa_id = tp.id
                       INNER JOIN eleitoral.tb_denuncia_membro_chapa tdmc
                                  ON td.id_denuncia = tdmc.id_denuncia
                       INNER JOIN (SELECT Max(tds.id_denuncia_situacao),
                                          tds.id_denuncia,
                                          tds.id_situacao_denuncia
                                   FROM   eleitoral.tb_denuncia_situacao tds
                                    INNER JOIN eleitoral.tb_denuncia etd ON tds.id_denuncia = etd.id_denuncia
                                   WHERE etd.id_pessoa =  $idPessoa
                                   GROUP BY tds.id_denuncia,
                                       tds.id_situacao_denuncia) sit
                                  ON sit.id_denuncia = td.id_denuncia
                       INNER JOIN eleitoral.tb_situacao_denuncia tsd
                                  ON tsd.id_situacao_denuncia = sit.id_situacao_denuncia
                       INNER JOIN eleitoral.tb_membro_chapa tmc
                                  ON tdmc.id_membro_chapa = tmc.id_membro_chapa
                       INNER JOIN eleitoral.tb_chapa_eleicao tce 
                                  ON tce.id_chapa_eleicao = tmc.id_chapa_eleicao
                       INNER JOIN public.tb_profissional denunciado
                                  ON denunciado.id = tmc.id_profissional
            WHERE  td.id_pessoa = $idPessoa AND  tce.id_cau_uf = $idCauUf
            ORDER  BY td.id_denuncia;";
            $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
            $stmt->execute();
            return $stmt->fetch();
        } catch (NoResultException $e) {
            echo null;
        }
    }

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
        $sqlSituacao .= " INNER JOIN eleitoral.tb_denuncia_membro_chapa subTdmc ON etd.id_denuncia = subTdmc.id_denuncia ";
        $sqlSituacao .= " INNER JOIN eleitoral.tb_membro_chapa subTmc ON subTdmc.id_membro_chapa = subTmc.id_membro_chapa ";
        $sqlSituacao .= " INNER JOIN eleitoral.tb_chapa_eleicao subTce ON subTce.id_chapa_eleicao = subTmc.id_chapa_eleicao ";
        $sqlSituacao .= " WHERE tds.id_denuncia = " . $idDenuncia;
        $sqlSituacao .= " GROUP BY tds.id_denuncia_situacao, tds.id_denuncia, tds.id_situacao_denuncia ";
        $sqlSituacao .= " limit 1 ";
        $sqlSituacao .= " ) sit ON td.id_denuncia IN ( sit.id_denuncia ) ";

        $sql = " SELECT td.id_denuncia, denunciante.nome as nome_denunciante, denunciante.registro_nacional, tp.email, ";
        $sql .= " td.ds_fatos, denunciado.nome nome_denunciado, td.id_cau_uf, tsd.id_situacao_denuncia, denunciado.uf_naturalidade as uf_denunciado, ";
        $sql .= " td.dt_denuncia, td.sq_denuncia as numero_sequencial, ";
        $sql .= " case when tf.prefixo IS null then 'IES' else tf.prefixo end as prefixo ";
        $sql .= " FROM   eleitoral.tb_denuncia td ";
        $sql .= " INNER JOIN PUBLIC.tb_pessoa tp ON td.id_pessoa = tp.id ";
        $sql .= " INNER JOIN PUBLIC.tb_profissional denunciante ON denunciante.pessoa_id = tp.id ";
        $sql  .= $sqlSituacao;

        $sql .= " INNER JOIN eleitoral.tb_situacao_denuncia tsd ON tsd.id_situacao_denuncia = sit.id_situacao_denuncia ";
        $sql .= " INNER JOIN eleitoral.tb_denuncia_membro_chapa tdmc ON td.id_denuncia = tdmc.id_denuncia ";
        $sql .= " INNER JOIN eleitoral.tb_membro_chapa tmc ON tdmc.id_membro_chapa = tmc.id_membro_chapa ";
        $sql .= " INNER JOIN public.tb_profissional denunciado ON denunciado.id = tmc.id_profissional ";
        $sql .= " LEFT JOIN PUBLIC.tb_filial tf ON td.id_cau_uf = tf.id ";

        $sql .= " WHERE  td.id_denuncia = " . $idDenuncia;
        $sql .= " ORDER  BY td.id_denuncia ";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Retorna as informações do denunciante
     *
     * @param integer $idDenuncia
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */

    public function getDadosDenunciantePorDenuncia($idDenuncia)
    {
        $query = $this->createQueryBuilder('membroChapa');
        $query->select('denuncia.id', 'denunciante.email');
        $query->innerJoin('membroChapa.denuncia', 'denuncia');
        $query->innerJoin('denuncia.pessoa', 'denunciante');
        $query->where("denuncia.id = :id");
        $query->setParameter("id", $idDenuncia);
        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * Retorna as informações do denunciado
     *
     * @param integer $idDenuncia
     * @return mixed
     */

    public function getDadosDenunciadoPorDenuncia($idDenuncia)
    {
        $query = $this->createQueryBuilder('denunciaMembroChapa');
        $query->select('denunciante.email, profissional.nome');
        $query->innerJoin('denunciaMembroChapa.denuncia', 'denuncia');
        $query->innerJoin('denunciaMembroChapa.membroChapa','membroChapa');
        $query->innerJoin('membroChapa.profissional','profissional');
        $query->innerJoin('profissional.pessoa','denunciante');
        $query->where("denuncia.id = :id");
        $query->setParameter("id", $idDenuncia);

       return $query->getQuery()->getArrayResult();
    }
}
