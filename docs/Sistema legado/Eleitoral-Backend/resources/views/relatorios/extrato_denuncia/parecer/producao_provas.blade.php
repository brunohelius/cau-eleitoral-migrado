@if($detalhe->getIdStatus() == 2)
    <div class="sub-titulo">Encaminhamento transcorrido:</div>
    <div class="centralizado">NÃO HOUVE INSERÇÃO DE PROVAS PELO DESTINATÁRIO.</div>
@elseif($detalhe->getIdStatus() == 3)
    <div class="sub-titulo">Encaminhamento fechado(Justificativa):</div>
    <div class="justificado">{!! $detalhe->getJustificativa() !!}</div>
@elseif($detalhe->getIdStatus() == 4)
    <div class="sub-titulo">Provas inseridas:</div>

    <table style="border: 0px; width: 100%;">
        <tr>
            <td style="width: 50%">
                <fieldset>
                    <legend class="text-nowrap"> Data e hora do cadastro: </legend>
                    @if($detalhe->getProducaoProvas()->getDataProva() != null)
                        <span class="text-nowrap">{{ $detalhe->getProducaoProvas()->getDataProva()->format('d/m/Y') . " às " . $detalhe->getProducaoProvas()->getDataProva()->format('H:i') }}</span>
                    @endif
                </fieldset>
            </td>
            <td style="width: 50%">
                <fieldset>
                    <legend class="text-nowrap"> Usuário: </legend>
                    <span class="text-nowrap text-uppercase">{{ $detalhe->getProducaoProvas()->getDestinatario() }}</span>
                </fieldset>
            </td>
        </tr>
    </table>

    <div class="sub-titulo">Descrição das provas apresentadas:</div>
    <div class="justificado">{!! $detalhe->getProducaoProvas()->getDescricaoProvasApresentadas() !!}</div>

    @if($detalhe->getProducaoProvas()->getDescricaoArquivo() != null)
        @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $detalhe->getProducaoProvas()->getDescricaoArquivo()])
    @endif

@endif