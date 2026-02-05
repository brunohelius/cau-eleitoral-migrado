<?php

namespace App\Jobs;

use App\Business\DefesaImpugnacaoBO;
use App\Business\ImpugnacaoResultadoBO;
use App\Business\JulgamentoFinalBO;
use App\Business\JulgamentoImpugnacaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use App\Business\PedidoImpugnacaoBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailCadastrolmpugnacaoResultadoJob extends Job
{

    const TIPO_IMPUGNANTE = 1;
    const TIPO_IMPUGNADO = 2;
    const TIPO_COMISSAO = 3;
    const TIPO_ASSESSORES = 4;

    /**
     * @var integer
     */
    private $idImpugnacaoResultado;

    /**
     * @var integer
     */
    private $tipo;

    /**
     * Criação de uma nova instância de EnviarEmailJulgamentoFinalJob.
     *
     * @param $idImpugnacaoResultado
     */
    public function __construct($idImpugnacaoResultado, $tipo)
    {
        $this->idImpugnacaoResultado = $idImpugnacaoResultado;
        $this->tipo = $tipo;
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
        $this->getImpugnacaoResultadoBO()->enviarEmailCadastroImpugnacao($this->idImpugnacaoResultado, $this->tipo);
    }

    /**
     * Retorna uma instância de ImpugnacaoResultadoBO
     *
     * @return ImpugnacaoResultadoBO
     */
    private function getImpugnacaoResultadoBO()
    {
        return app()->make(ImpugnacaoResultadoBO::class);
    }
}
