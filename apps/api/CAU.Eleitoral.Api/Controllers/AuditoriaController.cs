using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para gerenciamento de logs de auditoria
/// </summary>
[Authorize(Roles = "Admin")]
public class AuditoriaController : BaseController
{
    private readonly IAuditoriaService _auditoriaService;
    private readonly ILogger<AuditoriaController> _logger;

    public AuditoriaController(IAuditoriaService auditoriaService, ILogger<AuditoriaController> logger)
    {
        _auditoriaService = auditoriaService;
        _logger = logger;
    }

    /// <summary>
    /// Lista logs de auditoria com filtros
    /// </summary>
    /// <param name="filtro">Filtros de busca</param>
    /// <param name="page">Pagina</param>
    /// <param name="pageSize">Tamanho da pagina</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de logs</returns>
    [HttpGet]
    [ProducesResponseType(typeof(PagedResult<AuditoriaLogDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<PagedResult<AuditoriaLogDto>>> GetAll(
        [FromQuery] FiltroAuditoriaDto filtro,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 50,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var logs = await _auditoriaService.GetAllAsync(filtro, page, pageSize, cancellationToken);
            return Ok(logs);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar logs de auditoria");
            return InternalError("Erro ao listar logs");
        }
    }

    /// <summary>
    /// Obtem um log pelo ID
    /// </summary>
    /// <param name="id">ID do log</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados do log</returns>
    [HttpGet("{id:guid}")]
    [ProducesResponseType(typeof(AuditoriaLogDetalheDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<AuditoriaLogDetalheDto>> GetById(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var log = await _auditoriaService.GetByIdAsync(id, cancellationToken);
            if (log == null)
                return NotFound(new { message = "Log nao encontrado" });

            return Ok(log);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter log {Id}", id);
            return InternalError("Erro ao obter log");
        }
    }

    /// <summary>
    /// Lista logs por usuario
    /// </summary>
    /// <param name="usuarioId">ID do usuario</param>
    /// <param name="page">Pagina</param>
    /// <param name="pageSize">Tamanho da pagina</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de logs</returns>
    [HttpGet("usuario/{usuarioId:guid}")]
    [ProducesResponseType(typeof(PagedResult<AuditoriaLogDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<PagedResult<AuditoriaLogDto>>> GetByUsuario(
        Guid usuarioId,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 50,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var logs = await _auditoriaService.GetByUsuarioAsync(usuarioId, page, pageSize, cancellationToken);
            return Ok(logs);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar logs do usuario {UsuarioId}", usuarioId);
            return InternalError("Erro ao listar logs");
        }
    }

    /// <summary>
    /// Lista logs por entidade
    /// </summary>
    /// <param name="entidadeTipo">Tipo da entidade</param>
    /// <param name="entidadeId">ID da entidade</param>
    /// <param name="page">Pagina</param>
    /// <param name="pageSize">Tamanho da pagina</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de logs</returns>
    [HttpGet("entidade/{entidadeTipo}/{entidadeId:guid}")]
    [ProducesResponseType(typeof(PagedResult<AuditoriaLogDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<PagedResult<AuditoriaLogDto>>> GetByEntidade(
        string entidadeTipo,
        Guid entidadeId,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 50,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var logs = await _auditoriaService.GetByEntidadeAsync(entidadeTipo, entidadeId, page, pageSize, cancellationToken);
            return Ok(logs);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar logs da entidade {EntidadeTipo}/{EntidadeId}", entidadeTipo, entidadeId);
            return InternalError("Erro ao listar logs");
        }
    }

    /// <summary>
    /// Lista logs por acao
    /// </summary>
    /// <param name="acao">Tipo de acao</param>
    /// <param name="page">Pagina</param>
    /// <param name="pageSize">Tamanho da pagina</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de logs</returns>
    [HttpGet("acao/{acao}")]
    [ProducesResponseType(typeof(PagedResult<AuditoriaLogDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<PagedResult<AuditoriaLogDto>>> GetByAcao(
        string acao,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 50,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var logs = await _auditoriaService.GetByAcaoAsync(acao, page, pageSize, cancellationToken);
            return Ok(logs);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar logs da acao {Acao}", acao);
            return InternalError("Erro ao listar logs");
        }
    }

    /// <summary>
    /// Lista logs por periodo
    /// </summary>
    /// <param name="dataInicio">Data de inicio</param>
    /// <param name="dataFim">Data de fim</param>
    /// <param name="page">Pagina</param>
    /// <param name="pageSize">Tamanho da pagina</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de logs</returns>
    [HttpGet("periodo")]
    [ProducesResponseType(typeof(PagedResult<AuditoriaLogDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<PagedResult<AuditoriaLogDto>>> GetByPeriodo(
        [FromQuery] DateTime dataInicio,
        [FromQuery] DateTime dataFim,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 50,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var logs = await _auditoriaService.GetByPeriodoAsync(dataInicio, dataFim, page, pageSize, cancellationToken);
            return Ok(logs);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar logs do periodo");
            return InternalError("Erro ao listar logs");
        }
    }

    /// <summary>
    /// Obtem estatisticas de auditoria
    /// </summary>
    /// <param name="dataInicio">Data de inicio (opcional)</param>
    /// <param name="dataFim">Data de fim (opcional)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Estatisticas</returns>
    [HttpGet("estatisticas")]
    [ProducesResponseType(typeof(EstatisticasAuditoriaDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<EstatisticasAuditoriaDto>> GetEstatisticas(
        [FromQuery] DateTime? dataInicio,
        [FromQuery] DateTime? dataFim,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var estatisticas = await _auditoriaService.GetEstatisticasAsync(dataInicio, dataFim, cancellationToken);
            return Ok(estatisticas);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter estatisticas de auditoria");
            return InternalError("Erro ao obter estatisticas");
        }
    }

    /// <summary>
    /// Lista acoes disponiveis para filtro
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de acoes</returns>
    [HttpGet("acoes")]
    [ProducesResponseType(typeof(IEnumerable<string>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<string>>> GetAcoes(CancellationToken cancellationToken)
    {
        try
        {
            var acoes = await _auditoriaService.GetAcoesAsync(cancellationToken);
            return Ok(acoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar acoes");
            return InternalError("Erro ao listar acoes");
        }
    }

    /// <summary>
    /// Lista tipos de entidade disponiveis para filtro
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de tipos de entidade</returns>
    [HttpGet("entidades")]
    [ProducesResponseType(typeof(IEnumerable<string>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<string>>> GetTiposEntidade(CancellationToken cancellationToken)
    {
        try
        {
            var tipos = await _auditoriaService.GetTiposEntidadeAsync(cancellationToken);
            return Ok(tipos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar tipos de entidade");
            return InternalError("Erro ao listar tipos");
        }
    }

    /// <summary>
    /// Exporta logs de auditoria
    /// </summary>
    /// <param name="filtro">Filtros de busca</param>
    /// <param name="formato">Formato de saida (csv, excel)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Arquivo de logs</returns>
    [HttpGet("exportar")]
    [ProducesResponseType(typeof(FileContentResult), StatusCodes.Status200OK)]
    public async Task<IActionResult> Exportar(
        [FromQuery] FiltroAuditoriaDto filtro,
        [FromQuery] string formato = "csv",
        CancellationToken cancellationToken = default)
    {
        try
        {
            var (content, contentType, fileName) = await _auditoriaService.ExportarAsync(filtro, formato, cancellationToken);
            return File(content, contentType, fileName);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao exportar logs de auditoria");
            return InternalError("Erro ao exportar logs");
        }
    }

    /// <summary>
    /// Limpa logs antigos
    /// </summary>
    /// <param name="diasRetencao">Dias de retencao (logs mais antigos serao removidos)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Resultado da limpeza</returns>
    [HttpDelete("limpar")]
    [ProducesResponseType(typeof(ResultadoLimpezaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ResultadoLimpezaDto>> LimparLogsAntigos(
        [FromQuery] int diasRetencao = 365,
        CancellationToken cancellationToken = default)
    {
        try
        {
            if (diasRetencao < 30)
                return BadRequest(new { message = "O periodo minimo de retencao e de 30 dias" });

            var resultado = await _auditoriaService.LimparLogsAntigosAsync(diasRetencao, cancellationToken);
            return Ok(resultado);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao limpar logs antigos");
            return InternalError("Erro ao limpar logs");
        }
    }

    /// <summary>
    /// Obtem log de alteracoes de uma entidade (diff)
    /// </summary>
    /// <param name="entidadeTipo">Tipo da entidade</param>
    /// <param name="entidadeId">ID da entidade</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Historico de alteracoes</returns>
    [HttpGet("historico/{entidadeTipo}/{entidadeId:guid}")]
    [ProducesResponseType(typeof(IEnumerable<HistoricoAlteracaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<HistoricoAlteracaoDto>>> GetHistoricoAlteracoes(
        string entidadeTipo,
        Guid entidadeId,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var historico = await _auditoriaService.GetHistoricoAlteracoesAsync(entidadeTipo, entidadeId, cancellationToken);
            return Ok(historico);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter historico de alteracoes");
            return InternalError("Erro ao obter historico");
        }
    }
}

// DTOs para Auditoria
public record AuditoriaLogDto
{
    public Guid Id { get; init; }
    public DateTime DataHora { get; init; }
    public string Acao { get; init; } = string.Empty;
    public string? EntidadeTipo { get; init; }
    public Guid? EntidadeId { get; init; }
    public string? EntidadeNome { get; init; }
    public Guid? UsuarioId { get; init; }
    public string? UsuarioNome { get; init; }
    public string? UsuarioEmail { get; init; }
    public string? IpAddress { get; init; }
    public string? UserAgent { get; init; }
    public bool Sucesso { get; init; }
    public string? Mensagem { get; init; }
}

public record AuditoriaLogDetalheDto : AuditoriaLogDto
{
    public string? DadosAnteriores { get; init; }
    public string? DadosNovos { get; init; }
    public Dictionary<string, object>? Metadados { get; init; }
    public string? RequestPath { get; init; }
    public string? RequestMethod { get; init; }
    public int? ResponseStatusCode { get; init; }
    public long? DuracaoMs { get; init; }
}

public record FiltroAuditoriaDto
{
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
    public string? Acao { get; init; }
    public string? EntidadeTipo { get; init; }
    public Guid? EntidadeId { get; init; }
    public Guid? UsuarioId { get; init; }
    public string? IpAddress { get; init; }
    public bool? Sucesso { get; init; }
    public string? Busca { get; init; }
}

public record EstatisticasAuditoriaDto
{
    public int TotalLogs { get; init; }
    public int LogsHoje { get; init; }
    public int LogsSemana { get; init; }
    public int LogsMes { get; init; }
    public int ErrosTotal { get; init; }
    public List<AcaoContadorDto> LogsPorAcao { get; init; } = new();
    public List<EntidadeContadorDto> LogsPorEntidade { get; init; } = new();
    public List<UsuarioAtividadeDto> UsuariosMaisAtivos { get; init; } = new();
    public List<LogPorDiaDto> LogsPorDia { get; init; } = new();
}

public record AcaoContadorDto
{
    public string Acao { get; init; } = string.Empty;
    public int Total { get; init; }
}

public record EntidadeContadorDto
{
    public string EntidadeTipo { get; init; } = string.Empty;
    public int Total { get; init; }
}

public record UsuarioAtividadeDto
{
    public Guid UsuarioId { get; init; }
    public string UsuarioNome { get; init; } = string.Empty;
    public int TotalAcoes { get; init; }
}

public record LogPorDiaDto
{
    public DateTime Data { get; init; }
    public int Total { get; init; }
    public int Erros { get; init; }
}

public record ResultadoLimpezaDto
{
    public int LogsRemovidos { get; init; }
    public DateTime DataCorte { get; init; }
    public DateTime DataExecucao { get; init; }
}

public record HistoricoAlteracaoDto
{
    public Guid Id { get; init; }
    public DateTime DataHora { get; init; }
    public string Acao { get; init; } = string.Empty;
    public Guid? UsuarioId { get; init; }
    public string? UsuarioNome { get; init; }
    public List<CampoAlteradoDto> CamposAlterados { get; init; } = new();
}

public record CampoAlteradoDto
{
    public string Campo { get; init; } = string.Empty;
    public string? ValorAnterior { get; init; }
    public string? ValorNovo { get; init; }
}

// Interface do servico (a ser implementada)
public interface IAuditoriaService
{
    Task<PagedResult<AuditoriaLogDto>> GetAllAsync(FiltroAuditoriaDto filtro, int page, int pageSize, CancellationToken cancellationToken = default);
    Task<AuditoriaLogDetalheDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<PagedResult<AuditoriaLogDto>> GetByUsuarioAsync(Guid usuarioId, int page, int pageSize, CancellationToken cancellationToken = default);
    Task<PagedResult<AuditoriaLogDto>> GetByEntidadeAsync(string entidadeTipo, Guid entidadeId, int page, int pageSize, CancellationToken cancellationToken = default);
    Task<PagedResult<AuditoriaLogDto>> GetByAcaoAsync(string acao, int page, int pageSize, CancellationToken cancellationToken = default);
    Task<PagedResult<AuditoriaLogDto>> GetByPeriodoAsync(DateTime dataInicio, DateTime dataFim, int page, int pageSize, CancellationToken cancellationToken = default);
    Task<EstatisticasAuditoriaDto> GetEstatisticasAsync(DateTime? dataInicio, DateTime? dataFim, CancellationToken cancellationToken = default);
    Task<IEnumerable<string>> GetAcoesAsync(CancellationToken cancellationToken = default);
    Task<IEnumerable<string>> GetTiposEntidadeAsync(CancellationToken cancellationToken = default);
    Task<(byte[] Content, string ContentType, string FileName)> ExportarAsync(FiltroAuditoriaDto filtro, string formato, CancellationToken cancellationToken = default);
    Task<ResultadoLimpezaDto> LimparLogsAntigosAsync(int diasRetencao, CancellationToken cancellationToken = default);
    Task<IEnumerable<HistoricoAlteracaoDto>> GetHistoricoAlteracoesAsync(string entidadeTipo, Guid entidadeId, CancellationToken cancellationToken = default);
}
