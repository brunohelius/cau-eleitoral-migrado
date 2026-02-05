using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Usuarios;
using CAU.Eleitoral.Domain.Entities.Julgamentos;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class VotacaoJulgamentoDenuncia : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public virtual JulgamentoDenuncia Julgamento { get; set; } = null!;

    public Guid MembroComissaoId { get; set; }
    public virtual MembroComissaoJulgadora MembroComissao { get; set; } = null!;

    public TipoVotoJulgamento Voto { get; set; }
    public string? Fundamentacao { get; set; }

    public DateTime DataVoto { get; set; }
    public bool VotoVencedor { get; set; }
}
