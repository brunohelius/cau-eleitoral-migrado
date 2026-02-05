<?php
/*
 * DenunciaChapaRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\DenunciaChapa;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'DenunciaChapa'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class DenunciaChapaRepository extends AbstractRepository
{
    /**
     * Retorna o total de denúncias de membros por comissões agrupados em UF por pessoa
     *
     * @param $idPessoa
     * @return DenunciaChapa
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDenunciaChapaPorUF($idPessoa)
    {
        try {
            $query = $this->createQueryBuilder('denunciaChapa');
            $query->select('COUNT(denuncia.id)', 'filial.id', 'filial.prefixo', 'filial.descricao');
            $query->innerJoin("denunciaChapa.denuncia", "denuncia");
            $query->innerJoin("denunciaChapa.chapaEleicao", "chapaEleicao");
            $query->innerJoin("chapaEleicao.filial", "filial");
            $query->where("denuncia.pessoa = :id");
            $query->setParameter("id", $idPessoa);

            $query->groupBy('filial.id', 'filial.prefixo', 'filial.descricao');
            return $query->getQuery()->getArrayResult();
        } catch (NoResultException $e) {
            echo $e;
        }
    }

    /**
     * Retorna a lista de denúncias por UF e pessoa
     *
     * @param $idPessoa
     * @return DenunciaChapa
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getListaDenunciaChapaPorUF($idPessoa, $idUF)
    {
        try {
            $query = $this->createQueryBuilder('denunciaChapa');
            $query->select('denuncia.id','denuncia.dataHora','denunciante.nome');
            $query->innerJoin("denunciaChapa.denuncia", "denuncia");
            $query->innerJoin("denuncia.pessoa", "denunciante");
            $query->innerJoin("denunciaChapa.chapaEleicao", "chapaEleicao");
            $query->innerJoin("chapaEleicao.filial", "filial");
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
        $sqlSituacao .= " INNER JOIN eleitoral.tb_denuncia_chapa subTdc ON etd.id_denuncia = subTdc.id_denuncia ";
        $sqlSituacao .= " INNER JOIN eleitoral.tb_chapa_eleicao subTce ON subTce.id_chapa_eleicao = subTdc.id_chapa_eleicao ";
        $sqlSituacao .= " WHERE   tds.id_denuncia = " . $idDenuncia;
        $sqlSituacao .= " GROUP BY tds.id_denuncia_situacao, tds.id_denuncia, tds.id_situacao_denuncia ";
        $sqlSituacao .= " limit 1 ";
        $sqlSituacao .= " ) sit ON td.id_denuncia IN ( sit.id_denuncia ) ";

        $sql = " SELECT td.sq_denuncia numero_sequencial, denunciante.nome as nome_denunciante, ";
        $sql .= " denunciante.registro_nacional, tp.email, td.ds_fatos, td.id_cau_uf, tsd.id_situacao_denuncia, tce.nu_chapa as nome_denunciado, ";
        $sql .= " fl.prefixo, td.dt_denuncia, ";
        $sql .= " case when fl.prefixo IS null then 'IES' else fl.prefixo end as prefixo ";
        $sql .= " FROM eleitoral.tb_denuncia td ";
        $sql .= " INNER JOIN PUBLIC.tb_pessoa tp ON td.id_pessoa = tp.id ";
        $sql .= " INNER JOIN PUBLIC.tb_profissional denunciante ON denunciante.pessoa_id = tp.id ";
        $sql  .= $sqlSituacao;
        $sql .= " INNER JOIN eleitoral.tb_denuncia_chapa tdc ON td.id_denuncia = tdc.id_denuncia ";
        $sql .= " INNER JOIN eleitoral.tb_situacao_denuncia tsd ON tsd.id_situacao_denuncia = sit.id_situacao_denuncia ";
        $sql .= " INNER JOIN eleitoral.tb_chapa_eleicao tce ON tce.id_chapa_eleicao = tdc.id_chapa_eleicao ";
        $sql .= " INNER JOIN public.tb_filial fl ON fl.id = td.id_cau_uf ";
        $sql .= " WHERE td.id_denuncia = " . $idDenuncia;

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
}
