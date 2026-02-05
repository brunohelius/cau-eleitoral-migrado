using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Usuarios;

public class Role : BaseEntity
{
    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public string? Codigo { get; set; }

    public bool Ativo { get; set; } = true;
    public bool SistemaRole { get; set; }

    public virtual ICollection<UsuarioRole> UsuarioRoles { get; set; } = new List<UsuarioRole>();
    public virtual ICollection<RolePermissao> RolePermissoes { get; set; } = new List<RolePermissao>();
}
