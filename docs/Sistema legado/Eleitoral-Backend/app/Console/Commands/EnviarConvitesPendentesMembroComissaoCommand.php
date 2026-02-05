<?php


namespace App\Console\Commands;


use App\Business\MembroComissaoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnviarConvitesPendentesMembroComissaoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'membrosComissao:enviarConvitesPendentesMembroComissao';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Enviar e-mail, informando o fim do período do cadastro de membros para os assessores das CE-UF e da CEN';

    /**
     * Cria uma nova instância de EnviarConvitesPendentesMembroComissaoCommand
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
        Log::info("membrosComissao:enviarConvitesPendentesMembroComissao");
        $this->getMembroComissaoBO()->enviarEmailsConvitesPendentes();
    }

    /**
     * Retorna uma instância de MembroComissaoBO
     *
     * @return MembroComissaoBO
     */
    private function getMembroComissaoBO()
    {
        return app()->make(MembroComissaoBO::class);
    }
}
