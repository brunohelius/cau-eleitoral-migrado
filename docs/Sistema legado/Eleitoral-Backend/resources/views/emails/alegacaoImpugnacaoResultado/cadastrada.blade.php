<div>
    @include('emails.padrao.cabecalho')
    @include('emails.padrao.corpo')
    <div><b>Protocolo:</b> {{ $numeroProtocolo }}</div>
    <div><b>Processo Eleitoral:</b> {{ $anoProcessoEleitoral }}</div>
    <div><b>Tipo de impugnação:</b> {{ $tipoImpugnacao }}</div>
    <div><b>UF:</b> {{ $uf }}</div>
    <div><b>Alegação:</b> {!! $alegacao !!}</div>
    <div><b>Data/Hora:</b> {{ $dataCadastro }}</div>
    @include('emails.padrao.rodape')
</div>
