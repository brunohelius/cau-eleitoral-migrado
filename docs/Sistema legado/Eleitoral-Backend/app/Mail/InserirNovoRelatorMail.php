<?php

namespace App\Mail;

use App\To\EmailTO;
use App\To\EmailEncaminhamentoAlegacaoFinalTO;

class InserirNovoRelatorMail extends AtividadeSecundariaMail
{

    /**
     * @var EmailEncaminhamentoAlegacaoFinalTO
     */
    public $emailEncaminhamentoAlegacaoFinalTO;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     * @param EmailEncaminhamentoAlegacaoFinalTO $emailEncaminhamentoAlegacaoFinalTO
     */
    public function __construct(EmailTO $emailTO, EmailEncaminhamentoAlegacaoFinalTO $emailEncaminhamentoAlegacaoFinalTO)
    {
        parent::__construct($emailTO);
        $this->emailEncaminhamentoAlegacaoFinalTO = $emailEncaminhamentoAlegacaoFinalTO;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.relator.inserir')->subject($this->email->getAssunto());
    }
}
