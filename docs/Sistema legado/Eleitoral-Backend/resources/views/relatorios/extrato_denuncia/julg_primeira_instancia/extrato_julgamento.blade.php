<div class="titulo">Julgamento 1ª Instância</div>
<div class="sub-titulo">Dados Gerais:</div>

<table style="border: 0px; width: 100%;">
    <tr>
        <td style="width: 33%">
            <fieldset>
                <legend class="text-nowrap"> Data e hora do cadastro: </legend>
                @if($julgamento->getData() != null)
                    <span class="text-nowrap">{{ $julgamento->getData()->format('d/m/Y') . " às " . $julgamento->getData()->format('H:i') }}</span>
                @endif
            </fieldset>
        </td>
    </tr>
</table>

<div class="sub-titulo">Detalhamento do Julgamento:</div>

@include('relatorios.extrato_denuncia.detalhamento_julgamento', [
    "tituloJulgamento" => "Julgado", "julgamento" => $julgamento->getDescricaoTipoJulgamento(),
    "tituloSentenca" => "Sansão Aplicada", "sentenca" => $julgamento->getDescricaoTipoSentencaJulgamento(),
    "tituloSuspencao" => "Qtde Dias", "qtDias" => $julgamento->getQuantidadeDiasSuspensaoPropaganda(),
    "valorMulta" => $julgamento->getValorPercentualMulta(), "multa" => $julgamento->isMulta(),
    "idSentenca" => $julgamento->getIdTipoSentencaJulgamento(),
    "idJulgamento" => $julgamento->getIdTipoJulgamento()
])

<div class="sub-titulo">Descrição do Julgamento:</div>
<div class="justificado">{!! $julgamento->getDescricaoJulgamento() !!}</div>

@if($julgamento->getDescricaoArquivo() != null)
    @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $julgamento->getDescricaoArquivo()])
@endif

@if($retificacoes != null)
    <div class="sub-titulo">Histórico de Retificação:</div>
    <table class="tabela">
        <tr>
            <th style="width: 50%">Data/Hora</th>
            <th style="width: 50%">Usuário</th>
        </tr>
        <tbody>
        @foreach($retificacoes as $retificacao)
            <tr>
                <td class="pl-5">
                    @if($retificacao->dataHora != null)
                        <span class="text-nowrap">{{ $retificacao->dataHora->format('d/m/Y') . " às " . $retificacao->dataHora->format('H:i') }}</span>
                    @endif
                </td>
                <td class="pl-5">{{ $retificacao->usuario }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif