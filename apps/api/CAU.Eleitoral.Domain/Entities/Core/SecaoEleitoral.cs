using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Core;

public class SecaoEleitoral : BaseEntity
{
    public Guid ZonaEleitoralId { get; set; }
    public virtual ZonaEleitoral ZonaEleitoral { get; set; } = null!;

    public string Numero { get; set; } = string.Empty;
    public string? Local { get; set; }
    public string? Endereco { get; set; }

    public int CapacidadeEleitores { get; set; }
    public bool Acessivel { get; set; }

    public bool Ativo { get; set; } = true;
}
