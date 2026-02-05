<?php


namespace App\Console\Commands;


use App\Business\DefesaImpugnacaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarImpugnanteFimPeriodoDefesaImpugnacaoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'defesaImpugnacao:alertarFimPeriodoCadastro';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail para impugnante informando que possuí defesa cadastrada no fim do período do cadastro de defesa';

    /**
     * Cria uma nova instância de AlertarImpugnanteFimPeriodoDefesaImpugnacaoCommand
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
        Log::info("defesaImpugnacao:alertarFimPeriodoCadastro");
        $this->getDefesaImpugnacaoBO()->enviarEmailFimPeriodoCadastro();
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
