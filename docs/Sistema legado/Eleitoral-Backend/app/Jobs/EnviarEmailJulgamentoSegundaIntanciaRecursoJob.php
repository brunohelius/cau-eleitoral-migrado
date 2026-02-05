<?php

namespace App\Jobs;

use App\Business\DefesaImpugnacaoBO;
use App\Business\JulgamentoFinalBO;
use App\Business\JulgamentoImpugnacaoBO;
use App\Business\JulgamentoSegundaInstanciaRecursoBO;
use App\Business\JulgamentoSegundaInstanciaSubstituicaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use App\Business\PedidoImpugnacaoBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailJulgamentoSegundaIntanciaRecursoJob extends Job
{

    /**
     * @var integer
     */
    private $idJulgamentoFinal;

    /**
     * Criação de uma nova instância de EnviarEmailJulgamentoSegundaIntanciaRecursoJob.
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
        $this->getJulgamentoSegundaInstanciaRecursoBO()->enviarEmailCadastroJulgamento($this->idJulgamentoFinal);
    }

    /**
     * Retorna uma instância de JulgamentoSegundaInstanciaRecursoBO
     *
     * @return JulgamentoSegundaInstanciaRecursoBO
     */
    private function getJulgamentoSegundaInstanciaRecursoBO()
    {
        return app()->make(JulgamentoSegundaInstanciaRecursoBO::class);
    }
}
