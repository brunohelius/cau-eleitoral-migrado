using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para gerenciamento de configuracoes do sistema
/// </summary>
[Authorize(Roles = "Admin")]
public class ConfiguracaoController : BaseController
{
    private readonly IConfiguracaoService _configuracaoService;
    private readonly ILogger<ConfiguracaoController> _logger;

    public ConfiguracaoController(IConfiguracaoService configuracaoService, ILogger<ConfiguracaoController> logger)
    {
        _configuracaoService = configuracaoService;
        _logger = logger;
    }

    /// <summary>
    /// Obtem todas as configuracoes do sistema
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Configuracoes do sistema</returns>
    [HttpGet]
    [ProducesResponseType(typeof(ConfiguracaoSistemaDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<ConfiguracaoSistemaDto>> GetAll(CancellationToken cancellationToken)
    {
        try
        {
            var configuracoes = await _configuracaoService.GetAllAsync(cancellationToken);
            return Ok(configuracoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter configuracoes do sistema");
            return InternalError("Erro ao obter configuracoes");
        }
    }

    /// <summary>
    /// Obtem uma configuracao especifica
    /// </summary>
    /// <param name="chave">Chave da configuracao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Valor da configuracao</returns>
    [HttpGet("{chave}")]
    [ProducesResponseType(typeof(ConfiguracaoItemDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ConfiguracaoItemDto>> GetByChave(string chave, CancellationToken cancellationToken)
    {
        try
        {
            var configuracao = await _configuracaoService.GetByChaveAsync(chave, cancellationToken);
            if (configuracao == null)
                return NotFound(new { message = "Configuracao nao encontrada" });

            return Ok(configuracao);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter configuracao {Chave}", chave);
            return InternalError("Erro ao obter configuracao");
        }
    }

    /// <summary>
    /// Atualiza uma configuracao
    /// </summary>
    /// <param name="chave">Chave da configuracao</param>
    /// <param name="dto">Novo valor</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Configuracao atualizada</returns>
    [HttpPut("{chave}")]
    [ProducesResponseType(typeof(ConfiguracaoItemDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ConfiguracaoItemDto>> Update(
        string chave,
        [FromBody] UpdateConfiguracaoDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var configuracao = await _configuracaoService.UpdateAsync(chave, dto.Valor, userId, cancellationToken);
            return Ok(configuracao);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Configuracao nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar configuracao {Chave}", chave);
            return InternalError("Erro ao atualizar configuracao");
        }
    }

    /// <summary>
    /// Atualiza multiplas configuracoes
    /// </summary>
    /// <param name="dto">Configuracoes a atualizar</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Configuracoes atualizadas</returns>
    [HttpPut]
    [ProducesResponseType(typeof(ConfiguracaoSistemaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ConfiguracaoSistemaDto>> UpdateMultiple(
        [FromBody] UpdateMultiplasConfiguracoesDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var configuracoes = await _configuracaoService.UpdateMultipleAsync(dto.Configuracoes, userId, cancellationToken);
            return Ok(configuracoes);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar configuracoes");
            return InternalError("Erro ao atualizar configuracoes");
        }
    }

    /// <summary>
    /// Obtem configuracoes de email
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Configuracoes de email</returns>
    [HttpGet("email")]
    [ProducesResponseType(typeof(ConfiguracaoEmailDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<ConfiguracaoEmailDto>> GetEmailConfig(CancellationToken cancellationToken)
    {
        try
        {
            var config = await _configuracaoService.GetEmailConfigAsync(cancellationToken);
            return Ok(config);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter configuracoes de email");
            return InternalError("Erro ao obter configuracoes");
        }
    }

    /// <summary>
    /// Atualiza configuracoes de email
    /// </summary>
    /// <param name="dto">Configuracoes de email</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Configuracoes atualizadas</returns>
    [HttpPut("email")]
    [ProducesResponseType(typeof(ConfiguracaoEmailDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ConfiguracaoEmailDto>> UpdateEmailConfig(
        [FromBody] ConfiguracaoEmailDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var config = await _configuracaoService.UpdateEmailConfigAsync(dto, userId, cancellationToken);
            return Ok(config);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar configuracoes de email");
            return InternalError("Erro ao atualizar configuracoes");
        }
    }

    /// <summary>
    /// Testa configuracoes de email
    /// </summary>
    /// <param name="request">Email de teste</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Resultado do teste</returns>
    [HttpPost("email/testar")]
    [ProducesResponseType(StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> TestarEmail(
        [FromBody] TestarEmailRequest request,
        CancellationToken cancellationToken)
    {
        try
        {
            var resultado = await _configuracaoService.TestarEmailAsync(request.EmailDestino, cancellationToken);
            if (resultado)
                return Ok(new { message = "Email de teste enviado com sucesso" });

            return BadRequest(new { message = "Falha ao enviar email de teste" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao testar email");
            return InternalError("Erro ao testar email");
        }
    }

    /// <summary>
    /// Obtem configuracoes de seguranca
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Configuracoes de seguranca</returns>
    [HttpGet("seguranca")]
    [ProducesResponseType(typeof(ConfiguracaoSegurancaDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<ConfiguracaoSegurancaDto>> GetSegurancaConfig(CancellationToken cancellationToken)
    {
        try
        {
            var config = await _configuracaoService.GetSegurancaConfigAsync(cancellationToken);
            return Ok(config);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter configuracoes de seguranca");
            return InternalError("Erro ao obter configuracoes");
        }
    }

    /// <summary>
    /// Atualiza configuracoes de seguranca
    /// </summary>
    /// <param name="dto">Configuracoes de seguranca</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Configuracoes atualizadas</returns>
    [HttpPut("seguranca")]
    [ProducesResponseType(typeof(ConfiguracaoSegurancaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ConfiguracaoSegurancaDto>> UpdateSegurancaConfig(
        [FromBody] ConfiguracaoSegurancaDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var config = await _configuracaoService.UpdateSegurancaConfigAsync(dto, userId, cancellationToken);
            return Ok(config);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar configuracoes de seguranca");
            return InternalError("Erro ao atualizar configuracoes");
        }
    }

    /// <summary>
    /// Obtem configuracoes de votacao
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Configuracoes de votacao</returns>
    [HttpGet("votacao")]
    [ProducesResponseType(typeof(ConfiguracaoVotacaoDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<ConfiguracaoVotacaoDto>> GetVotacaoConfig(CancellationToken cancellationToken)
    {
        try
        {
            var config = await _configuracaoService.GetVotacaoConfigAsync(cancellationToken);
            return Ok(config);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter configuracoes de votacao");
            return InternalError("Erro ao obter configuracoes");
        }
    }

    /// <summary>
    /// Atualiza configuracoes de votacao
    /// </summary>
    /// <param name="dto">Configuracoes de votacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Configuracoes atualizadas</returns>
    [HttpPut("votacao")]
    [ProducesResponseType(typeof(ConfiguracaoVotacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ConfiguracaoVotacaoDto>> UpdateVotacaoConfig(
        [FromBody] ConfiguracaoVotacaoDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var config = await _configuracaoService.UpdateVotacaoConfigAsync(dto, userId, cancellationToken);
            return Ok(config);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar configuracoes de votacao");
            return InternalError("Erro ao atualizar configuracoes");
        }
    }

    /// <summary>
    /// Restaura configuracoes padrao
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Configuracoes restauradas</returns>
    [HttpPost("restaurar-padrao")]
    [ProducesResponseType(typeof(ConfiguracaoSistemaDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<ConfiguracaoSistemaDto>> RestaurarPadrao(CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var configuracoes = await _configuracaoService.RestaurarPadraoAsync(userId, cancellationToken);
            return Ok(configuracoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao restaurar configuracoes padrao");
            return InternalError("Erro ao restaurar configuracoes");
        }
    }

    /// <summary>
    /// Exporta configuracoes
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Arquivo de configuracoes</returns>
    [HttpGet("exportar")]
    [ProducesResponseType(typeof(FileContentResult), StatusCodes.Status200OK)]
    public async Task<IActionResult> Exportar(CancellationToken cancellationToken)
    {
        try
        {
            var (content, contentType, fileName) = await _configuracaoService.ExportarAsync(cancellationToken);
            return File(content, contentType, fileName);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao exportar configuracoes");
            return InternalError("Erro ao exportar configuracoes");
        }
    }

    /// <summary>
    /// Importa configuracoes
    /// </summary>
    /// <param name="file">Arquivo de configuracoes</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Configuracoes importadas</returns>
    [HttpPost("importar")]
    [ProducesResponseType(typeof(ConfiguracaoSistemaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ConfiguracaoSistemaDto>> Importar(
        IFormFile file,
        CancellationToken cancellationToken)
    {
        try
        {
            if (file == null || file.Length == 0)
                return BadRequest(new { message = "Arquivo nao enviado" });

            var userId = GetUserId();
            using var stream = file.OpenReadStream();
            var configuracoes = await _configuracaoService.ImportarAsync(stream, userId, cancellationToken);
            return Ok(configuracoes);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao importar configuracoes");
            return InternalError("Erro ao importar configuracoes");
        }
    }

    /// <summary>
    /// Lista roles disponiveis no sistema
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de roles</returns>
    [HttpGet("roles")]
    [ProducesResponseType(typeof(IEnumerable<RoleDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<RoleDto>>> GetRoles(CancellationToken cancellationToken)
    {
        try
        {
            var roles = await _configuracaoService.GetRolesAsync(cancellationToken);
            return Ok(roles);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar roles");
            return InternalError("Erro ao listar roles");
        }
    }

    /// <summary>
    /// Obtem informacoes do sistema
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Informacoes do sistema</returns>
    [HttpGet("sistema")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(InfoSistemaDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<InfoSistemaDto>> GetInfoSistema(CancellationToken cancellationToken)
    {
        try
        {
            var info = await _configuracaoService.GetInfoSistemaAsync(cancellationToken);
            return Ok(info);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter informacoes do sistema");
            return InternalError("Erro ao obter informacoes");
        }
    }
}

// DTOs para Configuracao
public record ConfiguracaoSistemaDto
{
    public ConfiguracaoGeralDto Geral { get; init; } = new();
    public ConfiguracaoEmailDto Email { get; init; } = new();
    public ConfiguracaoSegurancaDto Seguranca { get; init; } = new();
    public ConfiguracaoVotacaoDto Votacao { get; init; } = new();
}

public record ConfiguracaoGeralDto
{
    public string NomeSistema { get; init; } = string.Empty;
    public string Versao { get; init; } = string.Empty;
    public string? LogoUrl { get; init; }
    public string? FaviconUrl { get; init; }
    public string? CorPrimaria { get; init; }
    public string? CorSecundaria { get; init; }
    public bool ModoManutencao { get; init; }
    public string? MensagemManutencao { get; init; }
    public string TimeZone { get; init; } = "America/Sao_Paulo";
    public string Locale { get; init; } = "pt-BR";
}

public record ConfiguracaoEmailDto
{
    public string SmtpHost { get; init; } = string.Empty;
    public int SmtpPort { get; init; }
    public bool SmtpUseSsl { get; init; }
    public string? SmtpUsername { get; init; }
    public string? SmtpPassword { get; init; }
    public string EmailRemetente { get; init; } = string.Empty;
    public string NomeRemetente { get; init; } = string.Empty;
    public bool EmailHabilitado { get; init; }
}

public record ConfiguracaoSegurancaDto
{
    public int TentativasLoginMax { get; init; }
    public int TempoBloqueioConta { get; init; }
    public int ExpiracaoSenhaEmDias { get; init; }
    public int TamanhoMinimoSenha { get; init; }
    public bool RequerLetraMaiuscula { get; init; }
    public bool RequerNumero { get; init; }
    public bool RequerCaractereEspecial { get; init; }
    public int ExpiracaoTokenEmMinutos { get; init; }
    public int ExpiracaoRefreshTokenEmDias { get; init; }
    public bool DoisFatoresObrigatorio { get; init; }
}

public record ConfiguracaoVotacaoDto
{
    public bool PermitirVotoBranco { get; init; }
    public bool PermitirVotoNulo { get; init; }
    public bool MostrarResultadoParcial { get; init; }
    public bool NotificarVotoRegistrado { get; init; }
    public int TempoSessaoVotacaoEmMinutos { get; init; }
    public bool ConfirmacaoVotoObrigatoria { get; init; }
    public string? MensagemVotacao { get; init; }
    public string? MensagemConfirmacao { get; init; }
}

public record ConfiguracaoItemDto
{
    public string Chave { get; init; } = string.Empty;
    public string Valor { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public string Tipo { get; init; } = string.Empty;
    public bool Editavel { get; init; }
    public DateTime? UltimaAtualizacao { get; init; }
    public string? AtualizadoPor { get; init; }
}

public record UpdateConfiguracaoDto
{
    public string Valor { get; init; } = string.Empty;
}

public record UpdateMultiplasConfiguracoesDto
{
    public Dictionary<string, string> Configuracoes { get; init; } = new();
}

public record TestarEmailRequest
{
    public string EmailDestino { get; init; } = string.Empty;
}

public record RoleDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public List<string> Permissoes { get; init; } = new();
    public bool Editavel { get; init; }
}

public record InfoSistemaDto
{
    public string Nome { get; init; } = string.Empty;
    public string Versao { get; init; } = string.Empty;
    public string Ambiente { get; init; } = string.Empty;
    public DateTime DataHoraServidor { get; init; }
    public string TimeZone { get; init; } = string.Empty;
    public bool EmManutencao { get; init; }
    public string? MensagemManutencao { get; init; }
}

// Interface do servico (a ser implementada)
public interface IConfiguracaoService
{
    Task<ConfiguracaoSistemaDto> GetAllAsync(CancellationToken cancellationToken = default);
    Task<ConfiguracaoItemDto?> GetByChaveAsync(string chave, CancellationToken cancellationToken = default);
    Task<ConfiguracaoItemDto> UpdateAsync(string chave, string valor, Guid userId, CancellationToken cancellationToken = default);
    Task<ConfiguracaoSistemaDto> UpdateMultipleAsync(Dictionary<string, string> configuracoes, Guid userId, CancellationToken cancellationToken = default);
    Task<ConfiguracaoEmailDto> GetEmailConfigAsync(CancellationToken cancellationToken = default);
    Task<ConfiguracaoEmailDto> UpdateEmailConfigAsync(ConfiguracaoEmailDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<bool> TestarEmailAsync(string emailDestino, CancellationToken cancellationToken = default);
    Task<ConfiguracaoSegurancaDto> GetSegurancaConfigAsync(CancellationToken cancellationToken = default);
    Task<ConfiguracaoSegurancaDto> UpdateSegurancaConfigAsync(ConfiguracaoSegurancaDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<ConfiguracaoVotacaoDto> GetVotacaoConfigAsync(CancellationToken cancellationToken = default);
    Task<ConfiguracaoVotacaoDto> UpdateVotacaoConfigAsync(ConfiguracaoVotacaoDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<ConfiguracaoSistemaDto> RestaurarPadraoAsync(Guid userId, CancellationToken cancellationToken = default);
    Task<(byte[] Content, string ContentType, string FileName)> ExportarAsync(CancellationToken cancellationToken = default);
    Task<ConfiguracaoSistemaDto> ImportarAsync(Stream stream, Guid userId, CancellationToken cancellationToken = default);
    Task<IEnumerable<RoleDto>> GetRolesAsync(CancellationToken cancellationToken = default);
    Task<InfoSistemaDto> GetInfoSistemaAsync(CancellationToken cancellationToken = default);
}
