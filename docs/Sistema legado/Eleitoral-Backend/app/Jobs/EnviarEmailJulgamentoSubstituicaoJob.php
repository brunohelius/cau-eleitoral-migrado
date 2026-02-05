<?php

namespace App\Jobs;

use App\Business\JulgamentoSubstituicaoBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailJulgamentoSubstituicaoJob extends Job
{

    /**
     * @var integer
     */
    private $idJulgamentoSubstituicao;

    /**
     * Criação de uma nova instância de EnviarEmailJulgamentoSubstituicaoJob.
     *
     * @param $idJulgamentoSubstituicao
     */
    public function __construct($idJulgamentoSubstituicao)
    {
        $this->idJulgamentoSubstituicao = $idJulgamentoSubstituicao;
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
        $this->getJulgamentoSubstituicaoBO()->enviarEmailCadastroJulgamento($this->idJulgamentoSubstituicao);
    }

    /**
     * Retorna uma instância de JulgamentoSubstituicaoBO
     *
     * @return JulgamentoSubstituicaoBO
     */
    private function getJulgamentoSubstituicaoBO()
    {
        return app()->make(JulgamentoSubstituicaoBO::class);
    }
}
