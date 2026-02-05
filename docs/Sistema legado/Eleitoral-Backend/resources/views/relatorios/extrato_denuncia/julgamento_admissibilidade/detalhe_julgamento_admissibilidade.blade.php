<div class="titulo">Julgamento de Admissibilidade</div>
<div class="sub-titulo">Dados gerais:</div>
<table class="table">
    <tr>
        <td>
            <fieldset>
                <legend >Juizo de Admissibilidade</legend>
                <span class="text-nowrap">{{$julgamento->getJulgamento()}}</span>
            </fieldset>
        </td>
        <td>
            <fieldset>
                <legend class="text-nowrap">Data e hora do cadastro</legend>
                <span class="text-nowrap">{{$julgamento->getDataCriacao()->format('d/m/Y')}} às {{ $julgamento->getDataCriacao()->format('H:i')}}</span>
            </fieldset>
        </td>
    </tr>
</table>

<br clear="all">
<div class="sub-titulo">Julgamento de admissibilidade:</div>
<div class="justificado">{!! $julgamento->getDescricao() !!}</div>

@if(!empty($julgamento->getArquivos()))
    @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $julgamento->getArquivos()])
@endif
