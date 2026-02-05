<?php

namespace App\Jobs;

use App\Business\JulgamentoRecursoImpugnacaoBO;

class EnviarEmailJulgamentoRecursoImpugnacaoJob extends Job
{
    /**
     * @var integer
     */
    private $idJulgamentoRecursoImpugnacao;

    /**
     * @var integer
     */
    private $idCalendario;

    /**
     * Criação de uma nova instância de EnviarEmailContrarrazaoJob.
     *
     * @param $idJulgamentoRecursoImpugnacao
     * @param $idCalendario
     */
    public function __construct($idJulgamentoRecursoImpugnacao, $idCalendario)
    {
        $this->idJulgamentoRecursoImpugnacao = $idJulgamentoRecursoImpugnacao;
        $this->idCalendario = $idCalendario;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->getJulgamentoRecursoImpugnacaoBO()->enviarEmailCadastroJulgamento(
            $this->idJulgamentoRecursoImpugnacao, $this->idCalendario
        );
    }

    /**
     * Retorna uma instância de JulgamentoRecursoImpugnacaoBO
     *
     * @return JulgamentoRecursoImpugnacaoBO
     */
    private function getJulgamentoRecursoImpugnacaoBO()
    {
        return app()->make(JulgamentoRecursoImpugnacaoBO::class);
    }
}
