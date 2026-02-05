<?php

namespace App\Jobs;

use App\Business\DenunciaBO;

class EnviarEmailInserirNovoRelatorJob extends Job
{

    /**
     * @var integer
     */
    private $idEncaminhamentoDenuncia;

    /**
     * Criação de uma nova instância de EnviarEmailInserirNovoRelatorJob.
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
        $this->getDenunciaBO()->enviarEmailInserirNovoRelator($this->idEncaminhamentoDenuncia);
    }

    /**
     * Retorna uma instância de DenunciaBO
     *
     * @return DenunciaBO
     */
    private function getDenunciaBO()
    {
        return app()->make(DenunciaBO::class);
    }
}
