<?php

namespace App\Jobs;

use App\Business\ChapaEleicaoBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailChapaConfirmadaJob extends Job
{

    /**
     * @var integer
     */
    private $idChapaEleicao;

    /**
     * Criação de uma nova instância de EnviarEmailChapaConfirmadaJob.
     *
     * @param $idChapaEleicao
     */
    public function __construct($idChapaEleicao)
    {
        $this->idChapaEleicao = $idChapaEleicao;
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
        $this->getChapaEleicaoBO()->enviarEmailsChapaConfirmada($this->idChapaEleicao);
    }

    /**
     * Retorna uma instância de ChapaEleicaoBO
     *
     * @return ChapaEleicaoBO
     */
    private function getChapaEleicaoBO()
    {
        return app()->make(ChapaEleicaoBO::class);
    }
}
