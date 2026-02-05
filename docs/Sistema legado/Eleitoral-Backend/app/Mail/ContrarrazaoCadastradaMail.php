<?php

namespace App\Mail;

use App\To\ContrarrazaoRecursoImpugnacaoTO;
use App\To\EmailTO;
use App\Util\Utils;

class ContrarrazaoCadastradaMail extends AtividadeSecundariaMail
{
    /**
     *
     * @var ContrarrazaoRecursoImpugnacaoTO
     */
    public $contrarrazaoTO;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $email
     * @param ContrarrazaoRecursoImpugnacaoTO $contrarrazaoTO
     */
    public function __construct(EmailTO $email, ContrarrazaoRecursoImpugnacaoTO $contrarrazaoTO)
    {
        parent::__construct($email);
        $this->contrarrazaoTO = $contrarrazaoTO;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.contrarrazao.cadastrada')->with([
            'numeroProtocolo' => $this->contrarrazaoTO->getRecursoImpugnacao()->getJulgamentoImpugnacao()
                ->getPedidoImpugnacao()->getNumeroProtocolo(),
            'dataCadastro' => Utils::getStringFromDate($this->contrarrazaoTO->getDataCadastro(), 'd/m/Y - H:i')
        ])->subject($this->email->getAssunto());
    }
}
