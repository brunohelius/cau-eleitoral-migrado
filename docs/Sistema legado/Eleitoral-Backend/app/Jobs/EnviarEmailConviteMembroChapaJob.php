<?php

namespace App\Jobs;

use App\Business\ChapaEleicaoBO;
use App\Business\MembroChapaBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailConviteMembroChapaJob extends Job
{

    /**
     * @var integer
     */
    private $idChapaEleicao;

    /**
     * @var integer
     */
    private $idTipoEmail;

    /**
     * Criação de uma nova instância de EnviarEmailConviteChapaChapaJob.
     *
     * @param $idChapaEleicao
     * @param $idTipoEmail
     */
    public function __construct($idChapaEleicao, $idTipoEmail)
    {
        $this->idChapaEleicao = $idChapaEleicao;
        $this->idTipoEmail = $idTipoEmail;
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
        $this->getMembroChapaBO()->enviarEmailResponsaveisChapaConvite($this->idChapaEleicao, $this->idTipoEmail);
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
