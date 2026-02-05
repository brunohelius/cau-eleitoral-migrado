using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Chapas;

namespace CAU.Eleitoral.Domain.Entities.Core;

/// <summary>
/// Registro de apuracao de resultado por eleicao/regiao
/// </summary>
public class ApuracaoResultado : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? RegiaoPleitoId { get; set; }
    public virtual RegiaoPleito? RegiaoPleito { get; set; }

    public Guid? SecaoId { get; set; }
    public virtual SecaoEleitoral? Secao { get; set; }

    public Guid? UrnaId { get; set; }
    public virtual UrnaEletronica? Urna { get; set; }

    public StatusApuracao Status { get; set; }

    public DateTime? DataInicio { get; set; }
    public DateTime? DataFim { get; set; }

    public int TotalEleitoresAptos { get; set; }
    public int TotalVotantes { get; set; }
    public int TotalAbstencoes { get; set; }

    public int TotalVotosValidos { get; set; }
    public int TotalVotosNulos { get; set; }
    public int TotalVotosBrancos { get; set; }
    public int TotalVotosAnulados { get; set; }

    public decimal PercentualParticipacao { get; set; }
    public decimal PercentualAbstencao { get; set; }

    public Guid? ChapaVencedoraId { get; set; }
    public virtual ChapaEleicao? ChapaVencedora { get; set; }
    public int? VotosChapaVencedora { get; set; }

    public string? HashApuracao { get; set; }
    public string? AssinaturaDigital { get; set; }

    public bool Parcial { get; set; }
    public int? PercentualApurado { get; set; }

    public bool Homologada { get; set; }
    public DateTime? DataHomologacao { get; set; }
    public string? HomologadoPor { get; set; }

    public string? Observacao { get; set; }

    public virtual ICollection<ApuracaoResultadoChapa> VotosPorChapa { get; set; } = new List<ApuracaoResultadoChapa>();
}
