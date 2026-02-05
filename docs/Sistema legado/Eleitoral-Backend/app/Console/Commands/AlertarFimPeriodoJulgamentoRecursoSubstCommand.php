<?php


namespace App\Console\Commands;


use App\Business\JulgamentoRecursoSubstituicaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarFimPeriodoJulgamentoRecursoSubstCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'julgamentoRecursoSubstituicao:alertarFimPeriodo';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail informando o fim do período atividade de julgamento de substituição 2ª instância';

    /**
     * Cria uma nova instância de AlertarFimPeriodoJulgamentoRecursoSubstCommand
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
        Log::info("julgamentoRecursoSubstituicao:alertarFimPeriodo");
        $this->getJulgamentoRecursoSubstituicaoBO()->enviarEmailFimPeriodoJulgamento();
    }

    /**
     * Retorna uma instância de JulgamentoRecursoSubstituicaoBO
     *
     * @return JulgamentoRecursoSubstituicaoBO
     */
    private function getJulgamentoRecursoSubstituicaoBO()
    {
        return app()->make(JulgamentoRecursoSubstituicaoBO::class);
    }
}
