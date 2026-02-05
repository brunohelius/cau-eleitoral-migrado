<div class="titulo">Defesa</div>

@if($defesa->defesa != null)
    <div class="sub-titulo">Dados gerais:</div>

    <table style="border: 0px; width: 100%;">
        <tr>
            <td style="width: 50%">
                <fieldset>
                    <legend class="text-nowrap"> Data e hora do cadastro: </legend>
                    @if($defesa->defesa->getDataDefesa() != null)
                        <span class="text-nowrap">{{ $defesa->defesa->getDataDefesa()->format('d/m/Y') . " às " . $defesa->defesa->getDataDefesa()->format('H:i') }}</span>
                    @endif
                </fieldset>
            </td>
            <td style="width: 50%">
                <fieldset class="text-nowrap">
                    <legend> Usuário: </legend>
                    <span class="text-nowrap text-uppercase">{{ $defesa->defesa->getUsuario() }}</span>
                </fieldset>
            </td>
        </tr>
    </table>

    <div class="sub-titulo">Apresentação da defesa:</div>
    <div class="justificado">{!! $defesa->defesa->getDescricaoDefesa() !!}</div>

    @if($defesa->defesa->getDescricaoArquivo() != null)
        @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $defesa->defesa->getDescricaoArquivo()])
    @endif

@elseif($defesa->isPrazoEncerrado)
    <div class="sub-titulo">Aviso:</div>
    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <td align="center">
                NÃO HOUVE APRESENTAÇÃO DE DEFESA PARA ESTÁ DENÚNCIA.
            </td>
        </tr>
    </table>
@endif
