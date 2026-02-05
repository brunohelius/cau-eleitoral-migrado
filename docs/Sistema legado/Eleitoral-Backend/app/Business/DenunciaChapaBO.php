<?php
/*
 * DenunciaChapaBO.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Business;

use App\Entities\DenunciaChapa;

/**
 * Classe responsável por encapsular as implementações de negócio referente a entidade 'AtividadePrincipalCalendario'.
 *
 * @package App\Business
 * @author Squadra Tecnologia S/A.
 */
class DenunciaChapaBO extends AbstractBO
{
    /**
     * @var \App\Repository\DenunciaChapaRepository
     */
    private $denunciaChapaRepository;

    /**
     * @var \App\Repository\DenunciaRepository
     */
    private $denunciaRepository;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->denunciaChapaRepository = $this->getRepository(DenunciaChapa::class);
    }

    /**
     * @return string
     */
    public function getTotalChapaPorUf($idPessoa)
    {
      return $this->denunciaChapaRepository->getTotalDenunciaChapaPorUF($idPessoa);
    }

    /**
     * @return string
     */
    public function getListaDenunciaChapaPorUF($idPessoa,$idUF)
    {
      return $this->denunciaChapaRepository->getListaDenunciaChapaPorUF($idPessoa,$idUF);
    }
}
?>