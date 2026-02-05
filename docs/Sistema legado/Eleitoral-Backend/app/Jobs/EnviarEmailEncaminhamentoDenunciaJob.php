<?php

namespace App\Jobs;

use App\Business\EncaminhamentoDenunciaBO;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailEncaminhamentoDenunciaJob extends Job
{
    /**
     * @var integer
     */
    private $idEncaminhamento;

    /**
     * Criação de uma nova instância de EnviarEmailEncaminhamentoDenunciaJob.
     *
     * EnviarEmailEncaminhamentoDenunciaJob constructor.
     * @param $idEncaminhamento
     */
    public function __construct($idEncaminhamento)
    {
        $this->idEncaminhamento = $idEncaminhamento;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function handle()
    {
        $this->getEncaminhamentoDenunciaBO()->enviarEmailsPorIdEncaminhamento($this->idEncaminhamento);
    }

    /**
     * Retorna uma instância de EncaminhamentoDenunciaBO
     *
     * @return EncaminhamentoDenunciaBO
     */
    private function getEncaminhamentoDenunciaBO()
    {
        return app()->make(EncaminhamentoDenunciaBO::class);
    }
}