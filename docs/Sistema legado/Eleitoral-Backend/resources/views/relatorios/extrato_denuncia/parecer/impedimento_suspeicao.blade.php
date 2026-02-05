@if($idStatus == 4)
    <div class="sub-titulo">Designação de novo relator:</div>

    <table style="border: 0px; width: 100%;">
            <tr>
                <td style="width: 30%">
                    <fieldset>
                        <legend class="text-nowrap"> Data e hora do cadastro: </legend>
                        @if($impedimento->getDataHora() != null)
                            <span class="text-nowrap">{{ $impedimento->getDataHora()->format('d/m/Y') . " às " . $impedimento->getDataHora()->format('H:i') }}</span>
                        @endif
                    </fieldset>
                </td>
                <td style="width: 30%">
                    <fieldset>
                        <legend class="text-nowrap"> Usuário: </legend>
                        <span class="text-nowrap text-uppercase">{{ $impedimento->getUsuario() }}</span>
                    </fieldset>
                </td>
                <td style="width: 40%">
                    <fieldset>
                        <legend class="text-nowrap"> Relator Substituto: </legend>
                        <span class="text-nowrap text-uppercase">{{ $impedimento->getRelator() }}</span>
                    </fieldset>
                </td>
            </tr>
    </table>

    <div class="sub-titulo">Despacho de designação de relator:</div>
    <div class="justificado">{!! $impedimento->getDespacho() !!}</div>

    @if($impedimento->getDocumentos() != null)
        @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $impedimento->getDocumentos()])
    @endif

@endif