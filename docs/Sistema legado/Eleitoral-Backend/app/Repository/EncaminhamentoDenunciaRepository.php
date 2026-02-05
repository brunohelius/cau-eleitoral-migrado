<?php
/*
 * EncaminhamentoDenunciaRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\HistoricoCalendario;
use App\Util\Utils;
use Doctrine\ORM\NoResultException;
use Illuminate\Support\Facades\Date;
use App\Config\Constants;
use Doctrine\ORM\AbstractQuery;
use OpenApi\Util;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'EncaminhamentoDenuncia'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class EncaminhamentoDenunciaRepository extends AbstractRepository
{

    /**
     * REGRA: Caso o destinatário seja o denunciado e já exista um encaminhamento “Produção de provas”
     * pendente para ele na mesma denúncia, então o sistema impede o cadastro e exibe a mensagem ME04.
     *
     * @param $filtroTO
     * @return array|null
     */
    public function validaMembroComissaoChapaEncaminhamento($filtroTO)
    {
        try {
            $query = $this->createQueryBuilder("encaminhamentoDenuncia");
            $query->innerJoin("encaminhamentoDenuncia.denuncia", "denuncia")->addSelect("denuncia");
            $query->innerJoin("encaminhamentoDenuncia.tipoEncaminhamento", "tipoEncaminhamento")->addSelect("tipoEncaminhamento");
            $query->innerJoin("encaminhamentoDenuncia.tipoSituacaoEncaminhamento", "tipoSituacaoEncaminhamento")->addSelect('tipoSituacaoEncaminhamento');

            if (!empty($filtroTO->tipoDenuncia)) {
                if ($filtroTO->tipoDenuncia == Constants::TIPO_MEMBRO_CHAPA) {
                    $query->innerJoin("denuncia.denunciaMembroChapa", "denunciaMembroChapa")->addSelect("denunciaMembroChapa");
                    $query->innerJoin("denunciaMembroChapa.membroChapa", "membroChapa")->addSelect("membroChapa");
                } else if ($filtroTO->tipoDenuncia == Constants::TIPO_MEMBRO_COMISSAO) {
                    $query->innerJoin("denuncia.denunciaMembroComissao", "denunciaMembroComissao")->addSelect("denunciaMembroComissao");
                    $query->innerJoin("denunciaMembroComissao.membroComissao", "membroComissao")->addSelect("membroComissao");
                }

                $query->where('denuncia.tipoDenuncia = :tipoDenuncia');
                $query->setParameter('tipoDenuncia', $filtroTO->tipoDenuncia);

                $query->andWhere('tipoEncaminhamento.id = :tipoEncaminhamento');
                $query->andWhere("tipoSituacaoEncaminhamento.id = :idTipoSituacaoEncaminhamento");
                $query->andWhere("encaminhamentoDenuncia.destinoDenunciado = :destinoDenunciado");

                $query->setParameter('tipoEncaminhamento', Constants::TIPO_ENCAMINHAMENTO_PRODUCAO_PROVAS);
                $query->setParameter("idTipoSituacaoEncaminhamento",    Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE);
                $query->setParameter("destinoDenunciado",    true);

                if ($filtroTO->tipoDenuncia == Constants::TIPO_MEMBRO_CHAPA and !empty($filtroTO->idMembro)) {
                    $query->andWhere('membroChapa.id = :idMembro');
                    $query->setParameter('idMembro', $filtroTO->idMembro);
                } else if ($filtroTO->tipoDenuncia == Constants::TIPO_MEMBRO_COMISSAO and !empty($filtroTO->idMembro)) {
                    $query->andWhere('membroComissao.id = :idMembro');
                    $query->setParameter('idMembro', $filtroTO->idMembro);
                }
            }
            return $query->getQuery()->getArrayResult();

        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Caso o destinatário seja o denunciante e já exista um encaminhamento “Produção de provas”
     * pendente para ele na mesma denúncia, então o sistema impede o cadastro e exibe a mensagem ME04;
     *
     * @param $filtroTO
     * @return array|null
     */
    public function validarDenuncianteEncaminhamento($filtroTO)
    {
        try {
            $query = $this->createQueryBuilder("encaminhamentoDenuncia");
            $query->innerJoin("encaminhamentoDenuncia.denuncia", "denuncia")->addSelect("denuncia");
            $query->innerJoin("encaminhamentoDenuncia.tipoEncaminhamento", "tipoEncaminhamento")->addSelect("tipoEncaminhamento");
            $query->innerJoin("encaminhamentoDenuncia.tipoSituacaoEncaminhamento", "tipoSituacaoEncaminhamento")->addSelect('tipoSituacaoEncaminhamento');

            $query->where('denuncia.pessoa = :idPessoa');
            $query->setParameter('idPessoa', $filtroTO->idPessoa);

            $query->andWhere('tipoEncaminhamento.id = :tipoEncaminhamento');
            $query->andWhere("tipoSituacaoEncaminhamento.id = :idTipoSituacaoEncaminhamento");
            $query->andWhere("encaminhamentoDenuncia.destinoDenunciante = :destinoDenunciante");

            $query->setParameter('tipoEncaminhamento', Constants::TIPO_ENCAMINHAMENTO_PRODUCAO_PROVAS);
            $query->setParameter("idTipoSituacaoEncaminhamento",    Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE);
            $query->setParameter("destinoDenunciante",    true);

            if (!empty($filtroTO->idDenuncia)) {
                $query->andWhere('denuncia.id = :idDenuncia');
                $query->setParameter('idDenuncia', $filtroTO->idDenuncia);
            }

            return $query->getQuery()->getArrayResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna os encaminhamentos da denúncia
     *
     * @param $idDenuncia
     * @return array|null
     */
    public function getEncaminhamentosPorDenuncia($idDenuncia)
    {
        try {
            $query = $this->createQueryBuilder('encaminhamentoDenuncia');
            $query->innerJoin("encaminhamentoDenuncia.tipoEncaminhamento", "tipoEncaminhamento")->addSelect('tipoEncaminhamento');
            $query->innerJoin("encaminhamentoDenuncia.tipoSituacaoEncaminhamento", "tipoSituacaoEncaminhamento")->addSelect('tipoSituacaoEncaminhamento');
            $query->innerJoin("encaminhamentoDenuncia.membroComissao", "membroComissao")->addSelect('membroComissao');
            $query->innerJoin("membroComissao.profissionalEntity", "profissionalEntity")->addSelect('profissionalEntity');
            $query->leftJoin('encaminhamentoDenuncia.agendamentoEncaminhamento', 'agendamentoEncaminhamento')->addSelect('agendamentoEncaminhamento');
            $query->where("encaminhamentoDenuncia.denuncia = :id");
            $query->setParameter("id", $idDenuncia);
            $query->orderBy('encaminhamentoDenuncia.data', 'DESC');
            $query->addOrderBy('encaminhamentoDenuncia.sequencia', 'DESC');

            return $query->getQuery()->getArrayResult();

        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Retorna o encaminhamento pelo $idEncaminhamento informado, buscando também, seus arquivos relacionados
     * @param $idEncaminhamento
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getEncaminhamentoPorId($idEncaminhamento)
    {
        try {
            $query = $this->createQueryBuilder('encaminhamentoDenuncia');
            $query->innerJoin("encaminhamentoDenuncia.tipoEncaminhamento", "tipoEncaminhamento")->addSelect('tipoEncaminhamento');
            $query->innerJoin("encaminhamentoDenuncia.tipoSituacaoEncaminhamento", "tipoSituacaoEncaminhamento")->addSelect('tipoSituacaoEncaminhamento');
            $query->innerJoin("encaminhamentoDenuncia.membroComissao", "membroComissao")->addSelect('membroComissao');
            $query->innerJoin("membroComissao.profissionalEntity", "profissionalEntity")->addSelect('profissionalEntity');
            $query->leftJoin('encaminhamentoDenuncia.agendamentoEncaminhamento', 'agendamentoEncaminhamento')->addSelect('agendamentoEncaminhamento');
            $query->leftJoin('encaminhamentoDenuncia.arquivoEncaminhamento', 'arquivoEncaminhamento')->addSelect('arquivoEncaminhamento');
            $query->where("encaminhamentoDenuncia.id = :id");
            $query->setParameter("id", $idEncaminhamento);

            return $query->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Recupera os encaminhamentos do Tipo Prova com o prazo encerrado
     *
     * @return array
     */
    public function getEncaminhamentosProvaPrazoEncerrado()
    {
        $dql = " SELECT encaminhamentoDenuncia.id ";
        $dql .= " FROM App\Entities\EncaminhamentoDenuncia encaminhamentoDenuncia ";
        $dql .= " LEFT JOIN encaminhamentoDenuncia.denunciaProvas denunciaProvas ";
        $dql .= " JOIN encaminhamentoDenuncia.tipoEncaminhamento tipoEncaminhamento ";
        $dql .= " JOIN encaminhamentoDenuncia.tipoSituacaoEncaminhamento tipoSituacaoEncaminhamento ";

        $dql .= " WHERE encaminhamentoDenuncia.prazoProducaoProvas + 1 = ( ";

        $dql .= " SELECT DATE_DIFF(CURRENT_DATE(), tedSub.data)";
        $dql .= " FROM App\Entities\EncaminhamentoDenuncia tedSub ";
        $dql .= " WHERE tedSub.id = encaminhamentoDenuncia.id ";

        $dql .= " ) ";

        $dql .= " AND denunciaProvas.id is NULL ";
        $dql .= " AND tipoEncaminhamento.id  = " . Constants::TIPO_ENCAMINHAMENTO_PRODUCAO_PROVAS;
        $dql .= " AND tipoSituacaoEncaminhamento.id  = " . Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE;
        $dql .= " ORDER BY encaminhamentoDenuncia.id ";

        return $this->getEntityManager()->createQuery($dql)->getArrayResult();
    }

    /**
     * Retorna todos os Encaminhamentos de Audiencia de Intrução que venceram um dia antes do dia atual.
     *
     * @return int|mixed|string|null
     * @throws \Exception
     */
    public function getEncaminhamentoAudienciaInstrucaoPorData()
    {
        $data = Utils::removerDiasData(Utils::getData(), 1);
        $dataHoraZero = Utils::getDataHoraZero($data);
        $dataHora = Utils::getDataHoraFinal($data);

        $query = $this->createQueryBuilder('encaminhamentoDenuncia');
        $query->innerJoin("encaminhamentoDenuncia.tipoEncaminhamento", "tipoEncaminhamento")->addSelect('tipoEncaminhamento');
        $query->innerJoin("encaminhamentoDenuncia.tipoSituacaoEncaminhamento", "tipoSituacaoEncaminhamento")->addSelect('tipoSituacaoEncaminhamento');
        $query->innerJoin("encaminhamentoDenuncia.denuncia", "denuncia")->addSelect('denuncia');
        $query->where("tipoEncaminhamento.id = :idTipoEncaminhamento");
        $query->andWhere("tipoSituacaoEncaminhamento.id = :idTipoSituacaoEncaminhamento");
        $query->andWhere("encaminhamentoDenuncia.data > :dataHoraZero");
        $query->andWhere("encaminhamentoDenuncia.data < :dataHoraFinal");

        $query->setParameter("dataHoraZero", $dataHoraZero);
        $query->setParameter("dataHoraFinal", $dataHora);
        $query->setParameter("idTipoEncaminhamento", Constants::TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO);
        $query->setParameter("idTipoSituacaoEncaminhamento", Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE);

        return $query->getQuery()->getResult();
    }

    /**
     * Retorna todos os encaminhamentos com o Tipo e a Denuncia Informado com situação Pendente.
     *
     * @return int|mixed|string|null
     * @throws \Exception
     */
    public function getEncaminhamentosPendentesPorTipoEDenuncia($idTipoEncaminhamento, $idDenuncia)
    {
        $query = $this->createQueryBuilder('encaminhamentoDenuncia');
        $query->innerJoin("encaminhamentoDenuncia.tipoEncaminhamento", "tipoEncaminhamento")->addSelect('tipoEncaminhamento');
        $query->innerJoin("encaminhamentoDenuncia.tipoSituacaoEncaminhamento", "tipoSituacaoEncaminhamento")->addSelect('tipoSituacaoEncaminhamento');
        $query->innerJoin("encaminhamentoDenuncia.denuncia", "denuncia")->addSelect('denuncia');
        $query->where("tipoEncaminhamento.id = :idTipoEncaminhamento");
        $query->andWhere("tipoSituacaoEncaminhamento.id = :idTipoSituacaoEncaminhamento");
        $query->andWhere("denuncia.id = :idDenuncia");

        $query->setParameter("idDenuncia", $idDenuncia);
        $query->setParameter("idTipoEncaminhamento", $idTipoEncaminhamento);
        $query->setParameter("idTipoSituacaoEncaminhamento", Constants::TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE);

        return $query->getQuery()->getResult();
    }

    /**
     * Retorna os encaminhamentos de 'ImpedimentoSuspeicao'
     *
     * @param $idEmcaminhamento
     * @return mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getEmcaminhamentoImpedimentoSuspeicao($idEmcaminhamento)
    {
        try {
            $query = $this->createQueryBuilder("encaminhamentoDenuncia");
            $query->innerJoin("encaminhamentoDenuncia.tipoEncaminhamento", "tipoEncaminhamento")->addSelect('tipoEncaminhamento');
            $query->innerJoin("encaminhamentoDenuncia.tipoSituacaoEncaminhamento", "tipoSituacaoEncaminhamento")->addSelect('tipoSituacaoEncaminhamento');

            $query->leftJoin("encaminhamentoDenuncia.impedimentoSuspeicao", "impedimentoSuspeicao")->addSelect("impedimentoSuspeicao");
            $query->leftJoin('impedimentoSuspeicao.denunciaAdmitida', 'denunciaAdmitida')->addSelect('denunciaAdmitida');
            $query->leftJoin('denunciaAdmitida.arquivoDenunciaAdmitida', 'arquivoDenunciaAdmitida')->addSelect('arquivoDenunciaAdmitida');
            $query->leftJoin("denunciaAdmitida.membroComissao", "membroComissaoDenunciaAdmitida")->addSelect('membroComissaoDenunciaAdmitida');
            $query->leftJoin("membroComissaoDenunciaAdmitida.profissionalEntity", "profissionalDenunciaAdmitida")->addSelect('profissionalDenunciaAdmitida');
            $query->leftJoin('denunciaAdmitida.denuncia', 'denuncia')->addSelect('denuncia') ;

            $query->leftJoin("encaminhamentoDenuncia.membroComissao", "membroComissaoEncaminhamento")->addSelect('membroComissaoEncaminhamento');
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

}
