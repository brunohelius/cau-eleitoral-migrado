using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Usuarios;

public class Usuario : BaseEntity
{
    public string Email { get; set; } = string.Empty;
    public string Nome { get; set; } = string.Empty;
    public string? NomeCompleto { get; set; }
    public string? Cpf { get; set; }
    public string? Telefone { get; set; }

    public string PasswordHash { get; set; } = string.Empty;
    public string? PasswordSalt { get; set; }

    public StatusUsuario Status { get; set; }
    public TipoUsuario Tipo { get; set; }

    public DateTime? UltimoAcesso { get; set; }
    public int TentativasLogin { get; set; }
    public DateTime? BloqueadoAte { get; set; }

    public bool EmailConfirmado { get; set; }
    public string? TokenConfirmacaoEmail { get; set; }
    public DateTime? TokenConfirmacaoEmailExpiracao { get; set; }

    public string? TokenRecuperacaoSenha { get; set; }
    public DateTime? TokenRecuperacaoSenhaExpiracao { get; set; }

    public bool DoisFatoresHabilitado { get; set; }
    public string? DoisFatoresSecret { get; set; }

    public string? RefreshToken { get; set; }
    public DateTime? RefreshTokenExpiracao { get; set; }

    public virtual ICollection<UsuarioRole> UsuarioRoles { get; set; } = new List<UsuarioRole>();
    public virtual Profissional? Profissional { get; set; }
    public virtual ICollection<LogAcesso> LogsAcesso { get; set; } = new List<LogAcesso>();
}
