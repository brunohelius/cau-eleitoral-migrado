<?php

namespace App\Jobs;

use App\Business\DefesaImpugnacaoBO;
use App\Business\PedidoImpugnacaoBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailDefesaImpugnacaoJob extends Job
{

    /**
     * @var integer
     */
    private $idDefesaImpugnacao;

    /**
     * Criação de uma nova instância de EnviarEmailDefesaImpugnacaoJob.
     *
     * @param $idDefesaImpugnacao
     */
    public function __construct($idDefesaImpugnacao)
    {
        $this->idDefesaImpugnacao = $idDefesaImpugnacao;
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
        $this->getDefesaImpugnacaoBO()->enviarEmailsDefesaImpugnacaoIncluida($this->idDefesaImpugnacao);
    }

    /**
     * Retorna uma instância de DefesaImpugnacaoBO
     *
     * @return DefesaImpugnacaoBO
     */
    private function getDefesaImpugnacaoBO()
    {
        return app()->make(DefesaImpugnacaoBO::class);
    }
}
