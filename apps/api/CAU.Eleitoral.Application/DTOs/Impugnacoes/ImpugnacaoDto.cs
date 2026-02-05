using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.DTOs.Impugnacoes;

public record ImpugnacaoDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public string Protocolo { get; init; } = string.Empty;
    public TipoImpugnacao Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public StatusImpugnacao Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public Guid? ChapaImpugnanteId { get; init; }
    public string? ChapaImpugnanteNome { get; init; }
    public Guid? ChapaImpugnadaId { get; init; }
    public string? ChapaImpugnadaNome { get; init; }
    public Guid? MembroImpugnadoId { get; init; }
    public string? MembroImpugnadoNome { get; init; }
    public Guid? ImpugnanteId { get; init; }
    public string? ImpugnanteNome { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
    public DateTime DataImpugnacao { get; init; }
    public DateTime? DataRecebimento { get; init; }
    public DateTime? PrazoAlegacoes { get; init; }
    public DateTime? PrazoContraAlegacoes { get; init; }
    public int TotalPedidos { get; init; }
    public int TotalAlegacoes { get; init; }
    public DateTime CreatedAt { get; init; }
}

public record CreateImpugnacaoDto
{
    public Guid EleicaoId { get; init; }
    public TipoImpugnacao Tipo { get; init; }
    public Guid? ChapaImpugnanteId { get; init; }
    public Guid? ChapaImpugnadaId { get; init; }
    public Guid? MembroImpugnadoId { get; init; }
    public Guid? ImpugnanteId { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
}

public record UpdateImpugnacaoDto
{
    public string? Titulo { get; init; }
    public string? Descricao { get; init; }
    public string? Fundamentacao { get; init; }
    public DateTime? PrazoAlegacoes { get; init; }
    public DateTime? PrazoContraAlegacoes { get; init; }
}

public record PedidoImpugnacaoDto
{
    public Guid Id { get; init; }
    public Guid ImpugnacaoId { get; init; }
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
    public DateTime DataPedido { get; init; }
}

public record CreatePedidoImpugnacaoDto
{
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
}

public record AlegacaoDto
{
    public Guid Id { get; init; }
    public Guid ImpugnacaoId { get; init; }
    public TipoAlegacao Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
    public DateTime DataAlegacao { get; init; }
}

public record CreateAlegacaoDto
{
    public TipoAlegacao Tipo { get; init; }
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
}

public record JulgamentoImpugnacaoDto
{
    public Guid Id { get; init; }
    public Guid ImpugnacaoId { get; init; }
    public StatusJulgamento Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public TipoDecisao? TipoDecisao { get; init; }
    public string? Decisao { get; init; }
    public string? Fundamentacao { get; init; }
    public DateTime? DataJulgamento { get; init; }
}
