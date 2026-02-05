<?php

namespace App\Mail;

use App\To\EmailTO;
use App\To\JulgamentoSegundaInstanciaSubstituicaoTO;

class JulgamentoSegundaInstanciaSubstituicaoDecisaoMail extends AtividadeSecundariaMail
{

    /**
     * @var JulgamentoSegundaInstanciaSubstituicaoTO
     */
    public $julgamentoSegundaInstanciaSubstituicaoTO;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     * @param JulgamentoSegundaInstanciaSubstituicaoTO $julgamentoSegundaInstanciaSubstituicaoTO
     */
    public function __construct(EmailTO $emailTO, JulgamentoSegundaInstanciaSubstituicaoTO $julgamentoSegundaInstanciaSubstituicaoTO)
    {
        parent::__construct($emailTO);
        $this->julgamentoSegundaInstanciaSubstituicaoTO = $julgamentoSegundaInstanciaSubstituicaoTO;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.julgamentoFinalSegundaInstancia.julgamento_substituicao_decisao')->with([
            'decisao' => $this->julgamentoSegundaInstanciaSubstituicaoTO->getStatusJulgamentoFinal()->getDescricao(),
            'descricao' => $this->julgamentoSegundaInstanciaSubstituicaoTO->getDescricao()
        ])->subject($this->email->getAssunto());
    }
}
