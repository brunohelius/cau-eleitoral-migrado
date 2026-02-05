<?php


namespace App\Console\Commands;


use App\Business\JulgamentoRecursoImpugnacaoBO;
use App\Business\JulgamentoRecursoSubstituicaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarInicioPeriodoJulgamentoRecursoImpugCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'julgamentoRecursoImpugnacao:alertarInicioPeriodo';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail informando o início do período atividade de julgamento de impugnação 2ª instância';

    /**
     * Cria uma nova instância de AlertarInicioPeriodoJulgamentoRecursoImpugCommand
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
        Log::info("julgamentoRecursoImpugnacao:alertarInicioPeriodo");
        $this->getJulgamentoRecursoImpugnacaoBO()->enviarEmailIncioPeriodoJulgamento();
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
