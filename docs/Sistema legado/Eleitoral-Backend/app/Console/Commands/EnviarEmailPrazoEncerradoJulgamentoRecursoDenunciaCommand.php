<?php


namespace App\Console\Commands;


use App\Business\DenunciaAudienciaInstrucaoBO;
use App\Business\DenunciaDefesaBO;
use App\Business\DenunciaProvasBO;
use App\Business\JulgamentoRecursoDenunciaBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnviarEmailPrazoEncerradoJulgamentoRecursoDenunciaCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'julgamentoRecursoDenuncia:enviarEmailPrazoEncerradoJulgamentoRecursoDenuncia';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Envia email quando não for realizado o julgamento 2 instância no prazo de 10 dias.';

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
        Log::info("julgamentoRecursoDenuncia:enviarEmailPrazoEncerradoJulgamentoRecursoDenuncia");
        $this->getJulgamentoRecursoDenunciaBO()->envioEmailPrazoEncerrado();
    }

    /**
     * Retorna uma instância de JulgamentoRecursoDenunciaBO
     *
     * @return JulgamentoRecursoDenunciaBO
     */
    private function getJulgamentoRecursoDenunciaBO()
    {
        return app()->make(JulgamentoRecursoDenunciaBO::class);
    }
}
