<div class="titulo">Julgamento 2ª Instância</div>
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

@include('relatorios.extrato_denuncia.julg_segunda_instancia.detalhamento_julgamento', [
    "tituloJulgamentoDenunciante" => "Julgamento do Recurso do Denunciante", "julgamentoDenunciante" => $julgamento->getDescricaoTipoJulgamentoDenunciante(),
    "tituloJulgamentoDenunciado" => "Julgamento do Recurso do Denunciado", "julgamentoDenunciado" => $julgamento->getDescricaoTipoJulgamentoDenunciado(),
    "sancao" => "Houve Sanção?", "sentenca" => $julgamento->getDescricaoSancao(), "isSancao" => $julgamento->isSancao(),
    "tituloSentenca" => "Sanção Aplicada", "sancaoAplicada" => $julgamento->getDescricaoTipoSentencaJulgamento(),
    "tituloSuspencao" => "Qtde Dias", "qtDias" => $julgamento->getQuantidadeDiasSuspensaoPropaganda(),
    "valorMulta" => $julgamento->getValorPercentualMulta(), "multa" => $julgamento->isMulta(),
    "idSentenca" => $julgamento->getIdTipoSentencaJulgamento(),
    "idJulgamentoDenunciado" => $julgamento->getIdTipoJulgamentoDenunciado(),
    "idJulgamentoDenunciante" => $julgamento->getIdTipoJulgamentoDenunciante()
])

<div class="sub-titulo text-nowrap">Descrição do Julgamento:</div>
<div class="justificado">{!! $julgamento->getDescricaoJulgamento() !!}</div>

@if($julgamento->getArquivosJulgamentoRecursoDenuncia() != null)
    @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $julgamento->getArquivosJulgamentoRecursoDenuncia()])
@endif

@if($retificacoes != null)
    @include('relatorios.extrato_denuncia.retificacoes_julgamento', ["retificacoes" => $retificacoes])
@endif