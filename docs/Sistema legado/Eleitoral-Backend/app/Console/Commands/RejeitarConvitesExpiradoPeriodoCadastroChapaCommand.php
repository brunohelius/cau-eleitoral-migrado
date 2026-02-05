<?php


namespace App\Console\Commands;


use App\Business\MembroChapaBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RejeitarConvitesExpiradoPeriodoCadastroChapaCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'membrosChapa:rejeitarConvitesExpiradosPeriodoCadastroChapa';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Rejeita os convites de participação da chapa após data fim da atividade secundária 2.2';

    /**
     * Cria uma nova instância de RejeitarConvitesExpiradoPeriodoCadastroChapaCommand
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
        Log::info("membrosChapa:rejeitarConvitesExpiradosPeriodoCadastroChapa");
        $this->getMembroChapaBO()->rejeitarConvitesForaVigenciaPeriodoCadastro();
    }

    /**
     * Retorna uma instância de MembroChapaBO
     *
     * @return MembroChapaBO
     */
    private function getMembroChapaBO()
    {
        return app()->make(MembroChapaBO::class);
    }
}
