<?php
/*
 * ChapaEleicaoBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Entities\MembroChapa;
use App\Entities\MembroChapaPendencia;
use App\Entities\RedeSocialChapa;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Repository\MembroChapaPendenciaRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'MembroChapaPendencia'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class MembroChapaPendenciaBO extends AbstractBO
{

    /**
     * @var MembroChapaPendenciaRepository
     */
    private $membroChapaPendenciaRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->membroChapaPendenciaRepository = $this->getRepository(MembroChapaPendencia::class);
    }

    /**
     * Método que salva as pendências de um membro da chapa da eleição
     *
     * @param MembroChapa $membroChapa
     * @param array $membroChapaPendencias
     * @throws NegocioException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function salvarMembroChapaPendencias(MembroChapa $membroChapa, $membroChapaPendencias)
    {
        $this->excluirMembroChapaPendencias($membroChapa);

        $membroChapaPendenciasSalvo = null;
        if (!empty($membroChapaPendencias)) {
            $membroChapaPendenciasSalvo = $this->membroChapaPendenciaRepository->persistEmLote($membroChapaPendencias);
        }

        return $membroChapaPendenciasSalvo;
    }

    /**
     * Método para remover todas as Pendências associado a um Membro da Chapa Eleção
     *
     * @param MembroChapa $membroChapa
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function excluirMembroChapaPendencias($membroChapa)
    {
        $this->membroChapaPendenciaRepository->excluirPorMembroChapa($membroChapa);
    }

    /**
     * Verifica se os campos obrigatórios foram preenchidos da Rede Social da Chapa.
     *
     * @param RedeSocialChapa $redeSocialChapa
     *
     * @throws NegocioException
     */
    private function validarCamposObrigatoriosMembroChapaPendencia(MembroChapaPendencia $membroChapaPendencia)
    {
        if (empty($membroChapaPendencia->getTipoPendencia())) {
            throw new NegocioException(Message::MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO, [], true);
        }
    }
}
