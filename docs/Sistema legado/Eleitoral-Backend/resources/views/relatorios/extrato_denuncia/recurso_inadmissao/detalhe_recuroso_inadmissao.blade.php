<div class="titulo">Recurso de Inadmissão</div>
@if(!empty($recurso) && !empty($recurso->getId()))
    <div class="sub-titulo">Dados gerais: </div>
    <table class="table">
        <tr>
            <td>
                <fieldset>
                    <legend class="text-nowrap">Data e hora do cadastro</legend>
                    <span class="text-nowrap">{{$recurso->getDataCriacao()->format('d/m/Y')}} às {{ $recurso->getDataCriacao()->format('H:i')}}</span>
                </fieldset>
            </td>
            <td>
                <fieldset>
                    <legend >Usuário</legend>
                    <span class="text-nowrap">{{$recurso->getSolicitante()->getNome()}}</span>
                </fieldset>
            </td>
        </tr>
    </table>

    <br clear="all">
    <div class="sub-titulo">Descrição do recurso de inadmissão:</div>
    <div class="justificado">{!! $recurso->getDescricao() !!}</div>

    @if(!empty($recurso->getArquivos()))
        @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $recurso->getArquivos()])
    @endif
@else
    <div class="sub-titulo">Dados gerais:</div>
    <div align="center">
        NÃO HOUVE APRESENTAÇÃO DE RECURSO DE INADMISSÃO PARA ESTA DENUNCIA
    </div>
@endif
