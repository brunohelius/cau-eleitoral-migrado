<?php

namespace App\Mail;

use App\Config\Constants;
use App\To\EleicaoTO;
use App\To\EmailTO;
use App\To\ImpugnacaoResultadoTO;
use App\To\JulgamentoFinalTO;

class JulgamentoAlegacaoImpugnacaoResultadoMail extends AtividadeSecundariaMail
{

    /**
     * @var ImpugnacaoResultadoTO
     */
    private $impugnacaoResultadoTO;

    /**
     * @var string
     */
    private $descricaoEleicao;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $emailTO
     * @param ImpugnacaoResultadoTO $impugnacaoResultadoTO
     */
    public function __construct(EmailTO $emailTO, ImpugnacaoResultadoTO $impugnacaoResultadoTO, string $descricaoEleicao)
    {
        parent::__construct($emailTO);
        $this->impugnacaoResultadoTO = $impugnacaoResultadoTO;
        $this->descricaoEleicao = $descricaoEleicao;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $isIES = empty($this->impugnacaoResultadoTO->getCauBR());
        return $this->view('emails.julgamentoAlegacaoImpugnacaoResultado.cadastro')->with([
            'protocolo' => $this->impugnacaoResultadoTO->getNumero(),
            'processoEleitoral' => $this->descricaoEleicao,
            'tipoImpugnacao' => 'Impugnação de Resultado',
            'uf' => $isIES ? Constants::PREFIXO_IES : $this->impugnacaoResultadoTO->getCauBR()->getPrefixo(),
            'descricao' => $this->impugnacaoResultadoTO->getNarracao()
        ])->subject($this->email->getAssunto());
    }
}
