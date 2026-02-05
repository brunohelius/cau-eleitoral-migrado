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
    <p>Julgamento do Denunciante: {!!  $emailJulgamentoDenunciaTO->getJulgamentoRecursoDenuncia()->getDescricaoTipoJulgamentoDenunciante() !!}</p>
    <p>Julgamento do Denunciado: {!!  $emailJulgamentoDenunciaTO->getJulgamentoRecursoDenuncia()->getDescricaoTipoJulgamentoDenunciado() !!}</p>

    @if ($emailJulgamentoDenunciaTO->getJulgamentoRecursoDenuncia()->getIdTipoJulgamentoDenunciado() === 1 ||
         $emailJulgamentoDenunciaTO->getJulgamentoRecursoDenuncia()->getIdTipoJulgamentoDenunciante() === 1)

        <p>Julgamento da Comissão: {{ $emailJulgamentoDenunciaTO->getJulgamentoRecursoDenuncia()->getDescricaoTipoSentencaJulgamento() }}</p>

        @if ($emailJulgamentoDenunciaTO->getJulgamentoRecursoDenuncia()->getIdTipoSentencaJulgamento() === 4 ||
                $emailJulgamentoDenunciaTO->getJulgamentoRecursoDenuncia()->isMulta())
            <p>Valor percentual da multa sobe a anuidade: {{ $emailJulgamentoDenunciaTO->getJulgamentoRecursoDenuncia()->getValorPercentualMulta() }}</p>
        @endif

        @if ($emailJulgamentoDenunciaTO->getJulgamentoRecursoDenuncia()->getIdTipoSentencaJulgamento() === 2)
            <p>Qtde Dias: {{ $emailJulgamentoDenunciaTO->getJulgamentoRecursoDenuncia()->getQuantidadeDiasSuspensaoPropaganda() }}</p>
        @endif
    @endif

    <p>Descrição do Julgamento: {!! $emailJulgamentoDenunciaTO->getJulgamentoRecursoDenuncia()->getDescricaoJulgamento() !!}</p>


    @include('emails.padrao.rodape')
</div>
