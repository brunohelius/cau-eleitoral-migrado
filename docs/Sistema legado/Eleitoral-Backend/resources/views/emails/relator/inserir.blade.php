<div>
    @include('emails.padrao.cabecalho')
    @include('emails.padrao.corpo')

    <p>Protocolo: {{ $emailEncaminhamentoAlegacaoFinalTO->getProtocolo() }}</p>
    <p>Processo Eleitoral: {{ $emailEncaminhamentoAlegacaoFinalTO->getProcessoEleitoral() }}</p>
    <p>Tipo de denúncia: {{ $emailEncaminhamentoAlegacaoFinalTO->getTipoDenuncia() }}</p>

    @if ($emailEncaminhamentoAlegacaoFinalTO->getIdTipoDenuncia() === 1)

        <p>Nº Chapa: {{ $emailEncaminhamentoAlegacaoFinalTO->getNumeroChapa() }}</p>

    @elseif ($emailEncaminhamentoAlegacaoFinalTO->getIdTipoDenuncia() === 2)

        <p>Nome do Denunciado: {{ $emailEncaminhamentoAlegacaoFinalTO->getNomeDenunciado() }}</p>
        <p>Nº Chapa: {{ $emailEncaminhamentoAlegacaoFinalTO->getNumeroChapa() }}</p>

    @elseif ($emailEncaminhamentoAlegacaoFinalTO->getIdTipoDenuncia() === 3)

        <p>Nome do Denunciado: {{ $emailEncaminhamentoAlegacaoFinalTO->getNomeDenunciado() }}</p>

    @endif

    <p>UF: {{ $emailEncaminhamentoAlegacaoFinalTO->getUF() }}</p>
    <p>Status da denúncia: {{ $emailEncaminhamentoAlegacaoFinalTO->getStatusDenuncia() }}</p>
    <p>Relator: {{ $emailEncaminhamentoAlegacaoFinalTO->getRelatorAtual() }}</p>

    @include('emails.padrao.rodape')
</div>
