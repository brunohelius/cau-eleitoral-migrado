namespace CAU.Eleitoral.Domain.Enums;

public enum StatusChapa
{
    Rascunho = 0,
    PendenteDocumentos = 1,
    AguardandoAnalise = 2,
    EmAnalise = 3,
    Deferida = 4,
    Indeferida = 5,
    Impugnada = 6,
    AguardandoJulgamento = 7,
    Registrada = 8,
    Cancelada = 9
}

public enum TipoMembroChapa
{
    Presidente = 0,
    VicePresidente = 1,
    PrimeiroSecretario = 2,
    SegundoSecretario = 3,
    PrimeiroTesoureiro = 4,
    SegundoTesoureiro = 5,
    ConselheiroTitular = 6,
    ConselheiroSuplente = 7,
    Delegado = 8,
    DelegadoSuplente = 9
}

public enum StatusMembroChapa
{
    Pendente = 0,
    AguardandoConfirmacao = 1,
    Confirmado = 2,
    Recusado = 3,
    Substituido = 4,
    Inabilitado = 5
}

public enum TipoDocumentoChapa
{
    AtaFundacao = 0,
    AtaIndicacao = 1,
    TermoAceite = 2,
    FichaInscricao = 3,
    DeclaracaoQuitacaoEleitoral = 4,
    DeclaracaoAdimplencia = 5,
    CertidaoNegativaEtica = 6,
    PlataformaEleitoral = 7,
    DocumentoPessoal = 8,
    ComprovanteResidencia = 9,
    Outros = 99
}

public enum StatusDocumentoChapa
{
    Pendente = 0,
    Enviado = 1,
    EmAnalise = 2,
    Aprovado = 3,
    Rejeitado = 4,
    Substituicao = 5
}
