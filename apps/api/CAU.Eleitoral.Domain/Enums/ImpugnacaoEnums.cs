namespace CAU.Eleitoral.Domain.Enums;

public enum TipoImpugnacao
{
    ImpugnacaoChapa = 0,
    ImpugnacaoMembro = 1,
    ImpugnacaoDocumento = 2,
    ImpugnacaoResultado = 3
}

public enum StatusImpugnacao
{
    Recebida = 0,
    EmAnalise = 1,
    AguardandoAlegacoes = 2,
    AlegacoesApresentadas = 3,
    AguardandoContraAlegacoes = 4,
    ContraAlegacoesApresentadas = 5,
    AguardandoJulgamento = 6,
    Julgada = 7,
    Procedente = 8,
    Improcedente = 9,
    ParcialmenteProcedente = 10,
    Arquivada = 11,
    AguardandoRecurso = 12,
    RecursoApresentado = 13,
    RecursoJulgado = 14
}

public enum TipoAlegacao
{
    Inelegibilidade = 0,
    IrregularidadeDocumental = 1,
    ViolacaoNorma = 2,
    FraudeEleitoral = 3,
    AbusoPoder = 4,
    Outros = 99
}
