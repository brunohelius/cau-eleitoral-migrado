using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Usuarios;

public class Permissao : BaseEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public string? Modulo { get; set; }
    public string? Recurso { get; set; }
    public string? Acao { get; set; }

    public bool Ativo { get; set; } = true;

    public virtual ICollection<RolePermissao> RolePermissoes { get; set; } = new List<RolePermissao>();
}
