<?php


namespace App\Mail;


use App\To\EmailTO;
use App\To\RecursoSegundoJulgamentoSubstituicaoTO;

class RecursoSegundoJulgamentoSubstituicaoMail extends AtividadeSecundariaMail
{
    /**
     * @var RecursoSegundoJulgamentoSubstituicaoTO
     */
    public $recursoSegundoJulgamentoSubstituicaoTO;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     * @param RecursoSegundoJulgamentoSubstituicaoTO $recursoSegundoJulgamentoSubstituicaoTO
     */
    public function __construct(EmailTO $emailTO, RecursoSegundoJulgamentoSubstituicaoTO $recursoSegundoJulgamentoSubstituicaoTO)
    {
        parent::__construct($emailTO);
        $this->recursoSegundoJulgamentoSubstituicaoTO = $recursoSegundoJulgamentoSubstituicaoTO;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.recursoSegundoJulgamentoSubstituicao.cadastrado')->with([
            'descricao' => $this->recursoSegundoJulgamentoSubstituicaoTO->getDescricao()
        ])->subject($this->email->getAssunto());
    }
}