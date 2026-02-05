<?php

namespace App\Mail;

use App\Config\Constants;
use App\To\EmailTO;
use App\To\RecursoJulgamentoFinalTO;

class RecursoJulgamentoFinalCadastradoMail extends AtividadeSecundariaMail
{
    /**
     * @var RecursoJulgamentoFinalTO
     */
    public $recursoJulgamentoFinalTO;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     * @param RecursoJulgamentoFinalTO $recursoJulgamentoFinalTO
     */
    public function __construct(EmailTO $emailTO, RecursoJulgamentoFinalTO $recursoJulgamentoFinalTO)
    {
        parent::__construct($emailTO);
        $this->recursoJulgamentoFinalTO = $recursoJulgamentoFinalTO;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.recursoJulgamentoFinal.cadastrado')->with([
            'indicacoes' => $this->recursoJulgamentoFinalTO->getIndicacoes(),
            'descricao' => $this->recursoJulgamentoFinalTO->getDescricao(),
            'descricaoSemMembro' => Constants::DS_LABEL_SEM_INCLUSAO_MEMBRO,
            'descricaoConselheiroFederal' => Constants::DS_REPRESENTACAO_FEDERAL
        ])->subject($this->email->getAssunto());
    }
}
