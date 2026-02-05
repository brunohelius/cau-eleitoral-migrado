<?php


namespace App\Console\Commands;


use App\Business\JulgamentoImpugnacaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarInicioPeriodoJulgamentoImpugnacaoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'julgamentoImpugnacao:alertarInicioPeriodo';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail informando o início do período atividade de julgamento de impugnação';

    /**
     * Cria uma nova instância de AlertarInicioPeriodoJulgamentoImpugnacaoCommand
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
        Log::info("julgamentoImpugnacao:alertarInicioPeriodo");
        $this->getJulgamentoImpugnacaoBO()->enviarEmailIncioPeriodoJulgamento();
    }

    /**
     * Retorna uma instância de JulgamentoImpugnacaoBO
     *
     * @return JulgamentoImpugnacaoBO
     */
    private function getJulgamentoImpugnacaoBO()
    {
        return app()->make(JulgamentoImpugnacaoBO::class);
    }
}
