<table style="border: 0px; width: 100%;">
    @if($idJulgamento == 1)
        <tr>
            <td style="width: 33%">
                <fieldset>
                    <legend class="text-nowrap"> {{ $tituloJulgamento }}: </legend>
                    <span class="text-nowrap text-uppercase">{{ $julgamento }}</span>
                </fieldset>
            </td>
            <td style="width: 33%">
                <fieldset>
                    <legend class="text-nowrap"> {{ $tituloSentenca }}: </legend>
                    <span class="text-nowrap text-uppercase">{{ $sentenca }}</span>
                </fieldset>
            </td>

            @if($idSentenca == 2)
                <td style="width: 33%">
                    <fieldset>
                        <legend class="text-nowrap"> {{ $tituloSuspencao }}: </legend>
                        <span class="text-nowrap">{{ $qtDias }} DIAS</span>
                    </fieldset>
                </td>
            @elseif($idSentenca == 4)
                <td style="width: 33%">
                    <fieldset>
                        <legend class="text-nowrap"> Valor Percentual da multa: </legend>
                        <span class="text-nowrap">{{ $valorMulta }}% EM RELAÇÃO À ANUIDADE</span>
                    </fieldset>
                </td>
            @else
                <td style="width: 33%">
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
                    <td style="width: 33%">
                        <fieldset>
                            <legend class="text-nowrap"> Multa: </legend>
                            <span class="text-nowrap">{{ $multa == true ? 'SIM' : 'NÃO' }}</span>
                        </fieldset>
                    </td>
                @endif

                @if($multa == true)
                    <td style="width: 33%">
                        <fieldset>
                            <legend class="text-nowrap"> Valor Percentual da multa: </legend>
                            <span class="text-nowrap">{{ $valorMulta }}% EM RELAÇÃO À ANUIDADE</span>
                        </fieldset>
                    </td>
                @endif
            @endif
        </tr>
    @else
        <tr>
            <td style="width: 33%">
                <fieldset>
                    <legend class="text-nowrap"> {{ $tituloJulgamento }}: </legend>
                    <span class="text-nowrap text-uppercase">{{ $julgamento }}</span>
                </fieldset>
            </td>
            <td style="width: 33%"></td>
            <td style="width: 33%"></td>
        </tr>
    @endif
</table>