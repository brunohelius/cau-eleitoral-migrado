<div>
    @include('emails.padrao.cabecalho')
    @include('emails.padrao.corpo')

    <p>Julgamento: {{ $julgamentoTo->julgamento }}</p>
    <p>Protocolo: {{ $julgamentoTo->protocolo }}</p>
    <p>Processo Eleitoral: {{ $julgamentoTo->processoEleitoral }}</p>
    <p>Tipo de denúncia: {{ $julgamentoTo->tipoDenuncia }}</p>
    @if ($julgamentoTo->nomeDenunciado)
        <p>Nome do Denunciado: {{ $julgamentoTo->nomeDenunciado }}</p>
    @endif
    @if ($julgamentoTo->numeroChapa)
        <p>Nº Chapa: {{ $julgamentoTo->numeroChapa }}</p>
    @endif
    <p>UF: {{ $julgamentoTo->uf }}</p>

    <p>Narração dos fatos:</p>
    <p>{!! $julgamentoTo->narracaoFatos !!}</p>
    @if ($julgamentoTo->testemunhas)
        <p>Testemunhas:</p>
        @foreach($julgamentoTo->testemunhas as $testemunha)
            <p>{{$testemunha}}</p>
        @endforeach
    @else
        <p>Não há testemunhas</p>
    @endif
    <p>Situação: {{ $julgamentoTo->statusDenuncia }}</p>

    @include('emails.padrao.rodape')
</div>
