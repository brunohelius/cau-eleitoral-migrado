using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Chapas;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for total votes tracking
/// </summary>
public class TotalVotos : BaseEntity
{
    public Guid ApuracaoId { get; set; }
    public virtual RegistroApuracaoVotos Apuracao { get; set; } = null!;

    public Guid? ChapaId { get; set; }
    public virtual ChapaEleicao? Chapa { get; set; }

    public int QuantidadeVotos { get; set; }
    public double Percentual { get; set; }

    public bool VotoBranco { get; set; }
    public bool VotoNulo { get; set; }
    public bool VotoAnulado { get; set; }

    public int Ordem { get; set; }

    public string? Observacoes { get; set; }
}
