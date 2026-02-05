using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using System.Security.Cryptography;
using System.Text;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.Logging;
using Microsoft.IdentityModel.Tokens;
using CAU.Eleitoral.Application.DTOs.Auth;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Usuarios;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

public class AuthService : IAuthService
{
    private readonly IRepository<Usuario> _usuarioRepository;
    private readonly IRepository<Role> _roleRepository;
    private readonly IRepository<UsuarioRole> _usuarioRoleRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly IConfiguration _configuration;
    private readonly ILogger<AuthService> _logger;

    public AuthService(
        IRepository<Usuario> usuarioRepository,
        IRepository<Role> roleRepository,
        IRepository<UsuarioRole> usuarioRoleRepository,
        IUnitOfWork unitOfWork,
        IConfiguration configuration,
        ILogger<AuthService> logger)
    {
        _usuarioRepository = usuarioRepository;
        _roleRepository = roleRepository;
        _usuarioRoleRepository = usuarioRoleRepository;
        _unitOfWork = unitOfWork;
        _configuration = configuration;
        _logger = logger;
    }

    public async Task<LoginResponseDto> LoginAsync(LoginRequestDto request, CancellationToken cancellationToken = default)
    {
        var usuario = await _usuarioRepository.Query()
            .Include(u => u.UsuarioRoles)
            .ThenInclude(ur => ur.Role)
            .ThenInclude(r => r!.RolePermissoes)
            .ThenInclude(rp => rp.Permissao)
            .FirstOrDefaultAsync(u => u.Email.ToLower() == request.Email.ToLower(), cancellationToken);

        if (usuario == null)
        {
            _logger.LogWarning("Tentativa de login com email inexistente: {Email}", request.Email);
            throw new InvalidOperationException("Credenciais invalidas");
        }

        if (usuario.Status == StatusUsuario.Bloqueado)
        {
            if (usuario.BloqueadoAte.HasValue && usuario.BloqueadoAte.Value > DateTime.UtcNow)
            {
                throw new InvalidOperationException($"Usuario bloqueado ate {usuario.BloqueadoAte.Value:dd/MM/yyyy HH:mm}");
            }
            throw new InvalidOperationException("Usuario bloqueado");
        }

        if (usuario.Status == StatusUsuario.Inativo)
        {
            throw new InvalidOperationException("Usuario inativo");
        }

        if (!VerifyPassword(request.Password, usuario.PasswordHash, usuario.PasswordSalt))
        {
            usuario.TentativasLogin++;

            if (usuario.TentativasLogin >= 5)
            {
                usuario.Status = StatusUsuario.Bloqueado;
                usuario.BloqueadoAte = DateTime.UtcNow.AddMinutes(30);
            }

            await _usuarioRepository.UpdateAsync(usuario, cancellationToken);
            await _unitOfWork.SaveChangesAsync(cancellationToken);

            _logger.LogWarning("Tentativa de login com senha incorreta: {Email} - Tentativas: {Tentativas}",
                request.Email, usuario.TentativasLogin);

            throw new InvalidOperationException("Credenciais invalidas");
        }

        // Reset login attempts on successful login
        usuario.TentativasLogin = 0;
        usuario.UltimoAcesso = DateTime.UtcNow;

        // Generate tokens
        var accessToken = GenerateAccessToken(usuario);
        var refreshToken = GenerateRefreshToken();

        usuario.RefreshToken = refreshToken;
        usuario.RefreshTokenExpiracao = request.RememberMe
            ? DateTime.UtcNow.AddDays(30)
            : DateTime.UtcNow.AddDays(7);

        await _usuarioRepository.UpdateAsync(usuario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Login realizado com sucesso: {UsuarioId} - {Email}", usuario.Id, usuario.Email);

        var roles = usuario.UsuarioRoles?
            .Select(ur => ur.Role?.Nome ?? "")
            .Where(r => !string.IsNullOrEmpty(r))
            .ToList() ?? new List<string>();

        var permissions = usuario.UsuarioRoles?
            .SelectMany(ur => ur.Role?.RolePermissoes?.Select(rp => rp.Permissao?.Codigo ?? "") ?? Enumerable.Empty<string>())
            .Where(p => !string.IsNullOrEmpty(p))
            .Distinct()
            .ToList() ?? new List<string>();

        return new LoginResponseDto
        {
            AccessToken = accessToken,
            RefreshToken = refreshToken,
            ExpiresAt = DateTime.UtcNow.AddHours(1),
            User = new UserInfoDto
            {
                Id = usuario.Id,
                Email = usuario.Email,
                Nome = usuario.Nome,
                NomeCompleto = usuario.NomeCompleto,
                Roles = roles,
                Permissions = permissions
            }
        };
    }

    public async Task<LoginResponseDto> RefreshTokenAsync(string refreshToken, CancellationToken cancellationToken = default)
    {
        var usuario = await _usuarioRepository.Query()
            .Include(u => u.UsuarioRoles)
            .ThenInclude(ur => ur.Role)
            .ThenInclude(r => r!.RolePermissoes)
            .ThenInclude(rp => rp.Permissao)
            .FirstOrDefaultAsync(u => u.RefreshToken == refreshToken, cancellationToken);

        if (usuario == null)
        {
            throw new InvalidOperationException("Refresh token invalido");
        }

        if (usuario.RefreshTokenExpiracao < DateTime.UtcNow)
        {
            throw new InvalidOperationException("Refresh token expirado");
        }

        var accessToken = GenerateAccessToken(usuario);
        var newRefreshToken = GenerateRefreshToken();

        usuario.RefreshToken = newRefreshToken;
        usuario.RefreshTokenExpiracao = DateTime.UtcNow.AddDays(7);

        await _usuarioRepository.UpdateAsync(usuario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Token atualizado para usuario: {UsuarioId}", usuario.Id);

        var roles = usuario.UsuarioRoles?
            .Select(ur => ur.Role?.Nome ?? "")
            .Where(r => !string.IsNullOrEmpty(r))
            .ToList() ?? new List<string>();

        var permissions = usuario.UsuarioRoles?
            .SelectMany(ur => ur.Role?.RolePermissoes?.Select(rp => rp.Permissao?.Codigo ?? "") ?? Enumerable.Empty<string>())
            .Where(p => !string.IsNullOrEmpty(p))
            .Distinct()
            .ToList() ?? new List<string>();

        return new LoginResponseDto
        {
            AccessToken = accessToken,
            RefreshToken = newRefreshToken,
            ExpiresAt = DateTime.UtcNow.AddHours(1),
            User = new UserInfoDto
            {
                Id = usuario.Id,
                Email = usuario.Email,
                Nome = usuario.Nome,
                NomeCompleto = usuario.NomeCompleto,
                Roles = roles,
                Permissions = permissions
            }
        };
    }

    public async Task LogoutAsync(Guid userId, CancellationToken cancellationToken = default)
    {
        var usuario = await _usuarioRepository.GetByIdAsync(userId, cancellationToken);
        if (usuario == null) return;

        usuario.RefreshToken = null;
        usuario.RefreshTokenExpiracao = null;

        await _usuarioRepository.UpdateAsync(usuario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Logout realizado: {UsuarioId}", userId);
    }

    public async Task<bool> ValidateTokenAsync(string token, CancellationToken cancellationToken = default)
    {
        try
        {
            var key = _configuration["Jwt:Key"] ?? "DefaultSecretKeyForDevelopment123456789012345678901234567890";
            var tokenHandler = new JwtSecurityTokenHandler();
            var keyBytes = Encoding.UTF8.GetBytes(key);

            tokenHandler.ValidateToken(token, new TokenValidationParameters
            {
                ValidateIssuerSigningKey = true,
                IssuerSigningKey = new SymmetricSecurityKey(keyBytes),
                ValidateIssuer = true,
                ValidIssuer = _configuration["Jwt:Issuer"] ?? "CAU.Eleitoral",
                ValidateAudience = true,
                ValidAudience = _configuration["Jwt:Audience"] ?? "CAU.Eleitoral.Client",
                ValidateLifetime = true,
                ClockSkew = TimeSpan.Zero
            }, out _);

            return true;
        }
        catch
        {
            return false;
        }
    }

    public async Task<RegisterResponseDto> RegisterAsync(RegisterRequestDto request, CancellationToken cancellationToken = default)
    {
        // Validate password match
        if (request.Password != request.ConfirmPassword)
        {
            throw new InvalidOperationException("Senhas nao conferem");
        }

        // Check if email exists
        var emailExists = await _usuarioRepository.AnyAsync(
            u => u.Email.ToLower() == request.Email.ToLower(), cancellationToken);

        if (emailExists)
        {
            throw new InvalidOperationException("Email ja esta em uso");
        }

        var (hash, salt) = HashPassword(request.Password);

        var usuario = new Usuario
        {
            Email = request.Email.ToLower(),
            Nome = request.Nome,
            NomeCompleto = request.NomeCompleto,
            Cpf = request.Cpf,
            Telefone = request.Telefone,
            PasswordHash = hash,
            PasswordSalt = salt,
            Status = StatusUsuario.PendenteConfirmacao,
            Tipo = TipoUsuario.Profissional,
            TokenConfirmacaoEmail = GenerateToken(),
            TokenConfirmacaoEmailExpiracao = DateTime.UtcNow.AddDays(7)
        };

        await _usuarioRepository.AddAsync(usuario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        // Add default role
        var defaultRole = await _roleRepository.FirstOrDefaultAsync(
            r => r.Nome == "Profissional", cancellationToken);

        if (defaultRole != null)
        {
            var usuarioRole = new UsuarioRole
            {
                UsuarioId = usuario.Id,
                RoleId = defaultRole.Id
            };
            await _usuarioRoleRepository.AddAsync(usuarioRole, cancellationToken);
            await _unitOfWork.SaveChangesAsync(cancellationToken);
        }

        _logger.LogInformation("Usuario registrado: {UsuarioId} - {Email}", usuario.Id, usuario.Email);

        return new RegisterResponseDto
        {
            UserId = usuario.Id,
            Email = usuario.Email,
            RequiresEmailConfirmation = true,
            Message = "Cadastro realizado com sucesso. Verifique seu email para confirmar a conta."
        };
    }

    public async Task<bool> ConfirmEmailAsync(string token, CancellationToken cancellationToken = default)
    {
        var usuario = await _usuarioRepository.FirstOrDefaultAsync(
            u => u.TokenConfirmacaoEmail == token, cancellationToken);

        if (usuario == null)
        {
            return false;
        }

        if (usuario.TokenConfirmacaoEmailExpiracao < DateTime.UtcNow)
        {
            return false;
        }

        usuario.EmailConfirmado = true;
        usuario.TokenConfirmacaoEmail = null;
        usuario.TokenConfirmacaoEmailExpiracao = null;
        usuario.Status = StatusUsuario.Ativo;

        await _usuarioRepository.UpdateAsync(usuario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Email confirmado para usuario: {UsuarioId}", usuario.Id);

        return true;
    }

    public async Task<string> ForgotPasswordAsync(string email, CancellationToken cancellationToken = default)
    {
        var usuario = await _usuarioRepository.FirstOrDefaultAsync(
            u => u.Email.ToLower() == email.ToLower(), cancellationToken);

        if (usuario == null)
        {
            // Return success message even if user doesn't exist for security
            return "Se o email estiver cadastrado, voce recebera um link para redefinir sua senha.";
        }

        var token = GenerateToken();
        usuario.TokenRecuperacaoSenha = token;
        usuario.TokenRecuperacaoSenhaExpiracao = DateTime.UtcNow.AddHours(24);

        await _usuarioRepository.UpdateAsync(usuario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Token de recuperacao de senha gerado para: {Email}", email);

        // In a real implementation, send email here
        return "Se o email estiver cadastrado, voce recebera um link para redefinir sua senha.";
    }

    public async Task<bool> ResetPasswordAsync(ResetPasswordRequestDto request, CancellationToken cancellationToken = default)
    {
        if (request.NewPassword != request.ConfirmPassword)
        {
            throw new InvalidOperationException("Senhas nao conferem");
        }

        var usuario = await _usuarioRepository.FirstOrDefaultAsync(
            u => u.TokenRecuperacaoSenha == request.Token, cancellationToken);

        if (usuario == null)
        {
            throw new InvalidOperationException("Token invalido");
        }

        if (usuario.TokenRecuperacaoSenhaExpiracao < DateTime.UtcNow)
        {
            throw new InvalidOperationException("Token expirado");
        }

        var (hash, salt) = HashPassword(request.NewPassword);
        usuario.PasswordHash = hash;
        usuario.PasswordSalt = salt;
        usuario.TokenRecuperacaoSenha = null;
        usuario.TokenRecuperacaoSenhaExpiracao = null;

        await _usuarioRepository.UpdateAsync(usuario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Senha resetada para usuario: {UsuarioId}", usuario.Id);

        return true;
    }

    private string GenerateAccessToken(Usuario usuario)
    {
        var key = _configuration["Jwt:Key"] ?? "DefaultSecretKeyForDevelopment123456789012345678901234567890";
        var issuer = _configuration["Jwt:Issuer"] ?? "CAU.Eleitoral";
        var audience = _configuration["Jwt:Audience"] ?? "CAU.Eleitoral.Client";

        var securityKey = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(key));
        var credentials = new SigningCredentials(securityKey, SecurityAlgorithms.HmacSha256);

        var claims = new List<Claim>
        {
            new(ClaimTypes.NameIdentifier, usuario.Id.ToString()),
            new(ClaimTypes.Email, usuario.Email),
            new(ClaimTypes.Name, usuario.Nome),
            new("tipo", usuario.Tipo.ToString())
        };

        // Add role claims
        if (usuario.UsuarioRoles != null)
        {
            foreach (var ur in usuario.UsuarioRoles.Where(ur => ur.Role != null))
            {
                claims.Add(new Claim(ClaimTypes.Role, ur.Role!.Nome));
            }
        }

        var token = new JwtSecurityToken(
            issuer: issuer,
            audience: audience,
            claims: claims,
            expires: DateTime.UtcNow.AddHours(1),
            signingCredentials: credentials);

        return new JwtSecurityTokenHandler().WriteToken(token);
    }

    private static string GenerateRefreshToken()
    {
        return Convert.ToBase64String(RandomNumberGenerator.GetBytes(64));
    }

    private static string GenerateToken()
    {
        return Convert.ToBase64String(RandomNumberGenerator.GetBytes(32));
    }

    private static (string Hash, string Salt) HashPassword(string password)
    {
        var saltBytes = RandomNumberGenerator.GetBytes(16);
        var salt = Convert.ToBase64String(saltBytes);

        using var pbkdf2 = new Rfc2898DeriveBytes(password, saltBytes, 100000, HashAlgorithmName.SHA256);
        var hash = Convert.ToBase64String(pbkdf2.GetBytes(32));

        return (hash, salt);
    }

    private static bool VerifyPassword(string password, string hash, string? salt)
    {
        if (string.IsNullOrEmpty(salt)) return false;

        var saltBytes = Convert.FromBase64String(salt);
        using var pbkdf2 = new Rfc2898DeriveBytes(password, saltBytes, 100000, HashAlgorithmName.SHA256);
        var computedHash = Convert.ToBase64String(pbkdf2.GetBytes(32));

        return hash == computedHash;
    }
}
