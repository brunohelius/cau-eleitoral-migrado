@if($idStatus == 2)
    <div class="sub-titulo">Encaminhamento transcorrido:</div>
    <div class="centralizado">NÃO HOUVE CADASTRO DE ALEGAÇÕES FINAIS DENTRO DO PRAZO DE ENVIO.</div>
@elseif($idStatus == 4)
    <div class="sub-titulo">Resposta:</div>

    <table style="border: 0px; width: 100%;">
        <tr>
            <td style="width: 50%">
                <fieldset>
                    <legend class="text-nowrap"> Data e hora do cadastro: </legend>
                    @if($alegacao->getDataHora() != null)
                        <span class="text-nowrap">{{ $alegacao->getDataHora()->format('d/m/Y') . " às " . $alegacao->getDataHora()->format('H:i') }}</span>
                    @endif
                </fieldset>
            </td>
            <td style="width: 50%">
                <fieldset>
                    <legend class="text-nowrap"> Usuário: </legend>
                    <span class="text-nowrap text-uppercase">{{ $alegacao->getDestinatario() }}</span>
                </fieldset>
            </td>
        </tr>
    </table>

    <div class="sub-titulo">Alegações finais:</div>
    <div class="justificado">{!! $alegacao->getDescricao() !!}</div>

    @if($alegacao->getDescricaoArquivo() != null)
        @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $alegacao->getDescricaoArquivo()])
    @endif

@endif