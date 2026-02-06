using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.DTOs.Votacao;

// ===== Core DTOs for Controller API =====

public record ElegibilidadeVotoDto
{
    public bool PodeVotar { get; init; }
    public bool JaVotou { get; init; }
    public string? MotivoInelegibilidade { get; init; }
    public bool EleicaoEmAndamento { get; init; }
    public DateTime? DataInicioVotacao { get; init; }
    public DateTime? DataFimVotacao { get; init; }
}

public record StatusVotoDto
{
    public bool Votou { get; init; }
    public DateTime? DataVoto { get; init; }
    public string? HashComprovante { get; init; }
}

public record CedulaVotacaoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public string? Instrucoes { get; init; }
    public List<OpcaoVotoCedulaDto> Opcoes { get; init; } = new();
    public bool PermiteBranco { get; init; }
    public bool PermiteNulo { get; init; }
}

public record OpcaoVotoCedulaDto
{
    public Guid ChapaId { get; init; }
    public int Numero { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Sigla { get; init; }
    public string? Lema { get; init; }
    public List<MembroChapaResumoDto> Membros { get; init; } = new();
}

public record MembroChapaResumoDto
{
    public string Nome { get; init; } = string.Empty;
    public string Cargo { get; init; } = string.Empty;
}

public record RegistrarVotoDto
{
    public Guid EleicaoId { get; init; }
    public Guid? ChapaId { get; init; }
    public TipoVoto TipoVoto { get; init; }
}

public record ComprovanteVotoDto
{
    public Guid Id { get; init; }
    public string Protocolo { get; init; } = string.Empty;
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public DateTime DataHoraVoto { get; init; }
    public string HashComprovante { get; init; } = string.Empty;
    public string? Mensagem { get; init; }
}

public record EleicaoVotacaoDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public DateTime DataInicioVotacao { get; init; }
    public DateTime DataFimVotacao { get; init; }
    public bool EmAndamento { get; init; }
    public bool JaVotou { get; init; }
    public int TotalChapas { get; init; }
}

public record HistoricoVotoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public int AnoEleicao { get; init; }
    public DateTime DataVoto { get; init; }
    public string HashComprovante { get; init; } = string.Empty;
}

public record EstatisticasVotacaoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public int TotalEleitores { get; init; }
    public int TotalEleitoresAptos { get; init; }
    public int TotalVotantes { get; init; }
    public int TotalVotos { get; init; }
    public int VotosValidos { get; init; }
    public int VotosBrancos { get; init; }
    public int VotosNulos { get; init; }
    public int VotosAnulados { get; init; }
    public int TotalAbstencoes { get; init; }
    public double PercentualComparecimento { get; init; }
    public double PercentualAbstencao { get; init; }
    public decimal PercentualParticipacao { get; init; }
    public int VotosPresenciais { get; init; }
    public int VotosOnline { get; init; }
    public DateTime? UltimaAtualizacao { get; init; }
}

public record EleitorVotouDto
{
    public Guid EleitorId { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? RegistroCAU { get; init; }
    public DateTime DataVoto { get; init; }
}

public record PagedResultDto<T>
{
    public IEnumerable<T> Items { get; init; } = new List<T>();
    public int TotalCount { get; init; }
    public int Page { get; init; }
    public int PageSize { get; init; }
    public int TotalPages => PageSize > 0 ? (int)Math.Ceiling(TotalCount / (double)PageSize) : 0;
    public bool HasNextPage => Page < TotalPages;
    public bool HasPreviousPage => Page > 1;
}

// ===== Legacy DTOs (for backwards compatibility) =====

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

public record RegistrarVotoLegadoDto
{
    public Guid EleicaoId { get; init; }
    public Guid EleitorId { get; init; }
    public Guid? ChapaId { get; init; }
    public TipoVoto Tipo { get; init; }
    public ModoVotacao Modo { get; init; }
    public string? IpAddress { get; init; }
    public string? UserAgent { get; init; }
}

public record ComprovanteVotoLegadoDto
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
