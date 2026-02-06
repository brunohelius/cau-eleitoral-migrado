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

// Voter Auth DTOs
public record VoterVerificationRequestDto
{
    public string Cpf { get; init; } = string.Empty;
    public string RegistroCAU { get; init; } = string.Empty;
}

public record VoterLoginRequestDto
{
    public string Cpf { get; init; } = string.Empty;
    public string RegistroCAU { get; init; } = string.Empty;
    public string CodigoVerificacao { get; init; } = string.Empty;
}

// Candidate Auth DTOs
public record CandidateLoginRequestDto
{
    public string Cpf { get; init; } = string.Empty;
    public string RegistroCAU { get; init; } = string.Empty;
    public string Senha { get; init; } = string.Empty;
}

public record CandidateRegisterRequestDto
{
    public string Nome { get; init; } = string.Empty;
    public string Cpf { get; init; } = string.Empty;
    public string RegistroCAU { get; init; } = string.Empty;
    public string Email { get; init; } = string.Empty;
    public string? Telefone { get; init; }
    public string Senha { get; init; } = string.Empty;
    public string ConfirmacaoSenha { get; init; } = string.Empty;
    public bool AceitouTermos { get; init; }
}

public record CandidateForgotPasswordRequestDto
{
    public string Cpf { get; init; } = string.Empty;
    public string RegistroCAU { get; init; } = string.Empty;
}

public record CandidateResetPasswordRequestDto
{
    public string Token { get; init; } = string.Empty;
    public string NovaSenha { get; init; } = string.Empty;
    public string ConfirmacaoSenha { get; init; } = string.Empty;
}

public record CandidateChangePasswordRequestDto
{
    public string SenhaAtual { get; init; } = string.Empty;
    public string NovaSenha { get; init; } = string.Empty;
    public string ConfirmacaoSenha { get; init; } = string.Empty;
}

public record VerifyTokenRequestDto
{
    public string Token { get; init; } = string.Empty;
    public string Tipo { get; init; } = string.Empty;
}
