<?php

namespace App\Jobs;

use App\Business\DenunciaBO;
use App\Config\Constants;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class EnviarEmailDenunciaAdmitirInadmitirJob extends Job
{
    /**
     * @var integer
     */
    private $idDenuncia;

    /**
     * @var integer
     */
    private $idTipoDenuncia;

    /**
     * @var integer
     */
    private $tipoDenuncia;

    /**
     * Criação de uma nova instância de EnviarEmailDenunciaAdmitirInadmitirJob.
     *
     * EnviarEmailDenunciaAdmitirInadmitirJob constructor.
     * @param $idDenuncia
     * @param $idTipoDenuncia
     * @param $tipoDenuncia
     */
    public function __construct($idDenuncia, $idTipoDenuncia, $tipoDenuncia = Constants::STATUS_DENUNCIA_ADMITIDA)
    {
        $this->idDenuncia = $idDenuncia;
        $this->idTipoDenuncia = $idTipoDenuncia;
        $this->tipoDenuncia = $tipoDenuncia;
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
        $this->getDenunciaBO()->enviarEmailAdmitirEInadmitir($this->idDenuncia, $this->idTipoDenuncia, $this->tipoDenuncia);
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