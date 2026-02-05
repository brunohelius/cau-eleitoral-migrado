using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for partial results
/// </summary>
public class ResultadoParcial : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? RegionalId { get; set; }
    public virtual RegionalCAU? Regional { get; set; }

    public int NumeroAtualizacao { get; set; }
    public DateTime DataHoraAtualizacao { get; set; }

    public StatusResultado Status { get; set; }

    public int TotalSecoesApuradas { get; set; }
    public int TotalSecoesFaltantes { get; set; }
    public double PercentualApurado { get; set; }

    public int TotalEleitoresAptos { get; set; }
    public int TotalVotantes { get; set; }
    public int TotalAbstencoes { get; set; }

    public int TotalVotosValidos { get; set; }
    public int TotalVotosBrancos { get; set; }
    public int TotalVotosNulos { get; set; }

    public double PercentualComparecimento { get; set; }
    public double PercentualAbstencao { get; set; }

    public string? DadosJson { get; set; }

    public string? ArquivoUrl { get; set; }

    public string? Observacoes { get; set; }

    public virtual ICollection<VotoChapa> VotosChapas { get; set; } = new List<VotoChapa>();
}
