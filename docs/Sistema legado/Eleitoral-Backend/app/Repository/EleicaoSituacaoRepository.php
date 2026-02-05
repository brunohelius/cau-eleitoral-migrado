<?php
/*
 * EleicaoSituacaoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use App\Util\Utils;
use Exception;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'EleicaoSituacao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class EleicaoSituacaoRepository extends AbstractRepository
{
    /**
     * Retorna as situações da eleição, pela o id da pessoa informada, verificando se o mesmo
     * participa de alguma comissão de alguma eleição válida.
     *
     * @param integer $idPessoa
     *
     * @return array
     * @throws Exception
     */
    public function getSituacoesPorIdPessoa($idPessoa)
    {
        $hoje = Utils::getDataHoraZero();
        $query = $this->createQueryBuilder("eleicaoSituacao");
        $query->select ("atividadeSecundaria_dec.id idAtividadeSecundaria, 
            membroComissao.id idMembroComissao, situacaoMembroComissao.id situacaoMembro, atividadeSecundaria_dec.id, 
            calendario.ativo sitCalendario, membroComissaoSituacao.data dataSituacao, filial.id idCauUf, filial.prefixo prefixo");
        $query->innerJoin("eleicaoSituacao.eleicao", "eleicao");
        $query->innerJoin("eleicao.calendario", "calendario");
        $query->innerJoin("calendario.atividadesPrincipais", "atividadePrincipal");
        $query->innerJoin("atividadePrincipal.atividadesSecundarias", "atividadeSecundaria");
        $query->innerJoin("atividadeSecundaria.informacaoComissaoMembro", "informacaoComissaoMembro");
        $query->innerJoin("informacaoComissaoMembro.membrosComissao", "membroComissao");
        $query->innerJoin("membroComissao.filial", "filial");
        $query->innerJoin("membroComissao.membroComissaoSituacao", "membroComissaoSituacao");
        $query->innerJoin("membroComissaoSituacao.situacaoMembroComissao", "situacaoMembroComissao");
        $query->innerJoin("atividadePrincipal.atividadesSecundarias", "atividadeSecundaria_dec");
        $query->innerJoin("atividadeSecundaria_dec.declaracoesAtividadeSecundaria", "declaracoesAtividadeSecundaria");
        $query->where(':hoje BETWEEN atividadeSecundaria_dec.dataInicio AND atividadeSecundaria_dec.dataFim');
        $query->setParameter('hoje', $hoje->format('Y-m-d'));
        $query->andWhere("membroComissao.pessoa = :idPessoa");
        $query->andWhere("atividadeSecundaria_dec.nivel = 4");
        $query->setParameter("idPessoa", $idPessoa);
        $query->orderBy("membroComissaoSituacao.data");

        return $query->getQuery()->getArrayResult();
    }
}
