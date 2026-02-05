using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.DTOs.Apuracao;

public record ResultadoEleicaoDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public bool Parcial { get; init; }
    public bool Final { get; init; }
    public int TotalEleitoresAptos { get; init; }
    public int TotalVotantes { get; init; }
    public int TotalAbstencoes { get; init; }
    public int TotalVotosValidos { get; init; }
    public int TotalVotosBrancos { get; init; }
    public int TotalVotosNulos { get; init; }
    public int TotalVotosAnulados { get; init; }
    public double PercentualComparecimento { get; init; }
    public double PercentualAbstencao { get; init; }
    public DateTime DataApuracao { get; init; }
    public DateTime? DataPublicacao { get; init; }
    public bool Publicado { get; init; }
    public string? ArquivoUrl { get; init; }
    public string? AtaApuracaoUrl { get; init; }
    public IEnumerable<VotoChapaDto> VotosChapas { get; init; } = new List<VotoChapaDto>();
}

public record VotoChapaDto
{
    public Guid Id { get; init; }
    public Guid ResultadoId { get; init; }
    public Guid ChapaId { get; init; }
    public string ChapaNome { get; init; } = string.Empty;
    public int ChapaNumero { get; init; }
    public int TotalVotos { get; init; }
    public double Percentual { get; init; }
    public int Posicao { get; init; }
    public bool Eleita { get; init; }
}

public record IniciarApuracaoDto
{
    public Guid EleicaoId { get; init; }
    public bool Parcial { get; init; }
}

public record ApuracaoResumoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public StatusEleicao StatusEleicao { get; init; }
    public bool ApuracaoIniciada { get; init; }
    public bool ApuracaoFinalizada { get; init; }
    public int TotalVotosContados { get; init; }
    public int TotalVotosPendentes { get; init; }
    public double ProgressoApuracao { get; init; }
    public DateTime? DataInicioApuracao { get; init; }
    public DateTime? DataFimApuracao { get; init; }
    public IEnumerable<ApuracaoParcialChapaDto> ResultadosParciais { get; init; } = new List<ApuracaoParcialChapaDto>();
}

public record ApuracaoParcialChapaDto
{
    public Guid ChapaId { get; init; }
    public string ChapaNome { get; init; } = string.Empty;
    public int ChapaNumero { get; init; }
    public int TotalVotos { get; init; }
    public double Percentual { get; init; }
    public int Posicao { get; init; }
}

public record FinalizarApuracaoDto
{
    public Guid EleicaoId { get; init; }
    public string? Observacoes { get; init; }
}

public record PublicarResultadoDto
{
    public Guid ResultadoId { get; init; }
    public DateTime? DataPublicacao { get; init; }
}

public record AtaApuracaoDto
{
    public Guid Id { get; init; }
    public Guid ResultadoId { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public DateTime DataApuracao { get; init; }
    public string Local { get; init; } = string.Empty;
    public string Conteudo { get; init; } = string.Empty;
    public IEnumerable<MembroMesaApuracaoDto> MembrosPresentes { get; init; } = new List<MembroMesaApuracaoDto>();
    public string? ArquivoUrl { get; init; }
    public DateTime CreatedAt { get; init; }
}

public record MembroMesaApuracaoDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string Cargo { get; init; } = string.Empty;
    public bool Assinou { get; init; }
    public DateTime? DataAssinatura { get; init; }
}

public record GerarAtaApuracaoDto
{
    public Guid ResultadoId { get; init; }
    public string Local { get; init; } = string.Empty;
    public string? Observacoes { get; init; }
    public IEnumerable<Guid> MembrosPresentes { get; init; } = new List<Guid>();
}

public record AnularVotoDto
{
    public Guid VotoId { get; init; }
    public string Motivo { get; init; } = string.Empty;
    public Guid AutorId { get; init; }
}

public record ReanalizarVotoDto
{
    public Guid VotoId { get; init; }
    public string Motivo { get; init; } = string.Empty;
}
