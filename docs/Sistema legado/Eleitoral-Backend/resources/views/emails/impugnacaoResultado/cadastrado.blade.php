<div>
    @include('emails.padrao.cabecalho')
    @include('emails.padrao.corpo')
    <p><b>Protocolo:</b> {{ $protocolo }}</p>
    <p><b>Processo Eleitoral:</b> {{ $processoEleitoral }}</p>
    <p><b>Tipo de impugnação:</b> {{ $tipoImpugnacao }}</p>
    <p><b>UF:</b> {{ $uf }}</p>
    <p><b>Descrição da impugnação do resultado:</b> {!! $descricao !!}</p>
    @include('emails.padrao.rodape')
</div>
