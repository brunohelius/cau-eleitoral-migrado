using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.DTOs.Usuarios;

public record UsuarioDto
{
    public Guid Id { get; init; }
    public string Email { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string? NomeCompleto { get; init; }
    public string? Cpf { get; init; }
    public string? Telefone { get; init; }
    public StatusUsuario Status { get; init; }
    public TipoUsuario Tipo { get; init; }
    public DateTime? UltimoAcesso { get; init; }
    public bool EmailConfirmado { get; init; }
    public bool DoisFatoresHabilitado { get; init; }
    public IEnumerable<string> Roles { get; init; } = new List<string>();
    public DateTime CreatedAt { get; init; }
}

public record CreateUsuarioDto
{
    public string Email { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string? NomeCompleto { get; init; }
    public string? Cpf { get; init; }
    public string? Telefone { get; init; }
    public string Password { get; init; } = string.Empty;
    public TipoUsuario Tipo { get; init; }
    public IEnumerable<string>? Roles { get; init; }
}

public record UpdateUsuarioDto
{
    public string? Nome { get; init; }
    public string? NomeCompleto { get; init; }
    public string? Telefone { get; init; }
    public TipoUsuario? Tipo { get; init; }
    public IEnumerable<string>? Roles { get; init; }
}

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
    public StatusProfissional Status { get; init; }
    public Guid? RegionalId { get; init; }
    public string? RegionalNome { get; init; }
    public bool EleitorApto { get; init; }
}
