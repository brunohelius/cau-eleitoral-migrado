using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for null votes tracking
/// </summary>
public class VotoNulo : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? SecaoId { get; set; }
    public virtual SecaoEleitoral? Secao { get; set; }

    public Guid? ZonaId { get; set; }
    public virtual ZonaEleitoral? Zona { get; set; }

    public Guid? RegionalId { get; set; }
    public virtual RegionalCAU? Regional { get; set; }

    public Guid? ApuracaoId { get; set; }
    public virtual RegistroApuracaoVotos? Apuracao { get; set; }

    public int Quantidade { get; set; }
    public double Percentual { get; set; }

    public string? Motivo { get; set; }

    public DateTime DataRegistro { get; set; }

    public string? Observacoes { get; set; }
}
