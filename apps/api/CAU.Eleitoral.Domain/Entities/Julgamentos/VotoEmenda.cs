using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class VotoEmenda : BaseEntity
{
    public Guid EmendaId { get; set; }
    public virtual EmendaJulgamento Emenda { get; set; } = null!;

    public Guid MembroComissaoId { get; set; }
    public virtual MembroComissaoJulgadora MembroComissao { get; set; } = null!;

    public TipoVotoJulgamento Voto { get; set; }
    public DateTime DataVoto { get; set; }
    public int Ordem { get; set; }

    public string? Fundamentacao { get; set; }
    public string? Ressalva { get; set; }

    public bool Impedido { get; set; }
    public string? MotivoImpedimento { get; set; }
}
