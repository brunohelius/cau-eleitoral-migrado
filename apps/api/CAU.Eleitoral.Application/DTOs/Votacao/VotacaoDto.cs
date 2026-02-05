using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.DTOs.Votacao;

public record VotoDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public Guid? ChapaId { get; init; }
    public TipoVoto Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public StatusVoto Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public ModoVotacao Modo { get; init; }
    public string ModoNome { get; init; } = string.Empty;
    public string HashVoto { get; init; } = string.Empty;
    public DateTime DataVoto { get; init; }
    public string? Comprovante { get; init; }
}

public record RegistrarVotoDto
{
    public Guid EleicaoId { get; init; }
    public Guid EleitorId { get; init; }
    public Guid? ChapaId { get; init; }
    public TipoVoto Tipo { get; init; }
    public ModoVotacao Modo { get; init; }
    public string? IpAddress { get; init; }
    public string? UserAgent { get; init; }
}

public record ComprovanteVotoDto
{
    public string Comprovante { get; init; } = string.Empty;
    public string HashVoto { get; init; } = string.Empty;
    public DateTime DataVoto { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public string? ChapaNome { get; init; }
    public TipoVoto TipoVoto { get; init; }
    public string Mensagem { get; init; } = string.Empty;
}

public record ValidarVotoDto
{
    public Guid EleicaoId { get; init; }
    public Guid EleitorId { get; init; }
}

public record ValidacaoVotoResultDto
{
    public bool PodeVotar { get; init; }
    public bool JaVotou { get; init; }
    public bool EleitorApto { get; init; }
    public bool EleicaoAberta { get; init; }
    public string? MotivoImpedimento { get; init; }
}

public record EleitorDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public Guid ProfissionalId { get; init; }
    public string ProfissionalNome { get; init; } = string.Empty;
    public string? NumeroInscricao { get; init; }
    public bool Apto { get; init; }
    public string? MotivoInaptidao { get; init; }
    public bool Votou { get; init; }
    public DateTime? DataVoto { get; init; }
    public Guid? SecaoId { get; init; }
    public string? SecaoNome { get; init; }
}

public record EstatisticasVotacaoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public int TotalEleitoresAptos { get; init; }
    public int TotalVotantes { get; init; }
    public int TotalAbstencoes { get; init; }
    public double PercentualComparecimento { get; init; }
    public double PercentualAbstencao { get; init; }
    public int VotosPresenciais { get; init; }
    public int VotosOnline { get; init; }
    public DateTime? UltimaAtualizacao { get; init; }
}

public record CedulaEleitoralDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public Guid EleitorId { get; init; }
    public string EleitorNome { get; init; } = string.Empty;
    public IEnumerable<OpcaoVotoDto> Opcoes { get; init; } = new List<OpcaoVotoDto>();
    public bool PermiteVotoBranco { get; init; }
    public DateTime ValidoAte { get; init; }
}

public record OpcaoVotoDto
{
    public Guid ChapaId { get; init; }
    public int Numero { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Sigla { get; init; }
    public string? PresidenteNome { get; init; }
    public int Ordem { get; init; }
}
