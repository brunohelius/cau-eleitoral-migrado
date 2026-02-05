<div>
    @include('emails.padrao.cabecalho')
    @include('emails.padrao.corpo')

    <p>Protocolo: {{ $denunciaAdmitidaTo->protocolo }}</p>
    <p>Processo Eleitoral: {{ $denunciaAdmitidaTo->processoEleitoral }}</p>
    <p>Tipo de denúncia: {{ $denunciaAdmitidaTo->tipoDenuncia }}</p>
    @if ($denunciaAdmitidaTo->nomeDenunciado)
        <p>Nome do Denunciado: {{ $denunciaAdmitidaTo->nomeDenunciado }}</p>
    @endif
    @if ($denunciaAdmitidaTo->numeroChapa)
        <p>Nº Chapa: {{ $denunciaAdmitidaTo->numeroChapa }}</p>
    @endif
    <p>UF: {{ $denunciaAdmitidaTo->uf }}</p>
    <p>{{ $denunciaAdmitidaTo->statusDenuncia }}</p>
    <p>Relator: {{ $denunciaAdmitidaTo->relator }}</p>

    @include('emails.padrao.rodape')
</div>
