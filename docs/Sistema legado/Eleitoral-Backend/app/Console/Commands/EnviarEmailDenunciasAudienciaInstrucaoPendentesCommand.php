<?php


namespace App\Console\Commands;


use App\Business\DenunciaAudienciaInstrucaoBO;
use App\Business\DenunciaDefesaBO;
use App\Business\DenunciaProvasBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnviarEmailDenunciasAudienciaInstrucaoPendentesCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'denunciaAudienciaInstrucao:enviarEmailDenunciasAudienciaInstrucaoPendentes';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Envia email para Encaminhamentos de Audiencia de Instrução com mais de 24 Horas Pendentes.';

    /**
     * Cria uma nova instância de EnviarEmailDenunciasAudienciaInstrucaoPendentesCommand
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
        Log::info("denunciaAudienciaInstrucao:enviarEmailDenunciasAudienciaInstrucaoPendentes");
        $this->getDenunciaAudienciaInstrucaoBO()->enviarEmailDenunciasAudienciaInstrucaoPendentes();
    }

    /**
     * Retorna uma instância de DenunciaAudienciaInstrucaoBO
     *
     * @return DenunciaAudienciaInstrucaoBO
     */
    private function getDenunciaAudienciaInstrucaoBO()
    {
        return app()->make(DenunciaAudienciaInstrucaoBO::class);
    }
}
