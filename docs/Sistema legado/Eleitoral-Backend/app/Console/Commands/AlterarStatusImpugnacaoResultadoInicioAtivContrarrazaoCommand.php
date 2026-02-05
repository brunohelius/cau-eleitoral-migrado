<?php

namespace App\Console\Commands;

use App\Business\ImpugnacaoResultadoBO;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AlterarStatusImpugnacaoResultadoInicioAtivContrarrazaoCommand extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'impugnacaoResultado:alteraStatusEmContrarrazao';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Altera o Status do pedido de impugnação no iníco da atividade de contrarrazão.';

    /**
     * Cria uma nova instância de AlterarStatusImpugnacaoResultadoInicioAtivContrarrazaoCommand
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
        Log::info('impugnacaoResultado:alteraStatusEmContrarrazao');
        $this->getImpugnacaoResultadoBO()->atualizarStatusImpugnacaoInicioAtivContrarrazao();
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
