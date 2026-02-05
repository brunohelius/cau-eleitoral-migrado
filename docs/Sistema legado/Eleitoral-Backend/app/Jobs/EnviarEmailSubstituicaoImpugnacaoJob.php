<?php

namespace App\Jobs;

use App\Business\ContrarrazaoRecursoImpugnacaoBO;
use App\Business\SubstituicaoImpugnacaoBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailSubstituicaoImpugnacaoJob extends Job
{
    /**
     * @var integer
     */
    private $idSubstituicaoImpugnacao;

    /**
     * Criação de uma nova instância de EnviarEmailSubstituicaoImpugnacaoJob.
     *
     * @param $idSubstituicaoImpugnacao
     */
    public function __construct($idSubstituicaoImpugnacao)
    {
        $this->idSubstituicaoImpugnacao = $idSubstituicaoImpugnacao;
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
        $this->getSubstituicaoImpugnacaoBO()->enviarEmailsSubstituicaoIncluida($this->idSubstituicaoImpugnacao);
    }

    /**
     * Retorna uma instância de SubstituicaoImpugnacaoBO
     *
     * @return SubstituicaoImpugnacaoBO
     */
    private function getSubstituicaoImpugnacaoBO()
    {
        return app()->make(SubstituicaoImpugnacaoBO::class);
    }
}
