<?php

namespace App\Jobs;

use App\Business\ContrarrazaoRecursoImpugnacaoBO;
use App\Business\SubstituicaoJulgamentoFinalBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailSubstituicaoJulgamentoFinalJob extends Job
{
    /**
     * @var integer
     */
    private $idSubstituicaoJulgamentoFinal;

    /**
     * Criação de uma nova instância de EnviarEmailSubstituicaoJulgamentoFinalJob.
     *
     * @param $idSubstituicaoJulgamentoFinal
     */
    public function __construct($idSubstituicaoJulgamentoFinal)
    {
        $this->idSubstituicaoJulgamentoFinal = $idSubstituicaoJulgamentoFinal;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function handle()
    {
        $this->getSubstituicaoJulgamentoFinalBO()->enviarEmailAposCadastroSubstituicao(
            $this->idSubstituicaoJulgamentoFinal
        );
    }

    /**
     * Retorna uma instância de SubstituicaoJulgamentoFinalBO
     *
     * @return SubstituicaoJulgamentoFinalBO
     */
    private function getSubstituicaoJulgamentoFinalBO()
    {
        return app()->make(SubstituicaoJulgamentoFinalBO::class);
    }
}
