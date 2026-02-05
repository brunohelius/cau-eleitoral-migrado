<?php
/**
 * Created by PhpStorm.
 * User: squadra
 * Date: 16/09/2019
 * Time: 15:56
 */

namespace App\Repository;

use App\Entities\HistoricoCalendario;
use Doctrine\ORM\NoResultException;
use Illuminate\Support\Facades\Date;
use App\Config\Constants;
use Doctrine\ORM\AbstractQuery;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'ImpedimentoSuspeicao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ImpedimentoSuspeicaoRepository extends AbstractRepository
{

    /**
     * Retorna 'ImpedimentoSuspeicao' relacionada a um pedido de emcaminhamento.
     *
     * @param $idEmcaminhamento
     * @return mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getImpedimentoSuspeicaoPorEmcaminhamento($idEmcaminhamento)
    {
        try {
            $query = $this->createQueryBuilder("impedimentoSuspeicao");
            $query->innerJoin('impedimentoSuspeicao.denunciaAdmitida', 'denunciaAdmitida')->addSelect('denunciaAdmitida');
            $query->leftJoin('denunciaAdmitida.arquivoDenunciaAdmitida', 'arquivoDenunciaAdmitida')->addSelect('arquivoDenunciaAdmitida');
            $query->innerJoin("denunciaAdmitida.membroComissao", "membroComissaoDenunciaAdmitida")->addSelect('membroComissaoDenunciaAdmitida');
            $query->leftJoin("membroComissaoDenunciaAdmitida.profissionalEntity", "profissionalDenunciaAdmitida")->addSelect('profissionalDenunciaAdmitida');
            $query->leftJoin('denunciaAdmitida.denuncia', 'denuncia')->addSelect('denuncia') ;

            $query->innerJoin("impedimentoSuspeicao.encaminhamentoDenuncia", "encaminhamentoDenuncia")->addSelect("encaminhamentoDenuncia");
            $query->innerJoin("encaminhamentoDenuncia.tipoEncaminhamento", "tipoEncaminhamento")->addSelect('tipoEncaminhamento');
            $query->innerJoin("encaminhamentoDenuncia.tipoSituacaoEncaminhamento", "tipoSituacaoEncaminhamento")->addSelect('tipoSituacaoEncaminhamento');

            $query->innerJoin("encaminhamentoDenuncia.membroComissao", "membroComissaoEncaminhamento")->addSelect('membroComissaoEncaminhamento');
            $query->leftJoin("membroComissaoEncaminhamento.profissionalEntity", "profissionalEncaminhamento")->addSelect('profissionalEncaminhamento');

            $query->leftJoin('encaminhamentoDenuncia.agendamentoEncaminhamento', 'agendamentoEncaminhamento')->addSelect('agendamentoEncaminhamento');
            $query->leftJoin('encaminhamentoDenuncia.arquivoEncaminhamento', 'arquivoEncaminhamento')->addSelect('arquivoEncaminhamento');

            $query->where("encaminhamentoDenuncia.id = :idEmcaminhamento");
            $query->setParameter('idEmcaminhamento', $idEmcaminhamento);

            return $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna 'ImpedimentoSuspeicao' relacionada ao Encaminhamento e e DenunciaAdmitida.
     *
     * @param $idEmcaminhamento
     * @param $idDenunciaAdmitida
     * @return mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorEmcaminhamentoAndDenuncia($idEmcaminhamento, $idDenuncia)
    {
        try {
            $query = $this->createQueryBuilder("impedimentoSuspeicao");
            $query->innerJoin('impedimentoSuspeicao.denunciaAdmitida', 'denunciaAdmitida')->addSelect('denunciaAdmitida');
            $query->leftJoin('denunciaAdmitida.arquivoDenunciaAdmitida', 'arquivoDenunciaAdmitida')->addSelect('arquivoDenunciaAdmitida');
            $query->innerJoin("denunciaAdmitida.membroComissao", "membroComissaoDenunciaAdmitida")->addSelect('membroComissaoDenunciaAdmitida');
            $query->innerJoin("denunciaAdmitida.denuncia", "denuncia");
            $query->leftJoin("membroComissaoDenunciaAdmitida.profissionalEntity", "profissionalDenunciaAdmitida")->addSelect('profissionalDenunciaAdmitida');

            $query->innerJoin("impedimentoSuspeicao.encaminhamentoDenuncia", "encaminhamentoDenuncia")->addSelect("encaminhamentoDenuncia");
            $query->innerJoin("encaminhamentoDenuncia.tipoEncaminhamento", "tipoEncaminhamento")->addSelect('tipoEncaminhamento');
            $query->innerJoin("encaminhamentoDenuncia.tipoSituacaoEncaminhamento", "tipoSituacaoEncaminhamento")->addSelect('tipoSituacaoEncaminhamento');

            $query->innerJoin("encaminhamentoDenuncia.membroComissao", "membroComissaoEncaminhamento")->addSelect('membroComissaoEncaminhamento');
            $query->leftJoin("membroComissaoEncaminhamento.profissionalEntity", "profissionalEncaminhamento")->addSelect('profissionalEncaminhamento');

            $query->leftJoin('encaminhamentoDenuncia.agendamentoEncaminhamento', 'agendamentoEncaminhamento')->addSelect('agendamentoEncaminhamento');
            $query->leftJoin('encaminhamentoDenuncia.arquivoEncaminhamento', 'arquivoEncaminhamento')->addSelect('arquivoEncaminhamento');

            $query->where("encaminhamentoDenuncia.id = :idEmcaminhamento");
            $query->andWhere("denuncia.id = :idDenuncia");
            $query->setParameters(['idEmcaminhamento' => $idEmcaminhamento, 'idDenuncia' => $idDenuncia]);

            return $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna 'ImpedimentoSuspeicao' relacionadas à Denuncia.
     *
     * @param $idDenuncia
     * @return mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPorDenuncia($idDenuncia)
    {
        try {
            $query = $this->createQueryBuilder('impedimentoSuspeicao');
            $query->innerJoin('impedimentoSuspeicao.denunciaAdmitida', 'denunciaAdmitida')->addSelect('denunciaAdmitida');
            $query->leftJoin('denunciaAdmitida.membroComissao', 'membroComissao')->addSelect('membroComissao');
            $query->leftJoin("membroComissao.profissionalEntity", "profissional")->addSelect('profissional');
            $query->leftJoin('impedimentoSuspeicao.encaminhamentoDenuncia', 'encaminhamento')->addSelect('encaminhamento');
            $query->leftJoin('encaminhamento.membroComissao', 'membroComissaoRelator')->addSelect('membroComissaoRelator');
            $query->leftJoin('membroComissaoRelator.profissionalEntity', 'profissionalRelator')->addSelect('profissionalRelator');
            $query->andWhere("denunciaAdmitida.denuncia = :idDenuncia");
            $query->setParameter("idDenuncia", $idDenuncia);
            $query->orderBy('impedimentoSuspeicao.id', 'DESC');
            return $query->getQuery()->getArrayResult();
            
        } catch (NoResultException $e) {
            return null;
        }
    }

}
