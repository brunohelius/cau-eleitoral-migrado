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

class ParecerFinalMail extends AtividadeSecundariaMail
{

    /**
     * @var EmailDenunciaTO
     */
    public $emailParecerFinalTO;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     * @param EmailDenunciaTO $emailParecerFinalTO
     */
    public function __construct(EmailTO $emailTO, EmailDenunciaTO $emailParecerFinalTO)
    {
        parent::__construct($emailTO);
        $this->emailParecerFinalTO = $emailParecerFinalTO;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.parecerFinal.inserir')->subject($this->email->getAssunto());
    }
}
