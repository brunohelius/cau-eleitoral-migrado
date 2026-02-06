using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.DTOs;
using CAU.Eleitoral.Application.DTOs.Notificacoes;
using CAU.Eleitoral.Application.Interfaces;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para gerenciamento de notificacoes
/// </summary>
[Authorize]
public class NotificacaoController : BaseController
{
    private readonly INotificacaoService _notificacaoService;
    private readonly ILogger<NotificacaoController> _logger;

    public NotificacaoController(INotificacaoService notificacaoService, ILogger<NotificacaoController> logger)
    {
        _notificacaoService = notificacaoService;
        _logger = logger;
    }

    /// <summary>
    /// Lista notificacoes do usuario logado
    /// </summary>
    /// <param name="apenasNaoLidas">Filtrar apenas nao lidas</param>
    /// <param name="page">Pagina</param>
    /// <param name="pageSize">Tamanho da pagina</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de notificacoes</returns>
    [HttpGet]
    [ProducesResponseType(typeof(PagedResult<NotificacaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<PagedResult<NotificacaoDto>>> GetMinhas(
        [FromQuery] bool apenasNaoLidas = false,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 20,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var userId = GetUserId();
            var notificacoes = await _notificacaoService.GetByUsuarioAsync(userId, apenasNaoLidas, page, pageSize, cancellationToken);
            return Ok(notificacoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar notificacoes");
            return InternalError("Erro ao listar notificacoes");
        }
    }

    /// <summary>
    /// Obtem uma notificacao pelo ID
    /// </summary>
    /// <param name="id">ID da notificacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados da notificacao</returns>
    [HttpGet("{id:guid}")]
    [ProducesResponseType(typeof(NotificacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<NotificacaoDto>> GetById(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var notificacao = await _notificacaoService.GetByIdAsync(id, userId, cancellationToken);
            if (notificacao == null)
                return NotFound(new { message = "Notificacao nao encontrada" });

            return Ok(notificacao);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter notificacao {Id}", id);
            return InternalError("Erro ao obter notificacao");
        }
    }

    /// <summary>
    /// Obtem contagem de notificacoes nao lidas
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Contagem de notificacoes</returns>
    [HttpGet("nao-lidas/count")]
    [ProducesResponseType(typeof(ContagemNotificacoesDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<ContagemNotificacoesDto>> GetContagemNaoLidas(CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var contagem = await _notificacaoService.GetContagemNaoLidasAsync(userId, cancellationToken);
            return Ok(contagem);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter contagem de notificacoes");
            return InternalError("Erro ao obter contagem");
        }
    }

    /// <summary>
    /// Marca uma notificacao como lida
    /// </summary>
    /// <param name="id">ID da notificacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Notificacao atualizada</returns>
    [HttpPost("{id:guid}/marcar-lida")]
    [ProducesResponseType(typeof(NotificacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<NotificacaoDto>> MarcarComoLida(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var notificacao = await _notificacaoService.MarcarComoLidaAsync(id, userId, cancellationToken);
            return Ok(notificacao);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Notificacao nao encontrada" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao marcar notificacao como lida {Id}", id);
            return InternalError("Erro ao marcar como lida");
        }
    }

    /// <summary>
    /// Marca todas as notificacoes como lidas
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Resultado da operacao</returns>
    [HttpPost("marcar-todas-lidas")]
    [ProducesResponseType(StatusCodes.Status200OK)]
    public async Task<IActionResult> MarcarTodasComoLidas(CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var count = await _notificacaoService.MarcarTodasComoLidasAsync(userId, cancellationToken);
            return Ok(new { message = $"{count} notificacoes marcadas como lidas" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao marcar todas notificacoes como lidas");
            return InternalError("Erro ao marcar como lidas");
        }
    }

    /// <summary>
    /// Remove uma notificacao
    /// </summary>
    /// <param name="id">ID da notificacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>NoContent em caso de sucesso</returns>
    [HttpDelete("{id:guid}")]
    [ProducesResponseType(StatusCodes.Status204NoContent)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<IActionResult> Delete(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            await _notificacaoService.DeleteAsync(id, userId, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Notificacao nao encontrada" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao excluir notificacao {Id}", id);
            return InternalError("Erro ao excluir notificacao");
        }
    }

    /// <summary>
    /// Remove todas as notificacoes lidas
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Resultado da operacao</returns>
    [HttpDelete("lidas")]
    [ProducesResponseType(StatusCodes.Status200OK)]
    public async Task<IActionResult> DeleteLidas(CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var count = await _notificacaoService.DeleteLidasAsync(userId, cancellationToken);
            return Ok(new { message = $"{count} notificacoes removidas" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao excluir notificacoes lidas");
            return InternalError("Erro ao excluir notificacoes");
        }
    }

    /// <summary>
    /// Envia notificacao para um usuario (Admin)
    /// </summary>
    /// <param name="dto">Dados da notificacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Notificacao criada</returns>
    [HttpPost]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(NotificacaoDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<NotificacaoDto>> Enviar([FromBody] CreateNotificacaoDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var notificacao = await _notificacaoService.EnviarAsync(dto, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id = notificacao.Id }, notificacao);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao enviar notificacao");
            return InternalError("Erro ao enviar notificacao");
        }
    }

    /// <summary>
    /// Envia notificacao em massa (Admin)
    /// </summary>
    /// <param name="dto">Dados da notificacao em massa</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Resultado do envio</returns>
    [HttpPost("massa")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(ResultadoEnvioMassaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ResultadoEnvioMassaDto>> EnviarEmMassa([FromBody] CreateNotificacaoMassaDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var resultado = await _notificacaoService.EnviarEmMassaAsync(dto, cancellationToken);
            return Ok(resultado);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao enviar notificacoes em massa");
            return InternalError("Erro ao enviar notificacoes");
        }
    }

    /// <summary>
    /// Obtem configuracoes de notificacao do usuario
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Configuracoes de notificacao</returns>
    [HttpGet("configuracoes")]
    [ProducesResponseType(typeof(ConfiguracaoNotificacaoDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<ConfiguracaoNotificacaoDto>> GetConfiguracoes(CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var configuracoes = await _notificacaoService.GetConfiguracoesAsync(userId, cancellationToken);
            return Ok(configuracoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter configuracoes de notificacao");
            return InternalError("Erro ao obter configuracoes");
        }
    }

    /// <summary>
    /// Atualiza configuracoes de notificacao do usuario
    /// </summary>
    /// <param name="dto">Configuracoes atualizadas</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Configuracoes atualizadas</returns>
    [HttpPut("configuracoes")]
    [ProducesResponseType(typeof(ConfiguracaoNotificacaoDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<ConfiguracaoNotificacaoDto>> UpdateConfiguracoes(
        [FromBody] UpdateConfiguracaoNotificacaoDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var configuracoes = await _notificacaoService.UpdateConfiguracoesAsync(userId, dto, cancellationToken);
            return Ok(configuracoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar configuracoes de notificacao");
            return InternalError("Erro ao atualizar configuracoes");
        }
    }
}

// DTOs para Notificacao
public record NotificacaoDto
{
    public Guid Id { get; init; }
    public Guid UsuarioId { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string Mensagem { get; init; } = string.Empty;
    public TipoNotificacao Tipo { get; init; }
    public PrioridadeNotificacao Prioridade { get; init; }
    public bool Lida { get; init; }
    public DateTime? DataLeitura { get; init; }
    public string? Link { get; init; }
    public string? Icone { get; init; }
    public Dictionary<string, string>? Dados { get; init; }
    public DateTime CreatedAt { get; init; }
}

public record ContagemNotificacoesDto
{
    public int Total { get; init; }
    public int NaoLidas { get; init; }
    public int AltaPrioridade { get; init; }
}

public record CreateNotificacaoDto
{
    public Guid UsuarioId { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string Mensagem { get; init; } = string.Empty;
    public TipoNotificacao Tipo { get; init; }
    public PrioridadeNotificacao Prioridade { get; init; }
    public string? Link { get; init; }
    public string? Icone { get; init; }
    public Dictionary<string, string>? Dados { get; init; }
}

public record CreateNotificacaoMassaDto
{
    public List<Guid>? UsuarioIds { get; init; }
    public string? Role { get; init; }
    public Guid? EleicaoId { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string Mensagem { get; init; } = string.Empty;
    public TipoNotificacao Tipo { get; init; }
    public PrioridadeNotificacao Prioridade { get; init; }
    public string? Link { get; init; }
}

public record ResultadoEnvioMassaDto
{
    public int TotalEnviadas { get; init; }
    public int Sucesso { get; init; }
    public int Falhas { get; init; }
    public List<string>? Erros { get; init; }
}

public record ConfiguracaoNotificacaoDto
{
    public bool EmailHabilitado { get; init; }
    public bool PushHabilitado { get; init; }
    public bool SmsHabilitado { get; init; }
    public bool NotificacaoEleicao { get; init; }
    public bool NotificacaoDenuncia { get; init; }
    public bool NotificacaoImpugnacao { get; init; }
    public bool NotificacaoVotacao { get; init; }
    public bool NotificacaoResultado { get; init; }
    public bool NotificacaoSistema { get; init; }
    public bool ResumoDigital { get; init; }
    public string? FrequenciaResumo { get; init; }
}

public record UpdateConfiguracaoNotificacaoDto
{
    public bool? EmailHabilitado { get; init; }
    public bool? PushHabilitado { get; init; }
    public bool? SmsHabilitado { get; init; }
    public bool? NotificacaoEleicao { get; init; }
    public bool? NotificacaoDenuncia { get; init; }
    public bool? NotificacaoImpugnacao { get; init; }
    public bool? NotificacaoVotacao { get; init; }
    public bool? NotificacaoResultado { get; init; }
    public bool? NotificacaoSistema { get; init; }
    public bool? ResumoDigital { get; init; }
    public string? FrequenciaResumo { get; init; }
}

public enum TipoNotificacao
{
    Info = 0,
    Sucesso = 1,
    Alerta = 2,
    Erro = 3,
    Sistema = 4
}

public enum PrioridadeNotificacao
{
    Baixa = 0,
    Normal = 1,
    Alta = 2,
    Urgente = 3
}

// Interface do servico (a ser implementada)
public interface INotificacaoService
{
    Task<PagedResult<NotificacaoDto>> GetByUsuarioAsync(Guid userId, bool apenasNaoLidas, int page, int pageSize, CancellationToken cancellationToken = default);
    Task<NotificacaoDto?> GetByIdAsync(Guid id, Guid userId, CancellationToken cancellationToken = default);
    Task<ContagemNotificacoesDto> GetContagemNaoLidasAsync(Guid userId, CancellationToken cancellationToken = default);
    Task<NotificacaoDto> MarcarComoLidaAsync(Guid id, Guid userId, CancellationToken cancellationToken = default);
    Task<int> MarcarTodasComoLidasAsync(Guid userId, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, Guid userId, CancellationToken cancellationToken = default);
    Task<int> DeleteLidasAsync(Guid userId, CancellationToken cancellationToken = default);
    Task<NotificacaoDto> EnviarAsync(CreateNotificacaoDto dto, CancellationToken cancellationToken = default);
    Task<ResultadoEnvioMassaDto> EnviarEmMassaAsync(CreateNotificacaoMassaDto dto, CancellationToken cancellationToken = default);
    Task<ConfiguracaoNotificacaoDto> GetConfiguracoesAsync(Guid userId, CancellationToken cancellationToken = default);
    Task<ConfiguracaoNotificacaoDto> UpdateConfiguracoesAsync(Guid userId, UpdateConfiguracaoNotificacaoDto dto, CancellationToken cancellationToken = default);
}
