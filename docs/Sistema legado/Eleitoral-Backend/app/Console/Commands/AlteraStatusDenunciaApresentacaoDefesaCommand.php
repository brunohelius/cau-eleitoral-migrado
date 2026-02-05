<?php

namespace App\Console\Commands;

use App\Business\DenunciaDefesaBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlteraStatusDenunciaApresentacaoDefesaCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'denuncia:alteraStatusDenunciaApresentacaoDefesa';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Altera o Status da Denuncia de acordo com o prazo de Apresentação de Defesa.';

    /**
     * Cria uma nova instância de AlteraStatusDenunciaApresentacaoDefesaCommand
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
        Log::info('denuncia:alteraStatusDenunciaApresentacaoDefesa');
        $this->getDenunciaDefesaBO()->rotinaPrazoEncerradoApresentacaoDefesa();
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
