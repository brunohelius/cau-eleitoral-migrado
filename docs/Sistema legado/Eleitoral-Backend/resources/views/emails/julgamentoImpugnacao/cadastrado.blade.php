<div>
    @include('emails.padrao.cabecalho')
    @include('emails.padrao.corpo')
    <p><b>Protocolo:</b> {{ $protocolo }}</p>
    <p><b>Decisão:</b> {{ $decisao }}</p>
    <p><b>Descrição:</b> {!! $descricao !!}</p>
    @include('emails.padrao.rodape')
</div>
