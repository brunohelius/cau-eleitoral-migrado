@if ($email->isRodapeAtivo())
    <div>{!! $email->getTextoRodape() !!}</div>
    @if (!empty($email->getCaminhoImagemRodape()))
        <div>
            <img src="{{ $message->embed($email->getCaminhoImagemRodape()) }}"/>
        </div>
    @endif
@endif