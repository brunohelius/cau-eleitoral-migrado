<?php

namespace App\Jobs;

use App\Business\ContrarrazaoRecursoImpugnacaoBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailContrarrazaoJob extends Job
{
    /**
     * @var integer
     */
    private $idContrarrazaoRecursoImpugnacao;

    /**
     * @var integer
     */
    private $idCalendario;

    /**
     * Criação de uma nova instância de EnviarEmailContrarrazaoJob.
     *
     * @param $idContrarrazaoRecursoImpugnacao
     * @param $idCalendario
     */
    public function __construct($idContrarrazaoRecursoImpugnacao, $idCalendario)
    {
        $this->idContrarrazaoRecursoImpugnacao = $idContrarrazaoRecursoImpugnacao;
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
        $this->getContrarrazaoRecursoImpugnacaoBO()->enviarEmailCadastro($this->idContrarrazaoRecursoImpugnacao, $this->idCalendario);
    }

    /**
     * Retorna uma instância de ContrarrazaoRecursoImpugnacaoBO
     *
     * @return ContrarrazaoRecursoImpugnacaoBO
     */
    private function getContrarrazaoRecursoImpugnacaoBO()
    {
        return app()->make(ContrarrazaoRecursoImpugnacaoBO::class);
    }
}
