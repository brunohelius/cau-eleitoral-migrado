<p>
    @if($isEstadual)
        <span>{{str_pad($ordem,2, '0', STR_PAD_LEFT)}}</span>
        <span> - </span>
    @endif
        <span>
            <span class="fontBold">NOME (Titular {{ $membros[1]->getRespostaDeclaracaoRepresentatividade() ? ' - Representatividade' : '' }}):</span>
                {{ (\Illuminate\Support\Arr::exists($membros, 1)) ?
                $membros[1]->getProfissional()->getNome() : ' N/A ' }}
            </span>
        <span>e</span>
        <span>
            <span class="fontBold">NOME (Suplente):</span>
                    {{ (\Illuminate\Support\Arr::exists($membros, 2)) ?
                    $membros[2]->getProfissional()->getNome() : ' N/A ' }}
        </span>
</p>
