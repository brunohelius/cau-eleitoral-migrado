<?php
/*
 * RecursoSegundoJulgamentoSubstituicaoRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'RecursoSegundoJulgamentoSubstituicaoRepository'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class RecursoSegundoJulgamentoSubstituicaoRepository extends AbstractRepository
{

    /**
     * Buscar Substituição Julgamento por id chapa.
     *
     * @param $idChapa
     * @return mixed|null
     */
    public function getPorChapa($idChapa)
    {
        try {
            $query = $this->createQueryBuilder('recursoSegundoJulgamentoSubstituicao');
            $query->join("recursoSegundoJulgamentoSubstituicao.julgamentoSegundaInstanciaSubstituicao", "julgamentoSegundaInstanciaSubstituicao");
            $query->join("julgamentoSegundaInstanciaSubstituicao.substituicaoJulgamentoFinal", "substituicaoJulgamentoFinal");
            $query->join("substituicaoJulgamentoFinal.julgamentoFinal", "julgamentoFinal");
            $query->join("julgamentoFinal.chapaEleicao", "chapaEleicao");

            $query->where("chapaEleicao.id = :idChapa");
            $query->setParameters(["idChapa" => $idChapa]);

            $query->orderBy("recursoSegundoJulgamentoSubstituicao.dataCadastro", "ASC");

            return $query->getQuery()->getResult();
        } catch (NonUniqueResultException | NoResultException $e) {
            return null;
        }
    }

}
