<div>
    @include('emails.padrao.cabecalho')
    @include('emails.padrao.corpo')
    <div><b>Protocolo:</b> {{ $numeroProtocolo }}</div>
    <div><b>Contrarrazão:</b> {!! $contrarrazaoTO->getDescricao() !!}</div>
    <div><b>Data/Hora:</b> {{ $dataCadastro }}</div>
    @include('emails.padrao.rodape')
</div>
