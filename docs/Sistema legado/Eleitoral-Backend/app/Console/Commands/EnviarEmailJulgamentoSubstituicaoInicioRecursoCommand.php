<?php


namespace App\Console\Commands;


use App\Business\JulgamentoSubstituicaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnviarEmailJulgamentoSubstituicaoInicioRecursoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'julgamentoSubstituicao:alertarNoInicioRecurso';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail para os responsáveis e para os membros (substitutos e substituidos) alertando que o pedido de substituição foi julgado.';

    /**
     * Cria uma nova instância de EnviarEmailJulgamentoSubstituicaoInicioRecursoCommand
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
        Log::info("julgamentoSubstituicao:alertarNoInicioRecurso");
        $this->getJulgamentoSubstituicaoBO()->enviarEmailJulgamentoInicioRecurso();
    }

    /**
     * Retorna uma instância de JulgamentoSubstituicaoBO
     *
     * @return JulgamentoSubstituicaoBO
     */
    private function getJulgamentoSubstituicaoBO()
    {
        return app()->make(JulgamentoSubstituicaoBO::class);
    }
}
