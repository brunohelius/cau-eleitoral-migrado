<?php


namespace App\Console\Commands;


use App\Business\JulgamentoRecursoSubstituicaoBO;
use App\Business\JulgamentoSubstituicaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarCadastroJulgamentoRecursoSubstFimPeriodoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'julgamentoRecursoSubstituicao:alertarCadastroFimPeriodo';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail para os responsáveis e para os membros (substitutos e substituidos) alertando que o pedido de substituição foi julgado em 2ª instância.';

    /**
     * Cria uma nova instância de AlertarCadastroJulgamentoRecursoSubstFimPeriodoCommand
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
        Log::info("julgamentoRecursoSubstituicao:alertarCadastroFimPeriodo");
        $this->getJulgamentoRecursoSubstituicaoBO()->enviarEmailCadastroJulgamentoFimPeriodo();
    }

    /**
     * Retorna uma instância de JulgamentoRecursoSubstituicaoBO
     *
     * @return JulgamentoRecursoSubstituicaoBO
     */
    private function getJulgamentoRecursoSubstituicaoBO()
    {
        return app()->make(JulgamentoRecursoSubstituicaoBO::class);
    }
}
