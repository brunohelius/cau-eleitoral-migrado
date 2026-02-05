<?php


namespace App\Console\Commands;


use App\Business\ChapaEleicaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ValidarChapaEleicaoEMembrosCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'chapas:validarPeriodoVigente';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Valida chapas e membros dentro do período vigente';

    /**
     * Cria uma nova instância de ValidarChapaEleicaoEMembrosCommand
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
        Log::info("chapas:validarPeriodoVigente");
        $this->getChapaEleicaoBO()->validarChapasEMembrosChapa();
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
