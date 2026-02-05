<div class="titulo">Análise de Admissibilidade</div>
<div class="sub-titulo">Dados gerais:</div>

<table class="table">
    <tr>
        <td>
            <fieldset>
                <legend >Juizo de Admissibilidade</legend>
                <span class="text-nowrap">ADMITIDA</span>
            </fieldset>
        </td>
        <td>
            <fieldset>
                <legend class="text-nowrap">Data e hora do cadastro</legend>
                <span class="text-nowrap">{{$analiseAdmissibilidade->getDataAdmissao()->format('d/m/Y')." às ".$analiseAdmissibilidade->getDataAdmissao()->format('H:i')}}</span>
            </fieldset>
        </td>
    </tr>
    <tr>
        <td>
            <fieldset>
                <legend>Relator: </legend>
                <span class="text-nowrap">{{$analiseAdmissibilidade->getMembroComissao()->getProfissionalEntity()->getNome()}}</span>
            </fieldset>
        </td>
        <td>
            <fieldset>
                <legend>Coordenador da comissão: </legend>
                <span class="text-nowrap">
                    @if($analiseAdmissibilidade->getCoordenador() != null)
                        {{$analiseAdmissibilidade->getCoordenador()->getProfissionalEntity()->getNome()}}
                    @else
                        -
                    @endif
                </span>
            </fieldset>
        </td>
    </tr>
</table>

<br clear="all">
<div class="sub-titulo">Descrição da Admissão:</div>
<div class="justificado">{!! $analiseAdmissibilidade->getDescricaoDespacho() !!}</div>

@if(!empty($analiseAdmissibilidade->getArquivoDenunciaAdmitida()))
    @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $analiseAdmissibilidade->getArquivoDenunciaAdmitida()])
@endif

@if(!empty($historicoAdmissao))
    <div class="sub-titulo">Histórico de relatores da denúncia: </div>
    <table class="tabela">
        <tr>
            <th>Data/Hora do Cadastro</th>
            <th>Relator</th>
        </tr>
        @foreach($historicoAdmissao as $historico)
            <tr>
                <td>{{$historico->getDataAdmissao()->format('d/m/Y')." às ".$historico->getDataAdmissao()->format('H:i')}}</td>
                <td>{{$historico->getMembroComissao()->getProfissionalEntity()->getNome()}}</td>
            </tr>
        @endforeach
    </table>
@endif
