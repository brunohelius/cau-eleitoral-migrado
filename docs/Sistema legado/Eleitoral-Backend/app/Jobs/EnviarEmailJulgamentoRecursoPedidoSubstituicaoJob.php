<?php

namespace App\Jobs;

use App\Business\DefesaImpugnacaoBO;
use App\Business\JulgamentoFinalBO;
use App\Business\JulgamentoImpugnacaoBO;
use App\Business\JulgamentoRecursoPedidoSubstituicaoBO;
use App\Business\JulgamentoSegundaInstanciaSubstituicaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use App\Business\PedidoImpugnacaoBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailJulgamentoRecursoPedidoSubstituicaoJob extends Job
{

    /**
     * @var integer
     */
    private $idJulgamento;

    /**
     * @var integer
     */
    private $idChapaEleicao;

    /**
     * @var boolean
     */
    private $isIES;

    /**
     * @var integer
     */
    private $idCauUf;

    /**
     * Criação de uma nova instância de EnviarEmailJulgamentoSegundaIntanciaSubstituicaoJob.
     *
     * @param integer $idJulgamento
     * @param integer $idChapaEleicao
     * @param boolean $isIES
     * @param integer $idCauUf
     */
    public function __construct($idJulgamento, $idChapaEleicao, $isIES, $idCauUf)
    {
        $this->isIES = $isIES;
        $this->idCauUf = $idCauUf;
        $this->idJulgamento = $idJulgamento;
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
        $this->getJulgamentoRecursoPedidoSubstituicaoBO()->enviarEmailCadastroJulgamento(
            $this->idJulgamento, $this->idChapaEleicao, $this->isIES, $this->idCauUf
        );
    }

    /**
     * Retorna uma instância de JulgamentoRecursoPedidoSubstituicaoBO
     *
     * @return JulgamentoRecursoPedidoSubstituicaoBO
     */
    private function getJulgamentoRecursoPedidoSubstituicaoBO()
    {
        return app()->make(JulgamentoRecursoPedidoSubstituicaoBO::class);
    }
}
