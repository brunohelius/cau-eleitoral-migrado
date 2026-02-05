<?php
/*
 * IndicacaoJulgamentoSegundaInstanciaSubstituicaoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Entities\JulgamentoFinal;
use App\To\JulgamentoFinalTO;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'IndicacaoJulgamentoSegundaInstanciaSubstituicao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class IndicacaoJulgamentoSegundaInstanciaSubstituicaoRepository extends AbstractRepository
{
    /**
     * Buscar Substituição Julgamento por id chapa.
     *
     * @param $idChapa
     * @return mixed|null
     */
    public function getPorChapa($idChapa) {
        try {
            $query = $this->createQueryBuilder('substituicaoJulgamentoFinal');
            $query->leftJoin("substituicaoJulgamentoFinal.profissional", "profissionalSubstituicaoJulgamento")->addSelect("profissionalSubstituicaoJulgamento");
            $query->join("substituicaoJulgamentoFinal.julgamentoFinal", "julgamentoFinal")->addSelect("julgamentoFinal");
            $query->join("julgamentoFinal.chapaEleicao", "chapaEleicao")->addSelect("chapaEleicao");
            $query->join("chapaEleicao.filial", "filial")->addSelect("filial");
            $query->leftJoin("substituicaoJulgamentoFinal.membrosSubstituicaoJulgamentoFinal", "membrosSubstituicaoJulgamentoFinal")->addSelect("membrosSubstituicaoJulgamentoFinal");
            $query->leftJoin("membrosSubstituicaoJulgamentoFinal.indicacaoJulgamentoFinal","indicacaoJulgamentoFinal")->addSelect("indicacaoJulgamentoFinal");
            $query->leftJoin("indicacaoJulgamentoFinal.membroChapa", "membroChapaIndicacao")->addSelect("membroChapaIndicacao");
            $query->leftJoin("membroChapaIndicacao.statusParticipacaoChapa", "statusParticipacaoChapaMembroChapaIndicacao")->addSelect("statusParticipacaoChapaMembroChapaIndicacao");
            $query->leftJoin("membroChapaIndicacao.profissional", "profissionalIndicacao")->addSelect("profissionalIndicacao");
            $query->leftJoin("membroChapaIndicacao.pendencias", "pendenciasIndicacao")->addSelect("pendenciasIndicacao");
            $query->leftJoin('membroChapaIndicacao.statusValidacaoMembroChapa', 'statusValidacaoMembroChapaIndicacao')->addSelect('statusValidacaoMembroChapaIndicacao');
            $query->leftJoin("pendenciasIndicacao.tipoPendencia", "tipoPendenciaIndicacao")->addSelect("tipoPendenciaIndicacao");
            $query->leftJoin("membrosSubstituicaoJulgamentoFinal.membroChapa", "membroChapa")->addSelect("membroChapa");
            $query->leftJoin("membroChapa.statusParticipacaoChapa","statusParticipacaoChapa")->addSelect("statusParticipacaoChapa");
            $query->leftJoin("membroChapa.statusValidacaoMembroChapa","statusValidacaoMembroChapa")->addSelect("statusValidacaoMembroChapa");
            $query->leftJoin("membroChapa.profissional", "profissionalMembroSubstituicao")->addSelect("profissionalMembroSubstituicao");
            $query->leftJoin("membroChapa.pendencias", "pendencias")->addSelect("pendencias");
            $query->leftJoin('membroChapa.statusValidacaoMembroChapa', 'statusValidacaoMembroChapa')->addSelect('statusValidacaoMembroChapa');
            $query->leftJoin("pendencias.tipoPendencia", "tipoPendencia")->addSelect("tipoPendencia");

            $query->where("chapaEleicao.id = :idChapa");
            $query->setParameters(["idChapa" => $idChapa]);

            return $query->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        } catch (NoResultException $e) {
            return null;
        }
    }

}
