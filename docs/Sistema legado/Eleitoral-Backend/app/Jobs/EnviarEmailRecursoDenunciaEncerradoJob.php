<?php
/*
 * EnviarEmailRecursoDenunciaEncerradoJob.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Jobs;

use App\Business\RecursoContrarrazaoBO;

class EnviarEmailRecursoDenunciaEncerradoJob extends Job
{

    /**
     * @var integer
     */
    private $idDenuncia;

    /**
     * Criação de uma nova instância de EnviarEmailRecursoDenunciaEncerradoJob.
     *
     * @param $idDenuncia
     */
    public function __construct($idDenuncia)
    {
        $this->idDenuncia = $idDenuncia;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->getRecursoContrarrazaoBO()->enviarEmailEncerrarRecurso($this->idDenuncia);
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
