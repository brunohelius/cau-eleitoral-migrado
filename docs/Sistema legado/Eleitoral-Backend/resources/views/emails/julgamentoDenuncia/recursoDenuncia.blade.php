<div>
    @include('emails.padrao.cabecalho')
    @include('emails.padrao.corpo')

    <p>Protocolo: {{ $emailRecursoDenunciaTO->getProtocolo() }}</p>
    <p>Processo Eleitoral: {{ $emailRecursoDenunciaTO->getProcessoEleitoral() }}</p>
    <p>Tipo de denúncia: {{ $emailRecursoDenunciaTO->getTipoDenuncia() }}</p>

    @if ($emailRecursoDenunciaTO->getIdTipoDenuncia() === 1)

        <p>Nº Chapa: {{ $emailRecursoDenunciaTO->getNumeroChapa() }}</p>

    @elseif ($emailRecursoDenunciaTO->getIdTipoDenuncia() === 2)

        <p>Nome do Denunciado: {{ $emailRecursoDenunciaTO->getNomeDenunciado() }}</p>
        <p>Nº Chapa: {{ $emailRecursoDenunciaTO->getNumeroChapa() }}</p>

    @elseif ($emailRecursoDenunciaTO->getIdTipoDenuncia() === 3)

        <p>Nome do Denunciado: {{ $emailRecursoDenunciaTO->getNomeDenunciado() }}</p>

    @endif

    <p>UF: {{ $emailRecursoDenunciaTO->getUF() }}</p>

    @if ($isCadastro)
        <p>Responsável pelo cadastro: {{ $responsavelCadastro }}</p>
        <p>Recurso ou Reconsideração: {!! $emailRecursoDenunciaTO->getRecursoDenuncia()->getDescricaoRecurso() !!}</p>
    @endif

    @include('emails.padrao.rodape')
</div>
