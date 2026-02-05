<?php

namespace App\Jobs;

use App\Business\ChapaEleicaoBO;
use App\Business\PedidoSubstituicaoChapaBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class EnviarPedidoSubstituicaoChapaCadastradaJob extends Job
{

    /**
     * @var integer
     */
    private $idPedidoSubstituicao;

    /**
     * Criação de uma nova instância de EnviarPedidoSubstituicaoChapaCadastradaJob.
     *
     * @param $idPedidoSubstituicao
     */
    public function __construct($idPedidoSubstituicao)
    {
        $this->idPedidoSubstituicao = $idPedidoSubstituicao;
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
        $this->getPedidoSubstituicaoChapaBO()->enviarEmailsPedidoSubstituicaoIncluido($this->idPedidoSubstituicao);
    }

    /**
     * Retorna uma instância de PedidoSubstituicaoChapaBO
     *
     * @return PedidoSubstituicaoChapaBO
     */
    private function getPedidoSubstituicaoChapaBO()
    {
        return app()->make(PedidoSubstituicaoChapaBO::class);
    }
}
