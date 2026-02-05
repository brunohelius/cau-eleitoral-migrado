<?php

namespace App\Jobs;

use App\Business\MembroChapaBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailPendenciasMembroChapaJob extends Job
{
    /**
     * @var integer
     */
    private $idMembroChapa;

    /**
     * Criação de uma nova instância de EnviarEmailPendenciasMembroChapaJob.
     *
     * @param $idMembroChapa
     */
    public function __construct($idMembroChapa)
    {
        $this->idMembroChapa = $idMembroChapa;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws NegocioException
     * @throws NonUniqueResultException
     */
    public function handle()
    {
        $this->getMembroChapaBO()->prepararParamsEmailPendenciasEfetivarEnvio($this->idMembroChapa);
    }

    /**
     * Retorna uma instância de MembroChapaBO
     *
     * @return MembroChapaBO
     */
    private function getMembroChapaBO()
    {
        return app()->make(MembroChapaBO::class);
    }
}
