<?php


namespace App\Jobs;


use App\Business\AlegacaoImpugnacaoResultadoBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailAlegacaoImpugnacaoResultadoJob extends Job
{
    /**
     * @var integer
     */
    private $idAlegacaoImpugnacaoResultado;

    /**
     * @var integer
     */
    private $idCalendario;

    /**
     * Criação de uma nova instância de EnviarEmailAlegacaoImpugnacaoResultadoJob.
     *
     * @param $idAlegacaoImpugnacaoResultado
     * @param $idCalendario
     */
    public function __construct($idAlegacaoImpugnacaoResultado, $idCalendario)
    {
        $this->idAlegacaoImpugnacaoResultado = $idAlegacaoImpugnacaoResultado;
        $this->idCalendario = $idCalendario;
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
        $this->getAlegacaoImpugnacaoResultadoBO()->enviarEmailCadastro($this->idAlegacaoImpugnacaoResultado, $this->idCalendario);
    }

    /**
     * Retorna uma instância de AlegacaoImpugnacaoResultadoBO
     *
     * @return AlegacaoImpugnacaoResultadoBO
     */
    private function getAlegacaoImpugnacaoResultadoBO()
    {
        return app()->make(AlegacaoImpugnacaoResultadoBO::class);
    }
}