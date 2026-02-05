using CAU.Eleitoral.Application.DTOs.Usuarios;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IUsuarioService
{
    Task<UsuarioDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<UsuarioDetailDto?> GetByIdDetailedAsync(Guid id, CancellationToken cancellationToken = default);
    Task<UsuarioDto?> GetByEmailAsync(string email, CancellationToken cancellationToken = default);
    Task<IEnumerable<UsuarioDto>> GetAllAsync(CancellationToken cancellationToken = default);
    Task<PagedUsuarioResponseDto> GetPagedAsync(UsuarioFilterDto filter, CancellationToken cancellationToken = default);
    Task<IEnumerable<UsuarioDto>> GetByRoleAsync(string roleName, CancellationToken cancellationToken = default);
    Task<IEnumerable<UsuarioDto>> GetByTypeAsync(TipoUsuario tipo, CancellationToken cancellationToken = default);
    Task<IEnumerable<UsuarioDto>> GetByStatusAsync(StatusUsuario status, CancellationToken cancellationToken = default);
    Task<UsuarioDto> CreateAsync(CreateUsuarioDto dto, CancellationToken cancellationToken = default);
    Task<UsuarioDto> UpdateAsync(Guid id, UpdateUsuarioDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);
    Task<bool> ValidatePasswordAsync(Guid id, string password, CancellationToken cancellationToken = default);
    Task<UsuarioDto> ChangePasswordAsync(Guid id, string currentPassword, string newPassword, CancellationToken cancellationToken = default);
    Task<UsuarioDto> AdminResetPasswordAsync(Guid id, string newPassword, CancellationToken cancellationToken = default);
    Task<UsuarioDto> BlockAsync(Guid id, CancellationToken cancellationToken = default);
    Task<UsuarioDto> UnblockAsync(Guid id, CancellationToken cancellationToken = default);
    Task<UsuarioDto> ChangeStatusAsync(Guid id, StatusUsuario status, string? motivo, CancellationToken cancellationToken = default);
    Task<UsuarioDto> AssignRolesAsync(Guid id, IEnumerable<string> roles, CancellationToken cancellationToken = default);
    Task<IEnumerable<RoleDto>> GetAllRolesAsync(CancellationToken cancellationToken = default);
    Task<string> GeneratePasswordResetTokenAsync(Guid id, CancellationToken cancellationToken = default);
    Task<UsuarioDto> ResetPasswordAsync(string token, string newPassword, CancellationToken cancellationToken = default);
}
