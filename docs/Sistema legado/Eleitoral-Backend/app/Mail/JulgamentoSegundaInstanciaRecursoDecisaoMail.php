<?php

namespace App\Mail;

use App\To\EmailTO;
use App\To\JulgamentoSegundaInstanciaRecursoTO;

class JulgamentoSegundaInstanciaRecursoDecisaoMail extends AtividadeSecundariaMail
{

    /**
     * @var JulgamentoSegundaInstanciaRecursoTO
     */
    public $julgamentoSegundaInstanciaRecursoTO;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     * @param JulgamentoSegundaInstanciaRecursoTO $julgamentoSegundaInstanciaRecursoTO
     */
    public function __construct(EmailTO $emailTO, JulgamentoSegundaInstanciaRecursoTO $julgamentoSegundaInstanciaRecursoTO)
    {
        parent::__construct($emailTO);
        $this->julgamentoSegundaInstanciaRecursoTO = $julgamentoSegundaInstanciaRecursoTO;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.julgamentoFinalSegundaInstancia.julgamento_recurso_decisao')->with([
            'decisao' => $this->julgamentoSegundaInstanciaRecursoTO->getStatusJulgamentoFinal()->getDescricao(),
            'descricao' => $this->julgamentoSegundaInstanciaRecursoTO->getDescricao()
        ])->subject($this->email->getAssunto());
    }
}
