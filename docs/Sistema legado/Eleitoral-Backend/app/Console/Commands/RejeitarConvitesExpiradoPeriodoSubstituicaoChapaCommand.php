<?php


namespace App\Console\Commands;


use App\Business\MembroChapaBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RejeitarConvitesExpiradoPeriodoSubstituicaoChapaCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'membrosChapa:rejeitarConvitesExpiradosPeriodoSubstituicaoChapa';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Rejeita os convites de participação da chapa após data fim da atividade secundária 3.8';

    /**
     * Cria uma nova instância de RejeitarConvitesExpiradosMembroChapaCommand
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
        Log::info("membrosChapa:rejeitarConvitesExpiradosPeriodoSubstituicaoChapa");
        $this->getMembroChapaBO()->rejeitarConvitesForaVigenciaPeriodoSubstituicao();
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
