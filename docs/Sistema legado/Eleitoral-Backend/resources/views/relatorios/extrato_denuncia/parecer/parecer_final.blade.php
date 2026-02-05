@if($idStatus == 4)
    <div class="sub-titulo">Dados do Parecer Final:</div>

    @include('relatorios.extrato_denuncia.detalhamento_julgamento', [
        "tituloJulgamento" => "Voto do Relator", "julgamento" => $parecer->getDescricaoTipoJulgamento(),
        "tituloSentenca" => "Proposta de sansão", "sentenca" => $parecer->getDescricaoTipoSentenca(),
        "tituloSuspencao" => "Período Suspensão", "qtDias" => $parecer->getQuantidadeDias(),
        "valorMulta" => $parecer->getValorPercentual(), "multa" => $parecer->getMulta(),
        "idSentenca" => $parecer->getIdTipoSentencaJulgamento(),
        "idJulgamento" => $parecer->getIdTipoJulgamento()
    ])

    <div class="sub-titulo">Descrição:</div>
    <div class="justificado">{!! $parecer->getDescricao() !!}</div>

    @if($arquivos != null)
        @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $arquivos])
    @endif

@endif