@if($idStatus == 3)
    <div class="sub-titulo">Encaminhamento fechado(Justificativa):</div>
    <div class="justificado">{!! $justificativa !!}</div>
@elseif($idStatus == 4)
    <div class="sub-titulo">Audiência de instrução registrada:</div>

    <table style="border: 0px; width: 100%;">
        <tr>
            <td style="width: 50%">
                <fieldset>
                    <legend class="text-nowrap"> Data e hora do cadastro: </legend>
                    @if($audiencia->getDataCadastro() != null)
                        <span class="text-nowrap">{{ $audiencia->getDataCadastro()->format('d/m/Y') . " às " . $audiencia->getDataCadastro()->format('H:i') }}</span>
                    @endif
                </fieldset>
            </td>
            <td style="width: 50%">
                <fieldset>
                    <legend class="text-nowrap"> Data e hora da audiência: </legend>
                    @if($audiencia->getDataAudienciaInstrucao() != null)
                        <span class="text-nowrap">{{ $audiencia->getDataAudienciaInstrucao()->format('d/m/Y') . " às " . $audiencia->getDataAudienciaInstrucao()->format('H:i') }}</span>
                    @endif
                </fieldset>
            </td>
        </tr>
    </table>

    <div class="sub-titulo">Descrição da audiência de instrução:</div>
    <div class="justificado">{!! $audiencia->getDescricaoDenunciaAudienciaInstrucao() !!}</div>

    @if($audiencia->getDescricaoArquivo() != null)
        @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $audiencia->getDescricaoArquivo()])
    @endif

@endif