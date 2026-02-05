<?php


namespace App\Jobs;


use App\Business\RecursoSegundoJulgamentoSubstituicaoBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailRecursoSegundoJulgamentoSubstituicaoJob extends Job
{
    /**
     * @var integer
     */
    private $idRecursoSegundoJulgamentoSubstituicao;

    /**
     * Criação de uma nova instância de EnviarEmailRecursoSegundoJulgamentoSubstituicaoJob.
     *
     * @param $idRecursoSegundoJulgamentoSubstituicao
     */
    public function __construct($idRecursoSegundoJulgamentoSubstituicao)
    {
        $this->idRecursoSegundoJulgamentoSubstituicao = $idRecursoSegundoJulgamentoSubstituicao;
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
        $this->getRecursoSegundoJulgamentoSubstituicaoBO()->
            enviarEmailCadastroRecursoJulgamentoSubstituicao($this->idRecursoSegundoJulgamentoSubstituicao);
    }

    /**
     * Retorna uma instância de JulgamentoFinalBO
     *
     * @return RecursoSegundoJulgamentoSubstituicaoBO
     */
    private function getRecursoSegundoJulgamentoSubstituicaoBO()
    {
        return app()->make(RecursoSegundoJulgamentoSubstituicaoBO::class);
    }
}