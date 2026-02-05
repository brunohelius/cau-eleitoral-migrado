<?php
/*
 * CabecalhoEmailUfRepository.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Repository;

/**
 * Repositório responsável por encapsular as implementações de persistência da entidade 'CabecalhoEmailUf'.
 *
 * @package App\Repository
 * @author Squadra Tecnologia S/A.
 */
class CabecalhoEmailUfRepository extends AbstractRepository
{

    /**
     * Retorna UFs conforme o identificador de cabeçalho de e-mail informado.
     *
     * @param $idCabecalhoEmail
     * @return mixed
     */
    public function getPorCabecalhoEmail($idCabecalhoEmail)
    {
        $query = $this->createQueryBuilder('cabecalhoEmailUf');
        $query->leftJoin("cabecalhoEmailUf.cabecalhoEmail", "cabecalhoEmail");

        $query->where("cabecalhoEmail.id = :idCabecalhoEmail");
        $query->setParameter("idCabecalhoEmail", $idCabecalhoEmail);

        return $query->getQuery()->getResult();
    }

}