<?php

namespace App\Mail;

use App\To\EmailJulgamentoAdmissibilidadeTO;
use App\To\EmailTO;
use App\To\EmailEncaminhamentoAlegacaoFinalTO;

/**
 * Class JulgamentoAdmissibilidadeMail
 * @package App\Mail
 */
class JulgamentoAdmissibilidadeMail extends AtividadeSecundariaMail
{

    /**
     * @var EmailJulgamentoAdmissibilidadeTO
     */
    public $julgamentoTo;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     * @param EmailEncaminhamentoAlegacaoFinalTO $emailEncaminhamentoAlegacaoFinalTO
     */
    public function __construct(EmailTO $emailTO, EmailJulgamentoAdmissibilidadeTO $julgamentoTo)
    {
        parent::__construct($emailTO);
        $this->julgamentoTo = $julgamentoTo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.julgamentoAdmissibilidade.julgarAdmissibilidade')->subject($this->email->getAssunto());
    }
}
