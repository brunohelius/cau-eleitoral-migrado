@if(!empty($analiseAdmissibilidade->getDenunciaAdmitida()))
    @include('relatorios.extrato_denuncia.analise_admissibilidade.analise_admitida',
         ["analiseAdmissibilidade" => $analiseAdmissibilidade->getDenunciaAdmitida(),
            "historicoAdmissao" => $analiseAdmissibilidade->getHistoricoAdmissao()
         ])
@else
    @include('relatorios.extrato_denuncia.analise_admissibilidade.analise_inadmitida',
    ["analiseAdmissibilidade" => $analiseAdmissibilidade->getDenunciaInadmitida(),
      "coordenador" => $analiseAdmissibilidade->getCoordenadores()[0]
    ])
@endif
