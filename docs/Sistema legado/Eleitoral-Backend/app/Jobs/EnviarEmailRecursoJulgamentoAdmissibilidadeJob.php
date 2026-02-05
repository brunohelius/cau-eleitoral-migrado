<?php
/*
 * EnviarEmailRecursoJulgamentoAdmissibilidadeJob.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Jobs;

use App\Business\RecursoJulgamentoAdmissibilidadeBO;

class EnviarEmailRecursoJulgamentoAdmissibilidadeJob extends Job
{

    /**
     * @var integer
     */
    private $idRecursoJulgamentoAdmissibilidade;

    /**
     * Criação de uma nova instância de EnviarEmailRecursoJulgamentoAdmissibilidadeJob.
     *
     * @param $idRecursoJulgamentoAdmissibilidade
     */
    public function __construct($idRecursoJulgamentoAdmissibilidade)
    {
        $this->idRecursoJulgamentoAdmissibilidade = $idRecursoJulgamentoAdmissibilidade;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->getRecursoJulgamentoAdmissibilidadeBO()->notificacaoRecurso($this->idRecursoJulgamentoAdmissibilidade);
    }

    /**
     * Retorna uma instância de RecursoJulgamentoAdmissibilidadeBO
     *
     * @return RecursoJulgamentoAdmissibilidadeBO
     */
    private function getRecursoJulgamentoAdmissibilidadeBO()
    {
        return app()->make(RecursoJulgamentoAdmissibilidadeBO::class);
    }
}
