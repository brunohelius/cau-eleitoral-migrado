using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

public class ResultadoEleicao : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public bool Parcial { get; set; }
    public bool Final { get; set; }

    public int TotalEleitoresAptos { get; set; }
    public int TotalVotantes { get; set; }
    public int TotalAbstencoes { get; set; }

    public int TotalVotosValidos { get; set; }
    public int TotalVotosBrancos { get; set; }
    public int TotalVotosNulos { get; set; }
    public int TotalVotosAnulados { get; set; }

    public double PercentualComparecimento { get; set; }
    public double PercentualAbstencao { get; set; }

    public DateTime DataApuracao { get; set; }
    public DateTime? DataPublicacao { get; set; }
    public bool Publicado { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? AtaApuracaoUrl { get; set; }

    public virtual ICollection<VotoChapa> VotosChapas { get; set; } = new List<VotoChapa>();
}
