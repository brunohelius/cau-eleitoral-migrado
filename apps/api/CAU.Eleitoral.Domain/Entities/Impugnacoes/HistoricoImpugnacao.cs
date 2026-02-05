using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Impugnacoes;

public class HistoricoImpugnacao : BaseEntity
{
    public Guid ImpugnacaoId { get; set; }
    public virtual ImpugnacaoResultado Impugnacao { get; set; } = null!;

    public StatusImpugnacao StatusAnterior { get; set; }
    public StatusImpugnacao StatusNovo { get; set; }

    public string? Descricao { get; set; }
    public string? Observacao { get; set; }
    public DateTime DataAlteracao { get; set; }
    public string? AlteradoPor { get; set; }
}
