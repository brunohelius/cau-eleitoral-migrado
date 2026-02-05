<?php

namespace App\Jobs;

use App\Business\DenunciaBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class EnviarEmailDenunciaJob extends Job
{

    /**
     * @var integer
     */
    private $idDenuncia;

    /**
     * Criação de uma nova instância de EnviarEmailDenunciaJob.
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
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function handle()
    {
        $this->getDenunciaBO()->enviarEmailResponsaveis($this->idDenuncia);
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