<?php


namespace App\Mail;


use App\Config\Constants;
use App\Entities\JulgamentoRecursoImpugResultado;
use App\To\EmailTO;
use App\Util\Utils;

class JulgamentoSegundaInstanciaImpugnacaoResultadoMail extends AtividadeSecundariaMail
{

    /**
     * @var JulgamentoRecursoImpugResultado
     */
    private $julgamentoImpugnacaoResultado;
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
     * @var boolean
     */
    private $isIES;

    /**
     * Create a new message instance.
     * @param EmailTO $email
     */
    public function __construct(
        $isIES,
        EmailTO $emailTO,
        $julgamentoImpugnacaoResultado ,
        $protocolo,
        $descricaoEleicao,
        $descricaoUf
    ) {
        parent::__construct($emailTO);
        $this->julgamentoImpugnacaoResultado = $julgamentoImpugnacaoResultado;
        $this->protocolo = $protocolo;
        $this->descricaoEleicao = $descricaoEleicao;
        $this->descricaoUf = $descricaoUf;
        $this->isIES = $isIES;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->view('emails.julgamentoRecursoImpugnacaoResultado.cadastrada')->with([
            'protocolo' => $this->protocolo,
            'processoEleitoral' => $this->descricaoEleicao,
            'tipoImpugnacao' => 'Impugnação de Resultado',
            'uf' => $this->descricaoUf,
            'descricao' => $this->julgamentoImpugnacaoResultado->getDescricao()
        ])->subject($this->email->getAssunto());
    }
}