<?php


namespace App\Console\Commands;


use App\Business\JulgamentoFinalBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarCadastroJulgamentoFinalFimPeriodoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'julgamentoFinal:alertarCadastroFimPeriodo';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail para os responsáveis alertando que a chapa foi julgado em 1ª instância.';

    /**
     * Cria uma nova instância de AlertarCadastroJulgamentoFinalFimPeriodoCommando
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
        Log::info("ulgamentoFinal:alertarCadastroFimPeriodo");
        $this->getJulgamentoFinalBO()->enviarEmailCadastroJulgamentoFimPeriodo();
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
