<?php
/*
 * EnviarEmailJulgamentoAdmissibilidadeJob.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Jobs;

use App\Business\AlegacaoFinalBO;
use App\Business\JulgamentoAdmissibilidadeBO;

class EnviarEmailJulgamentoAdmissibilidadeJob extends Job
{

    /**
     * @var integer
     */
    private $idJulgamentoAdmissibilidade;

    /**
     * Criação de uma nova instância de EnviarEmailJulgamentoAdmissibilidadeJob.
     *
     * @param $idJulgamentoAdmissibilidade
     */
    public function __construct($idJulgamentoAdmissibilidade)
    {
        $this->idJulgamentoAdmissibilidade = $idJulgamentoAdmissibilidade;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->getJulgamentoAdmissibilidadeBO()->gerarEEnviarEmail($this->idJulgamentoAdmissibilidade);
    }

    /**
     * Retorna uma instância de JulgamentoAdmissibilidadeBO
     *
     * @return JulgamentoAdmissibilidadeBO
     */
    private function getJulgamentoAdmissibilidadeBO()
    {
        return app()->make(JulgamentoAdmissibilidadeBO::class);
    }
}
