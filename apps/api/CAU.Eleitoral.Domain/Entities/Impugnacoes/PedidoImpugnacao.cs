using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Impugnacoes;

public class PedidoImpugnacao : BaseEntity
{
    public Guid ImpugnacaoId { get; set; }
    public virtual ImpugnacaoResultado Impugnacao { get; set; } = null!;

    public string Descricao { get; set; } = string.Empty;
    public string? Fundamentacao { get; set; }
    public int Ordem { get; set; }

    public bool Deferido { get; set; }
    public string? MotivoIndeferimento { get; set; }

    public virtual ICollection<ArquivoPedidoImpugnacao> Arquivos { get; set; } = new List<ArquivoPedidoImpugnacao>();
}
