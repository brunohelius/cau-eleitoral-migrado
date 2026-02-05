<div class="titulo">Recurso do {{ $recurso->getTipoRecurso() === \App\Config\Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE ? 'Denunciante' : 'Denunciado' }}</div>

@if($recurso->getId() == null && !$recurso->isPrazoRecurso())
    <div class="sub-titulo text-nowrap">Aviso:</div>
    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <td align="center">
                NÃO HOUVE APRESENTAÇÃO DE RECURSO OU RECONSIDERAÇÃO PELO {{ $recurso->getTipoRecurso() === \App\Config\Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE ? 'DENUNCIANTE' : 'DENUNCIADO' }}.
            </td>
        </tr>
    </table>
@endif
@if($recurso->getId() != null)
    <div class="sub-titulo">Dados gerais:</div>
    <table style="border: 0px; width: 100%;">
        <tr>
            <td style="width: 30%">
                <fieldset>
                    <legend class="text-nowrap"> Data e hora do cadastro: </legend>
                    @if($recurso->getData() != null)
                        <span class="text-nowrap">{{ $recurso->getData()->format('d/m/Y') . " às " . $recurso->getData()->format('H:i') }}</span>
                    @endif
                </fieldset>
            </td>
            <td style="width: 40%">
                <fieldset>
                    <legend class="text-nowrap"> Usuário: </legend>
                    <span class="text-nowrap text-uppercase">{{ $recurso->getResponsavel() }}</span>
                </fieldset>
            </td>
        </tr>
    </table>

    <div class="sub-titulo text-nowrap">Descrição do Recurso (Eleição de representante CAU/UF e CAU/BR) ou Reconsideração (Eleição de representante de IES):</div>
    <div class="justificado">{!! $recurso->getDescricaoRecurso() !!}</div>

    @if($recurso->getArquivos() != null)
        @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $recurso->getArquivos()])
    @endif

    @if($recurso->getContrarrazao() != null)
        @include('relatorios.extrato_denuncia.recurso_denuncia.contrarrazao', ["recurso" => $recurso])
    @endif
@endif
