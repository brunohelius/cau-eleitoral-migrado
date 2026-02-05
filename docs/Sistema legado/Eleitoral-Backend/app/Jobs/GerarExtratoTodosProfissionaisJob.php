<?php

namespace App\Jobs;

use App\Business\ParametroConselheiroBO;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Exception;

class GerarExtratoTodosProfissionaisJob extends Job
{

    /**
     * @var int
     */
    private $idHistoricoExtratoConselheiro;

    /**
     * @var string
     */
    private $sigla;

    /**
     * Criação de uma nova instância de EnviarEmailChapaConfirmadaJob.
     *
     * @param $idHistoricoExtratoConselheiro
     * @param $sigla
     */
    public function __construct($idHistoricoExtratoConselheiro, $sigla)
    {
        $this->idHistoricoExtratoConselheiro = $idHistoricoExtratoConselheiro;
        $this->sigla = $sigla;

    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function handle()
    {
        $this->getParametroConselheiroBO()->gerarExtratoTodosProfissionais($this->idHistoricoExtratoConselheiro, $this->sigla);
    }

    /**
     * Retorna uma instância de ParametroConselheiroBO
     *
     * @return ParametroConselheiroBO
     */
    private function getParametroConselheiroBO()
    {
        return app()->make(ParametroConselheiroBO::class);
    }
}
