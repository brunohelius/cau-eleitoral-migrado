<table style="border: 0px; width: 100%;">
    <tr>
        <td colspan="2">
            <fieldset>
                <legend class="text-nowrap"> {{ $tituloJulgamentoDenunciante }}: </legend>
                <span class="text-nowrap text-uppercase">{{ $julgamentoDenunciante }}</span>
            </fieldset>
        </td>
        <td colspan="2">
            <fieldset>
                <legend class="text-nowrap"> {{ $tituloJulgamentoDenunciado }}: </legend>
                <span class="text-nowrap text-uppercase">{{ $julgamentoDenunciado }}</span>
            </fieldset>
        </td>
    </tr>
    <tr>
        <td>
            <fieldset>
                <legend class="text-nowrap"> {{ $sancao }}: </legend>
                <span class="text-nowrap text-uppercase">{{ $sentenca }}</span>
            </fieldset>
        </td>

        @if($isSancao)
        <td>
            <fieldset>
                <legend class="text-nowrap"> {{ $tituloSentenca }}: </legend>
                <span class="text-nowrap text-uppercase">{{ $sancaoAplicada }}</span>
            </fieldset>
        </td>
        @endif
        @if($idSentenca == 2)
            <td colspan="2">
                <fieldset>
                    <legend class="text-nowrap"> {{ $tituloSuspencao }}: </legend>
                    <span class="text-nowrap">{{ $qtDias }} DIAS</span>
                </fieldset>
            </td>
        @elseif($idSentenca == 4)
            <td colspan="2">
                <fieldset>
                    <legend class="text-nowrap"> Valor Percentual da multa: </legend>
                    <span class="text-nowrap">{{ $valorMulta }}% EM RELAÇÃO À ANUIDADE</span>
                </fieldset>
            </td>
        @else
            <td colspan="2">
                <fieldset>
                    <legend class="text-nowrap"> Multa: </legend>
                    <span class="text-nowrap">{{ $multa == true ? 'SIM' : 'NÃO' }}</span>
                </fieldset>
            </td>
        @endif
    </tr>
    <tr>
        @if($idSentenca != 4)

            @if($idSentenca == 2)
                <td colspan="2">
                    <fieldset>
                        <legend class="text-nowrap"> Multa: </legend>
                        <span class="text-nowrap">{{ $multa == true ? 'SIM' : 'NÃO' }}</span>
                    </fieldset>
                </td>
            @endif
            @if($multa == true)
                <td colspan="2">
                    <fieldset>
                        <legend class="text-nowrap"> Valor Percentual da multa: </legend>
                        <span class="text-nowrap">{{ $valorMulta }}% EM RELAÇÃO À ANUIDADE</span>
                    </fieldset>
                </td>
            @endif
        @endif
    </tr>
</table>