<div class="titulo">Julgamento do Recurso de Inadmissão</div>
    <div class="sub-titulo">Dados gerais:</div>

    <table style="border: 0px; width: 100%;">
        <tr>
            <td style="width: 50%">
                <fieldset>
                    <legend class="text-nowrap"> Julgamento do Recurso de Inadmissão: </legend>
                    <span class="text-nowrap text-uppercase">
                        {{ $julgamento->getParecer()->getId() == 1 ? "PROVIMENTO" : "IMPROVIMENTO" }}
                    </span>
                </fieldset>
            </td>
            <td style="width: 50%">
                <fieldset>
                    <legend class="text-nowrap"> Data e hora do cadastro: </legend>
                    @if($julgamento->getDataCriacao() != null)
                        <span class="text-nowrap">{{ $julgamento->getDataCriacao()->format('d/m/Y') . " às " . $julgamento->getDataCriacao()->format('H:i') }}</span>
                    @endif
                </fieldset>
            </td>
        </tr>
    </table>
    <br clear="all" />
    <div class="sub-titulo">Julgamento do Recurso de Inadmissão:</div>
    <div class="justificado">{!! $julgamento->getDescricao() !!}</div>

    @if($julgamento->getDescricaoArquivo() != null)
        @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $julgamento->getDescricaoArquivo()])
    @endif
