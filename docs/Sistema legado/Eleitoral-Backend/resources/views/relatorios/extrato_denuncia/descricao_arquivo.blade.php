<div class="sub-titulo">Documentos:</div>
<table class="tabela">
    <tr>
        <th>Descrição</th>
        <th>Formato</th>
        <th>Tamanho</th>
    </tr>
    <tbody>
    @foreach($documentos as $documento)
        <tr>
            <td class="pl-5">{{ $documento->getNome() }}</td>
            <td class="pl-5">{{ $documento->getExtensao() }}</td>
            <td class="pl-5">{{ $documento->getTamanho() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
