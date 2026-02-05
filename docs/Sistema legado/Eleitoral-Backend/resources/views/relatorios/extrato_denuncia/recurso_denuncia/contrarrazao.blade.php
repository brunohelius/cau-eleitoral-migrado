<div class="titulo">Contrarrazão do {{ $recurso->getContrarrazao()->getTpRecurso() === \App\Config\Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE ? 'Denunciado' : 'Denunciante' }}</div>

@if($recurso->getId() != null && $recurso->getContrarrazao() == null && !$recurso->isPrazoContrarrazao())
    <div class="sub-titulo text-nowrap">Aviso:</div>

    <div class="sub-titulo text-nowrap">Aviso:</div>
    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <td align="center">
                NÃO HOUVE CADASTRO DE CONTRARRAZÃO PARA O RECURSO DO {{ $recurso->getContrarrazao()->getTpRecurso() === \App\Config\Constants::TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE ? 'DENUNCIANTE' : 'DENUNCIADO' }}.
            </td>
        </tr>
    </table>
@endif
@if($recurso->getContrarrazao() != null)
    <div class="sub-titulo">Dados gerais:</div>
    <table style="border: 0px; width: 100%;">
        <tr>
            <td style="width: 30%">
                <fieldset>
                    <legend class="text-nowrap"> Data e hora do cadastro: </legend>
                    @if($recurso->getContrarrazao()->getData() != null)
                        <span class="text-nowrap">{{ $recurso->getContrarrazao()->getData()->format('d/m/Y') . " às " . $recurso->getContrarrazao()->getData()->format('H:i') }}</span>
                    @endif
                </fieldset>
            </td>
            <td style="width: 40%">
                <fieldset>
                    <legend class="text-nowrap"> Usuário: </legend>
                    <span class="text-nowrap text-uppercase">{{ $recurso->getContrarrazao()->getResponsavel() }}</span>
                </fieldset>
            </td>
        </tr>
    </table>

    <div class="sub-titulo text-nowrap">Descrição da contrarrazão:</div>
    <div class="justificado">{!! $recurso->getContrarrazao()->getDescricaoRecurso() !!}</div>

    @if($recurso->getContrarrazao()->getArquivosContrarrazao() != null)
        @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $recurso->getContrarrazao()->getArquivosContrarrazao()])
    @endif
@endif