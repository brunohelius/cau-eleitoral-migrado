@if ($email->isCabecalhoAtivo())
    @if (!empty($email->getCaminhoImagemCabecalho()))
        <div>
            <img src="{{ $message->embed($email->getCaminhoImagemCabecalho()) }}"/>
        </div>
    @endif
    <div>{!! $email->getTextoCabecalho() !!}</div>
@endif