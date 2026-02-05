using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Chapas;

namespace CAU.Eleitoral.Domain.Entities.Impugnacoes;

public class RecursoImpugnacao : BaseEntity
{
    public Guid ImpugnacaoId { get; set; }
    public virtual ImpugnacaoResultado Impugnacao { get; set; } = null!;

    public Guid? ChapaId { get; set; }
    public virtual ChapaEleicao? Chapa { get; set; }

    public string Protocolo { get; set; } = string.Empty;
    public StatusImpugnacao Status { get; set; }

    public string Fundamentacao { get; set; } = string.Empty;
    public string? Pedido { get; set; }

    public DateTime DataApresentacao { get; set; }
    public DateTime PrazoLimite { get; set; }
    public bool Tempestivo { get; set; }

    public virtual ICollection<ContrarrazoesRecursoImpugnacao> Contrarrazoes { get; set; } = new List<ContrarrazoesRecursoImpugnacao>();
    public virtual JulgamentoRecursoImpugnacao? Julgamento { get; set; }
}
