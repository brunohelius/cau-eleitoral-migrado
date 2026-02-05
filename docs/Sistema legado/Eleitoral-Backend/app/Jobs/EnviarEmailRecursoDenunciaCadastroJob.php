<?php
/*
 * EnviarEmailRecursoDenunciaCadastroJob.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Jobs;

use App\Business\RecursoContrarrazaoBO;

class EnviarEmailRecursoDenunciaCadastroJob extends Job
{

    /**
     * @var integer
     */
    private $idRecursoDenuncia;

    /**
     * Criação de uma nova instância de EnviarEmailRecursoDenunciaCadastroJob.
     *
     * @param $idRecursoDenuncia
     */
    public function __construct($idRecursoDenuncia)
    {
        $this->idRecursoDenuncia = $idRecursoDenuncia;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->getRecursoContrarrazaoBO()->enviarEmailCadastroRecurso($this->idRecursoDenuncia);
    }

    /**
     * Retorna uma instância de RecursoContrarrazaoBO
     *
     * @return RecursoContrarrazaoBO
     */
    private function getRecursoContrarrazaoBO()
    {
        return app()->make(RecursoContrarrazaoBO::class);
    }
}
