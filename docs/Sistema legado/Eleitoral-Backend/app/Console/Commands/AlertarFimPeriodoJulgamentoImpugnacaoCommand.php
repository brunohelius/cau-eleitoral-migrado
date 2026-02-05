<?php


namespace App\Console\Commands;


use App\Business\JulgamentoImpugnacaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarFimPeriodoJulgamentoImpugnacaoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'julgamentoImpugnacao:alertarFimPeriodo';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail informando o fim do período atividade de julgamento de impugnação';

    /**
     * Cria uma nova instância de AlertarFimPeriodoJulgamentoImpugnacaoCommand
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
        Log::info("julgamentoImpugnacao:alertarFimPeriodo");
        $this->getJulgamentoImpugnacaoBO()->enviarEmailFimPeriodoJulgamento();
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
