<div class="titulo">Dados do orgão</div>
<fieldset>
    <legend>Nome / Nome Fantasia:</legend>
    <span class="text-nowrap">{{$documentoDenuncia->getFilialTO()->getDescricao()}}</span>
</fieldset>

<fieldset>
    <legend>Endereço:</legend>
    <span class="text-nowrap">{{$documentoDenuncia->getFilialTO()->getEndereco()}}</span>
</fieldset>

<div class="titulo">Acompanhar Denúncia</div>
<div class="sub-titulo">Informações do Denunciante</div>

<table class="table">
    <tr>
        <td>
            <fieldset>
                <legend >Nome Completo</legend>
                <span class="text-nowrap">{{$documentoDenuncia->getDenuncia()->getDenunciante()->getProfissional()->getNome()}}</span>
            </fieldset>
        </td>
        <td>
            <fieldset>
                <legend>Registro</legend>
                <span class="text-nowrap">{{\App\Util\Utils::getRegistroNacionalFormatado($documentoDenuncia->getDenuncia()->getDenunciante()->getProfissional()->getRegistroNacional())}}</span>
            </fieldset>
        </td>
    </tr>
    <tr>
        <td>
            <fieldset>
                <legend>E-mail</legend>
                <span class="text-nowrap">{{$documentoDenuncia->getDenuncia()->getDenunciante()->getProfissional()->getPessoa()->getEmail()}}</span>
            </fieldset>
        </td>
        <td>
            <fieldset>
                <legend>HOUVE PEDIDO DE SIGILO?</legend>
                @if($documentoDenuncia->getDenuncia()->isSigilosa())
                    <span class="text-nowrap">SIM</span>
                @else
                    <span class="text-nowrap">NÃO</span>
                @endif
            </fieldset>
        </td>
    </tr>
</table>

<div class="sub-titulo">Informações do Denunciado</div>

<table class="table">
    <tr>
        <td>
            <fieldset>
                <legend>UF</legend>
                <span class="text-nowrap">{{$documentoDenuncia->getDenuncia()->getFilial()->getPrefixo()}}</span>
            </fieldset>
        </td>
        @if($documentoDenuncia->getDenuncia()->getTipoDenuncia()->getId() != \App\Config\Constants::TIPO_OUTROS)
            <td>
                <fieldset>
                    @if($documentoDenuncia->getDenuncia()->getTipoDenuncia()->getId() == \App\Config\Constants::TIPO_CHAPA)
                        <legend>Chapa</legend>
                    @else
                        <legend>Nome Completo</legend>
                    @endif
                    <span class="text-nowrap">{{$documentoDenuncia->getDenuncia()->getDenunciado()}}</span>
                </fieldset>
            </td>
        @endif
    </tr>
</table>

<div class="sub-titulo">Dados Gerais</div>

<table class="table">
    <tr>
        <td>
            <fieldset>
                <legend>Tipo de Denúncia</legend>
                <span class="text-nowrap">{{$documentoDenuncia->getDenuncia()->getTipoDenuncia()->getDescricao()}}</span>
            </fieldset>
        </td>
        <td>
            <fieldset>
                <legend>Data e hora do cadastro</legend>
                <span class="text-nowrap">{{$documentoDenuncia->getDenuncia()->getDataHora()->format('d/m/Y')." às ".$documentoDenuncia->getDenuncia()->getDataHora()->format('H:i')}}</span>
            </fieldset>
        </td>
    </tr>
</table>

<br clear="all" />
<div class="sub-titulo">Narração dos Fatos</div>
<div >
    {!! $documentoDenuncia->getDenuncia()->getNarracaoDosFatos() !!}
</div>

@if(!empty($documentoDenuncia->getDenuncia()->getDocumentos()))
    @include('relatorios.extrato_denuncia.descricao_arquivo', ["documentos" => $documentoDenuncia->getDenuncia()->getDocumentos()])
@endif

<div class="page-break"></div>
<div class="sub-titulo">Testemunhas</div>

@if(empty($documentoDenuncia->getDenuncia()->getTestemunhas()))
    <div class="sub-titulo">Aviso:</div>
    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <td align="center">
                NÃO HOUVE CADASTRO DE TESTEMUNHAS.
            </td>
        </tr>
    </table>
@elseif(!empty($documentoDenuncia->getDenuncia()->getTestemunhas()))
    <table class="tabela">
        <tr>
            <th>Nome Completo</th>
            <th>Telefone</th>
            <th>E-mail</th>
        </tr>
        @foreach($documentoDenuncia->getDenuncia()->getTestemunhas() as $testemunha)
            <tr>
                <td>{{$testemunha->getNome()}}</td>
                <td>{{$testemunha->getTelefone()}}</td>
                <td>{{$testemunha->getEmail()}}</td>
            </tr>
        @endforeach
    </table>
@endif

