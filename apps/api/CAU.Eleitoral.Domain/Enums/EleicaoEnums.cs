namespace CAU.Eleitoral.Domain.Enums;

public enum StatusEleicao
{
    Rascunho = 0,
    Agendada = 1,
    EmAndamento = 2,
    Encerrada = 3,
    Suspensa = 4,
    Cancelada = 5,
    ApuracaoEmAndamento = 6,
    Finalizada = 7
}

public enum TipoEleicao
{
    Ordinaria = 0,
    Extraordinaria = 1,
    Suplementar = 2
}

public enum FaseEleicao
{
    Preparatoria = 0,
    Inscricao = 1,
    Impugnacao = 2,
    Propaganda = 3,
    Votacao = 4,
    Apuracao = 5,
    Resultado = 6,
    Diplomacao = 7
}

public enum TipoCalendario
{
    Inscricao = 0,
    Impugnacao = 1,
    Defesa = 2,
    Julgamento = 3,
    Recurso = 4,
    Propaganda = 5,
    Votacao = 6,
    Apuracao = 7,
    Resultado = 8,
    Diplomacao = 9
}

public enum StatusCalendario
{
    Pendente = 0,
    EmAndamento = 1,
    Concluido = 2,
    Cancelado = 3
}

public enum StatusUrna
{
    Inativa = 0,
    Instalada = 1,
    Ativada = 2,
    EmOperacao = 3,
    Pausada = 4,
    Encerrada = 5,
    Lacrada = 6,
    ComProblema = 7
}

public enum TipoUrna
{
    Fisica = 0,
    Virtual = 1,
    Contingencia = 2
}

public enum StatusMesa
{
    NaoInstalada = 0,
    Instalada = 1,
    Aberta = 2,
    EmOperacao = 3,
    Pausada = 4,
    Encerrada = 5,
    Lacrada = 6
}

public enum TipoFiscal
{
    Chapa = 0,
    Partido = 1,
    Comissao = 2,
    Ministerio = 3
}

public enum StatusFiscal
{
    Pendente = 0,
    Credenciado = 1,
    Ativo = 2,
    Suspenso = 3,
    Descredenciado = 4
}

