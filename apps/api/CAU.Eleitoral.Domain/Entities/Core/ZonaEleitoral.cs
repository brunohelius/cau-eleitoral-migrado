using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Core;

public class ZonaEleitoral : BaseEntity
{
    public Guid CircunscricaoId { get; set; }
    public virtual Circunscricao Circunscricao { get; set; } = null!;

    public string Numero { get; set; } = string.Empty;
    public string Nome { get; set; } = string.Empty;
    public string? Endereco { get; set; }
    public string? Cidade { get; set; }
    public string? UF { get; set; }

    public bool Ativo { get; set; } = true;

    public virtual ICollection<SecaoEleitoral> SecoesEleitorais { get; set; } = new List<SecaoEleitoral>();
}
