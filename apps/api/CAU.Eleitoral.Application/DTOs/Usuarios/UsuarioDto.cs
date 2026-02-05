using System.ComponentModel.DataAnnotations;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.DTOs.Usuarios;

#region Main DTOs

/// <summary>
/// DTO completo de Usuario
/// </summary>
public record UsuarioDto
{
    public Guid Id { get; init; }
    public string Email { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string? NomeCompleto { get; init; }
    public string? Cpf { get; init; }
    public string? Telefone { get; init; }
    public StatusUsuario Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public TipoUsuario Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public DateTime? UltimoAcesso { get; init; }
    public bool EmailConfirmado { get; init; }
    public bool DoisFatoresHabilitado { get; init; }
    public IEnumerable<string> Roles { get; init; } = new List<string>();
    public DateTime CreatedAt { get; init; }
    public DateTime? UpdatedAt { get; init; }
}

/// <summary>
/// DTO resumido para listagens
/// </summary>
public record UsuarioListDto
{
    public Guid Id { get; init; }
    public string Email { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public StatusUsuario Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public TipoUsuario Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public DateTime? UltimoAcesso { get; init; }
    public bool EmailConfirmado { get; init; }
    public IEnumerable<string> Roles { get; init; } = new List<string>();
}

/// <summary>
/// DTO detalhado do usuario com todas as informacoes
/// </summary>
public record UsuarioDetailDto : UsuarioDto
{
    public IEnumerable<RoleDto> RolesDetail { get; init; } = new List<RoleDto>();
    public DateTime? UltimaTrocaSenha { get; init; }
    public int TentativasLogin { get; init; }
    public DateTime? BloqueadoAte { get; init; }
    public ProfissionalDto? Profissional { get; init; }
    public IEnumerable<LogAcessoDto> UltimosAcessos { get; init; } = new List<LogAcessoDto>();
}

#endregion

#region Create/Update DTOs

/// <summary>
/// DTO para criacao de usuario
/// </summary>
public record CreateUsuarioDto
{
    [Required(ErrorMessage = "Email eh obrigatorio")]
    [EmailAddress(ErrorMessage = "Email invalido")]
    [StringLength(200, ErrorMessage = "Email deve ter no maximo 200 caracteres")]
    public string Email { get; init; } = string.Empty;

    [Required(ErrorMessage = "Nome eh obrigatorio")]
    [StringLength(100, MinimumLength = 2, ErrorMessage = "Nome deve ter entre 2 e 100 caracteres")]
    public string Nome { get; init; } = string.Empty;

    [StringLength(200, ErrorMessage = "NomeCompleto deve ter no maximo 200 caracteres")]
    public string? NomeCompleto { get; init; }

    [StringLength(11, MinimumLength = 11, ErrorMessage = "CPF deve ter 11 digitos")]
    [RegularExpression(@"^\d{11}$", ErrorMessage = "CPF deve conter apenas numeros")]
    public string? Cpf { get; init; }

    [Phone(ErrorMessage = "Telefone invalido")]
    [StringLength(20, ErrorMessage = "Telefone deve ter no maximo 20 caracteres")]
    public string? Telefone { get; init; }

    [Required(ErrorMessage = "Senha eh obrigatoria")]
    [StringLength(100, MinimumLength = 8, ErrorMessage = "Senha deve ter entre 8 e 100 caracteres")]
    [RegularExpression(@"^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$",
        ErrorMessage = "Senha deve conter pelo menos uma letra maiuscula, uma minuscula, um numero e um caractere especial")]
    public string Password { get; init; } = string.Empty;

    [Required(ErrorMessage = "Tipo eh obrigatorio")]
    public TipoUsuario Tipo { get; init; }

    public IEnumerable<string>? Roles { get; init; }

    public bool EnviarEmailConfirmacao { get; init; } = true;
}

/// <summary>
/// DTO para atualizacao de usuario
/// </summary>
public record UpdateUsuarioDto
{
    [StringLength(100, MinimumLength = 2, ErrorMessage = "Nome deve ter entre 2 e 100 caracteres")]
    public string? Nome { get; init; }

    [StringLength(200, ErrorMessage = "NomeCompleto deve ter no maximo 200 caracteres")]
    public string? NomeCompleto { get; init; }

    [Phone(ErrorMessage = "Telefone invalido")]
    [StringLength(20, ErrorMessage = "Telefone deve ter no maximo 20 caracteres")]
    public string? Telefone { get; init; }

    public TipoUsuario? Tipo { get; init; }

    public IEnumerable<string>? Roles { get; init; }
}

/// <summary>
/// DTO para atribuicao de roles a um usuario
/// </summary>
public record AssignRolesDto
{
    [Required(ErrorMessage = "Pelo menos uma role deve ser informada")]
    public IEnumerable<string> Roles { get; init; } = new List<string>();
}

#endregion

#region Password DTOs

/// <summary>
/// DTO para alteracao de senha pelo usuario
/// </summary>
public record ChangePasswordDto
{
    [Required(ErrorMessage = "Senha atual eh obrigatoria")]
    public string CurrentPassword { get; init; } = string.Empty;

    [Required(ErrorMessage = "Nova senha eh obrigatoria")]
    [StringLength(100, MinimumLength = 8, ErrorMessage = "Senha deve ter entre 8 e 100 caracteres")]
    [RegularExpression(@"^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$",
        ErrorMessage = "Senha deve conter pelo menos uma letra maiuscula, uma minuscula, um numero e um caractere especial")]
    public string NewPassword { get; init; } = string.Empty;

    [Required(ErrorMessage = "Confirmacao de senha eh obrigatoria")]
    [Compare("NewPassword", ErrorMessage = "As senhas nao conferem")]
    public string ConfirmPassword { get; init; } = string.Empty;
}

/// <summary>
/// DTO para reset de senha pelo admin
/// </summary>
public record AdminResetPasswordDto
{
    [Required(ErrorMessage = "Nova senha eh obrigatoria")]
    [StringLength(100, MinimumLength = 8, ErrorMessage = "Senha deve ter entre 8 e 100 caracteres")]
    public string NewPassword { get; init; } = string.Empty;

    public bool SendEmail { get; init; } = true;
}

/// <summary>
/// DTO para solicitar reset de senha
/// </summary>
public record RequestPasswordResetDto
{
    [Required(ErrorMessage = "Email eh obrigatorio")]
    [EmailAddress(ErrorMessage = "Email invalido")]
    public string Email { get; init; } = string.Empty;
}

/// <summary>
/// DTO para resetar senha com token
/// </summary>
public record ResetPasswordDto
{
    [Required(ErrorMessage = "Token eh obrigatorio")]
    public string Token { get; init; } = string.Empty;

    [Required(ErrorMessage = "Nova senha eh obrigatoria")]
    [StringLength(100, MinimumLength = 8, ErrorMessage = "Senha deve ter entre 8 e 100 caracteres")]
    [RegularExpression(@"^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$",
        ErrorMessage = "Senha deve conter pelo menos uma letra maiuscula, uma minuscula, um numero e um caractere especial")]
    public string NewPassword { get; init; } = string.Empty;

    [Required(ErrorMessage = "Confirmacao de senha eh obrigatoria")]
    [Compare("NewPassword", ErrorMessage = "As senhas nao conferem")]
    public string ConfirmPassword { get; init; } = string.Empty;
}

#endregion

#region Status DTOs

/// <summary>
/// DTO para alteracao de status do usuario
/// </summary>
public record ChangeStatusDto
{
    [Required(ErrorMessage = "Status eh obrigatorio")]
    public StatusUsuario Status { get; init; }

    [StringLength(500, ErrorMessage = "Motivo deve ter no maximo 500 caracteres")]
    public string? Motivo { get; init; }
}

/// <summary>
/// DTO para bloquear usuario
/// </summary>
public record BloquearUsuarioDto
{
    [Required(ErrorMessage = "Motivo eh obrigatorio")]
    [StringLength(500, MinimumLength = 10, ErrorMessage = "Motivo deve ter entre 10 e 500 caracteres")]
    public string Motivo { get; init; } = string.Empty;

    /// <summary>
    /// Data ate quando o usuario ficara bloqueado (null = indefinido)
    /// </summary>
    public DateTime? BloqueadoAte { get; init; }
}

/// <summary>
/// DTO para desbloquear usuario
/// </summary>
public record DesbloquearUsuarioDto
{
    [StringLength(500, ErrorMessage = "Observacao deve ter no maximo 500 caracteres")]
    public string? Observacao { get; init; }
}

#endregion

#region 2FA DTOs

/// <summary>
/// DTO para habilitar 2FA
/// </summary>
public record Enable2FADto
{
    [Required(ErrorMessage = "Codigo eh obrigatorio")]
    [StringLength(6, MinimumLength = 6, ErrorMessage = "Codigo deve ter 6 digitos")]
    [RegularExpression(@"^\d{6}$", ErrorMessage = "Codigo deve conter apenas numeros")]
    public string Code { get; init; } = string.Empty;
}

/// <summary>
/// DTO para desabilitar 2FA
/// </summary>
public record Disable2FADto
{
    [Required(ErrorMessage = "Senha eh obrigatoria")]
    public string Password { get; init; } = string.Empty;
}

/// <summary>
/// DTO de resposta para setup de 2FA
/// </summary>
public record Setup2FAResponseDto
{
    public string Secret { get; init; } = string.Empty;
    public string QrCodeUrl { get; init; } = string.Empty;
    public IEnumerable<string> RecoveryCodes { get; init; } = new List<string>();
}

#endregion

#region Profissional DTOs

/// <summary>
/// DTO de profissional
/// </summary>
public record ProfissionalDto
{
    public Guid Id { get; init; }
    public Guid? UsuarioId { get; init; }
    public string RegistroCAU { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string Cpf { get; init; } = string.Empty;
    public string? Email { get; init; }
    public string? Telefone { get; init; }
    public TipoProfissional Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public StatusProfissional Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public Guid? RegionalId { get; init; }
    public string? RegionalNome { get; init; }
    public bool EleitorApto { get; init; }
    public DateTime? DataRegistro { get; init; }
    public DateTime? DataUltimaAtualizacao { get; init; }
}

/// <summary>
/// DTO para criacao/vinculacao de profissional
/// </summary>
public record CreateProfissionalDto
{
    [Required(ErrorMessage = "RegistroCAU eh obrigatorio")]
    [StringLength(20, MinimumLength = 5, ErrorMessage = "RegistroCAU deve ter entre 5 e 20 caracteres")]
    public string RegistroCAU { get; init; } = string.Empty;

    [Required(ErrorMessage = "Nome eh obrigatorio")]
    [StringLength(200, MinimumLength = 3, ErrorMessage = "Nome deve ter entre 3 e 200 caracteres")]
    public string Nome { get; init; } = string.Empty;

    [Required(ErrorMessage = "CPF eh obrigatorio")]
    [StringLength(11, MinimumLength = 11, ErrorMessage = "CPF deve ter 11 digitos")]
    [RegularExpression(@"^\d{11}$", ErrorMessage = "CPF deve conter apenas numeros")]
    public string Cpf { get; init; } = string.Empty;

    [EmailAddress(ErrorMessage = "Email invalido")]
    public string? Email { get; init; }

    [Phone(ErrorMessage = "Telefone invalido")]
    public string? Telefone { get; init; }

    [Required(ErrorMessage = "Tipo eh obrigatorio")]
    public TipoProfissional Tipo { get; init; }

    public Guid? RegionalId { get; init; }
}

#endregion

#region Role DTOs

/// <summary>
/// DTO para informacoes de role
/// </summary>
public record RoleDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public bool Ativo { get; init; }
    public int TotalUsuarios { get; init; }
    public IEnumerable<string> Permissoes { get; init; } = new List<string>();
}

/// <summary>
/// DTO para criacao de role
/// </summary>
public record CreateRoleDto
{
    [Required(ErrorMessage = "Nome eh obrigatorio")]
    [StringLength(50, MinimumLength = 2, ErrorMessage = "Nome deve ter entre 2 e 50 caracteres")]
    public string Nome { get; init; } = string.Empty;

    [StringLength(200, ErrorMessage = "Descricao deve ter no maximo 200 caracteres")]
    public string? Descricao { get; init; }

    public IEnumerable<string>? Permissoes { get; init; }
}

/// <summary>
/// DTO para atualizacao de role
/// </summary>
public record UpdateRoleDto
{
    [StringLength(50, MinimumLength = 2, ErrorMessage = "Nome deve ter entre 2 e 50 caracteres")]
    public string? Nome { get; init; }

    [StringLength(200, ErrorMessage = "Descricao deve ter no maximo 200 caracteres")]
    public string? Descricao { get; init; }

    public bool? Ativo { get; init; }

    public IEnumerable<string>? Permissoes { get; init; }
}

#endregion

#region Log Acesso DTOs

/// <summary>
/// DTO de log de acesso
/// </summary>
public record LogAcessoDto
{
    public Guid Id { get; init; }
    public Guid UsuarioId { get; init; }
    public string? IpAddress { get; init; }
    public string? UserAgent { get; init; }
    public string? Acao { get; init; }
    public bool Sucesso { get; init; }
    public string? Detalhes { get; init; }
    public DateTime DataAcesso { get; init; }
}

#endregion

#region Filtros e Paginacao

/// <summary>
/// Filtros para listagem de usuarios
/// </summary>
public record UsuarioFilterDto
{
    public TipoUsuario? Tipo { get; init; }
    public StatusUsuario? Status { get; init; }

    [StringLength(200, ErrorMessage = "Search deve ter no maximo 200 caracteres")]
    public string? Search { get; init; }

    public string? Role { get; init; }
    public bool? EmailConfirmado { get; init; }
    public bool? DoisFatoresHabilitado { get; init; }
    public DateTime? CriadoApos { get; init; }
    public DateTime? CriadoAntes { get; init; }

    [Range(1, int.MaxValue, ErrorMessage = "Page deve ser maior que 0")]
    public int Page { get; init; } = 1;

    [Range(1, 100, ErrorMessage = "PageSize deve ser entre 1 e 100")]
    public int PageSize { get; init; } = 20;

    public string? OrderBy { get; init; } = "Nome";
    public bool Ascending { get; init; } = true;
}

/// <summary>
/// Resultado paginado de usuarios
/// </summary>
public record UsuarioPagedResultDto
{
    public IEnumerable<UsuarioListDto> Items { get; init; } = new List<UsuarioListDto>();
    public int TotalCount { get; init; }
    public int Page { get; init; }
    public int PageSize { get; init; }
    public int TotalPages => PageSize > 0 ? (int)Math.Ceiling((double)TotalCount / PageSize) : 0;
    public bool HasNextPage => Page < TotalPages;
    public bool HasPreviousPage => Page > 1;
}

/// <summary>
/// Resposta paginada de usuarios (compatibilidade)
/// </summary>
public record PagedUsuarioResponseDto : UsuarioPagedResultDto
{
    public new IEnumerable<UsuarioDto> Items { get; init; } = new List<UsuarioDto>();
}

#endregion

#region Estatisticas DTOs

/// <summary>
/// Estatisticas de usuarios
/// </summary>
public record UsuarioEstatisticasDto
{
    public int Total { get; init; }
    public int Ativos { get; init; }
    public int Inativos { get; init; }
    public int Bloqueados { get; init; }
    public int PendentesConfirmacao { get; init; }
    public int Com2FA { get; init; }
    public Dictionary<string, int> PorTipo { get; init; } = new();
    public Dictionary<string, int> PorRole { get; init; } = new();
    public int NovosUltimos30Dias { get; init; }
    public int AcessosUltimos7Dias { get; init; }
}

#endregion
