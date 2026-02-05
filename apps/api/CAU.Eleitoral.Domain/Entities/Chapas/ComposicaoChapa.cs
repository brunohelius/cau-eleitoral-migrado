using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Chapas;

public class ComposicaoChapa : BaseEntity
{
    public Guid ChapaId { get; set; }
    public virtual ChapaEleicao Chapa { get; set; } = null!;

    public string Cargo { get; set; } = string.Empty;
    public int Quantidade { get; set; }
    public bool Obrigatorio { get; set; }
    public int Ordem { get; set; }

    public int? QuantidadeTitulares { get; set; }
    public int? QuantidadeSuplentes { get; set; }
}
