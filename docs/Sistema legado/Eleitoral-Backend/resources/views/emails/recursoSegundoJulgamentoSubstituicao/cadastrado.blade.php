<div>
    @include('emails.padrao.cabecalho')
    @include('emails.padrao.corpo')
    <p><b>Descrição:</b> {!! $descricao !!}</p>
    @include('emails.padrao.rodape')
</div>
