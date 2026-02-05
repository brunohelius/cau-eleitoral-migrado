<?php
/*
 * RespostaDeclaracaoRespository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'RespostaDeclaracao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class RespostaDeclaracaoRespository extends AbstractRepository
{
    /**
     * Retorna uma lista de instâncias de 'RespostaDeclaracao' conforme o id do membro da comissão informado.
     *
     * @param int $idMembroComissao
     *
     * @return array|null
     */
    public function getRespostasPorMembro(int $idMembroComissao)
    {
        $query = $this->createQueryBuilder("respostaDeclaracao");
        $query->innerJoin("respostaDeclaracao.membroComissao", "membroComissao");
        $query->where("membroComissao.id = :idEleicao");
        $query->setParameter("idMembroComissao", $idMembroComissao);

        return $query->getQuery()->getResult();
    }
}