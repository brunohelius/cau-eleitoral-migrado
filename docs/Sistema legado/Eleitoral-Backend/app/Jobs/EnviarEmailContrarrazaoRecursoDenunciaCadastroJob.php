<?php
/*
 * EnviarEmailContrarrazaoRecursoDenunciaCadastroJob.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Jobs;

use App\Business\ContrarrazaoRecursoDenunciaBO;

class EnviarEmailContrarrazaoRecursoDenunciaCadastroJob extends Job
{

    /**
     * @var integer
     */
    private $idContrarrazaoRecurso;

    /**
     * Criação de uma nova instância de EnviarEmailContrarrazaoRecursoDenunciaCadastroJob.
     *
     * @param $idContrarrazaoRecurso
     */
    public function __construct($idContrarrazaoRecurso)
    {
        $this->idContrarrazaoRecurso = $idContrarrazaoRecurso;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->getContrarrazaoRecursoDenunciaBO()->enviarEmailCadastroContrarrazao($this->idContrarrazaoRecurso);
    }

    /**
     * Retorna uma instância de ContrarrazaoRecursoDenunciaBO
     *
     * @return ContrarrazaoRecursoDenunciaBO
     */
    private function getContrarrazaoRecursoDenunciaBO()
    {
        return app()->make(ContrarrazaoRecursoDenunciaBO::class);
    }
}
