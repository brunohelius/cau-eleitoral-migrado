using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Julgamentos;

namespace CAU.Eleitoral.Domain.Entities.Impugnacoes;

public class VotacaoJulgamentoImpugnacao : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public virtual JulgamentoImpugnacao Julgamento { get; set; } = null!;

    public Guid MembroComissaoId { get; set; }
    public virtual MembroComissaoJulgadora MembroComissao { get; set; } = null!;

    public TipoVotoJulgamento Voto { get; set; }
    public string? Fundamentacao { get; set; }

    public DateTime DataVoto { get; set; }
    public bool VotoVencedor { get; set; }
}
