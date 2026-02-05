namespace CAU.Eleitoral.Application.DTOs.Notificacoes;

public record NotificacaoDto
{
    public Guid Id { get; init; }
    public Guid UsuarioId { get; init; }
    public string UsuarioNome { get; init; } = string.Empty;
    public TipoNotificacao Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public CanalNotificacao Canal { get; init; }
    public string CanalNome { get; init; } = string.Empty;
    public StatusNotificacao Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public string Titulo { get; init; } = string.Empty;
    public string Mensagem { get; init; } = string.Empty;
    public string? Link { get; init; }
    public string? Dados { get; init; }
    public bool Lida { get; init; }
    public DateTime? DataLeitura { get; init; }
    public DateTime DataEnvio { get; init; }
    public DateTime? DataEntrega { get; init; }
    public DateTime CreatedAt { get; init; }
}

public enum TipoNotificacao
{
    Sistema = 0,
    Eleicao = 1,
    Votacao = 2,
    Resultado = 3,
    Denuncia = 4,
    Impugnacao = 5,
    Julgamento = 6,
    Calendario = 7,
    Documento = 8,
    Alerta = 9,
    Lembrete = 10
}

public enum CanalNotificacao
{
    InApp = 0,
    Email = 1,
    SMS = 2,
    Push = 3
}

public enum StatusNotificacao
{
    Pendente = 0,
    Enviada = 1,
    Entregue = 2,
    Falha = 3,
    Cancelada = 4
}

public record EnviarNotificacaoDto
{
    public Guid UsuarioId { get; init; }
    public TipoNotificacao Tipo { get; init; }
    public CanalNotificacao Canal { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string Mensagem { get; init; } = string.Empty;
    public string? Link { get; init; }
    public Dictionary<string, string>? Dados { get; init; }
    public DateTime? AgendadoPara { get; init; }
}

public record EnviarNotificacaoEmMassaDto
{
    public IEnumerable<Guid> UsuarioIds { get; init; } = new List<Guid>();
    public TipoNotificacao Tipo { get; init; }
    public CanalNotificacao Canal { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string Mensagem { get; init; } = string.Empty;
    public string? Link { get; init; }
    public Dictionary<string, string>? Dados { get; init; }
}

public record EnviarNotificacaoPorFiltroDto
{
    public FiltroDestinatariosDto Filtro { get; init; } = new();
    public TipoNotificacao Tipo { get; init; }
    public CanalNotificacao Canal { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string Mensagem { get; init; } = string.Empty;
    public string? Link { get; init; }
    public Dictionary<string, string>? Dados { get; init; }
}

public record FiltroDestinatariosDto
{
    public Guid? EleicaoId { get; init; }
    public Guid? RegionalId { get; init; }
    public IEnumerable<string>? Roles { get; init; }
    public bool? ApenasEleitoresAptos { get; init; }
    public bool? ApenasQueNaoVotaram { get; init; }
}

public record MarcarComoLidaDto
{
    public Guid NotificacaoId { get; init; }
}

public record NotificacoesResumoDto
{
    public int TotalNaoLidas { get; init; }
    public int TotalHoje { get; init; }
    public int TotalSemana { get; init; }
    public IEnumerable<NotificacaoDto> UltimasNotificacoes { get; init; } = new List<NotificacaoDto>();
}

public record ConfiguracaoNotificacaoUsuarioDto
{
    public Guid UsuarioId { get; init; }
    public bool ReceberEmail { get; init; }
    public bool ReceberSMS { get; init; }
    public bool ReceberPush { get; init; }
    public bool NotificacoesEleicao { get; init; }
    public bool NotificacoesVotacao { get; init; }
    public bool NotificacoesResultado { get; init; }
    public bool NotificacoesCalendario { get; init; }
    public bool NotificacoesDenuncia { get; init; }
}

public record AtualizarConfiguracaoNotificacaoDto
{
    public bool? ReceberEmail { get; init; }
    public bool? ReceberSMS { get; init; }
    public bool? ReceberPush { get; init; }
    public bool? NotificacoesEleicao { get; init; }
    public bool? NotificacoesVotacao { get; init; }
    public bool? NotificacoesResultado { get; init; }
    public bool? NotificacoesCalendario { get; init; }
    public bool? NotificacoesDenuncia { get; init; }
}

public record TemplateNotificacaoDto
{
    public Guid Id { get; init; }
    public string Codigo { get; init; } = string.Empty;
    public TipoNotificacao Tipo { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string Corpo { get; init; } = string.Empty;
    public bool Ativo { get; init; }
}
