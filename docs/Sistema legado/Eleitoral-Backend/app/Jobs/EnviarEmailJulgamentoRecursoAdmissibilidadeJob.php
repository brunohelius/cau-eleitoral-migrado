<?php
/*
 * EnviarEmailJulgamentoRecursoAdmissibilidadeJob.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Jobs;

use App\Business\AlegacaoFinalBO;
use App\Business\JulgamentoRecursoAdmissibilidadeBO;

class EnviarEmailJulgamentoRecursoAdmissibilidadeJob extends Job
{

    /**
     * @var integer
     */
    private $idJulgamentoRecursoAdmissibilidade;

    /**
     * Criação de uma nova instância de EnviarEmailJulgamentoRecursoAdmissibilidadeJob.
     *
     * @param $idJulgamentoRecursoAdmissibilidade
     */
    public function __construct($idJulgamentoRecursoAdmissibilidade)
    {
        $this->idJulgamentoRecursoAdmissibilidade = $idJulgamentoRecursoAdmissibilidade;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->getJulgamentoRecursoAdmissibilidadeBO()->notificacaoJulgamentoRecurso($this->idJulgamentoRecursoAdmissibilidade);
    }

    /**
     * Retorna uma instância de JulgamentoRecursoAdmissibilidadeBO
     *
     * @return JulgamentoRecursoAdmissibilidadeBO
     */
    private function getJulgamentoRecursoAdmissibilidadeBO()
    {
        return app()->make(JulgamentoRecursoAdmissibilidadeBO::class);
    }
}
