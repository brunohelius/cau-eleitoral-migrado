<?php

namespace App\Mail;

use App\To\EmailTO;
use App\To\SubstituicaoImpugnacaoTO;

class SubstituicaoImpugnacaoCadastradoMail extends AtividadeSecundariaMail
{

    /**
     * @var SubstituicaoImpugnacaoTO
     */
    public $substituicaoImpugnacaoTO;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     * @param SubstituicaoImpugnacaoTO $substituicaoImpugnacaoTO
     */
    public function __construct(EmailTO $emailTO, SubstituicaoImpugnacaoTO $substituicaoImpugnacaoTO)
    {
        parent::__construct($emailTO);
        $this->substituicaoImpugnacaoTO = $substituicaoImpugnacaoTO;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.substituicaoImpugnacao.cadastrado')->with([
            'nomeSubstituido' => $this->substituicaoImpugnacaoTO->getPedidoImpugnacao()->getInformacoesCandidato()->getNome(),
            'nomeSubstituto' => $this->substituicaoImpugnacaoTO->getMembroChapaSubstituto()->getProfissional()->getNome()
        ])->subject($this->email->getAssunto());
    }
}
