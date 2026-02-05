<?php
/*
 * EncaminhamentoAlegacoesFinaisMail.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Mail;

use App\To\EmailTO;
use App\To\EmailEncaminhamentoAlegacaoFinalTO;

class EncaminhamentoAlegacoesFinaisMail extends AtividadeSecundariaMail
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
        return $this->view('emails.alegacoesFinais.encaminhamento')->subject($this->email->getAssunto());
    }
}
