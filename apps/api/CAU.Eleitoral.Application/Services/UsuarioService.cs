using System.Security.Cryptography;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Usuarios;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Usuarios;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

public class UsuarioService : IUsuarioService
{
    private readonly IRepository<Usuario> _usuarioRepository;
    private readonly IRepository<Role> _roleRepository;
    private readonly IRepository<UsuarioRole> _usuarioRoleRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<UsuarioService> _logger;

    public UsuarioService(
        IRepository<Usuario> usuarioRepository,
        IRepository<Role> roleRepository,
        IRepository<UsuarioRole> usuarioRoleRepository,
        IUnitOfWork unitOfWork,
        ILogger<UsuarioService> logger)
    {
        _usuarioRepository = usuarioRepository;
        _roleRepository = roleRepository;
        _usuarioRoleRepository = usuarioRoleRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    public async Task<UsuarioDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var usuario = await _usuarioRepository.Query()
            .Include(u => u.UsuarioRoles)
            .ThenInclude(ur => ur.Role)
            .FirstOrDefaultAsync(u => u.Id == id, cancellationToken);

        return usuario == null ? null : MapToDto(usuario);
    }

    public async Task<UsuarioDto?> GetByEmailAsync(string email, CancellationToken cancellationToken = default)
    {
        var usuario = await _usuarioRepository.Query()
            .Include(u => u.UsuarioRoles)
            .ThenInclude(ur => ur.Role)
            .FirstOrDefaultAsync(u => u.Email.ToLower() == email.ToLower(), cancellationToken);

        return usuario == null ? null : MapToDto(usuario);
    }

    public async Task<IEnumerable<UsuarioDto>> GetAllAsync(CancellationToken cancellationToken = default)
    {
        var usuarios = await _usuarioRepository.Query()
            .Include(u => u.UsuarioRoles)
            .ThenInclude(ur => ur.Role)
            .OrderBy(u => u.Nome)
            .ToListAsync(cancellationToken);

        return usuarios.Select(MapToDto);
    }

    public async Task<IEnumerable<UsuarioDto>> GetByRoleAsync(string roleName, CancellationToken cancellationToken = default)
    {
        var usuarios = await _usuarioRepository.Query()
            .Include(u => u.UsuarioRoles)
            .ThenInclude(ur => ur.Role)
            .Where(u => u.UsuarioRoles.Any(ur => ur.Role.Nome == roleName))
            .OrderBy(u => u.Nome)
            .ToListAsync(cancellationToken);

        return usuarios.Select(MapToDto);
    }

    public async Task<UsuarioDto> CreateAsync(CreateUsuarioDto dto, CancellationToken cancellationToken = default)
    {
        // Check if email already exists
        var emailExists = await _usuarioRepository.AnyAsync(
            u => u.Email.ToLower() == dto.Email.ToLower(), cancellationToken);

        if (emailExists)
            throw new InvalidOperationException("Email ja esta em uso");

        var (hash, salt) = HashPassword(dto.Password);

        var usuario = new Usuario
        {
            Email = dto.Email.ToLower(),
            Nome = dto.Nome,
            NomeCompleto = dto.NomeCompleto,
            Cpf = dto.Cpf,
            Telefone = dto.Telefone,
            PasswordHash = hash,
            PasswordSalt = salt,
            Status = StatusUsuario.PendenteConfirmacao,
            Tipo = dto.Tipo,
            TokenConfirmacaoEmail = GenerateToken(),
            TokenConfirmacaoEmailExpiracao = DateTime.UtcNow.AddDays(7)
        };

        await _usuarioRepository.AddAsync(usuario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        // Add roles
        if (dto.Roles != null && dto.Roles.Any())
        {
            foreach (var roleName in dto.Roles)
            {
                var role = await _roleRepository.FirstOrDefaultAsync(
                    r => r.Nome == roleName, cancellationToken);

                if (role != null)
                {
                    var usuarioRole = new UsuarioRole
                    {
                        UsuarioId = usuario.Id,
                        RoleId = role.Id
                    };
                    await _usuarioRoleRepository.AddAsync(usuarioRole, cancellationToken);
                }
            }
            await _unitOfWork.SaveChangesAsync(cancellationToken);
        }

        _logger.LogInformation("Usuario criado: {UsuarioId} - {Email}", usuario.Id, usuario.Email);

        return await GetByIdAsync(usuario.Id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar usuario criado");
    }

    public async Task<UsuarioDto> UpdateAsync(Guid id, UpdateUsuarioDto dto, CancellationToken cancellationToken = default)
    {
        var usuario = await _usuarioRepository.Query()
            .Include(u => u.UsuarioRoles)
            .FirstOrDefaultAsync(u => u.Id == id, cancellationToken)
            ?? throw new KeyNotFoundException($"Usuario {id} nao encontrado");

        if (dto.Nome != null) usuario.Nome = dto.Nome;
        if (dto.NomeCompleto != null) usuario.NomeCompleto = dto.NomeCompleto;
        if (dto.Telefone != null) usuario.Telefone = dto.Telefone;
        if (dto.Tipo.HasValue) usuario.Tipo = dto.Tipo.Value;

        await _usuarioRepository.UpdateAsync(usuario, cancellationToken);

        // Update roles if provided
        if (dto.Roles != null)
        {
            // Remove existing roles
            foreach (var ur in usuario.UsuarioRoles.ToList())
            {
                await _usuarioRoleRepository.DeleteAsync(ur, cancellationToken);
            }

            // Add new roles
            foreach (var roleName in dto.Roles)
            {
                var role = await _roleRepository.FirstOrDefaultAsync(
                    r => r.Nome == roleName, cancellationToken);

                if (role != null)
                {
                    var usuarioRole = new UsuarioRole
                    {
                        UsuarioId = usuario.Id,
                        RoleId = role.Id
                    };
                    await _usuarioRoleRepository.AddAsync(usuarioRole, cancellationToken);
                }
            }
        }

        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Usuario atualizado: {UsuarioId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar usuario atualizado");
    }

    public async Task DeleteAsync(Guid id, CancellationToken cancellationToken = default)
    {
        await _usuarioRepository.SoftDeleteAsync(id, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Usuario excluido: {UsuarioId}", id);
    }

    public async Task<bool> ValidatePasswordAsync(Guid id, string password, CancellationToken cancellationToken = default)
    {
        var usuario = await _usuarioRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Usuario {id} nao encontrado");

        return VerifyPassword(password, usuario.PasswordHash, usuario.PasswordSalt);
    }

    public async Task<UsuarioDto> ChangePasswordAsync(Guid id, string currentPassword, string newPassword, CancellationToken cancellationToken = default)
    {
        var usuario = await _usuarioRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Usuario {id} nao encontrado");

        if (!VerifyPassword(currentPassword, usuario.PasswordHash, usuario.PasswordSalt))
            throw new InvalidOperationException("Senha atual incorreta");

        var (hash, salt) = HashPassword(newPassword);
        usuario.PasswordHash = hash;
        usuario.PasswordSalt = salt;

        await _usuarioRepository.UpdateAsync(usuario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Senha alterada para usuario: {UsuarioId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar usuario");
    }

    public async Task<UsuarioDto> BlockAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var usuario = await _usuarioRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Usuario {id} nao encontrado");

        usuario.Status = StatusUsuario.Bloqueado;

        await _usuarioRepository.UpdateAsync(usuario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Usuario bloqueado: {UsuarioId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar usuario");
    }

    public async Task<UsuarioDto> UnblockAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var usuario = await _usuarioRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Usuario {id} nao encontrado");

        usuario.Status = StatusUsuario.Ativo;
        usuario.TentativasLogin = 0;
        usuario.BloqueadoAte = null;

        await _usuarioRepository.UpdateAsync(usuario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Usuario desbloqueado: {UsuarioId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar usuario");
    }

    public async Task<string> GeneratePasswordResetTokenAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var usuario = await _usuarioRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Usuario {id} nao encontrado");

        var token = GenerateToken();
        usuario.TokenRecuperacaoSenha = token;
        usuario.TokenRecuperacaoSenhaExpiracao = DateTime.UtcNow.AddHours(24);

        await _usuarioRepository.UpdateAsync(usuario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Token de recuperacao de senha gerado para usuario: {UsuarioId}", id);

        return token;
    }

    public async Task<UsuarioDto> ResetPasswordAsync(string token, string newPassword, CancellationToken cancellationToken = default)
    {
        var usuario = await _usuarioRepository.FirstOrDefaultAsync(
            u => u.TokenRecuperacaoSenha == token, cancellationToken)
            ?? throw new InvalidOperationException("Token invalido");

        if (usuario.TokenRecuperacaoSenhaExpiracao < DateTime.UtcNow)
            throw new InvalidOperationException("Token expirado");

        var (hash, salt) = HashPassword(newPassword);
        usuario.PasswordHash = hash;
        usuario.PasswordSalt = salt;
        usuario.TokenRecuperacaoSenha = null;
        usuario.TokenRecuperacaoSenhaExpiracao = null;

        await _usuarioRepository.UpdateAsync(usuario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Senha resetada para usuario: {UsuarioId}", usuario.Id);

        return await GetByIdAsync(usuario.Id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar usuario");
    }

    private static UsuarioDto MapToDto(Usuario usuario)
    {
        return new UsuarioDto
        {
            Id = usuario.Id,
            Email = usuario.Email,
            Nome = usuario.Nome,
            NomeCompleto = usuario.NomeCompleto,
            Cpf = usuario.Cpf,
            Telefone = usuario.Telefone,
            Status = usuario.Status,
            Tipo = usuario.Tipo,
            UltimoAcesso = usuario.UltimoAcesso,
            EmailConfirmado = usuario.EmailConfirmado,
            DoisFatoresHabilitado = usuario.DoisFatoresHabilitado,
            Roles = usuario.UsuarioRoles?.Select(ur => ur.Role?.Nome ?? "").Where(r => !string.IsNullOrEmpty(r)).ToList() ?? new List<string>(),
            CreatedAt = usuario.CreatedAt
        };
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

    private static string GenerateToken()
    {
        return Convert.ToBase64String(RandomNumberGenerator.GetBytes(32));
    }
}
