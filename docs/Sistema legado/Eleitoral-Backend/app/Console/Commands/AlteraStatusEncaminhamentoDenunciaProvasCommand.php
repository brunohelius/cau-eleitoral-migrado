<?php


namespace App\Console\Commands;


use App\Business\DenunciaDefesaBO;
use App\Business\DenunciaProvasBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlteraStatusEncaminhamentoDenunciaProvasCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'denunciaProvas:alteraStatusEncaminhamentoDenunciaProvas';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Altera o Status do Encaminhamento de Provas caso tenha expirado o Prazo.';

    /**
     * Cria uma nova instância de AlteraStatusEncaminhamentoDenunciaProvasCommand
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
        Log::info("denunciaProvas:alteraStatusEncaminhamentoDenunciaProvas");
        $this->getDenunciaProvasBO()->alteraStatusProvaPorPrazo();
    }

    /**
     * Retorna uma instância de DenunciaProvasBO
     *
     * @return DenunciaProvasBO
     */
    private function getDenunciaProvasBO()
    {
        return app()->make(DenunciaProvasBO::class);
    }
}
