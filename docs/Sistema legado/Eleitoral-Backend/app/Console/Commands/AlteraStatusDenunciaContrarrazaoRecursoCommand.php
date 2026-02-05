<?php

namespace App\Console\Commands;

use App\Business\ContrarrazaoRecursoDenunciaBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlteraStatusDenunciaContrarrazaoRecursoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'denuncia:alteraStatusDenunciaContrarrazaoRecurso';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Altera o Status da Denuncia de acordo com o prazo da Contrarrazao.';

    /**
     * Cria uma nova instância de AlteraStatusDenunciaContrarrazaoRecursoCommand
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
        Log::info('denuncia:alteraStatusDenunciaContrarrazaoRecurso');
        $this->getContrarrazaoRecursoDenunciaBO()->alteraStatusDenunciaPorPrazoContrarrazao();
    }

    /**
     * Retorna uma instância de ContrarrazaoRecursoDenunciaBO
     *
     * @return ContrarrazaoRecursoDenunciaBO
     */
    private function getContrarrazaoRecursoDenunciaBO()
    {
        return app()->make(ContrarrazaoRecursoDenunciaBO::class);
    }
}
