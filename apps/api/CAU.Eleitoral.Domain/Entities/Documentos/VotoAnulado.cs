using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for annulled votes tracking
/// </summary>
public class VotoAnulado : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? VotoId { get; set; }
    public virtual Voto? Voto { get; set; }

    public Guid? SecaoId { get; set; }
    public virtual SecaoEleitoral? Secao { get; set; }

    public Guid? ZonaId { get; set; }
    public virtual ZonaEleitoral? Zona { get; set; }

    public Guid? ApuracaoId { get; set; }
    public virtual RegistroApuracaoVotos? Apuracao { get; set; }

    public int Quantidade { get; set; }
    public double Percentual { get; set; }

    public string Motivo { get; set; } = string.Empty;
    public string? Justificativa { get; set; }

    public Guid? AnuladoPorId { get; set; }
    public virtual Usuario? AnuladoPor { get; set; }

    public DateTime DataAnulacao { get; set; }

    public string? NumeroProcesso { get; set; }
    public string? Decisao { get; set; }

    public string? ArquivoUrl { get; set; }

    public string? Observacoes { get; set; }
}
