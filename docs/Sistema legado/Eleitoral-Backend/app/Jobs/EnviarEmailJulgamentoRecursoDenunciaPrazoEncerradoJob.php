<?php
/*
 * EnviarEmailJulgamentoRecursoDenunciaPrazoEncerradoJob.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Jobs;

use App\Business\JulgamentoDenunciaBO;
use App\Business\JulgamentoRecursoDenunciaBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailJulgamentoRecursoDenunciaPrazoEncerradoJob extends Job
{

    /**
     * @var integer
     */
    private $idDenuncia;

    /**
     * Criação de uma nova instância de EnviarEmailJulgamentoRecursoDenunciaPrazoEncerradoJob.
     *
     * @param $idDenuncia
     */
    public function __construct($idDenuncia)
    {
        $this->idDenuncia = $idDenuncia;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws NegocioException
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function handle()
    {
        $this->getJulgamentoRecursoDenunciaBO()->enviarEmailPrazoEncerrado($this->idDenuncia);
    }

    /**
     * Retorna uma instância de JulgamentoRecursoDenunciaBO
     *
     * @return JulgamentoRecursoDenunciaBO
     */
    private function getJulgamentoRecursoDenunciaBO()
    {
        return app()->make(JulgamentoRecursoDenunciaBO::class);
    }
}
