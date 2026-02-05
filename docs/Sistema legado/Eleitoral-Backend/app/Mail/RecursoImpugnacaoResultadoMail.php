<?php


namespace App\Mail;


use App\Config\Constants;
use App\Entities\RecursoImpugnacaoResultado;
use App\To\EmailTO;
use App\Util\Utils;

class RecursoImpugnacaoResultadoMail extends AtividadeSecundariaMail
{

    // TODO criar e retornar RecursoImpugnacaoResultadoTO:newInstanceFormEntity
    /**
     * @var RecursoImpugnacaoResultado
     */
    private $recursoImpugnacaoResultado;
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
        $recursoImpugnacaoResultado ,
        $protocolo,
        $descricaoEleicao,
        $descricaoUf
    ) {
        parent::__construct($emailTO);
        $this->recursoImpugnacaoResultado = $recursoImpugnacaoResultado;
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
        $recursoReconsideracao = $this->isIES ? "da Reconsideração" : "do Recurso";

        return $this->view('emails.recursoImpugnacaoResultado.cadastrada')->with([
            'protocolo' => $this->protocolo,
            'processoEleitoral' => $this->descricaoEleicao,
            'tipoImpugnacao' => 'Impugnação de Resultado',
            'uf' => $this->descricaoUf,
            'labelRecurso' => $recursoReconsideracao,
            'descricao' => $this->recursoImpugnacaoResultado->getDescricao()
        ])->subject($this->email->getAssunto());
    }
}