using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Chapas;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

public class VotoChapa : BaseEntity
{
    public Guid ResultadoId { get; set; }
    public virtual ResultadoEleicao Resultado { get; set; } = null!;

    public Guid ChapaId { get; set; }
    public virtual ChapaEleicao Chapa { get; set; } = null!;

    public int TotalVotos { get; set; }
    public double Percentual { get; set; }
    public int Posicao { get; set; }

    public bool Eleita { get; set; }
}
