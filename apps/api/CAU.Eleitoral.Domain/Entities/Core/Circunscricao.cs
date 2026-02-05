using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Core;

public class Circunscricao : BaseEntity
{
    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public string? Codigo { get; set; }

    public Guid? RegionalId { get; set; }
    public virtual RegionalCAU? Regional { get; set; }

    public bool Ativo { get; set; } = true;

    public virtual ICollection<ZonaEleitoral> ZonasEleitorais { get; set; } = new List<ZonaEleitoral>();
}
