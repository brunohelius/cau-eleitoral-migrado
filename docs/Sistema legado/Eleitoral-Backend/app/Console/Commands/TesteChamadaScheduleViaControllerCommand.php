<?php


namespace App\Console\Commands;


use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TesteChamadaScheduleViaControllerCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'teste:chamadaSchedule';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Envia e-mail alterta cinco dias antes do fim da atividade secundária 2.1';

    /**
     * Cria uma nova instância de TesteChamadaScheduleController
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
        Log::info("Chamou");
    }
}
