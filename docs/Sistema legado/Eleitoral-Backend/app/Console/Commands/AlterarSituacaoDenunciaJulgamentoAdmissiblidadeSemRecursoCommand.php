<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 30/05/20
 * Time: 12:29
 */

namespace App\Console\Commands;

use App\Business\RecursoJulgamentoAdmissibilidadeBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlterarSituacaoDenunciaJulgamentoAdmissiblidadeSemRecursoCommand extends Command
{

    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'julgamentoaAdmissibilidadeSemRecurso:alterarSituacaoDenuncia';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Realiza a substituição de membros chapa após o fim do julgamento do recurso (2ª instância)';

    /**
     * Cria uma nova instância de SubstituirMembrosChapaFimJulgamentoRecursoCommand
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws Exception
     */
    public function handle()
    {
        Log::info("julgamentoaAdmissibilidadeSemRecurso:alterarSituacaoDenuncia");
        $this->getRecursoJulgamentoAdmissibilidadeBO()->rotinaValidarPrazoRecursoJulgamento();
    }

    /**
     * Retorna uma instância de RecursoJulgamentoAdmissibilidadeBO
     *
     * @return RecursoJulgamentoAdmissibilidadeBO
     */
    private function getRecursoJulgamentoAdmissibilidadeBO()
    {
        return app()->make(RecursoJulgamentoAdmissibilidadeBO::class);
    }
}