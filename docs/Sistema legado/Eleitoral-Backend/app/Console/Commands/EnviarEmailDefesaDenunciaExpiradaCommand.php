<?php


namespace App\Console\Commands;


use App\Business\DenunciaDefesaBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnviarEmailDefesaDenunciaExpiradaCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'defesaDenuncia:enviarEmailDenunciasDefesaExpira';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail para Defesa de Denuncia Expirada.';

    /**
     * Cria uma nova instância de EnviarEmailDefesaDenunciaExpiradaCommand
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
        Log::info("defesaDenuncia:enviarEmailDenunciasDefesaExpira");
        $this->getDenunciaDefesaBO()->enviarEmailDenunciasDefesaExpira();
    }

    /**
     * Retorna uma instância de DenunciaDefesaBO
     *
     * @return DenunciaDefesaBO
     */
    private function getDenunciaDefesaBO()
    {
        return app()->make(DenunciaDefesaBO::class);
    }
}
