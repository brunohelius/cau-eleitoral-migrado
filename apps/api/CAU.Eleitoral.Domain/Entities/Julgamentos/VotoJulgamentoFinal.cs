using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class VotoJulgamentoFinal : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public virtual JulgamentoFinal Julgamento { get; set; } = null!;

    public Guid MembroComissaoId { get; set; }
    public virtual MembroComissaoJulgadora MembroComissao { get; set; } = null!;

    public TipoVotoJulgamento Voto { get; set; }
    public string? Fundamentacao { get; set; }

    public DateTime DataVoto { get; set; }
    public bool VotoVencedor { get; set; }
    public bool VotoRelator { get; set; }
}
