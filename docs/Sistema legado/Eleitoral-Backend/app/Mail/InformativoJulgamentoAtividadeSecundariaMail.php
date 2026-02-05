<?php

namespace App\Mail;

use App\To\EmailTO;
use App\To\JulgamentoFinalTO;

class InformativoJulgamentoAtividadeSecundariaMail extends AtividadeSecundariaMail
{
    /**
     * @var JulgamentoFinalTO
     */
    public $julgamentoFinalTO;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     */
    public function __construct(EmailTO $emailTO)
    {
        parent::__construct($emailTO);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.julgamentoFinal.informativoJulgamentoAtividadeSecundaria')
            ->with([])->subject($this->email->getAssunto());
    }
}
