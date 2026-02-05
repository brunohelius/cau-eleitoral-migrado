<?php


namespace App\Console\Commands;


use App\Business\AtividadeSecundariaCalendarioBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarFimPeriodoDefinicaoComissaoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'membrosComissao:enviarEmailFimPeriodoDefinicao';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail, informando o fim do período do cadastro de membros para os assessores das CE-UF e da CEN';

    /**
     * Cria uma nova instância de AlertarFimPeriodoDefinicaoComissaoCommand
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
        Log::info('membrosComissao:enviarEmailFimPeriodoDefinicao');
        $this->getAtividadeSecundariaCalendarioBO()->enviarEmailsAtividadesSemParametrizacaoInicial();
    }

    /**
     * Retorna uma instância de AtividadeSecundariaCalendarioBO
     *
     * @return AtividadeSecundariaCalendarioBO
     */
    private function getAtividadeSecundariaCalendarioBO()
    {
        return app()->make(AtividadeSecundariaCalendarioBO::class);
    }
}
