<?php

namespace App\Repository;

use App\Config\Constants;
use App\Entities\Filial;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'Filial'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class FilialRepository extends AbstractRepository
{
    /**
     * Retorna a instância do 'Filial' conforme o prefixo informado.
     *
     * @param $prefixo
     * @return mixed|null
     * @throws NonUniqueResultException
     */
    public function getPorPrefixo($prefixo)
    {
        try {
            $query = $this->createQueryBuilder("filial");
            $query->where("filial.prefixo = :prefixo");
            $query->setParameter("prefixo", $prefixo);

            return $query->getQuery()->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna todas as filiais.
     *
     * @return array|null
     */
    public function getFiliais()
    {
        try {
            $query = $this->createQueryBuilder('filial')->addSelect('filial');
            $query->where('filial.filialId = '.Constants::ID_CAU_BR)
                ->orWhere('filial.filialId is null');
            $query->andWhere('filial.tipoFilialId = :tipoFilial');
            $query->setParameter('tipoFilial', Constants::TIPO_SEDE);
            $query->orderBy('filial.prefixo', 'ASC');

            return $query->getQuery()->getResult();
        }catch (NoResultException $e){
            return null;
        }
    }

    /**
     * Retorna as filiais associadas ao calendário informado.
     *
     * @param int $idCalendario
     * @param bool $incluirFilialCauBr
     * @return Filial[]
     */
    public function getFiliaisComBandeirasPorCalendario($idCalendario, $incluirFilialCauBr = false)
    {
        try {
            $dql = " SELECT DISTINCT filial ";
            $dql .= " FROM App\Entities\Filial filial ";
            $dql .= " INNER JOIN App\Entities\UfCalendario ufCalendario WITH ufCalendario.calendario = :idCalendario ";
            $dql .= ($incluirFilialCauBr)
                ? " AND (filial.id = ufCalendario.idCauUf OR filial.id = :idCauBr) "
                : " AND filial.id = ufCalendario.idCauUf ";
            $dql .= " ORDER BY filial.prefixo ASC ";

            $query = $this->getEntityManager()->createQuery($dql);
            $query->setParameter('idCalendario', $idCalendario);
            $query->setParameter('idCauBr', Constants::ID_CAU_BR);

            $filiais = $query->getArrayResult();

            return array_map(function ($data) {
                return Filial::newInstance($data);
            }, $filiais);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Retorna a lista de filiais, associadas às UF's que ainda não tiveram Membros de Comissão cadastradas, para um
     * determinado calendário.
     *
     * @param int $idCalendario
     * @param int $idInformacaoComissao
     * @return Filial[]
     */
    public function getFiliaisMembrosNaoCadastradosPorCalendarioInformacaoComissao($idCalendario, $idInformacaoComissao)
    {
        try {
            $dql = " SELECT DISTINCT filial1.id ";
            $dql .= " FROM App\Entities\Filial filial1 ";
            $dql .= " INNER JOIN App\Entities\MembroComissao membroComissao WITH membroComissao.idCauUf = filial1.id ";
            $dql .= " INNER JOIN membroComissao.informacaoComissaoMembro informacaoComissaoMembro ";
            $dql .= " INNER JOIN membroComissao.membroComissaoSituacao membroComissaoSituacao ";
            $dql .= " WHERE informacaoComissaoMembro.id = :idInformacaoComissao ";
            $dql .= " AND membroComissao.excluido = :excluido ";
            $dqlFiliaisCadastradas = $dql;

            $dql = " SELECT filial ";
            $dql .= " FROM App\Entities\Filial filial ";
            $dql .= " INNER JOIN App\Entities\UfCalendario ufCalendario WITH ufCalendario.calendario = :idCalendario AND filial.id = ufCalendario.idCauUf ";
            $dql .= " WHERE filial.id NOT IN ($dqlFiliaisCadastradas) ";
            $dql .= " ORDER BY filial.prefixo ASC ";

            $query = $this->getEntityManager()->createQuery($dql);
            $query->setParameter('excluido', false);
            $query->setParameter('idCalendario', $idCalendario);
            $query->setParameter('idInformacaoComissao', $idInformacaoComissao);

            $filiais = $query->getArrayResult();

            return array_map(function ($data) {
                return Filial::newInstance($data);
            }, $filiais);
        } catch (\Exception $e) {
            return [];
        }
    }
}
