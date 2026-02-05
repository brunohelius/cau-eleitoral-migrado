<?php

namespace App\Business;

use App\Entities\StatusRecursoDenuncia;
use App\Repository\StatusRecursoDenunciaRepository;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'StatusrecursoDenuncia'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class StatusRecursoDenunciaBO extends AbstractBO
{
    /**
     * @var StatusRecursoDenunciaRepository
     */
    private $statusRecursoRepository;

    public function __construct()
    {
        $this->statusRecursoRepository = $this->getRepository(StatusRecursoDenuncia::class);
    }

    public function salvar(StatusRecursoDenuncia $statusRecurso)
    {
        try {
            $this->statusRecursoRepository->persist($statusRecurso);
        }catch (\Exception $e)
        {
            throw $e;
        }
    }
}
