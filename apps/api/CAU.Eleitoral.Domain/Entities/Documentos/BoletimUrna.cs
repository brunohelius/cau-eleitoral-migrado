using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for ballot reports (Boletim de Urna)
/// </summary>
public class BoletimUrna : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? SecaoId { get; set; }
    public virtual SecaoEleitoral? Secao { get; set; }

    public Guid? ZonaId { get; set; }
    public virtual ZonaEleitoral? Zona { get; set; }

    public string? NumeroUrna { get; set; }
    public string? CodigoIdentificacao { get; set; }

    public DateTime DataGeracao { get; set; }
    public TimeSpan? HoraAbertura { get; set; }
    public TimeSpan? HoraEncerramento { get; set; }

    public int TotalEleitoresAptos { get; set; }
    public int TotalComparecimento { get; set; }
    public int TotalAbstencao { get; set; }

    public int TotalVotosValidos { get; set; }
    public int TotalVotosBrancos { get; set; }
    public int TotalVotosNulos { get; set; }

    public double PercentualComparecimento { get; set; }
    public double PercentualAbstencao { get; set; }

    public string? HashVerificacao { get; set; }
    public string? Assinaturas { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }

    public string? Observacoes { get; set; }

    public virtual ICollection<VotoChapa> VotosChapas { get; set; } = new List<VotoChapa>();
}
