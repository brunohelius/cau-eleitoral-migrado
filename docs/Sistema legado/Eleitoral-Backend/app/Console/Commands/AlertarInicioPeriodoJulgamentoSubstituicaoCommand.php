<?php


namespace App\Console\Commands;


use App\Business\JulgamentoSubstituicaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarInicioPeriodoJulgamentoSubstituicaoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'julgamentoSubstituicao:alertarInicioPeriodo';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail informando o início do período atividade de julgamento de substituição';

    /**
     * Cria uma nova instância de AlertarInicioPeriodoJulgamentoSubstituicaoCommand
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
        Log::info("julgamentoSubstituicao:alertarInicioPeriodo");
        $this->getJulgamentoSubstituicaoBO()->enviarEmailIncioPeriodoJulgamento();
    }

    /**
     * Retorna uma instância de JulgamentoSubstituicaoBO
     *
     * @return JulgamentoSubstituicaoBO
     */
    private function getJulgamentoSubstituicaoBO()
    {
        return app()->make(JulgamentoSubstituicaoBO::class);
    }
}
