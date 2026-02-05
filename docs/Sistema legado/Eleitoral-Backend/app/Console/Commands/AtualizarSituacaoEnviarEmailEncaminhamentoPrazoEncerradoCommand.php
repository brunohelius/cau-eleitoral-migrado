<?php


namespace App\Console\Commands;


use App\Business\AlegacaoFinalBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AtualizarSituacaoEnviarEmailEncaminhamentoPrazoEncerradoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'alegacaoFinal:atualizarSituacaoEncaminhamento';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Atualiza a situação do encaminhamento alegação final e envia e-mail se o prazo encerrar';

    /**
     * Cria uma nova instância de AtualizarSituacaoEnviarEmailEncaminhamentoPrazoEncerradoCommand
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
        Log::info("alegacaoFinal:atualizarSituacaoEncaminhamento");
        $this->getAlegacaoFinalBO()->rotinaVerificaoEncaminhamentosDenuncia();
    }

    /**
     * Retorna uma instância de AlegacaoFinalBO
     *
     * @return AlegacaoFinalBO
     */
    private function getAlegacaoFinalBO()
    {
        return app()->make(AlegacaoFinalBO::class);
    }
}
