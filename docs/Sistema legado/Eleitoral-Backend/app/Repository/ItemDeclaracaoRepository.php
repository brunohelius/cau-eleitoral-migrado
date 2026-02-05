<?php
/*
 * ItemDeclaracaoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'ItemDeclaracao'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class ItemDeclaracaoRepository extends AbstractRepository
{
    /**
     * Exclui um item de declaração pelo item informado.
     *
     * @param integer $id
     */
    public function deleteById($id)
    {
        $dql =  "DELETE App\Entities\ItemDeclaracao itemDeclaracao WHERE itemDeclaracao.id = :id ";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("id", $id);
        $query->execute();
    }
}