<?php
/*
 * JulgamentoFinalBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Entities\Entity;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use App\Entities\MembroSubstituicaoJulgamentoFinal;
use App\Repository\MembroSubstituicaoJulgamentoFinalRepository;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'MembroSubstituicaoJulgamentoFinal'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class MembroSubstituicaoJulgamentoFinalBO extends AbstractBO
{

    /**
     * @var MembroSubstituicaoJulgamentoFinalRepository
     */
    private $membroSubstituicaoJulgamentoFinalRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
    }

    /**
     * Salva o membro substituto para julgamento final
     *
     * @param MembroSubstituicaoJulgamentoFinal[] $membrosSubstituicaoJulgamentoFinal
     * @return Entity[]
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvarMembrosSubstituicaoJulgamentoFinal($membrosSubstituicaoJulgamentoFinal)
    {
        return $this->getMembroSubstituicaoJulgamentoFinalRepository()->persistEmLote($membrosSubstituicaoJulgamentoFinal);
    }

    /**
     * Retorna uma nova instância de 'MembroSubstituicaoJulgamentoFinalRepository'.
     *
     * @return MembroSubstituicaoJulgamentoFinalRepository
     */
    private function getMembroSubstituicaoJulgamentoFinalRepository()
    {
        if (empty($this->membroSubstituicaoJulgamentoFinalRepository)) {
            $this->membroSubstituicaoJulgamentoFinalRepository = $this->getRepository(MembroSubstituicaoJulgamentoFinal::class);
        }

        return $this->membroSubstituicaoJulgamentoFinalRepository;
    }
}




