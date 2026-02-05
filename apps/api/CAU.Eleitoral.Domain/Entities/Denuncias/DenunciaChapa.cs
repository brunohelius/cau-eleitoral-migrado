using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Chapas;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class DenunciaChapa : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Denuncia Denuncia { get; set; } = null!;

    public Guid ChapaId { get; set; }
    public virtual ChapaEleicao Chapa { get; set; } = null!;

    public bool Principal { get; set; }
    public string? Observacao { get; set; }

    public DateTime DataVinculacao { get; set; }
}
