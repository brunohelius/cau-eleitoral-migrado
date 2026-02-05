using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Usuarios;

public class UsuarioRole : BaseEntity
{
    public Guid UsuarioId { get; set; }
    public virtual Usuario Usuario { get; set; } = null!;

    public Guid RoleId { get; set; }
    public virtual Role Role { get; set; } = null!;

    public DateTime? ValidoAte { get; set; }
    public bool Ativo { get; set; } = true;
}
