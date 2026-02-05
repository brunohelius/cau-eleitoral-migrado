<?php

namespace App\Jobs;

use App\Business\JulgamentoRecursoSubstituicaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use App\Entities\JulgamentoRecursoSubstituicao;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailJulgamentoRecursoSubstituicaoJob extends Job
{

    /**
     * @var integer
     */
    private $idJulgamentoRecursoSubstituicao;

    /**
     * Criação de uma nova instância de EnviarEmailJulgamentoRecursoSubstituicaoJob.
     *
     * @param $idJulgamentoRecursoSubstituicao
     */
    public function __construct($idJulgamentoRecursoSubstituicao)
    {
        $this->idJulgamentoRecursoSubstituicao = $idJulgamentoRecursoSubstituicao;
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
        $this->getJulgamentoRecursoSubstituicaoBO()->enviarEmailCadastroJulgamento(
            $this->idJulgamentoRecursoSubstituicao
        );
    }

    /**
     * Retorna uma instância de JulgamentoRecursoSubstituicaoBO
     *
     * @return JulgamentoRecursoSubstituicaoBO
     */
    private function getJulgamentoRecursoSubstituicaoBO()
    {
        return app()->make(JulgamentoRecursoSubstituicaoBO::class);
    }
}
