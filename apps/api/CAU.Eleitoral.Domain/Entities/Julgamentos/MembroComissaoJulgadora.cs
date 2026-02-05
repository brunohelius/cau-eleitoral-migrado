using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class MembroComissaoJulgadora : BaseEntity
{
    public Guid ComissaoId { get; set; }
    public virtual ComissaoJulgadora Comissao { get; set; } = null!;

    public Guid ConselheiroId { get; set; }
    public virtual Conselheiro Conselheiro { get; set; } = null!;

    public TipoMembroComissao Tipo { get; set; }
    public int Ordem { get; set; }

    public DateTime? DataInicio { get; set; }
    public DateTime? DataFim { get; set; }

    public bool Ativo { get; set; } = true;
    public string? MotivoAfastamento { get; set; }
}
