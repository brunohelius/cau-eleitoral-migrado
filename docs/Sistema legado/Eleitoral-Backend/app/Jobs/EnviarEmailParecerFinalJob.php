<?php
/*
 * EnviarEmailParecerFinalJob.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Jobs;

use App\Business\ParecerFinalBO;

class EnviarEmailParecerFinalJob extends Job
{

    /**
     * @var integer
     */
    private $idParecerFinal;

    /**
     * Criação de uma nova instância de EnviarEmailParecerFinalJob.
     *
     * @param $idParecerFinal
     */
    public function __construct($idParecerFinal)
    {
        $this->idParecerFinal = $idParecerFinal;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->getParecerFinalBO()->enviarEmailCadastroParecerFinal($this->idParecerFinal);
    }

    /**
     * Retorna uma instância de ParecerFinalBO
     *
     * @return ParecerFinalBO
     */
    private function getParecerFinalBO()
    {
        return app()->make(ParecerFinalBO::class);
    }
}
