<?php

namespace App\Jobs;

use App\Business\DenunciaAdmitidaBO;
use App\Business\DenunciaBO;
use App\Config\Constants;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class EnviarEmailDenunciaAdmitidaInserirRelatorJob extends Job
{
    /**
     * @var integer
     */
    private $idDenunciaAdmitida;

    /**
     * Criação de uma nova instância de EnviarEmailDenunciaAdmitidaInserirRelatorJob.
     *
     * @param $idDenunciaAdmitida
     */
    public function __construct($idDenunciaAdmitida)
    {
        $this->idDenunciaAdmitida = $idDenunciaAdmitida;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->getDenunciaAdmitidaBO()->gerarEEnviarEmail($this->idDenunciaAdmitida);
    }

    /**
     * Retorna uma instância de DenunciaAdmitidaBO
     *
     * @return DenunciaAdmitidaBO
     */
    private function getDenunciaAdmitidaBO()
    {
        return app()->make(DenunciaAdmitidaBO::class);
    }
}