<p>
    <span>
        <span class="fontBold"> {{ $ordem }} - NOME {{$posicao == 1 ? ($membros[$posicao]->getRespostaDeclaracaoRepresentatividade() ? '(Titular - Representatividade)' : '(Titular)') : '(Suplente)'}}:</span>
            {{ !empty($membros) && (\Illuminate\Support\Arr::exists($membros, $posicao)) ?
            $membros[$posicao]->getProfissional()->getNome() : ' N/A ' }}
        </span>
</p>
@if(!empty($membros) && \Illuminate\Support\Arr::exists($membros, $posicao))
    @if(!empty($membros[$posicao]->getFotoMembroChapa()))
        <p>
            <img class="fotoMembroChapa"
                 src="{{ $membros[$posicao]->getFotoMembroChapa() }}">
        </p>
    @else
        <p>
            <img class="fotoMembroChapa" src="{{ $foto }}">
        </p>
    @endif
@endif
@if(!empty($membros) && \Illuminate\Support\Arr::exists($membros, $posicao))
    <div class="justificado">
        {!! $membros[$posicao]->getSinteseCurriculo() !!}
    </div>
@endif
