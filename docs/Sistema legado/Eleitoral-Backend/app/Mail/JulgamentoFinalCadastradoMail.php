<?php

namespace App\Mail;

use App\To\EmailTO;
use App\To\JulgamentoFinalTO;

class JulgamentoFinalCadastradoMail extends AtividadeSecundariaMail
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
        return $this->view('emails.julgamentoFinal.cadastrado')->with([
            'decisao' => $this->julgamentoFinalTO->getStatusJulgamentoFinal()->getDescricao(),
            'descricao' => $this->julgamentoFinalTO->getDescricao()
        ])->subject($this->email->getAssunto());
    }
}
