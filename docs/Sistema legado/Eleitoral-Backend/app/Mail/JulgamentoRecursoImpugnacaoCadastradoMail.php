<?php

namespace App\Mail;

use App\To\EmailTO;
use App\To\JulgamentoRecursoImpugnacaoTO;

class JulgamentoRecursoImpugnacaoCadastradoMail extends AtividadeSecundariaMail
{

    /**
     * @var JulgamentoRecursoImpugnacaoTO
     */
    public $julgamentoRecursoImpugnacaoTO;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     * @param JulgamentoRecursoImpugnacaoTO $substituicaoImpugnacaoTO
     */
    public function __construct(EmailTO $emailTO, JulgamentoRecursoImpugnacaoTO $substituicaoImpugnacaoTO)
    {
        parent::__construct($emailTO);
        $this->julgamentoRecursoImpugnacaoTO = $substituicaoImpugnacaoTO;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.julgamentoImpugnacao.cadastrado')->with([
            'protocolo' => $this->julgamentoRecursoImpugnacaoTO->getPedidoImpugnacao()->getNumeroProtocolo(),
            'decisao' => $this->julgamentoRecursoImpugnacaoTO->getStatusJulgamentoImpugnacao()->getDescricao(),
            'descricao' => $this->julgamentoRecursoImpugnacaoTO->getDescricao()
        ])->subject($this->email->getAssunto());
    }
}
