<?php
/*
 * ProfissionalRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Config\Constants;
use App\Entities\Profissional;
use App\To\ProfissionalTO;
use App\Util\Utils;
use DateTime;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use stdClass;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'Profissional'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ProfissionalRepository extends AbstractRepository
{

    /**
     * Retorna a instância do 'Profissional' conforme o identificador de usuário informado.
     *
     * @param $idPessoa
     * @return mixed|null
     * @throws NonUniqueResultException
     */
    public function getPorPessoa($idPessoa)
    {
        try {
            $query = $this->createQueryBuilder("profissional");
            $query->innerJoin("profissional.pessoa", "pessoa")->addSelect("pessoa");

            $query->where("pessoa.id = :idPessoa");
            $query->setParameter("idPessoa", $idPessoa);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a instância do 'Profissional' conforme o 'id' informado.
     *
     * @param integer $id
     *
     * @return ProfissionalTO|null
     */
    public function getPorId($id)
    {
        try {
            $query = $this->createQueryBuilder("profissional");
            $query->leftJoin("profissional.pessoa", "pessoa")->addSelect('pessoa');
            $query->leftJoin("pessoa.endereco", "endereco")->addSelect('endereco');
            $query->where("profissional.id = :id");
            $query->setParameter("id", $id);

            return $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
        } catch (NonUniqueResultException|NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a instância do 'Profissional' conforme o 'CPF' informado.
     *
     * @param string $cpf
     *
     * @return Profissional|null
     */
    public function getPorCpf($cpf)
    {
        try {
            $query = $this->createQueryBuilder("profissional");
            $query->leftJoin("profissional.pessoa", "pessoa")->addSelect('pessoa');
            $query->leftJoin("pessoa.endereco", "endereco")->addSelect('endereco');
            $query->where("profissional.cpf = :cpf");
            $query->setParameter("cpf", $cpf);

            $result = $query->getQuery()->getArrayResult();
            /** @var Profissional $profissional */
            $profissional = null;
            if (!empty($result)) {
                $profissional = Profissional::newInstance($result[0]);
            }

            return $profissional;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna lista de profissionais conforme o Nome informado.
     *
     * @param stdClass $profissionalTO
     *
     * @return array|null
     */
    public function getProfissionaisPorFiltro($profissionalTO, $limite)
    {
        try {
            $query = $this->createQueryBuilder("profissional");
            $query->innerJoin("profissional.pessoa", "pessoa")->addSelect('pessoa');
            $query->leftJoin("pessoa.endereco", "endereco")->addSelect('endereco');
            $query->where("1 = :true");
            $query->setParameter("true", 1);

            $condNome = "LOWER(IGNORE_SPECIAL_CHARACTER(profissional.nome)) LIKE LOWER(IGNORE_SPECIAL_CHARACTER(:nome))";

            if (!empty($profissionalTO->registroNome)) {
                $query->andWhere("LOWER(profissional.registroNacional) LIKE :registro OR {$condNome}");
                $query->setParameter("registro", "%" . strtolower($profissionalTO->registroNome) . "%");
                $query->setParameter("nome", "%" . $profissionalTO->registroNome . "%");
            } else {
                if (!empty($profissionalTO->cpf)) {
                    $query->andWhere("profissional.cpf LIKE :cpf");
                    $query->setParameter("cpf", $profissionalTO->cpf . "%");
                }

                if (!empty($profissionalTO->nome)) {
                    $query->andWhere($condNome);
                    $query->setParameter("nome", "%" . $profissionalTO->nome . "%");
                }
            }

            if (!empty($profissionalTO->ids) and is_array($profissionalTO->ids)) {
                $query->andWhere("profissional.id IN (:ids)");
                $query->setParameter("ids", $profissionalTO->ids);
            }

            $query->orderBy('profissional.nome', 'ASC');

            $result = $query->getQuery()->setMaxResults($limite)->getArrayResult();

            return array_map(function ($dadosProfissional) {
                return ProfissionalTO::newInstance($dadosProfissional);
            }, $result);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Profissionais ativos por UF.
     *
     * @return array|null
     * @throws Exception
     */
    public function getQtdAtivosPorUf()
    {
        try {
            $dql = " SELECT COUNT(DISTINCT pessoa.id) total, endereco.uf ";
            $dql .= " FROM App\Entities\ProfissionalRegistro profissionalRegistro ";
            $dql .= " INNER JOIN profissionalRegistro.pessoa pessoa ";
            $dql .= " INNER JOIN pessoa.endereco endereco ";
            $dql .= " INNER JOIN profissionalRegistro.situacaoRegistro situacaoRegistro ";

            $dql .= " WHERE situacaoRegistro.id = :codigo ";
            $dql .= " AND (profissionalRegistro.dataFim > :dataAtual OR profissionalRegistro.dataFim IS NULL) ";
            $dql .= " GROUP BY endereco.uf ";
            $dql .= " ORDER BY endereco.uf ";

            $parametros['codigo'] = Constants::SITUACAO_PROFISSIONAL_ATIVO;
            $parametros['dataAtual'] = new DateTime();

            $query = $this->getEntityManager()->createQuery($dql);
            $query->setParameters($parametros);

            return $query->getArrayResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de Profissionais.
     *
     * @return integer
     */
    public function getQtdProfissionais()
    {
        try {
            $query = $this->createQueryBuilder('profissionais');
            $query->select('COUNT(profissionais.id)');

            return $query->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Retorna todos os Conselheiros em ordem de CAU/UF
     *
     * @return array|null
     */
    public function getProfissionaisConselheiros()
    {
        try {
            $subDql = " SELECT MAX(profissionalRegistro2.id) ";
            $subDql .= " FROM App\Entities\ProfissionalRegistro as profissionalRegistro2 ";
            $subDql .= " WHERE profissionalRegistro2.pessoa = pessoa.id ";

            $dql = " SELECT DISTINCT profissional.nome, profissional.cpf, profissionalRegistro.id, ";
            $dql .= " profissionalRegistro.registroRegional, pessoa.email, profissionalRegistro.dataFim, ";
            $dql .= " situacaoRegistro.descricao, filial.descricao cauUf ";
            $dql .= " FROM App\Entities\ProfissionalRegistro profissionalRegistro ";
            $dql .= " INNER JOIN profissionalRegistro.pessoa pessoa ";
            $dql .= " INNER JOIN pessoa.profissional profissional ";
            $dql .= " INNER JOIN pessoa.conselheiro conselheiro ";
            $dql .= " INNER JOIN conselheiro.filial filial ";
            $dql .= " INNER JOIN profissionalRegistro.situacaoRegistro situacaoRegistro ";
            $dql .= " WHERE profissionalRegistro.id = (".$subDql.") ";
            $dql .= " ORDER BY filial.descricao ";

            $query = $this->getEntityManager()->createQuery($dql);

            return $query->getArrayResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a instância do 'Profissional' conforme o 'id' informado.
     *
     * @param integer $id
     *
     * @return Profissional|null
     * @throws NonUniqueResultException
     */
    public function getProfissional($id)
    {
        try {
            $query = $this->createQueryBuilder("profissional");
            $query->where("profissional.id = :id");
            $query->setParameter("id", $id);

            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de profissionais
     *
     * @param bool $isApenasAtivos
     * @param string|null $sigla
     * @return integer
     * @throws Exception
     */
    public function getQuantidadeProfissionais($isApenasAtivos = false, $sigla = null)
    {
        try {
            $subSql = $this->getSubSqlRegistroProfissional();

            $sql = " SELECT COUNT(DISTINCT profissional.id) ";
            $this->getSqlProfissionais($sql, $subSql, $isApenasAtivos);

            if (!empty($sigla))
                $sql .= " AND endereco.uf = '$sigla'";

            $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
            $stmt->execute();
            $quantidade = $stmt->fetch();
            return $quantidade['count'];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retorna a quantidade de profissionais agrupado por UF
     *
     * @param bool $isApenasAtivos
     * @return array
     * @throws Exception
     */
    public function getQuantidadeProfissionaisAgrupadoPorUf($isApenasAtivos = false)
    {
        try {
            $subSql = $this->getSubSqlRegistroProfissional();

            $sql = " SELECT COUNT(DISTINCT profissional.id) quantidade, endereco.uf ";
            $this->getSqlProfissionais($sql, $subSql, $isApenasAtivos);

            $sql .= " GROUP BY endereco.uf ";
            $sql .= " ORDER BY endereco.uf ASC ";

            $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retorna todos os profissionais
     *
     * @param string|null $sigla
     * @return ProfissionalTO[]
     * @throws Exception
     */
    public function getTodosProfissionais($sigla = null)
    {
        try {
            $subSql = $this->getSubSqlRegistroProfissional();

            $sql = $this->getSelectProfissionais();
            $this->getSqlProfissionais($sql, $subSql);

            if (!empty($sigla))
                $sql .= " AND endereco.uf = '$sigla'";

            $sql .= " ORDER BY endereco.uf ASC, profissional.nome ASC ";

            $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
            $stmt->execute();

            $dados = $stmt->fetchAll();

            return array_map(function ($profissional) {
                return ProfissionalTO::newInstance($profissional);
            }, $dados);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @param string $sql
     * @param string $subSql
     * @throws Exception
     * @see Observação Foi necessário utilizar SQL Nativo, pois o Doctrine não permite o uso de 'IS NULL' com 'SubQuery'.
     */
    private function getSqlProfissionais(string &$sql, string $subSql, $isApenasAtivos = false): void
    {
        $dataAtual = Utils::getDataHoraZero()->format("Y-m-d");

        $sql .= " FROM public.tb_profissional_registro AS profissionalRegistro ";
        $sql .= " INNER JOIN public.tb_pessoa pessoa ON pessoa.id = profissionalRegistro.profissional_id ";
        $sql .= " INNER JOIN public.tb_enderecos endereco ON endereco.ID = pessoa.enderecocorrespondencia ";
        $sql .= " INNER JOIN public.tb_profissional profissional ON profissional.pessoa_id = pessoa.id ";
        $sql .= " INNER JOIN public.tb_situacaoregistro situacaoRegistro ON situacaoRegistro.id = profissionalRegistro.situacaoregistro_id ";
        $sql .= " INNER JOIN public.tb_profissional_titulo profissionalTitulo ON profissionalTitulo.pessoa_id = pessoa.id ";
        $sql .= " WHERE profissionalRegistro.id = ($subSql) ";
        $sql .= " AND profissionalTitulo.titulo_id = ". Constants::TITULO_PROFISSIONAL_ARQUITETO_URBANISTA;

        if ($isApenasAtivos) {
            $subSqlDataFim = " SELECT data_fim_registro ";
            $subSqlDataFim .= " FROM public.tb_profissional_registro ";
            $subSqlDataFim .= " WHERE profissional_id = pessoa.id ";
            $subSqlDataFim .= " ORDER BY data_fim_registro DESC NULLS First LIMIT 1 ";

            // Data Fim do Registro do Profissional igual ou maior que a data atual, ou SEM LIMITE (NULL).
            $sql .= " AND (($subSqlDataFim) >= '$dataAtual' OR ($subSqlDataFim) IS NULL) ";
            $sql .= " AND profissionalRegistro.situacaoregistro_id = ". Constants::SITUACAO_REGISTRO_PROFISSIONAL_ATIVO;
        }

    }

    /**
     * @return string
     */
    private function getSubSqlRegistroProfissional(): string
    {
        $sql = " SELECT profissionalRegistro2.id ";
        $sql .= " FROM public.tb_profissional_registro AS profissionalRegistro2 ";
        $sql .= " WHERE profissionalRegistro2.profissional_id = pessoa.id ";
        $sql .= " ORDER BY profissionalRegistro2.data_fim_registro DESC NULLS First LIMIT 1 ";
        return $sql;
    }

    /**
     * @return string
     */
    private function getSelectProfissionais(): string
    {
        $sql = " SELECT DISTINCT profissional.nome, profissional.id, profissional.cpf, profissional.registro_nacional registro, ";
        $sql .= " situacaoRegistro.descricao situacao, profissionalRegistro.data_fim_registro datafimregistro, endereco.uf ";
        return $sql;
    }
}
