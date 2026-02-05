using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.DTOs.Julgamentos;

public record ComissaoJulgadoraDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public string? Sigla { get; init; }
    public string? Portaria { get; init; }
    public DateTime? DataPortaria { get; init; }
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
    public bool Ativa { get; init; }
    public int TotalMembros { get; init; }
    public int TotalSessoes { get; init; }
    public DateTime CreatedAt { get; init; }
}

public record CreateComissaoJulgadoraDto
{
    public Guid EleicaoId { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public string? Sigla { get; init; }
    public string? Portaria { get; init; }
    public DateTime? DataPortaria { get; init; }
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
}

public record UpdateComissaoJulgadoraDto
{
    public string? Nome { get; init; }
    public string? Descricao { get; init; }
    public string? Sigla { get; init; }
    public string? Portaria { get; init; }
    public DateTime? DataPortaria { get; init; }
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
    public bool? Ativa { get; init; }
}

public record MembroComissaoDto
{
    public Guid Id { get; init; }
    public Guid ComissaoId { get; init; }
    public Guid ConselheiroId { get; init; }
    public string ConselheiroNome { get; init; } = string.Empty;
    public TipoMembroComissao Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
    public bool Ativo { get; init; }
}

public record CreateMembroComissaoDto
{
    public Guid ConselheiroId { get; init; }
    public TipoMembroComissao Tipo { get; init; }
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
}

public record SessaoJulgamentoDto
{
    public Guid Id { get; init; }
    public Guid ComissaoId { get; init; }
    public string ComissaoNome { get; init; } = string.Empty;
    public string Numero { get; init; } = string.Empty;
    public TipoSessao Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public StatusSessao Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public DateTime DataSessao { get; init; }
    public TimeSpan? HoraInicio { get; init; }
    public TimeSpan? HoraFim { get; init; }
    public string? Local { get; init; }
    public string? Observacao { get; init; }
    public string? ConvocacaoUrl { get; init; }
    public DateTime? DataConvocacao { get; init; }
    public int TotalPautas { get; init; }
    public DateTime CreatedAt { get; init; }
}

public record CreateSessaoJulgamentoDto
{
    public Guid ComissaoId { get; init; }
    public string Numero { get; init; } = string.Empty;
    public TipoSessao Tipo { get; init; }
    public DateTime DataSessao { get; init; }
    public TimeSpan? HoraInicio { get; init; }
    public string? Local { get; init; }
    public string? Observacao { get; init; }
}

public record UpdateSessaoJulgamentoDto
{
    public string? Numero { get; init; }
    public TipoSessao? Tipo { get; init; }
    public DateTime? DataSessao { get; init; }
    public TimeSpan? HoraInicio { get; init; }
    public TimeSpan? HoraFim { get; init; }
    public string? Local { get; init; }
    public string? Observacao { get; init; }
}

public record PautaSessaoDto
{
    public Guid Id { get; init; }
    public Guid SessaoId { get; init; }
    public int Ordem { get; init; }
    public TipoJulgamento TipoProcesso { get; init; }
    public string TipoProcessoNome { get; init; } = string.Empty;
    public string NumeroProcesso { get; init; } = string.Empty;
    public string? Partes { get; init; }
    public string? Assunto { get; init; }
    public Guid? RelatorId { get; init; }
    public string? RelatorNome { get; init; }
    public bool Julgado { get; init; }
    public bool Adiado { get; init; }
    public string? MotivoAdiamento { get; init; }
    public string? Observacao { get; init; }
}

public record CreatePautaSessaoDto
{
    public int Ordem { get; init; }
    public TipoJulgamento TipoProcesso { get; init; }
    public string NumeroProcesso { get; init; } = string.Empty;
    public string? Partes { get; init; }
    public string? Assunto { get; init; }
    public Guid? RelatorId { get; init; }
    public string? Observacao { get; init; }
}
