using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.DTOs.Usuarios;
using CAU.Eleitoral.Application.Interfaces;

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
    /// Lista todos os usuarios
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
}

public record ChangePasswordRequest(string CurrentPassword, string NewPassword);
