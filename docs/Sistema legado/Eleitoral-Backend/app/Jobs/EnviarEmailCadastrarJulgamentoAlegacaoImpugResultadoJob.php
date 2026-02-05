<?php

namespace App\Jobs;

use App\Business\DefesaImpugnacaoBO;
use App\Business\ImpugnacaoResultadoBO;
use App\Business\JulgamentoAlegacaoImpugResultadoBO;
use App\Business\JulgamentoFinalBO;
use App\Business\JulgamentoImpugnacaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use App\Business\PedidoImpugnacaoBO;
use App\Entities\JulgamentoAlegacaoImpugResultado;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailCadastrarJulgamentoAlegacaoImpugResultadoJob extends Job
{
    /**
     * @var int
     */
    private $idJulgamentoAlegacaoImpugResultado;

    /**
     * Criação de uma nova instância de EnviarEmailCadastrolmpugnacaoResultadoJob.
     *
     * @param $idJulgamentoAlegacaoImpugResultado
     */
    public function __construct($idJulgamentoAlegacaoImpugResultado)
    {
        $this->idJulgamentoAlegacaoImpugResultado = $idJulgamentoAlegacaoImpugResultado;
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
        $this->getJulgamentoAlegacaoImpugResultadoBO()->enviarEmailCadastrarJulgamentoAlegacaoImpugResultado($this->idJulgamentoAlegacaoImpugResultado);
    }

    /**
     * Retorna instância de JulgamentoAlegacaoImpugResultadoBO.
     *
     * @return JulgamentoAlegacaoImpugResultadoBO
     */
    private function getJulgamentoAlegacaoImpugResultadoBO(): JulgamentoAlegacaoImpugResultadoBO
    {
        return app()->make(JulgamentoAlegacaoImpugResultadoBO::class);
    }
}