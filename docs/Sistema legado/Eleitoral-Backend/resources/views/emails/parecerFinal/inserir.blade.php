<div>
    @include('emails.padrao.cabecalho')
    @include('emails.padrao.corpo')

    <p>Protocolo: {{ $emailParecerFinalTO->getProtocolo() }}</p>
    <p>Processo Eleitoral: {{ $emailParecerFinalTO->getProcessoEleitoral() }}</p>
    <p>Tipo de denúncia: {{ $emailParecerFinalTO->getTipoDenuncia() }}</p>

    @if ($emailParecerFinalTO->getIdTipoDenuncia() === 1)

        <p>Nº Chapa: {{ $emailParecerFinalTO->getNumeroChapa() }}</p>

    @elseif ($emailParecerFinalTO->getIdTipoDenuncia() === 2)

        <p>Nome do Denunciado: {{ $emailParecerFinalTO->getNomeDenunciado() }}</p>
        <p>Nº Chapa: {{ $emailParecerFinalTO->getNumeroChapa() }}</p>

    @elseif ($emailParecerFinalTO->getIdTipoDenuncia() === 3)

        <p>Nome do Denunciado: {{ $emailParecerFinalTO->getNomeDenunciado() }}</p>

    @endif

    <p>UF: {{ $emailParecerFinalTO->getUF() }}</p>
    <p>Status da denúncia: {{ $emailParecerFinalTO->getStatusDenuncia() }}</p>
    <p>Relator: {{ $emailParecerFinalTO->getRelatorAtual() }}</p>
    <p>Parecer Final da denúncia: {!! $emailParecerFinalTO->getParecerFinal()->getDescricao() !!}</p>

    @include('emails.padrao.rodape')
</div>
