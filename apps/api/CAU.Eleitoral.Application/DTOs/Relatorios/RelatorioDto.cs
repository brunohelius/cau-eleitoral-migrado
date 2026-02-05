using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.DTOs.Relatorios;

public record RelatorioDto
{
    public Guid Id { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public TipoRelatorio Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public FormatoRelatorio Formato { get; init; }
    public string FormatoNome { get; init; } = string.Empty;
    public StatusRelatorio Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public Guid? EleicaoId { get; init; }
    public string? EleicaoNome { get; init; }
    public string? ArquivoUrl { get; init; }
    public string? ArquivoNome { get; init; }
    public long? ArquivoTamanho { get; init; }
    public DateTime DataSolicitacao { get; init; }
    public DateTime? DataGeracao { get; init; }
    public Guid SolicitanteId { get; init; }
    public string SolicitanteNome { get; init; } = string.Empty;
    public string? Parametros { get; init; }
    public string? Erro { get; init; }
}

public enum TipoRelatorio
{
    ResultadoEleicao = 0,
    EstatisticasVotacao = 1,
    ListaEleitores = 2,
    ListaChapas = 3,
    AtaApuracao = 4,
    BoletimUrna = 5,
    DenunciasPorEleicao = 6,
    ImpugnacoesPorEleicao = 7,
    AuditoriaSistema = 8,
    AcessosUsuarios = 9,
    CalendarioEleicao = 10,
    ComprovantesVotacao = 11,
    Personalizado = 99
}

public enum FormatoRelatorio
{
    PDF = 0,
    Excel = 1,
    CSV = 2,
    JSON = 3
}

public enum StatusRelatorio
{
    Solicitado = 0,
    EmProcessamento = 1,
    Concluido = 2,
    Erro = 3,
    Expirado = 4
}

public record GerarRelatorioDto
{
    public TipoRelatorio Tipo { get; init; }
    public FormatoRelatorio Formato { get; init; }
    public Guid? EleicaoId { get; init; }
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
    public Dictionary<string, string>? ParametrosAdicionais { get; init; }
}

public record RelatorioResultadoEleicaoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public DateTime DataEleicao { get; init; }
    public int TotalEleitores { get; init; }
    public int TotalVotantes { get; init; }
    public double PercentualComparecimento { get; init; }
    public int VotosValidos { get; init; }
    public int VotosBrancos { get; init; }
    public int VotosNulos { get; init; }
    public IEnumerable<ResultadoChapaRelatorioDto> ResultadosChapas { get; init; } = new List<ResultadoChapaRelatorioDto>();
    public ChapaVencedoraDto? ChapaVencedora { get; init; }
}

public record ResultadoChapaRelatorioDto
{
    public int Numero { get; init; }
    public string Nome { get; init; } = string.Empty;
    public int TotalVotos { get; init; }
    public double Percentual { get; init; }
    public int Posicao { get; init; }
}

public record ChapaVencedoraDto
{
    public Guid ChapaId { get; init; }
    public int Numero { get; init; }
    public string Nome { get; init; } = string.Empty;
    public int TotalVotos { get; init; }
    public double Percentual { get; init; }
    public string PresidenteNome { get; init; } = string.Empty;
}

public record RelatorioEstatisticasDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public EstatisticasGeraisDto EstatisticasGerais { get; init; } = new();
    public EstatisticasPorPeriodoDto EstatisticasPorPeriodo { get; init; } = new();
    public IEnumerable<EstatisticasPorRegionalDto> EstatisticasPorRegional { get; init; } = new List<EstatisticasPorRegionalDto>();
}

public record EstatisticasGeraisDto
{
    public int TotalEleitores { get; init; }
    public int TotalVotantes { get; init; }
    public int TotalAbstencoes { get; init; }
    public int TotalChapas { get; init; }
    public int TotalDenuncias { get; init; }
    public int TotalImpugnacoes { get; init; }
}

public record EstatisticasPorPeriodoDto
{
    public Dictionary<string, int> VotosPorHora { get; init; } = new();
    public Dictionary<string, int> VotosPorDia { get; init; } = new();
}

public record EstatisticasPorRegionalDto
{
    public Guid RegionalId { get; init; }
    public string RegionalNome { get; init; } = string.Empty;
    public int TotalEleitores { get; init; }
    public int TotalVotantes { get; init; }
    public double PercentualComparecimento { get; init; }
}

public record FiltroRelatorioDto
{
    public TipoRelatorio? Tipo { get; init; }
    public StatusRelatorio? Status { get; init; }
    public Guid? EleicaoId { get; init; }
    public Guid? SolicitanteId { get; init; }
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
    public int? Pagina { get; init; }
    public int? TamanhoPagina { get; init; }
}
