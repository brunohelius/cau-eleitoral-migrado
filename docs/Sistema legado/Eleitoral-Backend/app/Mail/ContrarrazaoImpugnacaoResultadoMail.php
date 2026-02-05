<?php

namespace App\Mail;

use App\Config\Constants;
use App\To\ContrarrazaoRecursoImpugnacaoResultadoTO;
use App\To\EleicaoTO;
use App\To\EmailTO;
use App\To\ImpugnacaoResultadoTO;
use App\To\JulgamentoFinalTO;

class ContrarrazaoImpugnacaoResultadoMail extends AtividadeSecundariaMail
{

    /**
     * @var ContrarrazaoRecursoImpugnacaoResultadoTO
     */
    private $contrarrazaoRecursoImpugnacaoResultadoTO;

    /**
     * @var string
     */
    private $descricaoEleicao;

    /**
     * @var string
     */
    private $descricaoUf;

    /**
     * @var string
     */
    private $protocolo;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     * @param ContrarrazaoRecursoImpugnacaoResultadoTO $contrarrazaoTO
     * @param $descricaoEleicao
     * @param boolean $isIES
     */
    public function __construct(EmailTO $emailTO, $protocolo, $contrarrazaoTO, $descricaoEleicao, $descricaoUf)
    {
        parent::__construct($emailTO);
        $this->protocolo = $protocolo;
        $this->contrarrazaoRecursoImpugnacaoResultadoTO = $contrarrazaoTO;
        $this->descricaoEleicao = $descricaoEleicao;
        $this->descricaoUf = $descricaoUf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.contrarrazaoImpugnacaoResultado.cadastro')->with([
            'protocolo' => $this->protocolo,
            'processoEleitoral' => $this->descricaoEleicao,
            'tipoImpugnacao' => 'Impugnação de Resultado',
            'uf' => $this->descricaoUf,
            'descricao' => $this->contrarrazaoRecursoImpugnacaoResultadoTO->getDescricao()
        ])->subject($this->email->getAssunto());
    }
}
