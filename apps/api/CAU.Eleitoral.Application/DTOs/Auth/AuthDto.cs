namespace CAU.Eleitoral.Application.DTOs.Auth;

public record LoginRequestDto
{
    public string Email { get; init; } = string.Empty;
    public string Password { get; init; } = string.Empty;
    public bool RememberMe { get; init; }
}

public record LoginResponseDto
{
    public string AccessToken { get; init; } = string.Empty;
    public string RefreshToken { get; init; } = string.Empty;
    public DateTime ExpiresAt { get; init; }
    public UserInfoDto User { get; init; } = null!;
}

public record UserInfoDto
{
    public Guid Id { get; init; }
    public string Email { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string? NomeCompleto { get; init; }
    public IEnumerable<string> Roles { get; init; } = new List<string>();
    public IEnumerable<string> Permissions { get; init; } = new List<string>();
}

public record RegisterRequestDto
{
    public string Email { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string? NomeCompleto { get; init; }
    public string? Cpf { get; init; }
    public string? Telefone { get; init; }
    public string Password { get; init; } = string.Empty;
    public string ConfirmPassword { get; init; } = string.Empty;
    public string? RegistroCAU { get; init; }
}

public record RegisterResponseDto
{
    public Guid UserId { get; init; }
    public string Email { get; init; } = string.Empty;
    public bool RequiresEmailConfirmation { get; init; }
    public string Message { get; init; } = string.Empty;
}

public record ResetPasswordRequestDto
{
    public string Token { get; init; } = string.Empty;
    public string NewPassword { get; init; } = string.Empty;
    public string ConfirmPassword { get; init; } = string.Empty;
}

public record ChangePasswordRequestDto
{
    public string CurrentPassword { get; init; } = string.Empty;
    public string NewPassword { get; init; } = string.Empty;
    public string ConfirmPassword { get; init; } = string.Empty;
}
