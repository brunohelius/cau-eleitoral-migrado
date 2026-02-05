using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.DTOs;

public record EleicaoDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public TipoEleicao Tipo { get; init; }
    public StatusEleicao Status { get; init; }
    public FaseEleicao FaseAtual { get; init; }
    public int Ano { get; init; }
    public int? Mandato { get; init; }
    public DateTime DataInicio { get; init; }
    public DateTime DataFim { get; init; }
    public DateTime? DataVotacaoInicio { get; init; }
    public DateTime? DataVotacaoFim { get; init; }
    public DateTime? DataApuracao { get; init; }
    public Guid? RegionalId { get; init; }
    public string? RegionalNome { get; init; }
    public ModoVotacao ModoVotacao { get; init; }
    public int? QuantidadeVagas { get; init; }
    public int? QuantidadeSuplentes { get; init; }
    public int TotalChapas { get; init; }
    public int TotalEleitores { get; init; }
    public DateTime CreatedAt { get; init; }
    public DateTime? UpdatedAt { get; init; }
}

public record CreateEleicaoDto
{
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public TipoEleicao Tipo { get; init; }
    public int Ano { get; init; }
    public int? Mandato { get; init; }
    public DateTime DataInicio { get; init; }
    public DateTime DataFim { get; init; }
    public DateTime? DataVotacaoInicio { get; init; }
    public DateTime? DataVotacaoFim { get; init; }
    public Guid? RegionalId { get; init; }
    public ModoVotacao ModoVotacao { get; init; }
    public int? QuantidadeVagas { get; init; }
    public int? QuantidadeSuplentes { get; init; }
}

public record UpdateEleicaoDto
{
    public string? Nome { get; init; }
    public string? Descricao { get; init; }
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
    public DateTime? DataVotacaoInicio { get; init; }
    public DateTime? DataVotacaoFim { get; init; }
    public DateTime? DataApuracao { get; init; }
    public ModoVotacao? ModoVotacao { get; init; }
    public int? QuantidadeVagas { get; init; }
    public int? QuantidadeSuplentes { get; init; }
}
