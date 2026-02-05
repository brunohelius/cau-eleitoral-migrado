<?php

namespace App\Mail;

use App\To\EmailDenunciaAdmitidaTO;
use App\To\EmailTO;

/**
 * Class DenunciaAdmitidaMail
 * @package App\Mail
 */
class DenunciaAdmitidaMail extends AtividadeSecundariaMail
{
    /**
     * @var EmailDenunciaAdmitidaTO
     */
    public $denunciaAdmitidaTo;

    /**
     * DenunciaAdmitidaMail constructor.
     * @param EmailTO $emailTO
     * @param EmailDenunciaAdmitidaTO $denunciaAdmitidaTO
     */
    public function __construct(EmailTO $emailTO, EmailDenunciaAdmitidaTO $denunciaAdmitidaTO)
    {
        parent::__construct($emailTO);
        $this->denunciaAdmitidaTo = $denunciaAdmitidaTO;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.denunciaAdmitida.inserir')->subject($this->email->getAssunto());
    }
}
