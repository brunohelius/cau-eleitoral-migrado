<div class="titulo">Encaminhamento Nº {{ $detalhe->getNumero() }}</div>

<div class="sub-titulo">Dados gerais do encaminhamento:</div>

<table style="border: 0px; width: 100%;">
    @if($detalhe->getIdTipoEncaminhamento() != 3)
        <tr>
            <td style="width: 30%">
                <fieldset>
                    <legend class="text-nowrap"> Data e hora do cadastro: </legend>
                    @if($detalhe->getDataHora() != null)
                        <span class="text-nowrap">{{ $detalhe->getDataHora()->format('d/m/Y') . " às " . $detalhe->getDataHora()->format('H:i') }}</span>
                    @endif
                </fieldset>
            </td>
            <td style="width: 30%">
                <fieldset>
                    <legend class="text-nowrap"> Encaminhamento: </legend>
                    <span class="text-nowrap text-uppercase">{{ $detalhe->getEncaminhamento() }}</span>
                </fieldset>
            </td>
            <td style="width: 40%">
                <fieldset>
                    <legend class="text-nowrap"> Relator: </legend>
                    <span class="text-nowrap text-uppercase">{{ $detalhe->getRelator() }}</span>
                </fieldset>
            </td>
        </tr>
    @else
        <tr>
            <td style="width: 50%">
                <fieldset>
                    <legend class="text-nowrap"> Data e hora do cadastro: </legend>
                    @if($detalhe->getDataHora() != null)
                        <span class="text-nowrap">{{ $detalhe->getDataHora()->format('d/m/Y') . " às " . $detalhe->getDataHora()->format('H:i') }}</span>
                    @endif
                </fieldset>
            </td>
            <td style="width: 50%">
                <fieldset>
                    <legend class="text-nowrap"> Encaminhamento: </legend>
                    <span class="text-nowrap text-uppercase">{{ $detalhe->getEncaminhamento() }}</span>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td style="width: 50%">
                <fieldset>
                    <legend class="text-nowrap"> Agendamento: </legend>
                    @if($detalhe->getAgendamento() != null)
                        <span class="text-nowrap">{{ $detalhe->getAgendamento()->format('d/m/Y') . " às " . $detalhe->getAgendamento()->format('H:i') }}</span>
                    @endif
                </fieldset>
            </td>
            <td style="width: 50%">
                <fieldset>
                    <legend class="text-nowrap"> Relator: </legend>
                    <span class="text-nowrap text-uppercase">{{ $detalhe->getRelator() }}</span>
                </fieldset>
            </td>
        </tr>
    @endif
</table>

@if($detalhe->getIdTipoEncaminhamento() != 5)

    <div class="sub-titulo">Descrição do encaminhamento:</div>
    <div class="justificado">{!! $detalhe->getDescricao() !!}</div>

    @if($detalhe->getDocumentos() != null)
        @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $detalhe->getDocumentos()])
    @endif
@endif
