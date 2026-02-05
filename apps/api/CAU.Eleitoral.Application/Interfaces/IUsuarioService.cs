using CAU.Eleitoral.Application.DTOs.Usuarios;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IUsuarioService
{
    Task<UsuarioDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<UsuarioDto?> GetByEmailAsync(string email, CancellationToken cancellationToken = default);
    Task<IEnumerable<UsuarioDto>> GetAllAsync(CancellationToken cancellationToken = default);
    Task<IEnumerable<UsuarioDto>> GetByRoleAsync(string roleName, CancellationToken cancellationToken = default);
    Task<UsuarioDto> CreateAsync(CreateUsuarioDto dto, CancellationToken cancellationToken = default);
    Task<UsuarioDto> UpdateAsync(Guid id, UpdateUsuarioDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);
    Task<bool> ValidatePasswordAsync(Guid id, string password, CancellationToken cancellationToken = default);
    Task<UsuarioDto> ChangePasswordAsync(Guid id, string currentPassword, string newPassword, CancellationToken cancellationToken = default);
    Task<UsuarioDto> BlockAsync(Guid id, CancellationToken cancellationToken = default);
    Task<UsuarioDto> UnblockAsync(Guid id, CancellationToken cancellationToken = default);
    Task<string> GeneratePasswordResetTokenAsync(Guid id, CancellationToken cancellationToken = default);
    Task<UsuarioDto> ResetPasswordAsync(string token, string newPassword, CancellationToken cancellationToken = default);
}
