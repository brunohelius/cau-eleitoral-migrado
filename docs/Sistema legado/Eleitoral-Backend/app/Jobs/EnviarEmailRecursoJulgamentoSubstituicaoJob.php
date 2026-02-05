<?php

namespace App\Jobs;

use App\Business\JulgamentoSubstituicaoBO;
use App\Business\RecursoSubstituicaoBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailRecursoJulgamentoSubstituicaoJob extends Job
{

    /**
     * @var integer
     */
    private $idRecursoSubstituicao;

    /**
     * Criação de uma nova instância de EnviarEmailRecursoJulgamentoSubstituicaoJob.
     *
     * @param $idRecursoSubstituicao
     */
    public function __construct($idRecursoSubstituicao)
    {
        $this->idRecursoSubstituicao = $idRecursoSubstituicao;
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
        $this->getRecursoSubstituicaoBO()->enviarEmailCadastroRecursoSubstituicao($this->idRecursoSubstituicao);
    }

    /**
     * Retorna uma instância de RecursoSubstituicaoBO
     *
     * @return RecursoSubstituicaoBO
     */
    private function getRecursoSubstituicaoBO()
    {
        return app()->make(RecursoSubstituicaoBO::class);
    }
}
