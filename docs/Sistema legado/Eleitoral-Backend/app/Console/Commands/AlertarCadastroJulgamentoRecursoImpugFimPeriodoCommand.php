<?php


namespace App\Console\Commands;


use App\Business\JulgamentoRecursoImpugnacaoBO;
use App\Business\JulgamentoRecursoSubstituicaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarCadastroJulgamentoRecursoImpugFimPeriodoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'julgamentoRecursoImpugnacao:alertarCadastroFimPeriodo';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail para os responsáveis alertando que o pedido de impugnação foi julgado em 2ª instância.';

    /**
     * Cria uma nova instância de AlertarCadastroJulgamentoRecursoImpugFimPeriodoCommand
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
        Log::info("julgamentoRecursoImpugnacao:alertarCadastroFimPeriodo");
        $this->getJulgamentoRecursoImpugnacaoBO()->enviarEmailCadastroJulgamentoFimPeriodo();
    }

    /**
     * Retorna uma instância de JulgamentoRecursoImpugnacaoBO
     *
     * @return JulgamentoRecursoImpugnacaoBO
     */
    private function getJulgamentoRecursoImpugnacaoBO()
    {
        return app()->make(JulgamentoRecursoImpugnacaoBO::class);
    }
}
