<?php


namespace App\Console\Commands;


use App\Business\JulgamentoSubstituicaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarFimPeriodoJulgamentoSubstituicaoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'julgamentoSubstituicao:alertarFimPeriodo';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail informando o fim do período atividade de julgamento de substituição';

    /**
     * Cria uma nova instância de AlertarFimPeriodoJulgamentoSubstituicaoCommand
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
        Log::info("julgamentoSubstituicao:alertarFimPeriodo");
        $this->getJulgamentoSubstituicaoBO()->enviarEmailFimPeriodoJulgamento();
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
