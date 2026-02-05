<?php

namespace App\Jobs;

use App\Business\DenunciaAudienciaInstrucaoBO;
use App\Config\Constants;
use App\Entities\DenunciaDefesa;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class EnviarEmailDenunciaAudienciaInstrucaoPendenteJob extends Job
{
    /**
     * @var integer
     */
    private $idDenuncia;

    /**
     * @var integer
     */
    private $idEncaminhamento;

    /**
     * Criação de uma nova instância de EnviarEmailDenunciaAudienciaInstrucaoPendenteJob.
     *
     * EnviarEmailDenunciaAudienciaInstrucaoPendenteJob constructor.
     * @param $idDenuncia
     * @param $idEncaminhamento
     */
    public function __construct($idDenuncia, $idEncaminhamento)
    {
        $this->idDenuncia = $idDenuncia;
        $this->idEncaminhamento = $idEncaminhamento;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws NonUniqueResultException
     */
    public function handle()
    {
        $this->getDenunciaAudienciaInstrucaoBO()->enviarEmailDenunciaAudienciaInstrucaoPendente(
            $this->idDenuncia,
            $this->idEncaminhamento
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