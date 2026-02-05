<?php
/*
 * EnviarEmailAlegacaoFinalPrazoEncerradoJob.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Jobs;

use App\Business\AlegacaoFinalBO;

class EnviarEmailAlegacaoFinalPrazoEncerradoJob extends Job
{

    /**
     * @var integer
     */
    private $idEncaminhamentoDenuncia;

    /**
     * Criação de uma nova instância de EnviarEmailAlegacaoFinalPrazoEncerradoJob.
     *
     * @param $idEncaminhamentoDenuncia
     */
    public function __construct($idEncaminhamentoDenuncia)
    {
        $this->idEncaminhamentoDenuncia = $idEncaminhamentoDenuncia;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->getAlegacaoFinalBO()->enviarEmailPrazoEncerradoAlegacaoFinal($this->idEncaminhamentoDenuncia);
    }

    /**
     * Retorna uma instância de AlegacaoFinalBO
     *
     * @return AlegacaoFinalBO
     */
    private function getAlegacaoFinalBO()
    {
        return app()->make(AlegacaoFinalBO::class);
    }
}
