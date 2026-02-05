<?php
/*
 * PessoaBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Business;

use App\Entities\Pessoa;
use App\Repository\PessoaRepository;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'Pessoa'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class PessoaBO extends AbstractBO
{
    /**
     * @var PessoaRepository
     */
    private $pessoaRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->pessoaRepository = $this->getRepository(Pessoa::class);
    }

    /**
     * Retorna as empresas as quais o profissional informado é o 'Responsável Técnico".
     *
     * @param integer $idPessoa
     * @return Pessoa[]
     * @throws \Exception
     */
    public function getEmpresasPorProfissionalResponsavelTecnico($idPessoa)
    {
        return $this->pessoaRepository->getEmpresasResponsabilidadeTecnicaPorPessoa($idPessoa);
    }

    /**
     * Recupera a instância de 'Pessoa' de acordo com 'id' informado.
     *
     * @param integer $id
     * @return Pessoa|null|object
     */
    public function getPessoaPorId($id)
    {
        return $this->pessoaRepository->find($id);
    }
}