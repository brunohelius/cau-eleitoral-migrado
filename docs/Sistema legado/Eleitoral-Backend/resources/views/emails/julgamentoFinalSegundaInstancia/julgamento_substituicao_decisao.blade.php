<div>
    @include('emails.padrao.cabecalho')
    @include('emails.padrao.corpo')
    <p><b>Julgamento:</b> Substituição</p>
    <p><b>Decisão:</b> {{ $decisao }}</p>
    <p><b>Descrição:</b> {!! $descricao !!}</p>
    @include('emails.padrao.rodape')
</div>
