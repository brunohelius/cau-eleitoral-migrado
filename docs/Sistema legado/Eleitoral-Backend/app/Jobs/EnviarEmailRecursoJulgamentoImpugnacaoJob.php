<?php

namespace App\Jobs;

use App\Business\JulgamentoSubstituicaoBO;
use App\Business\RecursoImpugnacaoBO;
use App\Business\RecursoSubstituicaoBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailRecursoJulgamentoImpugnacaoJob extends Job
{

    /**
     * @var integer
     */
    private $idRecursoImpugnacao;

    /**
     * Criação de uma nova instância de EnviarEmailRecursoJulgamentoImpugnacaoJob.
     *
     * @param $idRecursoImpugnacao
     */
    public function __construct($idRecursoImpugnacao)
    {
        $this->idRecursoImpugnacao = $idRecursoImpugnacao;
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
        $this->getRecursoImpugnacaoBO()->enviarEmailCadastroRecursoImpugnacao($this->idRecursoImpugnacao);
    }

    /**
     * Retorna uma instância de RecursoImpugnacaoBO
     *
     * @return RecursoImpugnacaoBO
     */
    private function getRecursoImpugnacaoBO()
    {
        return app()->make(RecursoImpugnacaoBO::class);
    }
}
