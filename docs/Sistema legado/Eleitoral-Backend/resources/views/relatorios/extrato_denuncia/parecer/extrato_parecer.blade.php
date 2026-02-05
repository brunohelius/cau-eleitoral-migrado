<div class="titulo">Parecer</div>
<div class="sub-titulo">Encaminhamentos:</div>
<table class="tabela fs-10">
    <tr>
        <th>Nº</th>
        <th>Etapas</th>
        <th>Relator</th>
        <th>Data Solicitação</th>
        <th>Envio/Agenda</th>
        <th>Destinatário</th>
        <th>Status</th>
    </tr>
    <tbody>
        @foreach($parecer->encaminhamentos as $encaminhamento)
            <tr>
                <td class="text-center">{{ $encaminhamento->getNumero() }}</td>
                <td class="text-center">{{ $encaminhamento->getTipoEncaminhamento() }}</td>
                <td class="pl-5">{{ $encaminhamento->getRelator() }}</td>
                <td class="text-center">
                    @if($encaminhamento->getDataEncaminhamento() != null)
                        {{ $encaminhamento->getDataEncaminhamento()->format("d/m/Y") }} às {{ $encaminhamento->getDataEncaminhamento()->format("H:i") }}
                    @endif
                </td>
                <td class="text-center">
                    @if($encaminhamento->getPrazoEnvio() != null)
                        {{ $encaminhamento->getPrazoEnvio()->format("d/m/Y") }} às {{ $encaminhamento->getPrazoEnvio()->format("H:i") }}
                    @endif
                </td>
                <td class="pl-5">
                    @if($encaminhamento->getDestinatarios() != '')
                        @foreach($encaminhamento->getDestinatarios() as $destinatario)
                            {{ $destinatario }}
                        @endforeach
                    @endif
                </td>
                <td class="text-center">{{ $encaminhamento->getStatus() }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@foreach($parecer->detalhes as $detalhe)
    <div class="page-break"></div>
    @include('relatorios.extrato_denuncia.parecer.dados_parecer', ["detalhe" => $detalhe])

    @switch($detalhe->getIdTipoEncaminhamento())
        @case(1)
            @if($detalhe->getImpedimentoSuspeicao() != null)
                @include('relatorios.extrato_denuncia.parecer.impedimento_suspeicao', ["impedimento" => $detalhe->getImpedimentoSuspeicao(), "idStatus" => $detalhe->getIdStatus()])
            @endif
        @break

        @case(2)
            @include('relatorios.extrato_denuncia.parecer.producao_provas', ["detalhe" => $detalhe])
        @break

        @case(3)
            @if($detalhe->getAudienciaInstrucao() != null)
                @include('relatorios.extrato_denuncia.parecer.audiencia_instrucao', [
                    "audiencia" => $detalhe->getAudienciaInstrucao(), "idStatus" => $detalhe->getIdStatus(), "justificativa" => $detalhe->getJustificativa()
                ])
            @endif
        @break

        @case(4)
            @if($detalhe->getAlegacaoFinal() != null)
                @include('relatorios.extrato_denuncia.parecer.alegacao_final', ["alegacao" => $detalhe->getAlegacaoFinal(), "idStatus" => $detalhe->getIdStatus()])
            @endif
        @break

        @case(5)
            @if($detalhe->getParecerFinal() != null)
                @include('relatorios.extrato_denuncia.parecer.parecer_final', [
                    "parecer" => $detalhe->getParecerFinal(), "idStatus" => $detalhe->getIdStatus(), "arquivos" => $detalhe->getDocumentos()
                ])
            @endif
        @break
    @endswitch
@endforeach