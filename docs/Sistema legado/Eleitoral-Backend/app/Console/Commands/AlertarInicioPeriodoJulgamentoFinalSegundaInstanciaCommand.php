<?php


namespace App\Console\Commands;


use App\Business\JulgamentoFinalBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarInicioPeriodoJulgamentoFinalSegundaInstanciaCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'julgamentoFinalSegundaInstancia:alertarInicioPeriodo';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail informando o início do período atividade de julgamento final';

    /**
     * Cria uma nova instância de AlertarInicioPeriodoJulgamentoFinalSegundaInstanciaCommand
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
        Log::info("julgamentoFinalSegundaInstancia:alertarInicioPeriodo");
        $this->getJulgamentoFinalBO()->enviarEmailInicioJulgamentoSegundaInstancia();
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
