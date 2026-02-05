<?php


namespace App\Console\Commands;


use App\Business\EmailAtividadeSecundariaBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlertarInicioPeriodoMembroComissaoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'membrosComissao:enviarEmailInicioPeriodoCadastro';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail, informando o início do cadastro de membros para os assessores das CE-UF e da CEN';

    /**
     * Cria uma nova instância de AlertarInicioPeriodoMembroComissaoCommand
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
        Log::info("membrosComissao:enviarEmailInicioPeriodoCadastro");
        $this->getEmailAtividadeSecundariaBO()->enviarEmailIncioPeriodoInformarMembros();
    }

    /**
     * Retorna uma instância de EmailAtividadeSecundariaBO
     *
     * @return EmailAtividadeSecundariaBO
     */
    private function getEmailAtividadeSecundariaBO()
    {
        return app()->make(EmailAtividadeSecundariaBO::class);
    }
}
