<?php

namespace App\Jobs;

use App\Business\PedidoImpugnacaoBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailPedidoImpugnacaoJob extends Job
{

    /**
     * @var integer
     */
    private $idPedidoImpugnacao;

    /**
     * Criação de uma nova instância de EnviarEmailPedidoImpugnacaoJob.
     *
     * @param $idPedidoImpugnacao
     */
    public function __construct($idPedidoImpugnacao)
    {
        $this->idPedidoImpugnacao = $idPedidoImpugnacao;
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
        $this->getPedidoImpugnacaoBO()->enviarEmailsPedidoImpugnacaoIncluido($this->idPedidoImpugnacao);
    }

    /**
     * Retorna uma instância de PedidoImpugnacaoBO
     *
     * @return PedidoImpugnacaoBO
     */
    private function getPedidoImpugnacaoBO()
    {
        return app()->make(PedidoImpugnacaoBO::class);
    }
}
