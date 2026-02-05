<?php
/*
 * ContrarrazaoRecursoDenunciaMail.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Mail;

use App\Config\Constants;
use App\To\EmailDenunciaTO;
use App\To\EmailTO;

class ContrarrazaoRecursoDenunciaMail extends AtividadeSecundariaMail
{

    /**
     * @var EmailDenunciaTO
     */
    public $emailContrarrazaoTO;

    /**
     * @var bool
     */
    public $isCadastro;

    /**
     * @var bool
     */
    public $isExibirResponsavelSigiloso;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     * @param EmailDenunciaTO $emailContrarrazaoTO
     * @param bool $isCadastro
     * @param bool $isExibirResponsavelSigiloso
     */
    public function __construct(
        EmailTO $emailTO,
        EmailDenunciaTO $emailContrarrazaoTO,
        bool $isCadastro = true,
        bool $isExibirResponsavelSigiloso = false
    ) {
        parent::__construct($emailTO);
        $this->isCadastro = $isCadastro;
        $this->emailContrarrazaoTO = $emailContrarrazaoTO;
        $this->isExibirResponsavelSigiloso = $isExibirResponsavelSigiloso;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.julgamentoDenuncia.contrarrazaoRecursoDenuncia')->with([
            'isCadastro' => $this->isCadastro,
            'responsavelCadastro' => $this->isExibirResponsavelSigiloso
                ? "Denúncia sigilosa"
                : $this->emailContrarrazaoTO->getContrarrazaoRecursoDenuncia()->getResponsavel()
        ])->subject($this->email->getAssunto());
    }
}
