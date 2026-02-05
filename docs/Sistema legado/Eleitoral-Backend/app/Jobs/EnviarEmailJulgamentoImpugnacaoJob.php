<?php

namespace App\Jobs;

use App\Business\DefesaImpugnacaoBO;
use App\Business\JulgamentoImpugnacaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use App\Business\PedidoImpugnacaoBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailJulgamentoImpugnacaoJob extends Job
{

    /**
     * @var integer
     */
    private $idJulgamentoImpugnacao;

    /**
     * Criação de uma nova instância de EnviarEmailJulgamentoImpugnacaoJob.
     *
     * @param $idJulgamentoImpugnacao
     */
    public function __construct($idJulgamentoImpugnacao)
    {
        $this->idJulgamentoImpugnacao = $idJulgamentoImpugnacao;
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
        $this->getJulgamentoImpugnacaoBO()->enviarEmailCadastroJulgamento($this->idJulgamentoImpugnacao);
    }

    /**
     * Retorna uma instância de JulgamentoImpugnacaoBO
     *
     * @return JulgamentoImpugnacaoBO
     */
    private function getJulgamentoImpugnacaoBO()
    {
        return app()->make(JulgamentoImpugnacaoBO::class);
    }
}
