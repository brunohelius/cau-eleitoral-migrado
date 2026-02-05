<?php
/*
 * PessoaRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Repository;

use Exception;
use App\Util\Utils;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'Pessoa'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class PessoaRepository extends AbstractRepository
{
    /**
     * Retorna as pessoas jurídicas (empresas) as quais o profissional informado é o 'Responsável Técnico".
     *
     * @param integer $idPessoa
     * @return Pessoa[]
     * @throws Exception
     */
    public function getEmpresasPorProfissionalResponsavelTecnico($idPessoa)
    {
        $dataAtual = Utils::getData();

        $query = $this->createQueryBuilder("pessoa");
        $query->innerJoin("pessoa.empresa", "empresa");
        $query->innerJoin("pessoa.registrosEmpresas", "registrosEmpresas");
        $query->innerJoin("registrosEmpresas.responsaveisTecnico", "responsaveisTecnico");
        $query->innerJoin("responsaveisTecnico.registroProfissional", "registroProfissional");
        $query->innerJoin("registroProfissional.pessoa", "pessoaFisica");
        $query->innerJoin("pessoaFisica.profissional", "profissional");

        $query->where("profissional.pessoa = :idProfissional");
        $query->setParameter("idProfissional", $idPessoa);

        $query->andWhere("responsaveisTecnico.dataInicioResponsabilidade <= :dataAtual");
        $query->andWhere(
            "(responsaveisTecnico.dataFimResponsabilidade >= :dataAtual " .
            " OR responsaveisTecnico.dataFimResponsabilidade IS NULL)"
        );
        $query->andWhere(
            "(responsaveisTecnico.dataFimContrato >= :dataAtual " .
            " OR responsaveisTecnico.dataFimContrato IS NULL)"
        );
        $query->setParameter("dataAtual", $dataAtual->format('Y-m-d'));

        $query->orderBy('responsaveisTecnico.id', 'ASC');

        return $query->getQuery()->getResult();
    }

    public function getEmpresasResponsabilidadeTecnicaPorPessoa($profissional_id)
    {
        $sql = "SELECT
				er.empresa_id,
				e.razao_social as razaoSocial,
				e.cnpj,
				e.pessoa_id,
				to_char(p.data_inicio_responsabilidade,'DD/MM/YYYY') as dataInicioResponsabilidadeTecnica,
				to_char(p.data_fim_responsabilidade,'DD/MM/YYYY') as dataFimResponsabilidadeTecnica,
				to_char(er.data_ini_registro,'DD/MM/YYYY') as dataInicioRegistro,
				e.registro_nacional as registroNacional
			FROM tb_responsavel_tecnico as p
				INNER JOIN tb_tiporesponsabilidade as tr ON tr.id = p.tiporesponsabilidade_id
				INNER JOIN tb_profissional_registro pr ON pr.id = p.registro_profissional_id 
				INNER JOIN tb_empresa_registro er ON er.id = p.registro_empresa_id
				INNER JOIN tb_empresa e ON er.empresa_id = e.pessoa_id
				WHERE pr.profissional_id = " . $profissional_id . "
				AND data_inicio_responsabilidade <= current_date
				AND (data_fim_responsabilidade >= current_date or data_fim_responsabilidade is null)
				AND (data_fim_contrato >= current_date or data_fim_contrato is null)
			ORDER BY p.id ASC";

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();


    }
}
