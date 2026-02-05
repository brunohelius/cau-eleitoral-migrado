<?php


namespace App\Console\Commands;


use App\Business\DefesaImpugnacaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarInicioPeriodoCadastroDefesaImpugnacaoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'defesaImpugnacao:alertarInicioPeriodoCadastro';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail informando o início do período de cadastro de defesa de impugnação';

    /**
     * Cria uma nova instância de AlertarInicioPeriodoCadastroDefesaImpugnacaoCommand
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
        Log::info("defesaImpugnacao:alertarInicioPeriodoCadastro");
        $this->getDefesaImpugnacaoBO()->enviarEmailIncioPeriodoCadastro();
    }

    /**
     * Retorna uma instância de DefesaImpugnacaoBO
     *
     * @return DefesaImpugnacaoBO
     */
    private function getDefesaImpugnacaoBO()
    {
        return app()->make(DefesaImpugnacaoBO::class);
    }
}
