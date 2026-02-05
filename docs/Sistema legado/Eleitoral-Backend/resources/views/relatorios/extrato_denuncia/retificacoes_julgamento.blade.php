<div class="sub-titulo">Histórico de Retificação:</div>
<table class="tabela">
    <tr>
        <th>Data/Hora</th>
        <th>Usuário</th>
    </tr>
    <tbody>
    @foreach($retificacoes as $retificacao)
        <tr>
            <td class="pl-5">
                <span class="text-nowrap">{{ $retificacao->getData()->format('d/m/Y') . " às " . $retificacao->getData()->format('H:i') }}</span>
            </td>
            <td class="pl-5">{{ $retificacao->getUsuario()->getNome() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
