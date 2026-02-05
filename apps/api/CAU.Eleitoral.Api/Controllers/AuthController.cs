using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.DTOs.Auth;
using CAU.Eleitoral.Application.Interfaces;

namespace CAU.Eleitoral.Api.Controllers;

public class AuthController : BaseController
{
    private readonly IAuthService _authService;
    private readonly ILogger<AuthController> _logger;

    public AuthController(IAuthService authService, ILogger<AuthController> logger)
    {
        _authService = authService;
        _logger = logger;
    }

    /// <summary>
    /// Realiza login no sistema
    /// </summary>
    [HttpPost("login")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(LoginResponseDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status401Unauthorized)]
    public async Task<IActionResult> Login([FromBody] LoginRequestDto request, CancellationToken cancellationToken)
    {
        try
        {
            var response = await _authService.LoginAsync(request, cancellationToken);
            return Ok(response);
        }
        catch (UnauthorizedAccessException ex)
        {
            _logger.LogWarning("Tentativa de login falhou para {Email}: {Message}", request.Email, ex.Message);
            return Unauthorized(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro durante login");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Renova o token de acesso
    /// </summary>
    [HttpPost("refresh-token")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(LoginResponseDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status401Unauthorized)]
    public async Task<IActionResult> RefreshToken([FromBody] RefreshTokenRequest request, CancellationToken cancellationToken)
    {
        try
        {
            var response = await _authService.RefreshTokenAsync(request.RefreshToken, cancellationToken);
            return Ok(response);
        }
        catch (UnauthorizedAccessException ex)
        {
            return Unauthorized(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro durante refresh token");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Realiza logout
    /// </summary>
    [HttpPost("logout")]
    [Authorize]
    [ProducesResponseType(StatusCodes.Status204NoContent)]
    public async Task<IActionResult> Logout(CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            await _authService.LogoutAsync(userId, cancellationToken);
            return NoContent();
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro durante logout");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Registra um novo usuário
    /// </summary>
    [HttpPost("register")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(RegisterResponseDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> Register([FromBody] RegisterRequestDto request, CancellationToken cancellationToken)
    {
        try
        {
            var response = await _authService.RegisterAsync(request, cancellationToken);
            return CreatedAtAction(nameof(Register), response);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro durante registro");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Confirma o email do usuário
    /// </summary>
    [HttpGet("confirm-email")]
    [AllowAnonymous]
    [ProducesResponseType(StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> ConfirmEmail([FromQuery] string token, CancellationToken cancellationToken)
    {
        try
        {
            var result = await _authService.ConfirmEmailAsync(token, cancellationToken);
            if (result)
                return Ok(new { message = "Email confirmado com sucesso" });

            return BadRequest(new { message = "Token inválido ou expirado" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao confirmar email");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Solicita recuperação de senha
    /// </summary>
    [HttpPost("forgot-password")]
    [AllowAnonymous]
    [ProducesResponseType(StatusCodes.Status200OK)]
    public async Task<IActionResult> ForgotPassword([FromBody] ForgotPasswordRequest request, CancellationToken cancellationToken)
    {
        try
        {
            await _authService.ForgotPasswordAsync(request.Email, cancellationToken);
            return Ok(new { message = "Se o email existir em nossa base, você receberá instruções para recuperar sua senha" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao processar recuperação de senha");
            return Ok(new { message = "Se o email existir em nossa base, você receberá instruções para recuperar sua senha" });
        }
    }

    /// <summary>
    /// Redefine a senha
    /// </summary>
    [HttpPost("reset-password")]
    [AllowAnonymous]
    [ProducesResponseType(StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> ResetPassword([FromBody] ResetPasswordRequestDto request, CancellationToken cancellationToken)
    {
        try
        {
            var result = await _authService.ResetPasswordAsync(request, cancellationToken);
            if (result)
                return Ok(new { message = "Senha redefinida com sucesso" });

            return BadRequest(new { message = "Token inválido ou expirado" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao redefinir senha");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Obtém informações do usuário logado
    /// </summary>
    [HttpGet("me")]
    [Authorize]
    [ProducesResponseType(typeof(UserInfoDto), StatusCodes.Status200OK)]
    public IActionResult GetCurrentUser()
    {
        var userInfo = new UserInfoDto
        {
            Id = GetUserId(),
            Email = GetUserEmail(),
            Nome = User.FindFirst("name")?.Value ?? string.Empty,
            NomeCompleto = User.FindFirst("fullName")?.Value,
            Roles = GetUserRoles(),
            Permissions = User.FindAll("permission").Select(c => c.Value)
        };

        return Ok(userInfo);
    }
}

public record RefreshTokenRequest(string RefreshToken);
public record ForgotPasswordRequest(string Email);
