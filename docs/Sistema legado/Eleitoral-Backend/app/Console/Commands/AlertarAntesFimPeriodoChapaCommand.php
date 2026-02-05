<?php


namespace App\Console\Commands;


use App\Business\ChapaEleicaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarAntesFimPeriodoChapaCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'chapas:enviarEmailCincoDiasAntesFim';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Envia e-mail alterta cinco dias antes do fim da atividade secundária 2.1';

    /**
     * Cria uma nova instância de AlertarAntesFimPeriodoChapaCommand
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
        Log::info('chapas:enviarEmailCincoDiasAntesFim');
        $this->getChapaEleicaoBO()->enviarEmailCincoDiasAntesFimAtividadeSecundaria();
    }

    /**
     * Retorna uma instância de ChapaEleicaoBO
     *
     * @return ChapaEleicaoBO
     */
    private function getChapaEleicaoBO()
    {
        return app()->make(ChapaEleicaoBO::class);
    }
}
