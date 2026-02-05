<?php

namespace App\Mail;

use App\To\EmailTO;
use App\To\JulgamentoFinalTO;

class JulgamentoFinalAlteradoMail extends AtividadeSecundariaMail
{
    /**
     * @var JulgamentoFinalTO
     */
    public $julgamentoFinalTO;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     * @param JulgamentoFinalTO $julgamentoFinalTO
     */
    public function __construct(EmailTO $emailTO, JulgamentoFinalTO $julgamentoFinalTO)
    {
        parent::__construct($emailTO);
        $this->julgamentoFinalTO = $julgamentoFinalTO;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.julgamentoFinal.alterado')->subject($this->email->getAssunto());
    }
}
