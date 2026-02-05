<?php


namespace App\Console\Commands;


use App\Business\JulgamentoAdmissibilidadeBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarDenunciaSemRelatorCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'denuncia:alertarSemRelator';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Envia e-mail alertando denúncia sem relator';

    /**
     * Cria uma nova instância de AlertarDenunciaSemRelatorCommand
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
        Log::info('denuncia:alertarSemRelator');
        $this->getJulgamentoAdmissibilidadeBO()->alertaDenunciaSemRelator();
    }

    /**
     * Retorna uma instância de JulgamentoAdmissibilidadeBO
     *
     * @return JulgamentoAdmissibilidadeBO
     */
    private function getJulgamentoAdmissibilidadeBO()
    {
        return app()->make(JulgamentoAdmissibilidadeBO::class);
    }
}
