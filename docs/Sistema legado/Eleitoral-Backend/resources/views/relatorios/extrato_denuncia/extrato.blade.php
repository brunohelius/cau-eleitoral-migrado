<html>
<head>
    <meta http-equiv="Content-Type" content="charset=utf-8" />
    <style>
        *, div, p, span {
            font-size: 12px;
            font-family: Arial, sans-serif;
        }

        .titulo {
            width: 100%;
            padding-left: 5px;
            font-weight: bold;
            background: #E6E6E6;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .sub-titulo {
            width: 100%;
            padding-left: 20px;
            font-weight: bold;
            border-bottom: 1px solid black;
            margin-bottom: 10px;
            margin-top: 15px;
        }

        .text-center {
            text-align: center;
        }

        .fs-14 {
            font-size: 14px;
        }

        .fs-10 {
            font-size: 10px;
        }

        .fs-18 {
            font-size: 18px;
        }

        .pl-5 {
            padding-left: 5px;
        }

        .ml-30 {
            margin-left: 30px;
        }

        .fontBold {
            font-weight: bold
        }

        h2 {
            font-size: 12px;
            font-family: Arial, sans-serif;
        }

        @page {
            width: 100%;
            margin: 0cm 0cm;
        }

        body {
            width: 100%;
            margin-top: 3cm;
            margin-left: 1cm;
            margin-right: 1cm;
            margin-bottom: 2cm;
        }

        header {
            position: fixed;
            top: 0cm;
            left: 1cm;
            right: 1cm;
            height: 3cm;
            text-align: center;
            color: #6E6E6E;
        }

        .logo-header {
            position: relative;
            float: left;
            width: 50%;
            height: 3cm;
            top: 1.5cm;
        }

        .descricao-header {
            position: relative;
            width: 50%;
            height: 2.5cm;
            float: right;
            border-left: 1px solid black;
            border-bottom: 1px solid black;
            top: 1.5cm;
        }

        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
        }

        .texto-footer {
            position: relative;
            margin-right: 0.5cm;
            float:right;
            top: 1.5cm;
            font-size: 11px;
            color: #6E6E6E;
        }

        .justificado {
            text-align: justify;
            text-justify: inter-word;
            clear: initial;
        }

        main {
            position: relative;
            top: 2cm;
        }

        .page-break {
            page-break-after: always;
        }

        .tabela {
            border: 1px solid;
            width: 100%;
            border-collapse: collapse;
            clear: initial;
        }

        .tabela tr th {
            background: #DCDCDC;
            border: 1px solid;
        }

        .tabela tr td {
            border: 1px solid;
            padding-left: 5px;
        }

        fieldset {
            border: 1px solid black;
            border-radius: 5px;
            padding-left: 5px;
            padding-top: 12px;
            padding-bottom: 10px;
            width: 100%;
        }

        legend {
            white-space: nowrap;
        }

        .text-uppercase {
            text-transform:uppercase;
        }

        .centralizado {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
        }

        .text-nowrap {
            white-space: nowrap;
        }

        .table {
            width: 100%;
        }

        .table tr td {
            width: 50%;
        }

    </style>
</head>
<body>

<script type="text/php">
    if (isset($pdf)) {
        $text = "Página {PAGE_NUM}/{PAGE_COUNT}";
        $size = 8;
        $font = $fontMetrics->getFont("Arial");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 4;
        $x = ($pdf->get_width() - $width) - 30;
        $y = $pdf->get_height() - 30;
        $color = array(0.43, 0.43, 0.43);
        $pdf->page_text($x, $y, $text, $font, $size, $color);

        $y = 20;
        $pdf->page_text($x, $y, $text, $font, $size, $color);
    }
</script>

<header>
    <div class="logo-header">
        <span class="fontBold fs-18">Conselho de Arquitetura e Urbanismo <br>do Brasil</span><br>
        <span class="fs-14">INFORMAÇÕES DA DENÚNCIA</span>
    </div>
    <div class="descricao-header">
        <span class="fontBold fs-14">Denúncia<br>{{ $documentoDenuncia->getDenuncia()->getNumeroSequencial() }}/{{ $documentoDenuncia->getDenuncia()->getDataHora()->format('Y') }}</span>
    </div>
</header>

<footer>
    <span class="texto-footer">Impresso em: {{ $documentoDenuncia->getData()->format('d/m/Y') }} às {{ $documentoDenuncia->getData()->format('H:i:s') }}
    por: {{ $documentoDenuncia->getUsuario() }}, ip: {{ $documentoDenuncia->getIp() }} - Denúncia:
        {{ $documentoDenuncia->getDenuncia()->getNumeroSequencial() }}/{{ $documentoDenuncia->getDenuncia()->getDataHora()->format('Y') }}</span>
</footer>

<main style="clear: initial">
    @if($abas->hasAcompanharDenuncia())
        @if($quebraPagina) <div class="page-break"></div> @else @php $quebraPagina = 1; @endphp @endif
        @include('relatorios.extrato_denuncia.acompanhar_denuncia.extrato_acompanhar_denuncia', ["documentoDenuncia" => $documentoDenuncia])
    @endif

    @if($abas->hasAnaliseAdmissibilidade())
        @if($quebraPagina) <div class="page-break"></div> @else @php $quebraPagina = 1; @endphp @endif
        @include('relatorios.extrato_denuncia.analise_admissibilidade.analise_admissibilidade',
        ["analiseAdmissibilidade" => $documentoDenuncia->getDenuncia()->getAnaliseAdmissibilidade()])
    @endif

    @if($abas->hasJulgamentoAdmissibilidade())
        @if($quebraPagina) <div class="page-break"></div> @else @php $quebraPagina = 1; @endphp @endif
        @include('relatorios.extrato_denuncia.julgamento_admissibilidade.detalhe_julgamento_admissibilidade',
        ["julgamento" => $documentoDenuncia->getDenuncia()->getJulgamentoAdmissibilidade()])
    @endif

    @if($abas->hasRecursoAdmissibilidade())
        @if($quebraPagina) <div class="page-break"></div> @else @php $quebraPagina = 1; @endphp @endif
        @include('relatorios.extrato_denuncia.recurso_inadmissao.detalhe_recuroso_inadmissao',
        ["recurso" => $documentoDenuncia->getDenuncia()->getJulgamentoAdmissibilidade()->getRecursoJulgamentoAdmissibilidade(),
            "hasPrazoRecurso" => $documentoDenuncia->getDenuncia()->getJulgamentoAdmissibilidade()->hasPrazoRecursoJulgamentoAdmissibilidade()
        ])
    @endif

    @if($abas->hasJulgamentoRecursoAdmissibilidade())
        @if($quebraPagina) <div class="page-break"></div> @else @php $quebraPagina = 1; @endphp @endif
        @include('relatorios.extrato_denuncia.julg_recurso_admissibilidade.extrato_julgamento', ["julgamento" => $documentoDenuncia->getJulgamentoRecursoAdmissibilidade()])
    @endif

    @if($abas->hasDefesa())
        @if($quebraPagina) <div class="page-break"></div> @else @php $quebraPagina = 1; @endphp @endif
        @include('relatorios.extrato_denuncia.defesa.extrato_defesa', ["defesa" => $documentoDenuncia->getDefesa()])
    @endif

    @if($abas->hasParecer())
        @if($quebraPagina) <div class="page-break"></div> @else @php $quebraPagina = 1; @endphp @endif
        @include('relatorios.extrato_denuncia.parecer.extrato_parecer', ["parecer" => $documentoDenuncia->getEncaminhamentos()])
    @endif

    @if($abas->hasJulgamentoPrimeiraInstancia())
        @if($quebraPagina) <div class="page-break"></div> @else @php $quebraPagina = 1; @endphp @endif
        @include('relatorios.extrato_denuncia.julg_primeira_instancia.extrato_julgamento', [
            "julgamento" => $documentoDenuncia->getJulgamentoPrimeiraInstancia()->julgamento,
            "retificacoes" => $documentoDenuncia->getJulgamentoPrimeiraInstancia()->retificacoes
        ])
    @endif

    @if($abas->hasRecursoDenunciante())
        @if($quebraPagina) <div class="page-break"></div> @else @php $quebraPagina = 1; @endphp @endif
        @include('relatorios.extrato_denuncia.recurso_denuncia.extrato_recurso_denuncia', ["recurso" => $documentoDenuncia->getRecursoDenunciante()])
    @endif

    @if($abas->hasRecursoDenunciado())
        @if($quebraPagina) <div class="page-break"></div> @else @php $quebraPagina = 1; @endphp @endif
        @include('relatorios.extrato_denuncia.recurso_denuncia.extrato_recurso_denuncia', ["recurso" => $documentoDenuncia->getRecursoDenunciado()])
    @endif

    @if($abas->hasJulgamentoSegundaInstancia())
        @if($quebraPagina) <div class="page-break"></div> @else @php $quebraPagina = 1; @endphp @endif
        @include('relatorios.extrato_denuncia.julg_segunda_instancia.extrato_julgamento', [
            "julgamento" => $documentoDenuncia->getJulgamentoSegundaInstancia()->julgamento,
            "retificacoes" => $documentoDenuncia->getJulgamentoSegundaInstancia()->retificacoes
        ])
    @endif
</main>
</body>
</html>
