<?php

namespace App\Jobs;

use App\Business\RecursoJulgamentoFinalBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailRecursoJulgamentoFinalJob extends Job
{

    /**
     * @var integer
     */
    private $idRecursoJulgamentoFinal;

    /**
     * Criação de uma nova instância de EnviarEmailRecursoJulgamentoFinalJob.
     *
     * @param $idRecursoJulgamentoFinal
     */
    public function __construct($idRecursoJulgamentoFinal)
    {
        $this->idRecursoJulgamentoFinal = $idRecursoJulgamentoFinal;
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
        $this->getRecursoJulgamentoFinalBO()->enviarEmailCadastroRecursoJulgamento($this->idRecursoJulgamentoFinal);
    }

    /**
     * Retorna uma instância de JulgamentoFinalBO
     *
     * @return RecursoJulgamentoFinalBO
     */
    private function getRecursoJulgamentoFinalBO()
    {
        return app()->make(RecursoJulgamentoFinalBO::class);
    }
}
