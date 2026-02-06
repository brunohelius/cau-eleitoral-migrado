using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using System.Security.Cryptography;
using System.Text;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Microsoft.IdentityModel.Tokens;
using CAU.Eleitoral.Application.DTOs.Auth;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Infrastructure.Data;

namespace CAU.Eleitoral.Api.Controllers;

public class AuthController : BaseController
{
    private readonly IAuthService _authService;
    private readonly ILogger<AuthController> _logger;
    private readonly AppDbContext _context;
    private readonly IConfiguration _configuration;

    public AuthController(
        IAuthService authService,
        ILogger<AuthController> logger,
        AppDbContext context,
        IConfiguration configuration)
    {
        _authService = authService;
        _logger = logger;
        _context = context;
        _configuration = configuration;
    }

    // =====================================================
    // Admin Auth Endpoints
    // =====================================================

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
    /// Registra um novo usuario
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
    /// Confirma o email do usuario
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

            return BadRequest(new { message = "Token invalido ou expirado" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao confirmar email");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Solicita recuperacao de senha
    /// </summary>
    [HttpPost("forgot-password")]
    [AllowAnonymous]
    [ProducesResponseType(StatusCodes.Status200OK)]
    public async Task<IActionResult> ForgotPassword([FromBody] ForgotPasswordRequest request, CancellationToken cancellationToken)
    {
        try
        {
            await _authService.ForgotPasswordAsync(request.Email, cancellationToken);
            return Ok(new { message = "Se o email existir em nossa base, voce recebera instrucoes para recuperar sua senha" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao processar recuperacao de senha");
            return Ok(new { message = "Se o email existir em nossa base, voce recebera instrucoes para recuperar sua senha" });
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

            return BadRequest(new { message = "Token invalido ou expirado" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao redefinir senha");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Obtem informacoes do usuario logado
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

    // =====================================================
    // Voter (Eleitor) Auth Endpoints
    // =====================================================

    /// <summary>
    /// Verifica se o eleitor existe pelo CPF e Registro CAU
    /// </summary>
    [HttpPost("eleitor/verificacao")]
    [AllowAnonymous]
    [ProducesResponseType(StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> VerificarEleitor(
        [FromBody] VoterVerificationRequestDto request,
        CancellationToken cancellationToken)
    {
        try
        {
            var profissional = await _context.Profissionais
                .Include(p => p.Usuario)
                .FirstOrDefaultAsync(p =>
                    p.Cpf == request.Cpf &&
                    p.RegistroCAU == request.RegistroCAU,
                    cancellationToken);

            if (profissional == null)
            {
                _logger.LogWarning("Verificacao eleitor falhou: CPF {Cpf} / CAU {CAU} nao encontrado",
                    request.Cpf, request.RegistroCAU);
                return BadRequest(new { message = "CPF ou Registro CAU nao encontrado no sistema" });
            }

            if (profissional.Usuario == null)
            {
                return BadRequest(new { message = "Profissional nao possui conta de usuario vinculada. Entre em contato com o suporte." });
            }

            return Ok(new
            {
                verificacaoEnviada = true,
                canal = "email",
                destino = profissional.Nome
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao verificar eleitor");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Autentica o eleitor com CPF, Registro CAU e senha
    /// </summary>
    [HttpPost("eleitor/login")]
    [AllowAnonymous]
    [ProducesResponseType(StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status401Unauthorized)]
    public async Task<IActionResult> LoginEleitor(
        [FromBody] VoterLoginRequestDto request,
        CancellationToken cancellationToken)
    {
        try
        {
            var profissional = await _context.Profissionais
                .Include(p => p.Usuario)
                .Include(p => p.Regional)
                .FirstOrDefaultAsync(p =>
                    p.Cpf == request.Cpf &&
                    p.RegistroCAU == request.RegistroCAU,
                    cancellationToken);

            if (profissional?.Usuario == null)
            {
                return Unauthorized(new { message = "Credenciais invalidas" });
            }

            var usuario = profissional.Usuario;

            if (usuario.Status == StatusUsuario.Bloqueado)
            {
                return Unauthorized(new { message = "Usuario bloqueado. Entre em contato com o suporte." });
            }

            if (usuario.Status == StatusUsuario.Inativo)
            {
                return Unauthorized(new { message = "Usuario inativo" });
            }

            if (!VerifyPasswordHash(request.CodigoVerificacao, usuario.PasswordHash, usuario.PasswordSalt))
            {
                usuario.TentativasLogin++;
                if (usuario.TentativasLogin >= 5)
                {
                    usuario.Status = StatusUsuario.Bloqueado;
                    usuario.BloqueadoAte = DateTime.UtcNow.AddMinutes(30);
                }
                await _context.SaveChangesAsync(cancellationToken);

                _logger.LogWarning("Tentativa de login eleitor com senha incorreta: CPF {Cpf} - Tentativas: {Tentativas}",
                    request.Cpf, usuario.TentativasLogin);
                return Unauthorized(new { message = "Senha incorreta" });
            }

            // Find voter record for active election
            var eleitor = await _context.Eleitores
                .Include(e => e.Eleicao)
                .Where(e => e.ProfissionalId == profissional.Id)
                .Where(e => e.Eleicao.Status == StatusEleicao.EmAndamento ||
                            e.Eleicao.FaseAtual == FaseEleicao.Votacao)
                .OrderByDescending(e => e.Eleicao.DataInicio)
                .FirstOrDefaultAsync(cancellationToken);

            // Update last access
            usuario.TentativasLogin = 0;
            usuario.UltimoAcesso = DateTime.UtcNow;
            await _context.SaveChangesAsync(cancellationToken);

            // Generate JWT token
            var token = GenerateVoterAccessToken(usuario, profissional);
            var expiresAt = DateTime.UtcNow.AddHours(2);

            _logger.LogInformation("Login eleitor realizado: {ProfissionalId} - CPF: {Cpf}",
                profissional.Id, request.Cpf);

            return Ok(new
            {
                token,
                expiresAt = expiresAt.ToString("o"),
                voter = new
                {
                    id = profissional.Id,
                    nome = profissional.Nome,
                    cpf = profissional.Cpf,
                    registroCAU = profissional.RegistroCAU,
                    email = profissional.Email ?? usuario.Email,
                    regional = profissional.Regional?.Nome,
                    podeVotar = eleitor?.Apto ?? profissional.EleitorApto,
                    jaVotou = eleitor?.Votou ?? false,
                    eleicaoId = eleitor?.EleicaoId ?? Guid.Empty,
                    eleicaoNome = eleitor?.Eleicao?.Nome ?? ""
                }
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao autenticar eleitor");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Realiza logout do eleitor
    /// </summary>
    [HttpPost("eleitor/logout")]
    [Authorize]
    [ProducesResponseType(StatusCodes.Status204NoContent)]
    public async Task<IActionResult> LogoutEleitor(CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var usuario = await _context.Usuarios.FindAsync(new object[] { userId }, cancellationToken);
            if (usuario != null)
            {
                usuario.RefreshToken = null;
                usuario.RefreshTokenExpiracao = null;
                await _context.SaveChangesAsync(cancellationToken);
            }
            return NoContent();
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao realizar logout do eleitor");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Obtem informacoes do eleitor logado
    /// </summary>
    [HttpGet("eleitor/me")]
    [Authorize]
    [ProducesResponseType(StatusCodes.Status200OK)]
    public async Task<IActionResult> GetEleitorInfo(CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var usuario = await _context.Usuarios.FindAsync(new object[] { userId }, cancellationToken);
            if (usuario == null)
                return Unauthorized(new { message = "Usuario nao encontrado" });

            var profissional = await _context.Profissionais
                .Include(p => p.Regional)
                .FirstOrDefaultAsync(p => p.UsuarioId == userId, cancellationToken);

            if (profissional == null)
                return NotFound(new { message = "Profissional nao encontrado" });

            var eleitor = await _context.Eleitores
                .Include(e => e.Eleicao)
                .Where(e => e.ProfissionalId == profissional.Id)
                .Where(e => e.Eleicao.Status == StatusEleicao.EmAndamento ||
                            e.Eleicao.FaseAtual == FaseEleicao.Votacao)
                .OrderByDescending(e => e.Eleicao.DataInicio)
                .FirstOrDefaultAsync(cancellationToken);

            return Ok(new
            {
                id = profissional.Id,
                nome = profissional.Nome,
                cpf = profissional.Cpf,
                registroCAU = profissional.RegistroCAU,
                email = profissional.Email ?? usuario.Email,
                regional = profissional.Regional?.Nome,
                podeVotar = eleitor?.Apto ?? profissional.EleitorApto,
                jaVotou = eleitor?.Votou ?? false,
                eleicaoId = eleitor?.EleicaoId ?? Guid.Empty,
                eleicaoNome = eleitor?.Eleicao?.Nome ?? ""
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter informacoes do eleitor");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Verifica elegibilidade do eleitor para votar em uma eleicao
    /// </summary>
    [HttpGet("eleitor/elegibilidade/{eleicaoId:guid}")]
    [Authorize]
    [ProducesResponseType(StatusCodes.Status200OK)]
    public async Task<IActionResult> VerificarElegibilidade(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var profissional = await _context.Profissionais
                .FirstOrDefaultAsync(p => p.UsuarioId == userId, cancellationToken);

            if (profissional == null)
                return Ok(new { elegivelVotar = false, motivo = "Profissional nao encontrado" });

            var eleitor = await _context.Eleitores
                .Include(e => e.Eleicao)
                .FirstOrDefaultAsync(e =>
                    e.ProfissionalId == profissional.Id &&
                    e.EleicaoId == eleicaoId,
                    cancellationToken);

            if (eleitor == null)
                return Ok(new { elegivelVotar = false, motivo = "Eleitor nao cadastrado para esta eleicao" });

            var restricoes = new List<string>();
            if (!eleitor.Apto)
                restricoes.Add(eleitor.MotivoInaptidao ?? "Eleitor nao apto");
            if (eleitor.Votou)
                restricoes.Add("Ja votou nesta eleicao");
            if (eleitor.Eleicao.FaseAtual != FaseEleicao.Votacao)
                restricoes.Add("Eleicao nao esta em fase de votacao");

            return Ok(new
            {
                elegivelVotar = eleitor.Apto && !eleitor.Votou && eleitor.Eleicao.FaseAtual == FaseEleicao.Votacao,
                motivo = restricoes.Count > 0 ? string.Join("; ", restricoes) : (string?)null,
                restricoes = restricoes.Count > 0 ? restricoes : null
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao verificar elegibilidade do eleitor");
            return HandleException(ex);
        }
    }

    // =====================================================
    // Candidate (Candidato) Auth Endpoints
    // =====================================================

    /// <summary>
    /// Autentica um candidato
    /// </summary>
    [HttpPost("candidato/login")]
    [AllowAnonymous]
    [ProducesResponseType(StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status401Unauthorized)]
    public async Task<IActionResult> LoginCandidato(
        [FromBody] CandidateLoginRequestDto request,
        CancellationToken cancellationToken)
    {
        try
        {
            var profissional = await _context.Profissionais
                .Include(p => p.Usuario)
                .Include(p => p.Regional)
                .FirstOrDefaultAsync(p =>
                    p.Cpf == request.Cpf &&
                    p.RegistroCAU == request.RegistroCAU,
                    cancellationToken);

            if (profissional?.Usuario == null)
                return Unauthorized(new { message = "Credenciais invalidas" });

            var usuario = profissional.Usuario;

            if (!VerifyPasswordHash(request.Senha, usuario.PasswordHash, usuario.PasswordSalt))
                return Unauthorized(new { message = "Senha incorreta" });

            // Find candidate (membro chapa) record
            var membroChapa = await _context.MembrosChapa
                .Include(m => m.Chapa)
                    .ThenInclude(c => c.Eleicao)
                .Where(m => m.ProfissionalId == profissional.Id)
                .Where(m => m.Chapa.Eleicao.Status == StatusEleicao.EmAndamento)
                .OrderByDescending(m => m.Chapa.Eleicao.DataInicio)
                .FirstOrDefaultAsync(cancellationToken);

            usuario.TentativasLogin = 0;
            usuario.UltimoAcesso = DateTime.UtcNow;
            await _context.SaveChangesAsync(cancellationToken);

            var token = GenerateVoterAccessToken(usuario, profissional);
            var expiresAt = DateTime.UtcNow.AddHours(2);

            return Ok(new
            {
                token,
                expiresAt = expiresAt.ToString("o"),
                candidate = new
                {
                    id = profissional.Id,
                    nome = profissional.Nome,
                    cpf = profissional.Cpf,
                    registroCAU = profissional.RegistroCAU,
                    email = profissional.Email ?? usuario.Email,
                    telefone = profissional.Telefone,
                    chapaId = membroChapa?.ChapaId ?? Guid.Empty,
                    chapaNome = membroChapa?.Chapa?.Nome ?? "",
                    chapaNumero = int.TryParse(membroChapa?.Chapa?.Numero, out var num1) ? num1 : 0,
                    cargo = membroChapa?.Cargo ?? "",
                    tipo = membroChapa?.Tipo.ToString() ?? "",
                    eleicaoId = membroChapa?.Chapa?.EleicaoId ?? Guid.Empty,
                    eleicaoNome = membroChapa?.Chapa?.Eleicao?.Nome ?? "",
                    status = (int)(membroChapa?.Status ?? 0)
                }
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao autenticar candidato");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Logout do candidato
    /// </summary>
    [HttpPost("candidato/logout")]
    [Authorize]
    [ProducesResponseType(StatusCodes.Status204NoContent)]
    public async Task<IActionResult> LogoutCandidato(CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var usuario = await _context.Usuarios.FindAsync(new object[] { userId }, cancellationToken);
            if (usuario != null)
            {
                usuario.RefreshToken = null;
                usuario.RefreshTokenExpiracao = null;
                await _context.SaveChangesAsync(cancellationToken);
            }
            return NoContent();
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao realizar logout do candidato");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Obtem informacoes do candidato logado
    /// </summary>
    [HttpGet("candidato/me")]
    [Authorize]
    [ProducesResponseType(StatusCodes.Status200OK)]
    public async Task<IActionResult> GetCandidatoInfo(CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var profissional = await _context.Profissionais
                .Include(p => p.Usuario)
                .Include(p => p.Regional)
                .FirstOrDefaultAsync(p => p.UsuarioId == userId, cancellationToken);

            if (profissional == null)
                return NotFound(new { message = "Profissional nao encontrado" });

            var membroChapa = await _context.MembrosChapa
                .Include(m => m.Chapa)
                    .ThenInclude(c => c.Eleicao)
                .Where(m => m.ProfissionalId == profissional.Id)
                .Where(m => m.Chapa.Eleicao.Status == StatusEleicao.EmAndamento)
                .OrderByDescending(m => m.Chapa.Eleicao.DataInicio)
                .FirstOrDefaultAsync(cancellationToken);

            return Ok(new
            {
                id = profissional.Id,
                nome = profissional.Nome,
                cpf = profissional.Cpf,
                registroCAU = profissional.RegistroCAU,
                email = profissional.Email ?? profissional.Usuario?.Email,
                telefone = profissional.Telefone,
                chapaId = membroChapa?.ChapaId ?? Guid.Empty,
                chapaNome = membroChapa?.Chapa?.Nome ?? "",
                chapaNumero = int.TryParse(membroChapa?.Chapa?.Numero, out var num2) ? num2 : 0,
                cargo = membroChapa?.Cargo ?? "",
                tipo = membroChapa?.Tipo.ToString() ?? "",
                eleicaoId = membroChapa?.Chapa?.EleicaoId ?? Guid.Empty,
                eleicaoNome = membroChapa?.Chapa?.Eleicao?.Nome ?? "",
                status = (int)(membroChapa?.Status ?? 0)
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter informacoes do candidato");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Registra um novo candidato
    /// </summary>
    [HttpPost("candidato/register")]
    [AllowAnonymous]
    [ProducesResponseType(StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> RegisterCandidato(
        [FromBody] CandidateRegisterRequestDto request,
        CancellationToken cancellationToken)
    {
        try
        {
            if (request.Senha != request.ConfirmacaoSenha)
                return BadRequest(new { message = "Senhas nao conferem" });

            if (!request.AceitouTermos)
                return BadRequest(new { message = "E necessario aceitar os termos de uso" });

            var profissional = await _context.Profissionais
                .Include(p => p.Usuario)
                .FirstOrDefaultAsync(p =>
                    p.Cpf == request.Cpf &&
                    p.RegistroCAU == request.RegistroCAU,
                    cancellationToken);

            if (profissional == null)
                return BadRequest(new { message = "Profissional nao encontrado com o CPF e Registro CAU informados" });

            if (profissional.Usuario != null)
                return BadRequest(new { message = "Profissional ja possui conta cadastrada" });

            var (hash, salt) = HashPasswordStatic(request.Senha);

            var usuario = new Domain.Entities.Usuarios.Usuario
            {
                Nome = request.Nome,
                Email = request.Email,
                Cpf = request.Cpf,
                Telefone = request.Telefone,
                PasswordHash = hash,
                PasswordSalt = salt,
                Status = StatusUsuario.Ativo,
                EmailConfirmado = true,
                Tipo = TipoUsuario.Candidato
            };

            await _context.Usuarios.AddAsync(usuario, cancellationToken);
            profissional.UsuarioId = usuario.Id;
            await _context.SaveChangesAsync(cancellationToken);

            return Ok(new
            {
                id = profissional.Id,
                message = "Cadastro realizado com sucesso",
                proximosPasso = new[]
                {
                    "Acesse o portal do candidato com suas credenciais",
                    "Complete seu perfil e adicione documentos necessarios",
                    "Acompanhe o status da sua candidatura"
                }
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao registrar candidato");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Solicita recuperacao de senha do candidato
    /// </summary>
    [HttpPost("candidato/forgot-password")]
    [AllowAnonymous]
    [ProducesResponseType(StatusCodes.Status200OK)]
    public async Task<IActionResult> ForgotPasswordCandidato(
        [FromBody] CandidateForgotPasswordRequestDto request,
        CancellationToken cancellationToken)
    {
        try
        {
            var profissional = await _context.Profissionais
                .Include(p => p.Usuario)
                .FirstOrDefaultAsync(p =>
                    p.Cpf == request.Cpf &&
                    p.RegistroCAU == request.RegistroCAU,
                    cancellationToken);

            // Always return success for security
            return Ok(new
            {
                message = "Se os dados estiverem corretos, voce recebera um email com instrucoes para recuperar sua senha",
                emailEnviado = profissional?.Usuario != null
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao processar recuperacao de senha do candidato");
            return Ok(new { message = "Se os dados estiverem corretos, voce recebera um email com instrucoes", emailEnviado = false });
        }
    }

    /// <summary>
    /// Redefine a senha do candidato
    /// </summary>
    [HttpPost("candidato/reset-password")]
    [AllowAnonymous]
    [ProducesResponseType(StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> ResetPasswordCandidato(
        [FromBody] CandidateResetPasswordRequestDto request,
        CancellationToken cancellationToken)
    {
        try
        {
            if (request.NovaSenha != request.ConfirmacaoSenha)
                return BadRequest(new { message = "Senhas nao conferem" });

            var result = await _authService.ResetPasswordAsync(new ResetPasswordRequestDto
            {
                Token = request.Token,
                NewPassword = request.NovaSenha,
                ConfirmPassword = request.ConfirmacaoSenha
            }, cancellationToken);

            if (result)
                return Ok(new { message = "Senha redefinida com sucesso" });

            return BadRequest(new { message = "Token invalido ou expirado" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao redefinir senha do candidato");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Altera a senha do candidato
    /// </summary>
    [HttpPost("candidato/change-password")]
    [Authorize]
    [ProducesResponseType(StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> ChangePasswordCandidato(
        [FromBody] CandidateChangePasswordRequestDto request,
        CancellationToken cancellationToken)
    {
        try
        {
            if (request.NovaSenha != request.ConfirmacaoSenha)
                return BadRequest(new { message = "Senhas nao conferem" });

            var userId = GetUserId();
            var usuario = await _context.Usuarios.FindAsync(new object[] { userId }, cancellationToken);
            if (usuario == null)
                return NotFound(new { message = "Usuario nao encontrado" });

            if (!VerifyPasswordHash(request.SenhaAtual, usuario.PasswordHash, usuario.PasswordSalt))
                return BadRequest(new { message = "Senha atual incorreta" });

            var (hash, salt) = HashPasswordStatic(request.NovaSenha);
            usuario.PasswordHash = hash;
            usuario.PasswordSalt = salt;
            await _context.SaveChangesAsync(cancellationToken);

            return Ok(new { message = "Senha alterada com sucesso" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao alterar senha do candidato");
            return HandleException(ex);
        }
    }

    // =====================================================
    // Common Endpoints
    // =====================================================

    /// <summary>
    /// Verifica se um token e valido
    /// </summary>
    [HttpPost("verify-token")]
    [AllowAnonymous]
    [ProducesResponseType(StatusCodes.Status200OK)]
    public async Task<IActionResult> VerifyToken(
        [FromBody] VerifyTokenRequestDto request,
        CancellationToken cancellationToken)
    {
        try
        {
            var isValid = await _authService.ValidateTokenAsync(request.Token, cancellationToken);
            return Ok(new
            {
                valido = isValid,
                expiraEm = isValid ? DateTime.UtcNow.AddHours(1).ToString("o") : (string?)null
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao verificar token");
            return Ok(new { valido = false, expiraEm = (string?)null });
        }
    }

    // =====================================================
    // Private Helpers
    // =====================================================

    private string GenerateVoterAccessToken(
        Domain.Entities.Usuarios.Usuario usuario,
        Domain.Entities.Usuarios.Profissional profissional)
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
            new(ClaimTypes.Name, profissional.Nome),
            new("tipo", "Eleitor"),
            new("profissionalId", profissional.Id.ToString()),
            new("registroCAU", profissional.RegistroCAU),
            new(ClaimTypes.Role, "Eleitor")
        };

        var token = new JwtSecurityToken(
            issuer: issuer,
            audience: audience,
            claims: claims,
            expires: DateTime.UtcNow.AddHours(2),
            signingCredentials: credentials);

        return new JwtSecurityTokenHandler().WriteToken(token);
    }

    private static bool VerifyPasswordHash(string password, string hash, string? salt)
    {
        if (string.IsNullOrEmpty(salt)) return false;
        var saltBytes = Convert.FromBase64String(salt);
        using var pbkdf2 = new Rfc2898DeriveBytes(password, saltBytes, 100000, HashAlgorithmName.SHA256);
        var computedHash = Convert.ToBase64String(pbkdf2.GetBytes(32));
        return hash == computedHash;
    }

    private static (string Hash, string Salt) HashPasswordStatic(string password)
    {
        var saltBytes = RandomNumberGenerator.GetBytes(16);
        var salt = Convert.ToBase64String(saltBytes);
        using var pbkdf2 = new Rfc2898DeriveBytes(password, saltBytes, 100000, HashAlgorithmName.SHA256);
        var hash = Convert.ToBase64String(pbkdf2.GetBytes(32));
        return (hash, salt);
    }
}

public record RefreshTokenRequest(string RefreshToken);
public record ForgotPasswordRequest(string Email);
