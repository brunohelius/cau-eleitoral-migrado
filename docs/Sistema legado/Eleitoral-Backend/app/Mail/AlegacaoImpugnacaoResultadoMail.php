<?php


namespace App\Mail;


use App\Config\Constants;
use App\To\AlegacaoImpugnacaoResultadoTO;
use App\To\EmailTO;
use App\Util\Utils;
use DoctrineProxies\__CG__\App\Entities\PedidoImpugnacaoResultado;

class AlegacaoImpugnacaoResultadoMail extends AtividadeSecundariaMail
{
    /**
     *
     * @var AlegacaoImpugnacaoResultadoTO
     */
    public $alegacaoTO;

    /**
     * @var string
     */
    public $descricaoUf;

    /**
     * @var string
     */
    public $anoEleitoral;

    /**
     * @var string
     */
    public $numeroProtocolo;

    /**
     * Create a new message instance.
     *
     * @param EmailTO $email
     * @param AlegacaoImpugnacaoResultadoTO $alegacaoTO
     * @param $idCauUF
     */
    public function __construct(
        EmailTO $email,
        AlegacaoImpugnacaoResultadoTO $alegacaoTO,
        $descricaoUf,
        $anoEleitoral,
        $numeroProtocolo
    )
    {
        parent::__construct($email);
        $this->alegacaoTO = $alegacaoTO;
        $this->descricaoUf = $descricaoUf;
        $this->anoEleitoral = $anoEleitoral;
        $this->numeroProtocolo = $numeroProtocolo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.alegacaoImpugnacaoResultado.cadastrada')->with([
            'numeroProtocolo' => $this->numeroProtocolo,
            'dataCadastro' => Utils::getStringFromDate($this->alegacaoTO->getDataCadastro(), 'd/m/Y - H:i'),
            'alegacao' => $this->alegacaoTO->getNarracao(),
            'tipoImpugnacao' => Constants::DESCRICAO_TIPO_IMPUGNACAO_DE_RESULTADO,
            'uf'=> $this->descricaoUf,
            'anoProcessoEleitoral' => $this->anoEleitoral
        ])->subject($this->email->getAssunto());
    }
}