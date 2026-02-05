<div class="titulo">Análise de Admissibilidade</div>
<div class="sub-titulo">Dados gerais:</div>

<table class="table">
    <tr>
        <td>
            <fieldset>
                <legend >Juizo de Admissibilidade</legend>
                <span class="text-nowrap">INADMITIDA</span>
            </fieldset>
        </td>
        <td>
            <fieldset>
                <legend class="text-nowrap">Data e hora do cadastro</legend>
                <span class="text-nowrap">{{$analiseAdmissibilidade->getDataInadmissao()->format('d/m/Y')}} às {{ $analiseAdmissibilidade->getDataInadmissao()->format('H:i')}}</span>
            </fieldset>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <fieldset>
                <legend>Coordenador da comissão: </legend>
                <span class="text-nowrap">{{$coordenador->getProfissionalEntity()->getNome()}}</span>
            </fieldset>
        </td>
    </tr>
</table>

<br clear="all">
<div class="sub-titulo">Descrição da Inadmissão:</div>
<div class="justificado">{!! $analiseAdmissibilidade->getDescricaoInadmissao() !!}</div>

@if(!empty($analiseAdmissibilidade->getArquivos()))
    @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $analiseAdmissibilidade->getArquivos()])
@endif
