<?php

namespace App\Jobs;

use App\Business\DenunciaBO;
use App\Business\DenunciaDefesaBO;
use App\Business\DenunciaProvasBO;
use App\Config\Constants;
use App\Entities\DenunciaDefesa;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class EnviarEmailDenunciaProvasJob extends Job
{
    /**
     * @var integer
     */
    private $idDenuncia;

    /**
     * @var integer
     */
    private $idDenunciaProvas;

    /**
     * Criação de uma nova instância de EnviarEmailDenunciaDefesaJob.
     *
     * EnviarEmailDenunciaAdmitirInadmitirJob constructor.
     * @param $idDenuncia
     * @param $idDenunciaProvas
     */
    public function __construct($idDenuncia, $idDenunciaProvas)
    {
        $this->idDenuncia = $idDenuncia;
        $this->idDenunciaProvas = $idDenunciaProvas;
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
        $this->getDenunciaProvasBO()->enviarEmailDenunciaProvas($this->idDenuncia, $this->idDenunciaProvas);
    }

    /**
     * Retorna uma instância de DenunciaBO
     *
     * @return DenunciaProvasBO
     */
    private function getDenunciaProvasBO()
    {
        return app()->make(DenunciaProvasBO::class);
    }
}