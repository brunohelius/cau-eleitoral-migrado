<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 27/02/2020
 * Time: 14:45
 */

namespace App\Repository;


use App\Entities\Denuncia;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;


/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'DenunciaMembroComissaoRepository'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class DenunciaMembroComissaoRepository extends AbstractRepository
{
    /**
     * Retorna o total de denúncias de membros por comissões agrupados em UF por pessoa
     *
     * @param $idPessoa
     * @return array|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMembroComissaoPorUF($idPessoa)
    {
        try {
            $query = $this->createQueryBuilder('denunciaMembroComissao');
            $query->select( 'COUNT(denuncia.id)',  'filial.id', 'filial.prefixo', 'filial.descricao');
            $query->innerJoin("denunciaMembroComissao.denuncia", "denuncia");
            $query->innerJoin("denunciaMembroComissao.membroComissao", "membroComissao");
            $query->innerJoin("membroComissao.filial", "filial");
            $query->where("denuncia.pessoa = :id");
            $query->setParameter("id", $idPessoa);
            $query->groupBy( 'filial.id', 'filial.prefixo', 'filial.descricao');
            return $query->getQuery()->getArrayResult();

        } catch (NoResultException $e) {
            echo $e;
        }
    }

    /**
     * Retorna a lista de denúncias por UF e pessoa
     *
     * @param $idPessoa
     * @return DenunciaMembroComissao
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getListaDenunciaMembroComissaoPorUF($idPessoa, $idUF)
    {
        try {
            $query = $this->createQueryBuilder('denunciaMembroComissao');
            $query->select('den.id_denuncia,den.dt_denuncia,denunciante.nome as denunciante,chapaEleicao.id as denunciado, tsd.id_situacao_denuncia,tsd.ds_situacao');
            $query->innerJoin("denunciaMembroComissao.denuncia", "denuncia");
            $query->innerJoin("denuncia.profissional", "denunciante");
            $query->innerJoin("denuncia.denunciaSituacao", "denunciaSituacao");
            $query->innerJoin("denunciaSituacao.situacaoDenuncia", "situacaoDenuncia");
            $query->innerJoin("denunciaChapa.membroComissao", "membroComissao");
            $query->where("denuncia.pessoa = :id");
            $query->where("membroChapa.filial = :idUF");
            $query->setParameter("id", $idPessoa);
            $query->setParameter("idUF", $idUF);

            $query->groupBy('filial.id', 'filial.prefixo', 'filial.descricao');
            return $query->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            echo $e;
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
        $sqlSituacao .= " INNER JOIN eleitoral.tb_denuncia_membro_comissao subTdmc ON etd.id_denuncia = subTdmc.id_denuncia ";
        $sqlSituacao .= " INNER JOIN eleitoral.tb_membro_comissao subTmc ON subTdmc.id_membro_comissao = subTmc.id_membro_comissao ";
        $sqlSituacao .= " WHERE tds.id_denuncia = " . $idDenuncia;
        $sqlSituacao .= " GROUP BY tds.id_denuncia_situacao, tds.id_denuncia, tds.id_situacao_denuncia ";
        $sqlSituacao .= " limit 1 ";
        $sqlSituacao .= " ) sit ON td.id_denuncia IN ( sit.id_denuncia ) ";

        $sql = " SELECT td.id_denuncia, denunciante.nome as nome_denunciante, denunciante.registro_nacional, tp.email, ";
        $sql .= " td.ds_fatos, denunciado.nome nome_denunciado, td.id_cau_uf, tsd.id_situacao_denuncia, denunciado.uf_naturalidade, td.dt_denuncia, ";
        $sql .= " td.sq_denuncia as numero_sequencial, ";
        $sql .= " case when tf.prefixo IS null then 'IES' else tf.prefixo end as prefixo ";
        $sql .= " FROM eleitoral.tb_denuncia td ";
        $sql .= " INNER JOIN PUBLIC.tb_pessoa tp ON td.id_pessoa = tp.id ";
        $sql .= " INNER JOIN PUBLIC.tb_profissional denunciante ON denunciante.pessoa_id = tp.id ";
        $sql .= " INNER JOIN eleitoral.tb_denuncia_membro_comissao tdmc ON td.id_denuncia = tdmc.id_denuncia ";
        $sql .= " LEFT JOIN PUBLIC.tb_filial tf ON td.id_cau_uf = tf.id ";
        $sql .= $sqlSituacao;
        $sql .= " INNER JOIN eleitoral.tb_situacao_denuncia tsd ON tsd.id_situacao_denuncia = sit.id_situacao_denuncia ";
        $sql .= " INNER JOIN eleitoral.tb_membro_comissao tmc ON tdmc.id_membro_comissao = tmc.id_membro_comissao ";
        $sql .= " INNER JOIN public.tb_profissional denunciado ON tmc.id_pessoa = denunciado.id ";
        $sql .= " INNER JOIN public.tb_pessoa tp1  ON denunciado.pessoa_id = tp1.id ";
        $sql .= " WHERE  td.id_denuncia = " . $idDenuncia;
        $sql .= " ORDER  BY td.id_denuncia ";

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Retorna as informações do denunciado
     *
     * @param integer $idDenuncia
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */

    public function getDadosDenunciadoPorDenuncia($idDenuncia)
    {
        $query = $this->createQueryBuilder('denunciaMembroComissao');
        $query->select('denunciante.email');
        $query->innerJoin('denunciaMembroComissao.denuncia', 'denuncia');
        $query->innerJoin('denunciaMembroComissao.membroComissao','membroComissao');
        $query->innerJoin('membroComissao.profissionalEntity','profissional');
        $query->innerJoin('profissional.pessoa','denunciante');
        $query->where("denuncia.id = :id");
        $query->setParameter("id", $idDenuncia);
        return $query->getQuery()->getArrayResult();
    }
}
