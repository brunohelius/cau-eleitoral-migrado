<?php

namespace App\Mail;

use App\To\EmailTO;
use App\To\JulgamentoRecursoPedidoSubstituicaoTO;

class JulgamentoRecursoPedidoSubstituicaoMail extends AtividadeSecundariaMail
{
    /**
     * @var JulgamentoRecursoPedidoSubstituicaoTO
     */
    public $julgamentoRecursoPedidoSubstituicaoTO;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     * @param JulgamentoRecursoPedidoSubstituicaoTO $julgamentoRecursoPedidoSubstituicaoTO
     */
    public function __construct(EmailTO $emailTO, JulgamentoRecursoPedidoSubstituicaoTO $julgamentoRecursoPedidoSubstituicaoTO)
    {
        parent::__construct($emailTO);
        $this->julgamentoRecursoPedidoSubstituicaoTO = $julgamentoRecursoPedidoSubstituicaoTO;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.julgamentoFinal.cadastrado')->with([
            'decisao' => $this->julgamentoRecursoPedidoSubstituicaoTO->getStatusJulgamentoFinal()->getDescricao(),
            'descricao' => $this->julgamentoRecursoPedidoSubstituicaoTO->getDescricao()
        ])->subject($this->email->getAssunto());
    }
}
