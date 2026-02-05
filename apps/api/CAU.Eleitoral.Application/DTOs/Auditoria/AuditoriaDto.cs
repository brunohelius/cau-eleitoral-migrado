namespace CAU.Eleitoral.Application.DTOs.Auditoria;

public record LogAuditoriaDto
{
    public Guid Id { get; init; }
    public Guid? UsuarioId { get; init; }
    public string? UsuarioNome { get; init; }
    public string? UsuarioEmail { get; init; }
    public TipoAcaoAuditoria Acao { get; init; }
    public string AcaoNome { get; init; } = string.Empty;
    public string Entidade { get; init; } = string.Empty;
    public Guid? EntidadeId { get; init; }
    public string? EntidadeNome { get; init; }
    public string? ValorAnterior { get; init; }
    public string? ValorNovo { get; init; }
    public string? IpAddress { get; init; }
    public string? UserAgent { get; init; }
    public string? Recurso { get; init; }
    public string? Metodo { get; init; }
    public int? StatusCode { get; init; }
    public bool Sucesso { get; init; }
    public string? Mensagem { get; init; }
    public DateTime DataAcao { get; init; }
}

public enum TipoAcaoAuditoria
{
    Criar = 0,
    Atualizar = 1,
    Excluir = 2,
    Visualizar = 3,
    Login = 4,
    Logout = 5,
    FalhaLogin = 6,
    AlterarSenha = 7,
    RecuperarSenha = 8,
    Votar = 9,
    IniciarApuracao = 10,
    FinalizarApuracao = 11,
    PublicarResultado = 12,
    EnviarNotificacao = 13,
    Upload = 14,
    Download = 15,
    Exportar = 16,
    Importar = 17,
    AlterarStatus = 18,
    AprovarDocumento = 19,
    RejeitarDocumento = 20,
    AssinarDocumento = 21,
    Outro = 99
}

public record RegistrarAuditoriaDto
{
    public Guid? UsuarioId { get; init; }
    public TipoAcaoAuditoria Acao { get; init; }
    public string Entidade { get; init; } = string.Empty;
    public Guid? EntidadeId { get; init; }
    public string? EntidadeNome { get; init; }
    public object? ValorAnterior { get; init; }
    public object? ValorNovo { get; init; }
    public string? IpAddress { get; init; }
    public string? UserAgent { get; init; }
    public string? Recurso { get; init; }
    public string? Metodo { get; init; }
    public int? StatusCode { get; init; }
    public bool Sucesso { get; init; }
    public string? Mensagem { get; init; }
}

public record FiltroAuditoriaDto
{
    public Guid? UsuarioId { get; init; }
    public TipoAcaoAuditoria? Acao { get; init; }
    public string? Entidade { get; init; }
    public Guid? EntidadeId { get; init; }
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
    public bool? ApenasSucesso { get; init; }
    public bool? ApenasFalhas { get; init; }
    public string? IpAddress { get; init; }
    public int Pagina { get; init; } = 1;
    public int TamanhoPagina { get; init; } = 50;
}

public record AuditoriaResumoDto
{
    public int TotalAcoes { get; init; }
    public int TotalHoje { get; init; }
    public int TotalSemana { get; init; }
    public int TotalMes { get; init; }
    public int TotalFalhas { get; init; }
    public Dictionary<string, int> AcoesPorTipo { get; init; } = new();
    public Dictionary<string, int> AcoesPorEntidade { get; init; } = new();
    public IEnumerable<LogAuditoriaDto> UltimasAcoes { get; init; } = new List<LogAuditoriaDto>();
}

public record AuditoriaPorUsuarioDto
{
    public Guid UsuarioId { get; init; }
    public string UsuarioNome { get; init; } = string.Empty;
    public string UsuarioEmail { get; init; } = string.Empty;
    public int TotalAcoes { get; init; }
    public DateTime? UltimaAcao { get; init; }
    public Dictionary<string, int> AcoesPorTipo { get; init; } = new();
    public IEnumerable<LogAuditoriaDto> UltimasAcoes { get; init; } = new List<LogAuditoriaDto>();
}

public record AuditoriaPorEntidadeDto
{
    public string Entidade { get; init; } = string.Empty;
    public Guid? EntidadeId { get; init; }
    public string? EntidadeNome { get; init; }
    public int TotalAlteracoes { get; init; }
    public DateTime? UltimaAlteracao { get; init; }
    public IEnumerable<LogAuditoriaDto> Historico { get; init; } = new List<LogAuditoriaDto>();
}

public record LogAcessoDto
{
    public Guid Id { get; init; }
    public Guid UsuarioId { get; init; }
    public string UsuarioNome { get; init; } = string.Empty;
    public string UsuarioEmail { get; init; } = string.Empty;
    public string Acao { get; init; } = string.Empty;
    public string? Recurso { get; init; }
    public string? Metodo { get; init; }
    public string? Url { get; init; }
    public string? IpAddress { get; init; }
    public string? UserAgent { get; init; }
    public DateTime DataAcesso { get; init; }
    public bool Sucesso { get; init; }
    public string? Mensagem { get; init; }
}

public record FiltroLogAcessoDto
{
    public Guid? UsuarioId { get; init; }
    public string? Acao { get; init; }
    public string? Recurso { get; init; }
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
    public bool? ApenasSucesso { get; init; }
    public string? IpAddress { get; init; }
    public int Pagina { get; init; } = 1;
    public int TamanhoPagina { get; init; } = 50;
}

public record ExportarAuditoriaDto
{
    public FiltroAuditoriaDto Filtro { get; init; } = new();
    public FormatoExportacao Formato { get; init; }
}

public enum FormatoExportacao
{
    CSV = 0,
    Excel = 1,
    PDF = 2,
    JSON = 3
}

public record PaginatedResultDto<T>
{
    public IEnumerable<T> Items { get; init; } = new List<T>();
    public int TotalItems { get; init; }
    public int Pagina { get; init; }
    public int TamanhoPagina { get; init; }
    public int TotalPaginas { get; init; }
    public bool TemProximaPagina { get; init; }
    public bool TemPaginaAnterior { get; init; }
}
