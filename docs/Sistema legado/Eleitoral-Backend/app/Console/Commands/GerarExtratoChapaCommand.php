<?php


namespace App\Console\Commands;

use App\Business\ChapaEleicaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GerarExtratoChapaCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'chapas:gerarExtratoChapa';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Gerar extrato de chapas da eleiçao.';

    /**
     * Cria uma nova instância de GerarExtratoChapaCommand
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
        Log::info('chapas:gerarExtratoChapa');
        $this->getChapaEleicaoBO()->gerarDadosExtratoChapaJsonParaTodosCauUf();
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