<?php

namespace App\Console\Commands;

use App\Business\ImpugnacaoResultadoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlterarStatusImpugnacaoResultadoInicioAtivRecursoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'impugnacaoResultado:alteraStatusEmRecurso';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Altera o Status do pedido de impugnação no iníco da atividade de recurso.';

    /**
     * Cria uma nova instância de AlterarStatusImpugnacaoResultadoInicioAtivRecursoCommand
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
        Log::info('impugnacaoResultado:alteraStatusEmRecurso');
        $this->getImpugnacaoResultadoBO()->atualizarStatusImpugnacaoInicioAtivRecurso();
    }

    /**
     * Retorna uma instância de ImpugnacaoResultadoBO
     *
     * @return ImpugnacaoResultadoBO
     */
    private function getImpugnacaoResultadoBO()
    {
        return app()->make(ImpugnacaoResultadoBO::class);
    }
}
