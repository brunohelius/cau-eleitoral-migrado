<?php


namespace App\Console\Commands;


use App\Business\JulgamentoFinalBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarFimPeriodoJulgamentoFinalSegundaInstanciaCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'julgamentoFinalSegundaInstancia:alertarFimPeriodo';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail informando o fim do período atividade de julgamento final';

    /**
     * Cria uma nova instância de AlertarFimPeriodoJulgamentoFinalSegundaInstanciaCommand
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
        Log::info("julgamentoFinalSegundaInstancia:alertarFimPeriodo");
        $this->getJulgamentoFinalBO()->enviarEmailFimPeriodoJulgamentoFinalSegundaInstancia();
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
