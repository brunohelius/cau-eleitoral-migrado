<?php


namespace App\Console\Commands;


use App\Business\ChapaEleicaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExcluirChapasNaoConfirmadasCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'chapas:excluirChapasNaoConfirmadas';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Exclui as Chapas que não confirmaram a criação após o fim da atividade secundária';

    /**
     * Cria uma nova instância de ExcluirChapasNaoConfirmadasCommand
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
        Log::info("chapas:excluirChapasNaoConfirmadas");
        $this->getChapaEleicaoBO()->excluirChapasNaoConfirmadasForaPeriodoVigente();
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
