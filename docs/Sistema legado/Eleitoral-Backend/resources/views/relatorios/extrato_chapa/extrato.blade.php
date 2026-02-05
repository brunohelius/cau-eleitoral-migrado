<html>
<head>
    <style>
        *, div, p, span {
            font-size: 12px;
            font-family: Arial, sans-serif;
        }

        .titulo {
            font-weight: bolder;
        }

        .text-center {
            text-align: center;
        }

        .pl-30 {
            padding-left: 30px;
        }

        .pb-10 {
            padding-bottom: 10px;
        }

        .fotoMembroChapa {
            height: 133px;
        }

        .fontBold {
            font-weight: bold
        }

        h2 {
            font-size: 12px;
            font-family: Arial, sans-serif;
        }

        @page {
            margin: 30px 0 20px 0;
        }

        .justificado {
            text-align: justify;
            text-justify: inter-word;
        }

        * {
            overflow: visible !important;
        }

    </style>
</head>
<body>
<main>
    <div style="text-align: center">
        <img src="{{ resource_path('images/bannermailsiccau.jpg')  }}">
    </div>
    <h2 class="text-center">DIVULGAÇÃO DE CHAPAS NO PROCESSO ELEITORAL DO CAU</h2>
    {{ info('Iniciou foreach DIVULGAÇÃO DE CHAPAS NO PROCESSO ELEITORAL DO CAU') }}
    @foreach ($chapasEleicao as $chapa)
        <p class="titulo">
            <span class="fontBold">CHAPA {{ empty($chapa->getNumeroChapa()) ? 'A definir' : str_pad
            ($chapa->getNumeroChapa(), 2, '0', STR_PAD_LEFT) }}
            - {{ $chapa->getCauUf()->getDescricao() }}</span>
        </p>
        <p class="titulo pb-1"><span class="fontBold">Candidatos:</span></p>
        <div class="pl-30">
            <div class="pb-10">
                <span class="titulo"> <span class="fontBold">Candidato a conselheiro federal e respectivo suplente
                        de conselheiro</span></span>
                {{-- Verifica se existe o membro na posiçao 0, referente a conselheiro federal --}}
                @if(\Illuminate\Support\Arr::exists($chapa->getMembrosChapa(), 0))
                    {{-- Conselheiro federal Titular --}}
                    {{ info('INICIOU - Candidato a conselheiro federal e respectivo suplente ' . $chapa->getId()) }}
                    @includeIf('relatorios.extrato_chapa.card_resumido', ['membros' => $chapa->getMembrosChapa()[0], 'isEstadual' => false, 'ordem' => 0])
                    {{ info('FINALIZOU - Candidatos a conselheiros estaduais e respectivos suplentes de
                    conselheiros ' . $chapa->getId()) }}
                @endif
            </div>

            @if($chapa->getTipoCandidatura()->getId() != \App\Config\Constants::TIPO_CANDIDATURA_IES)
            <div class="pb-10">
                <span class="titulo"><span class="fontBold"> Candidatos a conselheiros estaduais e respectivos suplentes de conselheiros</span></span>
                {{ info('INICIOU - Candidatos a conselheiros estaduais e respectivos suplentes de conselheiros ' . $chapa->getId()) }}
                @for ($i = 1; $i < count($chapa->getMembrosChapa()); $i++)
                    @if(\Illuminate\Support\Arr::exists($chapa->getMembrosChapa(), $i))
                        @includeIf('relatorios.extrato_chapa.card_resumido', ['membros' => $chapa->getMembrosChapa()[$i], 'isEstadual' => true, 'ordem' => $i])
                    @endif
                @endfor
                {{ info('FINALIZOU - Candidatos a conselheiros estaduais e respectivos suplentes de conselheiros ' . $chapa->getId()) }}
            </div>
            @endif
            <p class="pb-10">
                Obs: Veja a síntese de currículo de cada condidato após o plano de trabalho.
            </p>
        </div>
        @if(!empty($chapa->getRedesSociaisChapa()))
            <div class="pb-10">
                <span class="titulo pb-10"> <span class="fontBold">Meios Oficiais de divugação:</span> </span>
                <div class="pl-30">
                    <p>
                        <span>Lista de meios  oficiais de propaganda da chapa </span><br>
                        {{ info('INICIOU - Lista de meios  oficiais de propaganda da chapa ' . $chapa->getId()) }}
                        @foreach ($chapa->getRedesSociaisChapa() as $redeSocial)
                            @if($redeSocial->isAtivo())
                                <span> {{ $redeSocial->getDescricao() }}</span> <br>
                            @endif
                        @endforeach
                        {{ info('FINALIZOU - Lista de meios  oficiais de propaganda da chapa ' . $chapa->getId()) }}
                    </p>
                </div>
            </div>
        @else
            <div class="pb-10">
                <span class="titulo pb-10"> <span class="fontBold">Meios Oficiais de divugação: N/A</span> </span>
            </div>
        @endif

        <div class="pb-10">
            <span class="titulo pb-10"> <span class="fontBold">Plano de Trabalho</span></span>
            <div class="pl-30 justificado">
                {!! $chapa->getDescricaoPlataforma() !!}
            </div>
        </div>

        <div class="pb-10">
                <span class="titulo">
                    <span class="fontBold">Candidato a conselheiro federal e respectivo suplente de conselheiro</span>
                </span>
            {{-- Verifica se existe o membro na posiçao 0, referente a conselheiro federal --}}
            @if(\Illuminate\Support\Arr::exists($chapa->getMembrosChapa(), 0))
                {{-- Conselheiro federal Titular --}}
                @includeIf('relatorios.extrato_chapa.card_detalhado', ['membros' => $chapa->getMembrosChapa()[0],
                'foto' => $foto, 'posicao' => 1, 'ordem' => 1])
                {{-- Conselheiro federal Suplente --}}
                @includeIf('relatorios.extrato_chapa.card_detalhado', ['membros' => $chapa->getMembrosChapa()[0],
                'foto' => $foto, 'posicao' => 2, 'ordem' => 1])
            @endif
        </div>
        <br/>
        @if($chapa->getTipoCandidatura()->getId() != \App\Config\Constants::TIPO_CANDIDATURA_IES)
        <div class="pb-10">
            <span class="titulo">
                <span class="fontBold"> Candidatos a conselheiros estaduais e respectivos suplentes de conselheiros</span>
            </span>
            {{ info('INICIOU - Detalhado - Candidatos a conselheiros estaduais e respectivos suplentes de conselheiros
            ' . $chapa->getId()) }}
            @for ($i = 1; $i < count($chapa->getMembrosChapa()); $i++)
                @if(\Illuminate\Support\Arr::exists($chapa->getMembrosChapa(), $i))
                    @includeIf('relatorios.extrato_chapa.card_detalhado', ['membros' => $chapa->getMembrosChapa()[$i],
                   'foto' => $foto, 'posicao' => 1, 'ordem' => $i])

                    @includeIf('relatorios.extrato_chapa.card_detalhado', ['membros' => $chapa->getMembrosChapa()[$i],
                    'foto' => $foto, 'posicao' => 2, 'ordem' => $i])
                @endif
            @endfor
            {{ info('FINALIZOU - Detalhado - Candidatos a conselheiros estaduais e respectivos suplentes de
            conselheiros
            ' . $chapa->getId()) }}
            @if(!empty($chapa->getDescricaoPosicoesSemMembros()))
                <p>
                <span class="titulo">
                    <span class="fontBold">Não foram definidos membros para as posições
                        {{$chapa->getDescricaoPosicoesSemMembros()}}.</span>
                </span>
                </p>
            @endif
        </div>
        @endif
    @endforeach
</main>
</body>
</html>
