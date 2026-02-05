<?php


namespace App\Console\Commands;


use App\Business\JulgamentoFinalBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarFimPeriodoJulgamentoFinalCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'julgamentoFinal:alertarFimPeriodo';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail informando o fim do período atividade de julgamento final';

    /**
     * Cria uma nova instância de AlertarFimPeriodoJulgamentoFinalCommand
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
        Log::info("julgamentoFinal:alertarFimPeriodo");
        $this->getJulgamentoFinalBO()->enviarEmailFimPeriodoJulgamentoFinal();
    }

    /**
     * Retorna uma instância de JulgamentoFinalBO
     *
     * @return JulgamentoFinalBO
     */
    private function getJulgamentoFinalBO()
    {
        return app()->make(JulgamentoFinalBO::class);
    }
}
