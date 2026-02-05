<?php


namespace App\Console\Commands;


use App\Business\ParametroConselheiroBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AtualizarConselheirosAutomaticoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'paramConselheiro:atualizar';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Atualiza os parâmetros de conselheiros automático';

    /**
     * Cria uma nova instância de AtualizarConselheirosAutomaticoCommand
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
        Log::info("paramConselheiro:atualizar");
        $this->getParametroConselheiroBO()->atualizarConselheiroAutomatico();
        Log::info("paramConselheiro:finalizou");
    }

    /**
     * Retorna uma instância de ParametroConselheiroBO
     *
     * @return ParametroConselheiroBO
     */
    private function getParametroConselheiroBO()
    {
        return app()->make(ParametroConselheiroBO::class);
    }
}
