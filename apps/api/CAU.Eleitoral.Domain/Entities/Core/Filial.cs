using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Core;

public class Filial : BaseEntity
{
    public Guid RegionalId { get; set; }
    public virtual RegionalCAU Regional { get; set; } = null!;

    public string Codigo { get; set; } = string.Empty;
    public string Nome { get; set; } = string.Empty;

    public string? Endereco { get; set; }
    public string? Cidade { get; set; }
    public string? UF { get; set; }
    public string? Cep { get; set; }

    public string? Telefone { get; set; }
    public string? Email { get; set; }

    public bool Ativo { get; set; } = true;
}
