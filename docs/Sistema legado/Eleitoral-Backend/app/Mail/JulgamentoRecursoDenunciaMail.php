<?php
/*
 * ParecerFinalMail.php* Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Mail;

use App\To\EmailDenunciaTO;
use App\To\EmailTO;

class JulgamentoRecursoDenunciaMail extends AtividadeSecundariaMail
{

    /**
     * @var EmailDenunciaTO
     */
    public $emailJulgamentoDenunciaTO;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     * @param EmailDenunciaTO $emailJulgamentoDenunciaTO
     */
    public function __construct(EmailTO $emailTO, EmailDenunciaTO $emailJulgamentoDenunciaTO)
    {
        parent::__construct($emailTO);
        $this->emailJulgamentoDenunciaTO = $emailJulgamentoDenunciaTO;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.julgamentoDenuncia.inserirSegundaInstancia')->subject($this->email->getAssunto());
    }
}
