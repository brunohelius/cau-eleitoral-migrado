using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.DTOs.Usuarios;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para gerenciamento de usuarios
/// </summary>
[Authorize]
public class UsuarioController : BaseController
{
    private readonly IUsuarioService _usuarioService;
    private readonly ILogger<UsuarioController> _logger;

    public UsuarioController(IUsuarioService usuarioService, ILogger<UsuarioController> logger)
    {
        _usuarioService = usuarioService;
        _logger = logger;
    }

    /// <summary>
    /// Lista todos os usuarios (sem paginacao)
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de usuarios</returns>
    [HttpGet]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(IEnumerable<UsuarioDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<UsuarioDto>>> GetAll(CancellationToken cancellationToken)
    {
        try
        {
            var usuarios = await _usuarioService.GetAllAsync(cancellationToken);
            return Ok(usuarios);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar usuarios");
            return InternalError("Erro ao listar usuarios");
        }
    }

    /// <summary>
    /// Lista usuarios com paginacao e filtros
    /// </summary>
    /// <param name="tipo">Filtrar por tipo de usuario</param>
    /// <param name="status">Filtrar por status</param>
    /// <param name="search">Busca por nome, email ou CPF</param>
    /// <param name="role">Filtrar por role</param>
    /// <param name="page">Numero da pagina (1-based)</param>
    /// <param name="pageSize">Tamanho da pagina</param>
    /// <param name="orderBy">Campo para ordenacao (Nome, Email, Tipo, Status, CreatedAt, UltimoAcesso)</param>
    /// <param name="ascending">Ordenacao ascendente</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista paginada de usuarios</returns>
    [HttpGet("paged")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(PagedUsuarioResponseDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<PagedUsuarioResponseDto>> GetPaged(
        [FromQuery] TipoUsuario? tipo,
        [FromQuery] StatusUsuario? status,
        [FromQuery] string? search,
        [FromQuery] string? role,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 20,
        [FromQuery] string? orderBy = "Nome",
        [FromQuery] bool ascending = true,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var filter = new UsuarioFilterDto
            {
                Tipo = tipo,
                Status = status,
                Search = search,
                Role = role,
                Page = page > 0 ? page : 1,
                PageSize = pageSize > 0 && pageSize <= 100 ? pageSize : 20,
                OrderBy = orderBy,
                Ascending = ascending
            };

            var result = await _usuarioService.GetPagedAsync(filter, cancellationToken);
            return Ok(result);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar usuarios paginados");
            return InternalError("Erro ao listar usuarios");
        }
    }

    /// <summary>
    /// Lista usuarios por tipo
    /// </summary>
    /// <param name="tipo">Tipo do usuario</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de usuarios</returns>
    [HttpGet("tipo/{tipo:int}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(IEnumerable<UsuarioDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<UsuarioDto>>> GetByType(int tipo, CancellationToken cancellationToken)
    {
        try
        {
            if (!Enum.IsDefined(typeof(TipoUsuario), tipo))
                return BadRequest(new { message = "Tipo de usuario invalido" });

            var usuarios = await _usuarioService.GetByTypeAsync((TipoUsuario)tipo, cancellationToken);
            return Ok(usuarios);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar usuarios por tipo {Tipo}", tipo);
            return InternalError("Erro ao listar usuarios");
        }
    }

    /// <summary>
    /// Lista usuarios por status
    /// </summary>
    /// <param name="status">Status do usuario</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de usuarios</returns>
    [HttpGet("status/{status:int}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(IEnumerable<UsuarioDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<UsuarioDto>>> GetByStatus(int status, CancellationToken cancellationToken)
    {
        try
        {
            if (!Enum.IsDefined(typeof(StatusUsuario), status))
                return BadRequest(new { message = "Status de usuario invalido" });

            var usuarios = await _usuarioService.GetByStatusAsync((StatusUsuario)status, cancellationToken);
            return Ok(usuarios);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar usuarios por status {Status}", status);
            return InternalError("Erro ao listar usuarios");
        }
    }

    /// <summary>
    /// Obtem um usuario pelo ID
    /// </summary>
    /// <param name="id">ID do usuario</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados do usuario</returns>
    [HttpGet("{id:guid}")]
    [ProducesResponseType(typeof(UsuarioDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<UsuarioDto>> GetById(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var usuario = await _usuarioService.GetByIdAsync(id, cancellationToken);
            if (usuario == null)
                return NotFound(new { message = "Usuario nao encontrado" });

            return Ok(usuario);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter usuario {Id}", id);
            return InternalError("Erro ao obter usuario");
        }
    }

    /// <summary>
    /// Obtem um usuario pelo ID com informacoes detalhadas (roles, profissional, etc)
    /// </summary>
    /// <param name="id">ID do usuario</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados detalhados do usuario</returns>
    [HttpGet("{id:guid}/detail")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(UsuarioDetailDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<UsuarioDetailDto>> GetByIdDetailed(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var usuario = await _usuarioService.GetByIdDetailedAsync(id, cancellationToken);
            if (usuario == null)
                return NotFound(new { message = "Usuario nao encontrado" });

            return Ok(usuario);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter usuario detalhado {Id}", id);
            return InternalError("Erro ao obter usuario");
        }
    }

    /// <summary>
    /// Obtem o perfil do usuario logado
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados do perfil</returns>
    [HttpGet("profile")]
    [ProducesResponseType(typeof(UsuarioDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<UsuarioDto>> GetProfile(CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var usuario = await _usuarioService.GetByIdAsync(userId, cancellationToken);
            if (usuario == null)
                return NotFound(new { message = "Usuario nao encontrado" });

            return Ok(usuario);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter perfil do usuario");
            return InternalError("Erro ao obter perfil");
        }
    }

    /// <summary>
    /// Obtem um usuario pelo email
    /// </summary>
    /// <param name="email">Email do usuario</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados do usuario</returns>
    [HttpGet("email/{email}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(UsuarioDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<UsuarioDto>> GetByEmail(string email, CancellationToken cancellationToken)
    {
        try
        {
            var usuario = await _usuarioService.GetByEmailAsync(email, cancellationToken);
            if (usuario == null)
                return NotFound(new { message = "Usuario nao encontrado" });

            return Ok(usuario);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter usuario por email {Email}", email);
            return InternalError("Erro ao obter usuario");
        }
    }

    /// <summary>
    /// Lista usuarios por role
    /// </summary>
    /// <param name="roleName">Nome da role</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de usuarios</returns>
    [HttpGet("role/{roleName}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(IEnumerable<UsuarioDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<UsuarioDto>>> GetByRole(string roleName, CancellationToken cancellationToken)
    {
        try
        {
            var usuarios = await _usuarioService.GetByRoleAsync(roleName, cancellationToken);
            return Ok(usuarios);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar usuarios por role {Role}", roleName);
            return InternalError("Erro ao listar usuarios");
        }
    }

    /// <summary>
    /// Cria um novo usuario
    /// </summary>
    /// <param name="dto">Dados do usuario</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Usuario criado</returns>
    [HttpPost]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(UsuarioDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<UsuarioDto>> Create([FromBody] CreateUsuarioDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var usuario = await _usuarioService.CreateAsync(dto, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id = usuario.Id }, usuario);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao criar usuario");
            return InternalError("Erro ao criar usuario");
        }
    }

    /// <summary>
    /// Atualiza um usuario existente
    /// </summary>
    /// <param name="id">ID do usuario</param>
    /// <param name="dto">Dados atualizados</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Usuario atualizado</returns>
    [HttpPut("{id:guid}")]
    [ProducesResponseType(typeof(UsuarioDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<UsuarioDto>> Update(Guid id, [FromBody] UpdateUsuarioDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var currentUserId = GetUserId();
            var roles = GetUserRoles();

            // Apenas admin pode alterar outros usuarios
            if (id != currentUserId && !roles.Contains("Admin"))
                return Forbid();

            var usuario = await _usuarioService.UpdateAsync(id, dto, cancellationToken);
            return Ok(usuario);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Usuario nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar usuario {Id}", id);
            return InternalError("Erro ao atualizar usuario");
        }
    }

    /// <summary>
    /// Atualiza o perfil do usuario logado
    /// </summary>
    /// <param name="dto">Dados atualizados</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Usuario atualizado</returns>
    [HttpPut("profile")]
    [ProducesResponseType(typeof(UsuarioDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<UsuarioDto>> UpdateProfile([FromBody] UpdateUsuarioDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var usuario = await _usuarioService.UpdateAsync(userId, dto, cancellationToken);
            return Ok(usuario);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar perfil");
            return InternalError("Erro ao atualizar perfil");
        }
    }

    /// <summary>
    /// Remove um usuario
    /// </summary>
    /// <param name="id">ID do usuario</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>NoContent em caso de sucesso</returns>
    [HttpDelete("{id:guid}")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(StatusCodes.Status204NoContent)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<IActionResult> Delete(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            await _usuarioService.DeleteAsync(id, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Usuario nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao excluir usuario {Id}", id);
            return InternalError("Erro ao excluir usuario");
        }
    }

    /// <summary>
    /// Altera a senha do usuario logado
    /// </summary>
    /// <param name="request">Dados da alteracao de senha</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Usuario atualizado</returns>
    [HttpPost("change-password")]
    [ProducesResponseType(typeof(UsuarioDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<UsuarioDto>> ChangePassword([FromBody] ChangePasswordRequest request, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var usuario = await _usuarioService.ChangePasswordAsync(userId, request.CurrentPassword, request.NewPassword, cancellationToken);
            return Ok(usuario);
        }
        catch (UnauthorizedAccessException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao alterar senha");
            return InternalError("Erro ao alterar senha");
        }
    }

    /// <summary>
    /// Bloqueia um usuario
    /// </summary>
    /// <param name="id">ID do usuario</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Usuario bloqueado</returns>
    [HttpPost("{id:guid}/block")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(UsuarioDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<UsuarioDto>> Block(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var usuario = await _usuarioService.BlockAsync(id, cancellationToken);
            return Ok(usuario);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Usuario nao encontrado" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao bloquear usuario {Id}", id);
            return InternalError("Erro ao bloquear usuario");
        }
    }

    /// <summary>
    /// Desbloqueia um usuario
    /// </summary>
    /// <param name="id">ID do usuario</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Usuario desbloqueado</returns>
    [HttpPost("{id:guid}/unblock")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(UsuarioDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<UsuarioDto>> Unblock(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var usuario = await _usuarioService.UnblockAsync(id, cancellationToken);
            return Ok(usuario);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Usuario nao encontrado" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao desbloquear usuario {Id}", id);
            return InternalError("Erro ao desbloquear usuario");
        }
    }

    /// <summary>
    /// Atribui roles a um usuario
    /// </summary>
    /// <param name="id">ID do usuario</param>
    /// <param name="dto">Roles a serem atribuidas</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Usuario com as novas roles</returns>
    [HttpPost("{id:guid}/roles")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(UsuarioDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<UsuarioDto>> AssignRoles(Guid id, [FromBody] AssignRolesDto dto, CancellationToken cancellationToken)
    {
        try
        {
            if (dto.Roles == null || !dto.Roles.Any())
                return BadRequest(new { message = "Pelo menos uma role deve ser informada" });

            var usuario = await _usuarioService.AssignRolesAsync(id, dto.Roles, cancellationToken);
            return Ok(usuario);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Usuario nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atribuir roles ao usuario {Id}", id);
            return InternalError("Erro ao atribuir roles");
        }
    }

    /// <summary>
    /// Reseta a senha de um usuario (apenas admin)
    /// </summary>
    /// <param name="id">ID do usuario</param>
    /// <param name="dto">Nova senha</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Usuario com senha resetada</returns>
    [HttpPost("{id:guid}/reset-password")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(UsuarioDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<UsuarioDto>> AdminResetPassword(Guid id, [FromBody] AdminResetPasswordDto dto, CancellationToken cancellationToken)
    {
        try
        {
            if (string.IsNullOrWhiteSpace(dto.NewPassword))
                return BadRequest(new { message = "Nova senha e obrigatoria" });

            if (dto.NewPassword.Length < 8)
                return BadRequest(new { message = "A senha deve ter pelo menos 8 caracteres" });

            var usuario = await _usuarioService.AdminResetPasswordAsync(id, dto.NewPassword, cancellationToken);

            _logger.LogInformation("Senha do usuario {UsuarioId} resetada pelo admin {AdminId}", id, GetUserId());

            return Ok(usuario);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Usuario nao encontrado" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao resetar senha do usuario {Id}", id);
            return InternalError("Erro ao resetar senha");
        }
    }

    /// <summary>
    /// Altera o status de um usuario
    /// </summary>
    /// <param name="id">ID do usuario</param>
    /// <param name="dto">Novo status e motivo</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Usuario com status alterado</returns>
    [HttpPut("{id:guid}/status")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(UsuarioDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<UsuarioDto>> ChangeStatus(Guid id, [FromBody] ChangeStatusDto dto, CancellationToken cancellationToken)
    {
        try
        {
            if (!Enum.IsDefined(typeof(StatusUsuario), dto.Status))
                return BadRequest(new { message = "Status invalido" });

            var usuario = await _usuarioService.ChangeStatusAsync(id, dto.Status, dto.Motivo, cancellationToken);

            _logger.LogInformation("Status do usuario {UsuarioId} alterado para {Status} pelo admin {AdminId}. Motivo: {Motivo}",
                id, dto.Status, GetUserId(), dto.Motivo ?? "Nao informado");

            return Ok(usuario);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Usuario nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao alterar status do usuario {Id}", id);
            return InternalError("Erro ao alterar status");
        }
    }

    /// <summary>
    /// Lista todas as roles disponiveis
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de roles</returns>
    [HttpGet("roles")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(IEnumerable<RoleDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<RoleDto>>> GetAllRoles(CancellationToken cancellationToken)
    {
        try
        {
            var roles = await _usuarioService.GetAllRolesAsync(cancellationToken);
            return Ok(roles);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar roles");
            return InternalError("Erro ao listar roles");
        }
    }

    /// <summary>
    /// Ativa um usuario (atalho para change-status)
    /// </summary>
    /// <param name="id">ID do usuario</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Usuario ativado</returns>
    [HttpPost("{id:guid}/activate")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(UsuarioDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<UsuarioDto>> Activate(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var usuario = await _usuarioService.ChangeStatusAsync(id, StatusUsuario.Ativo, "Ativado pelo administrador", cancellationToken);
            return Ok(usuario);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Usuario nao encontrado" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao ativar usuario {Id}", id);
            return InternalError("Erro ao ativar usuario");
        }
    }

    /// <summary>
    /// Desativa um usuario (atalho para change-status)
    /// </summary>
    /// <param name="id">ID do usuario</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Usuario desativado</returns>
    [HttpPost("{id:guid}/deactivate")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(UsuarioDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<UsuarioDto>> Deactivate(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var usuario = await _usuarioService.ChangeStatusAsync(id, StatusUsuario.Inativo, "Desativado pelo administrador", cancellationToken);
            return Ok(usuario);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Usuario nao encontrado" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao desativar usuario {Id}", id);
            return InternalError("Erro ao desativar usuario");
        }
    }
}

public record ChangePasswordRequest(string CurrentPassword, string NewPassword);
