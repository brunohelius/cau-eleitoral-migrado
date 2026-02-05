<?php

namespace App\Jobs;

use App\Business\DenunciaAudienciaInstrucaoBO;
use App\Config\Constants;
use App\Entities\DenunciaDefesa;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class EnviarEmailDenunciaAudienciaInstrucaoJob extends Job
{
    /**
     * @var integer
     */
    private $idDenuncia;

    /**
     * @var integer
     */
    private $idAudienciaInstrucao;

    /**
     * Criação de uma nova instância de EnviarEmailDenunciaAudienciaInstrucaoJob.
     *
     * EnviarEmailDenunciaAudienciaInstrucaoJob constructor.
     * @param $idDenuncia
     * @param $idAudienciaInstrucao
     */
    public function __construct($idDenuncia, $idAudienciaInstrucao)
    {
        $this->idDenuncia = $idDenuncia;
        $this->idAudienciaInstrucao = $idAudienciaInstrucao;
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
        $this->getDenunciaAudienciaInstrucaoBO()->enviarEmailDenunciaAudienciaInstrucao(
            $this->idDenuncia,
            $this->idAudienciaInstrucao
        );
    }

    /**
     * Retorna uma instância de DenunciaAudienciaInstrucaoBO
     *
     * @return DenunciaAudienciaInstrucaoBO
     */
    private function getDenunciaAudienciaInstrucaoBO()
    {
        return app()->make(DenunciaAudienciaInstrucaoBO::class);
    }
}