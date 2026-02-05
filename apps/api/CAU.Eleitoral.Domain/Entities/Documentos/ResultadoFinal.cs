using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for final results
/// </summary>
public class ResultadoFinal : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? RegionalId { get; set; }
    public virtual RegionalCAU? Regional { get; set; }

    public StatusResultado Status { get; set; }

    public DateTime DataApuracao { get; set; }
    public DateTime? DataPublicacao { get; set; }
    public DateTime? DataHomologacao { get; set; }

    public bool Publicado { get; set; }
    public bool Homologado { get; set; }

    public int TotalSecoesApuradas { get; set; }
    public int TotalUrnas { get; set; }

    public int TotalEleitoresAptos { get; set; }
    public int TotalVotantes { get; set; }
    public int TotalAbstencoes { get; set; }

    public int TotalVotosValidos { get; set; }
    public int TotalVotosBrancos { get; set; }
    public int TotalVotosNulos { get; set; }
    public int TotalVotosAnulados { get; set; }

    public double PercentualComparecimento { get; set; }
    public double PercentualAbstencao { get; set; }

    public string? ChapaVencedoraId { get; set; }
    public int? VotosChapaVencedora { get; set; }
    public double? PercentualChapaVencedora { get; set; }

    public string? DadosJson { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? AtaApuracaoUrl { get; set; }

    public string? Observacoes { get; set; }

    public virtual ICollection<VotoChapa> VotosChapas { get; set; } = new List<VotoChapa>();
    public virtual ICollection<AssinaturaDigital> Assinaturas { get; set; } = new List<AssinaturaDigital>();
}
