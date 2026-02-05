<?php

namespace App\Jobs;

use App\Business\DenunciaBO;
use App\Business\DenunciaDefesaBO;
use App\Config\Constants;
use App\Entities\DenunciaDefesa;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class EnviarEmailDenunciaDefesaJob extends Job
{
    /**
     * @var integer
     */
    private $idDenuncia;

    /**
     * @var boolean
     */
    private $isJobPrazo;

    /**
     * Criação de uma nova instância de EnviarEmailDenunciaDefesaJob.
     *
     * EnviarEmailDenunciaAdmitirInadmitirJob constructor.
     * @param $idDenuncia
     * @param bool $isJobPrazo
     */
    public function __construct($idDenuncia, $isJobPrazo = false)
    {
        $this->idDenuncia = $idDenuncia;
        $this->isJobPrazo = $isJobPrazo;
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
        $this->getDenunciaDefesaBO()->enviarEmailDenunciaDefesa($this->idDenuncia, $this->isJobPrazo);
    }

    /**
     * Retorna uma instância de DenunciaBO
     *
     * @return DenunciaDefesaBO
     */
    private function getDenunciaDefesaBO()
    {
        return app()->make(DenunciaDefesaBO::class);
    }
}