<?php
/*
 * EnviarEmailContrarrazaoRecursoDenunciaEncerrarJob.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Jobs;

use App\Business\ContrarrazaoRecursoDenunciaBO;

class EnviarEmailContrarrazaoRecursoDenunciaEncerrarJob extends Job
{

    /**
     * @var integer
     */
    private $idDenuncia;

    /**
     * @var integer
     */
    private $idRecurso;

    /**
     * Criação de uma nova instância de EnviarEmailContrarrazaoRecursoDenunciaEncerrarJob.
     *
     * @param $idDenuncia
     * @param $idRecurso
     */
    public function __construct($idDenuncia, $idRecurso)
    {
        $this->idDenuncia = $idDenuncia;
        $this->idRecurso = $idRecurso;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->getContrarrazaoRecursoDenunciaBO()->enviarEmailEncerrarContrarrazao($this->idDenuncia, $this->idRecurso);
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
