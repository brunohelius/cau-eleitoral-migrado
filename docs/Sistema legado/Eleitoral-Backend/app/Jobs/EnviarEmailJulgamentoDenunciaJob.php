<?php
/*
 * EnviarEmailJulgamentoDenunciaJob.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização do CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Jobs;

use App\Business\JulgamentoDenunciaBO;
use App\Exceptions\NegocioException;
use Doctrine\ORM\NonUniqueResultException;

class EnviarEmailJulgamentoDenunciaJob extends Job
{

    /**
     * @var integer
     */
    private $idJulgamentoDenuncia;

    /**
     * Criação de uma nova instância de EnviarEmailJulgamentoDenunciaJob.
     *
     * @param $idJulgamentoDenuncia
     */
    public function __construct($idJulgamentoDenuncia)
    {
        $this->idJulgamentoDenuncia = $idJulgamentoDenuncia;
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
        $this->getJulgamentoDenunciaBO()->enviarEmailCadastroJulgamentoDenuncia($this->idJulgamentoDenuncia);
    }

    /**
     * Retorna uma instância de JulgamentoDenunciaBO
     *
     * @return JulgamentoDenunciaBO
     */
    private function getJulgamentoDenunciaBO()
    {
        return app()->make(JulgamentoDenunciaBO::class);
    }
}
