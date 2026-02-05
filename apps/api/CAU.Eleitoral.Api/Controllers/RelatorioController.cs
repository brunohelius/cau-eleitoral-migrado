using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para geracao de relatorios
/// </summary>
[Authorize]
public class RelatorioController : BaseController
{
    private readonly IRelatorioService _relatorioService;
    private readonly ILogger<RelatorioController> _logger;

    public RelatorioController(IRelatorioService relatorioService, ILogger<RelatorioController> logger)
    {
        _relatorioService = relatorioService;
        _logger = logger;
    }

    /// <summary>
    /// Lista relatorios disponiveis
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de relatorios disponiveis</returns>
    [HttpGet]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(IEnumerable<TipoRelatorioDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<TipoRelatorioDto>>> GetTiposDisponiveis(CancellationToken cancellationToken)
    {
        try
        {
            var tipos = await _relatorioService.GetTiposDisponiveisAsync(cancellationToken);
            return Ok(tipos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar tipos de relatorios");
            return InternalError("Erro ao listar relatorios");
        }
    }

    /// <summary>
    /// Gera relatorio de participacao eleitoral
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="formato">Formato de saida (pdf, excel, csv)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Arquivo do relatorio</returns>
    [HttpGet("participacao/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(FileContentResult), StatusCodes.Status200OK)]
    public async Task<IActionResult> GerarRelatorioParticipacao(
        Guid eleicaoId,
        [FromQuery] string formato = "pdf",
        CancellationToken cancellationToken = default)
    {
        try
        {
            var (content, contentType, fileName) = await _relatorioService.GerarRelatorioParticipacaoAsync(eleicaoId, formato, cancellationToken);
            return File(content, contentType, fileName);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao gerar relatorio de participacao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao gerar relatorio");
        }
    }

    /// <summary>
    /// Gera relatorio de resultado da eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="formato">Formato de saida (pdf, excel, csv)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Arquivo do relatorio</returns>
    [HttpGet("resultado/{eleicaoId:guid}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(FileContentResult), StatusCodes.Status200OK)]
    public async Task<IActionResult> GerarRelatorioResultado(
        Guid eleicaoId,
        [FromQuery] string formato = "pdf",
        CancellationToken cancellationToken = default)
    {
        try
        {
            var (content, contentType, fileName) = await _relatorioService.GerarRelatorioResultadoAsync(eleicaoId, formato, cancellationToken);
            return File(content, contentType, fileName);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao gerar relatorio de resultado {EleicaoId}", eleicaoId);
            return InternalError("Erro ao gerar relatorio");
        }
    }

    /// <summary>
    /// Gera relatorio de chapas inscritas
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="formato">Formato de saida (pdf, excel, csv)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Arquivo do relatorio</returns>
    [HttpGet("chapas/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(FileContentResult), StatusCodes.Status200OK)]
    public async Task<IActionResult> GerarRelatorioChapas(
        Guid eleicaoId,
        [FromQuery] string formato = "pdf",
        CancellationToken cancellationToken = default)
    {
        try
        {
            var (content, contentType, fileName) = await _relatorioService.GerarRelatorioChapasAsync(eleicaoId, formato, cancellationToken);
            return File(content, contentType, fileName);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao gerar relatorio de chapas {EleicaoId}", eleicaoId);
            return InternalError("Erro ao gerar relatorio");
        }
    }

    /// <summary>
    /// Gera relatorio de eleitores
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="formato">Formato de saida (pdf, excel, csv)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Arquivo do relatorio</returns>
    [HttpGet("eleitores/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(FileContentResult), StatusCodes.Status200OK)]
    public async Task<IActionResult> GerarRelatorioEleitores(
        Guid eleicaoId,
        [FromQuery] string formato = "pdf",
        CancellationToken cancellationToken = default)
    {
        try
        {
            var (content, contentType, fileName) = await _relatorioService.GerarRelatorioEleitoresAsync(eleicaoId, formato, cancellationToken);
            return File(content, contentType, fileName);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao gerar relatorio de eleitores {EleicaoId}", eleicaoId);
            return InternalError("Erro ao gerar relatorio");
        }
    }

    /// <summary>
    /// Gera relatorio de denuncias
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="formato">Formato de saida (pdf, excel, csv)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Arquivo do relatorio</returns>
    [HttpGet("denuncias/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(FileContentResult), StatusCodes.Status200OK)]
    public async Task<IActionResult> GerarRelatorioDenuncias(
        Guid eleicaoId,
        [FromQuery] string formato = "pdf",
        CancellationToken cancellationToken = default)
    {
        try
        {
            var (content, contentType, fileName) = await _relatorioService.GerarRelatorioDenunciasAsync(eleicaoId, formato, cancellationToken);
            return File(content, contentType, fileName);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao gerar relatorio de denuncias {EleicaoId}", eleicaoId);
            return InternalError("Erro ao gerar relatorio");
        }
    }

    /// <summary>
    /// Gera relatorio de impugnacoes
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="formato">Formato de saida (pdf, excel, csv)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Arquivo do relatorio</returns>
    [HttpGet("impugnacoes/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(FileContentResult), StatusCodes.Status200OK)]
    public async Task<IActionResult> GerarRelatorioImpugnacoes(
        Guid eleicaoId,
        [FromQuery] string formato = "pdf",
        CancellationToken cancellationToken = default)
    {
        try
        {
            var (content, contentType, fileName) = await _relatorioService.GerarRelatorioImpugnacoesAsync(eleicaoId, formato, cancellationToken);
            return File(content, contentType, fileName);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao gerar relatorio de impugnacoes {EleicaoId}", eleicaoId);
            return InternalError("Erro ao gerar relatorio");
        }
    }

    /// <summary>
    /// Gera relatorio de auditoria
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="formato">Formato de saida (pdf, excel, csv)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Arquivo do relatorio</returns>
    [HttpGet("auditoria/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(FileContentResult), StatusCodes.Status200OK)]
    public async Task<IActionResult> GerarRelatorioAuditoria(
        Guid eleicaoId,
        [FromQuery] string formato = "pdf",
        CancellationToken cancellationToken = default)
    {
        try
        {
            var (content, contentType, fileName) = await _relatorioService.GerarRelatorioAuditoriaAsync(eleicaoId, formato, cancellationToken);
            return File(content, contentType, fileName);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao gerar relatorio de auditoria {EleicaoId}", eleicaoId);
            return InternalError("Erro ao gerar relatorio");
        }
    }

    /// <summary>
    /// Gera relatorio consolidado da eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="formato">Formato de saida (pdf, excel)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Arquivo do relatorio</returns>
    [HttpGet("consolidado/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(FileContentResult), StatusCodes.Status200OK)]
    public async Task<IActionResult> GerarRelatorioConsolidado(
        Guid eleicaoId,
        [FromQuery] string formato = "pdf",
        CancellationToken cancellationToken = default)
    {
        try
        {
            var (content, contentType, fileName) = await _relatorioService.GerarRelatorioConsolidadoAsync(eleicaoId, formato, cancellationToken);
            return File(content, contentType, fileName);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao gerar relatorio consolidado {EleicaoId}", eleicaoId);
            return InternalError("Erro ao gerar relatorio");
        }
    }

    /// <summary>
    /// Gera relatorio comparativo entre eleicoes
    /// </summary>
    /// <param name="dto">Dados para comparacao</param>
    /// <param name="formato">Formato de saida (pdf, excel)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Arquivo do relatorio</returns>
    [HttpPost("comparativo")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(FileContentResult), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> GerarRelatorioComparativo(
        [FromBody] RelatorioComparativoDto dto,
        [FromQuery] string formato = "pdf",
        CancellationToken cancellationToken = default)
    {
        try
        {
            if (dto.EleicaoIds == null || dto.EleicaoIds.Count < 2)
                return BadRequest(new { message = "Informe pelo menos duas eleicoes para comparacao" });

            var (content, contentType, fileName) = await _relatorioService.GerarRelatorioComparativoAsync(dto.EleicaoIds, formato, cancellationToken);
            return File(content, contentType, fileName);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao gerar relatorio comparativo");
            return InternalError("Erro ao gerar relatorio");
        }
    }

    /// <summary>
    /// Gera relatorio personalizado
    /// </summary>
    /// <param name="dto">Parametros do relatorio</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Arquivo do relatorio</returns>
    [HttpPost("personalizado")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(FileContentResult), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> GerarRelatorioPersonalizado(
        [FromBody] RelatorioPersonalizadoDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var (content, contentType, fileName) = await _relatorioService.GerarRelatorioPersonalizadoAsync(dto, cancellationToken);
            return File(content, contentType, fileName);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao gerar relatorio personalizado");
            return InternalError("Erro ao gerar relatorio");
        }
    }

    /// <summary>
    /// Lista relatorios gerados anteriormente
    /// </summary>
    /// <param name="eleicaoId">Filtro opcional por eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de relatorios</returns>
    [HttpGet("historico")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(IEnumerable<RelatorioGeradoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<RelatorioGeradoDto>>> GetHistorico(
        [FromQuery] Guid? eleicaoId,
        CancellationToken cancellationToken)
    {
        try
        {
            var relatorios = await _relatorioService.GetHistoricoAsync(eleicaoId, cancellationToken);
            return Ok(relatorios);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar historico de relatorios");
            return InternalError("Erro ao listar historico");
        }
    }

    /// <summary>
    /// Faz download de um relatorio gerado anteriormente
    /// </summary>
    /// <param name="id">ID do relatorio</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Arquivo do relatorio</returns>
    [HttpGet("download/{id:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(FileContentResult), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<IActionResult> Download(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var (content, contentType, fileName) = await _relatorioService.DownloadAsync(id, cancellationToken);
            return File(content, contentType, fileName);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Relatorio nao encontrado" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao fazer download do relatorio {Id}", id);
            return InternalError("Erro ao fazer download");
        }
    }
}

// DTOs para Relatorio
public record TipoRelatorioDto
{
    public string Codigo { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public List<string> FormatosDisponiveis { get; init; } = new();
    public bool RequerEleicao { get; init; }
}

public record RelatorioComparativoDto
{
    public List<Guid> EleicaoIds { get; init; } = new();
}

public record RelatorioPersonalizadoDto
{
    public Guid EleicaoId { get; init; }
    public string Tipo { get; init; } = string.Empty;
    public string Formato { get; init; } = "pdf";
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
    public List<string>? Colunas { get; init; }
    public Dictionary<string, string>? Filtros { get; init; }
    public string? OrdenarPor { get; init; }
    public bool OrdemDescendente { get; init; }
}

public record RelatorioGeradoDto
{
    public Guid Id { get; init; }
    public Guid? EleicaoId { get; init; }
    public string? EleicaoNome { get; init; }
    public string Tipo { get; init; } = string.Empty;
    public string TipoNome { get; init; } = string.Empty;
    public string Formato { get; init; } = string.Empty;
    public string? NomeArquivo { get; init; }
    public long? Tamanho { get; init; }
    public Guid GeradoPor { get; init; }
    public string GeradoPorNome { get; init; } = string.Empty;
    public DateTime DataGeracao { get; init; }
}

// Interface do servico (a ser implementada)
public interface IRelatorioService
{
    Task<IEnumerable<TipoRelatorioDto>> GetTiposDisponiveisAsync(CancellationToken cancellationToken = default);
    Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioParticipacaoAsync(Guid eleicaoId, string formato, CancellationToken cancellationToken = default);
    Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioResultadoAsync(Guid eleicaoId, string formato, CancellationToken cancellationToken = default);
    Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioChapasAsync(Guid eleicaoId, string formato, CancellationToken cancellationToken = default);
    Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioEleitoresAsync(Guid eleicaoId, string formato, CancellationToken cancellationToken = default);
    Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioDenunciasAsync(Guid eleicaoId, string formato, CancellationToken cancellationToken = default);
    Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioImpugnacoesAsync(Guid eleicaoId, string formato, CancellationToken cancellationToken = default);
    Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioAuditoriaAsync(Guid eleicaoId, string formato, CancellationToken cancellationToken = default);
    Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioConsolidadoAsync(Guid eleicaoId, string formato, CancellationToken cancellationToken = default);
    Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioComparativoAsync(List<Guid> eleicaoIds, string formato, CancellationToken cancellationToken = default);
    Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioPersonalizadoAsync(RelatorioPersonalizadoDto dto, CancellationToken cancellationToken = default);
    Task<IEnumerable<RelatorioGeradoDto>> GetHistoricoAsync(Guid? eleicaoId, CancellationToken cancellationToken = default);
    Task<(byte[] Content, string ContentType, string FileName)> DownloadAsync(Guid id, CancellationToken cancellationToken = default);
}
