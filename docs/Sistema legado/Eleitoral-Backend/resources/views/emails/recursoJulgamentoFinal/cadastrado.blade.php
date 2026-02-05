<div>
    @include('emails.padrao.cabecalho')
    @include('emails.padrao.corpo')
    <p><b>Nome do(s) membro(s):</b></p>
    @foreach ($indicacoes as $indicacao)
        <p>
            @if($indicacao->getNumeroOrdem() == 0)
                <span>{{$descricaoConselheiroFederal . ' - '}}</span>
            @else
                <span>{{$indicacao->getNumeroOrdem() . ' - '}}</span>
            @endif

            <span>{{$indicacao->getTipoParticipacaoChapa()->getDescricao() . ' - '}}</span>

            @if (!empty($indicacao->getMembroChapa()))
                <span>{{ $indicacao->getMembroChapa()->getProfissional()->getNome()}}</span>
            @else
                <span>{{$descricaoSemMembro}}</span>
            @endif
        </p>
    @endforeach
    <p><b>Interpor Recurso:</b> {!! $descricao !!}</p>
    @include('emails.padrao.rodape')
</div>
