using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Chapas;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class RecursoDenuncia : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Denuncia Denuncia { get; set; } = null!;

    public Guid? ChapaId { get; set; }
    public virtual ChapaEleicao? Chapa { get; set; }

    public string Protocolo { get; set; } = string.Empty;
    public StatusDenuncia Status { get; set; }

    public string Fundamentacao { get; set; } = string.Empty;
    public string? Pedido { get; set; }

    public DateTime DataApresentacao { get; set; }
    public DateTime PrazoLimite { get; set; }
    public bool Tempestivo { get; set; }

    public virtual ICollection<ContrarrazoesRecursoDenuncia> Contrarrazoes { get; set; } = new List<ContrarrazoesRecursoDenuncia>();
    public virtual JulgamentoRecursoDenuncia? Julgamento { get; set; }
}
