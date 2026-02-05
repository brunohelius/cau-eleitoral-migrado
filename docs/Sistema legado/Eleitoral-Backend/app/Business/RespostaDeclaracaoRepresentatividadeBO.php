<?php
/*
 * RespostaDeclaracaoRepresentatividadeBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Entities\RespostaDeclaracaoRepresentatividade;
use App\Repository\RespostaDeclaracaoRepresentatividadeRepository;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'RespostaDeclaracao'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class RespostaDeclaracaoRepresentatividadeBO extends AbstractBO
{

    /**
     * @var RespostaDeclaracaoRepresentatividadeRepository
     */
    private $respostaDeclaracaoRepresentatividadeRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->respostaDeclaracaoRepresentatividadeRepository = $this->getRepository(RespostaDeclaracaoRepresentatividade::class);
    }

    public function excluirPorMembro($membro)
    {
        $this->respostaDeclaracaoRepresentatividadeRepository->excluirPorMembro($membro);
    }

}




