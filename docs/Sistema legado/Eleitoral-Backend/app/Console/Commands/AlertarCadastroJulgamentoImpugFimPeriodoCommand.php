<?php


namespace App\Console\Commands;


use App\Business\JulgamentoImpugnacaoBO;
use App\Business\JulgamentoRecursoImpugnacaoBO;
use App\Business\JulgamentoRecursoSubstituicaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarCadastroJulgamentoImpugFimPeriodoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'julgamentoImpugnacao:alertarCadastroFimPeriodo';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail para os responsáveis alertando que o pedido de impugnação foi julgado em 1ª instância.';

    /**
     * Cria uma nova instância de AlertarCadastroJulgamentoImpugFimPeriodoCommand
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
        Log::info("julgamentoImpugnacao:alertarCadastroFimPeriodo");
        $this->getJulgamentoImpugnacaoBO()->enviarEmailCadastroJulgamentoFimPeriodo();
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
