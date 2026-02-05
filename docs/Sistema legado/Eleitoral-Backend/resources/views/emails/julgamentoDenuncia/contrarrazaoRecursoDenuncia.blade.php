<div>
    @include('emails.padrao.cabecalho')
    @include('emails.padrao.corpo')

    <p>Protocolo: {{ $emailContrarrazaoTO->getProtocolo() }}</p>
    <p>Processo Eleitoral: {{ $emailContrarrazaoTO->getProcessoEleitoral() }}</p>
    <p>Tipo de denúncia: {{ $emailContrarrazaoTO->getTipoDenuncia() }}</p>

    @if ($emailContrarrazaoTO->getIdTipoDenuncia() === 1)

        <p>Nº Chapa: {{ $emailContrarrazaoTO->getNumeroChapa() }}</p>

    @elseif ($emailContrarrazaoTO->getIdTipoDenuncia() === 2)

        <p>Nome do Denunciado: {{ $emailContrarrazaoTO->getNomeDenunciado() }}</p>
        <p>Nº Chapa: {{ $emailContrarrazaoTO->getNumeroChapa() }}</p>

    @elseif ($emailContrarrazaoTO->getIdTipoDenuncia() === 3)

        <p>Nome do Denunciado: {{ $emailContrarrazaoTO->getNomeDenunciado() }}</p>

    @endif

    <p>UF: {{ $emailContrarrazaoTO->getUF() }}</p>

    @if ($isCadastro)
        <p>Responsável pelo cadastro: {{ $responsavelCadastro }}</p>
        <p>Descrição da Contrarrazão: {!! $emailContrarrazaoTO->getContrarrazaoRecursoDenuncia()->getDescricaoRecurso() !!}</p>
    @else
        @if ($emailContrarrazaoTO->getRecursoDenuncia()->getTipoRecurso() == 1)
            <p>Não houve cadastro de contrarrazão para o recurso do denunciante</p>
        @else
            <p>Não houve cadastro de contrarrazão para o recurso do denunciado</p>
        @endif
    @endif

    @include('emails.padrao.rodape')
</div>
