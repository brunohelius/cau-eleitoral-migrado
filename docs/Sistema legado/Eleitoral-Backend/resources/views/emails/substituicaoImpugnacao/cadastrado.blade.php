<div>
    @include('emails.padrao.cabecalho')
    @include('emails.padrao.corpo')
    <p><b>Protocolo:</b> {{ $substituicaoImpugnacaoTO->getPedidoImpugnacao()->getNumeroProtocolo() }}</p>
    <p><b>Substituído/Impugnado:</b> {{ $nomeSubstituido }}</p>
    <p><b>Substituto:</b> {{ $nomeSubstituto }}</p>
    @include('emails.padrao.rodape')
</div>
