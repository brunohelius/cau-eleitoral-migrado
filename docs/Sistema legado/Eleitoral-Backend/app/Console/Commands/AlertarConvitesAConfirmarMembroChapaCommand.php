<?php


namespace App\Console\Commands;


use App\Business\MembroChapaBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarConvitesAConfirmarMembroChapaCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'membrosChapa:enviarEmailCincoDiasAntesFimConvite';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Envia e-mail alterta cinco dias antes do fim da atividade secundária 2.2';

    /**
     * Cria uma nova instância de AlertarConvitesAConfirmarMembroChapaCommand
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
        Log::info('membrosChapa:enviarEmailCincoDiasAntesFimConvite');
        $this->getMembroChapaBO()->alertarConvitesASeremAceitos();
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
