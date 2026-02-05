<?php

namespace App\Jobs;

use App\Business\DefesaImpugnacaoBO;
use App\Business\JulgamentoFinalBO;
use App\Business\JulgamentoImpugnacaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use App\Business\PedidoImpugnacaoBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailAlteradoJulgamentoFinalJob extends Job
{

    /**
     * @var integer
     */
    private $idJulgamentoFinal;

    /**
     * Criação de uma nova instância de EnviarEmailJulgamentoFinalJob.
     *
     * @param $idJulgamentoFinal
     */
    public function __construct($idJulgamentoFinal)
    {
        $this->idJulgamentoFinal = $idJulgamentoFinal;
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
        $this->getJulgamentoFinalBO()->enviarEmailAlteracaoJulgamento($this->idJulgamentoFinal);
    }

    /**
     * Retorna uma instância de JulgamentoFinalBO
     *
     * @return JulgamentoFinalBO
     */
    private function getJulgamentoFinalBO()
    {
        return app()->make(JulgamentoFinalBO::class);
    }
}
