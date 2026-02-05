<?php

namespace App\Mail;

use App\To\EmailTO;
use App\To\JulgamentoImpugnacaoTO;
use App\To\JulgamentoRecursoImpugnacaoTO;

class JulgamentoImpugnacaoCadastradoMail extends AtividadeSecundariaMail
{

    /**
     * @var JulgamentoImpugnacaoTO
     */
    public $julgamentoImpugnacaoTO;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     * @param JulgamentoImpugnacaoTO $julgamentoImpugnacaoTO
     */
    public function __construct(EmailTO $emailTO, JulgamentoImpugnacaoTO $julgamentoImpugnacaoTO)
    {
        parent::__construct($emailTO);
        $this->julgamentoImpugnacaoTO = $julgamentoImpugnacaoTO;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.julgamentoImpugnacao.cadastrado')->with([
            'protocolo' => $this->julgamentoImpugnacaoTO->getPedidoImpugnacao()->getNumeroProtocolo(),
            'decisao' => $this->julgamentoImpugnacaoTO->getStatusJulgamentoImpugnacao()->getDescricao(),
            'descricao' => $this->julgamentoImpugnacaoTO->getDescricao()
        ])->subject($this->email->getAssunto());
    }
}
