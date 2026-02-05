<div>
    @include('emails.padrao.cabecalho')
    @include('emails.padrao.corpo')

    <p>Protocolo: {{ $emailJulgamentoDenunciaTO->getProtocolo() }}</p>
    <p>Processo Eleitoral: {{ $emailJulgamentoDenunciaTO->getProcessoEleitoral() }}</p>
    <p>Tipo de denúncia: {{ $emailJulgamentoDenunciaTO->getTipoDenuncia() }}</p>

    @if ($emailJulgamentoDenunciaTO->getIdTipoDenuncia() === 1)

        <p>Nº Chapa: {{ $emailJulgamentoDenunciaTO->getNumeroChapa() }}</p>

    @elseif ($emailJulgamentoDenunciaTO->getIdTipoDenuncia() === 2)

        <p>Nome do Denunciado: {{ $emailJulgamentoDenunciaTO->getNomeDenunciado() }}</p>
        <p>Nº Chapa: {{ $emailJulgamentoDenunciaTO->getNumeroChapa() }}</p>

    @elseif ($emailJulgamentoDenunciaTO->getIdTipoDenuncia() === 3)

        <p>Nome do Denunciado: {{ $emailJulgamentoDenunciaTO->getNomeDenunciado() }}</p>

    @endif

    <p>UF: {{ $emailJulgamentoDenunciaTO->getUF() }}</p>
    <p>Julgamento: {!! $emailJulgamentoDenunciaTO->getJulgamentoDenuncia()->getDescricaoTipoJulgamento() !!}</p>

    @if ($emailJulgamentoDenunciaTO->getJulgamentoDenuncia()->getIdTipoJulgamento() === 1)
        <p>Julgamento da Comissão: {{ $emailJulgamentoDenunciaTO->getJulgamentoDenuncia()->getDescricaoTipoSentencaJulgamento() }}</p>

        @if ($emailJulgamentoDenunciaTO->getJulgamentoDenuncia()->getIdTipoSentencaJulgamento() === 4 ||
                $emailJulgamentoDenunciaTO->getJulgamentoDenuncia()->isMulta())
            <p>Valor percentual da multa sobe a anuidade: {{ $emailJulgamentoDenunciaTO->getJulgamentoDenuncia()->getValorPercentualMulta() }}</p>
        @endif

        @if ($emailJulgamentoDenunciaTO->getJulgamentoDenuncia()->getIdTipoSentencaJulgamento() === 2)
            <p>Qtde Dias: {{ $emailJulgamentoDenunciaTO->getJulgamentoDenuncia()->getQuantidadeDiasSuspensaoPropaganda() }}</p>
        @endif
    @endif

    <p>Descrição do Julgamento: {!! $emailJulgamentoDenunciaTO->getJulgamentoDenuncia()->getDescricaoJulgamento() !!}</p>


    @include('emails.padrao.rodape')
</div>
