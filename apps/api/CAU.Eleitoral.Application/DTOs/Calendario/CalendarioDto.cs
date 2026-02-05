using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.DTOs.Calendario;

public record CalendarioDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public TipoCalendario Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public StatusCalendario Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public FaseEleicao Fase { get; init; }
    public string FaseNome { get; init; } = string.Empty;
    public DateTime DataInicio { get; init; }
    public DateTime DataFim { get; init; }
    public TimeSpan? HoraInicio { get; init; }
    public TimeSpan? HoraFim { get; init; }
    public int Ordem { get; init; }
    public bool Obrigatorio { get; init; }
    public bool NotificarInicio { get; init; }
    public bool NotificarFim { get; init; }
    public int TotalAtividadesPrincipais { get; init; }
    public int TotalAtividadesSecundarias { get; init; }
    public DateTime CreatedAt { get; init; }
}

public record CreateCalendarioDto
{
    public Guid EleicaoId { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public TipoCalendario Tipo { get; init; }
    public FaseEleicao Fase { get; init; }
    public DateTime DataInicio { get; init; }
    public DateTime DataFim { get; init; }
    public TimeSpan? HoraInicio { get; init; }
    public TimeSpan? HoraFim { get; init; }
    public int? Ordem { get; init; }
    public bool Obrigatorio { get; init; }
    public bool NotificarInicio { get; init; }
    public bool NotificarFim { get; init; }
}

public record UpdateCalendarioDto
{
    public string? Nome { get; init; }
    public string? Descricao { get; init; }
    public TipoCalendario? Tipo { get; init; }
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
    public TimeSpan? HoraInicio { get; init; }
    public TimeSpan? HoraFim { get; init; }
    public int? Ordem { get; init; }
    public bool? Obrigatorio { get; init; }
    public bool? NotificarInicio { get; init; }
    public bool? NotificarFim { get; init; }
}

public record AtividadeCalendarioDto
{
    public Guid Id { get; init; }
    public Guid CalendarioId { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public DateTime DataInicio { get; init; }
    public DateTime DataFim { get; init; }
    public bool Concluida { get; init; }
    public DateTime? DataConclusao { get; init; }
}

public record CreateAtividadeCalendarioDto
{
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public DateTime DataInicio { get; init; }
    public DateTime DataFim { get; init; }
}

public record CalendarioResumoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public FaseEleicao FaseAtual { get; init; }
    public string FaseAtualNome { get; init; } = string.Empty;
    public CalendarioDto? EventoAtual { get; init; }
    public CalendarioDto? ProximoEvento { get; init; }
    public IEnumerable<CalendarioDto> EventosPendentes { get; init; } = new List<CalendarioDto>();
    public int TotalEventos { get; init; }
    public int EventosConcluidos { get; init; }
    public int EventosPendentesCount { get; init; }
}
