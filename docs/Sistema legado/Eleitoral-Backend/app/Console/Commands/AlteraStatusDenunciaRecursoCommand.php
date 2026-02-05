<?php

namespace App\Console\Commands;

use App\Business\RecursoContrarrazaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlteraStatusDenunciaRecursoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'denuncia:alteraStatusDenunciaRecurso';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Altera o Status da Denuncia de acordo com o prazo do Recurso.';

    /**
     * Cria uma nova instância de AlteraStatusDenunciaRecursoCommand
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
        Log::info('denuncia:alteraStatusDenunciaRecurso');
        $this->getRecursoContrarrazaoBO()->rotinaPrazoEncerradoRecurso();
    }

    /**
     * Retorna uma instância de RecursoContrarrazaoBO
     *
     * @return RecursoContrarrazaoBO
     */
    private function getRecursoContrarrazaoBO()
    {
        return app()->make(RecursoContrarrazaoBO::class);
    }
}
