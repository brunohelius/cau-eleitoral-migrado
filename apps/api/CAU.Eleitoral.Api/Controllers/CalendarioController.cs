using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Infrastructure.Data;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para gerenciamento do calendario eleitoral
/// </summary>
[Authorize]
public class CalendarioController : BaseController
{
    private readonly ICalendarioService _calendarioService;
    private readonly AppDbContext _db;
    private readonly ILogger<CalendarioController> _logger;

    public CalendarioController(ICalendarioService calendarioService, AppDbContext db, ILogger<CalendarioController> logger)
    {
        _calendarioService = calendarioService;
        _db = db;
        _logger = logger;
    }

    /// <summary>
    /// Lista todos os eventos do calendario
    /// </summary>
    [HttpGet]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<CalendarioEventoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<CalendarioEventoDto>>> GetAll(
        [FromQuery] Guid? eleicaoId,
        [FromQuery] TipoCalendario? tipo,
        CancellationToken cancellationToken)
    {
        try
        {
            // Direct DB query bypassing service layer to avoid soft-delete filter issues
            var query = _db.Calendarios.IgnoreQueryFilters()
                .Include(c => c.Eleicao)
                .Where(c => !c.IsDeleted)
                .AsQueryable();

            if (eleicaoId.HasValue)
                query = query.Where(c => c.EleicaoId == eleicaoId.Value);
            if (tipo.HasValue)
                query = query.Where(c => c.Tipo == tipo.Value);

            var items = await query.OrderBy(c => c.Ordem).ToListAsync(cancellationToken);
            _logger.LogInformation("CalendarioController.GetAll: found {Count} items (eleicaoId={EleicaoId}, tipo={Tipo})", items.Count, eleicaoId, tipo);

            var dtos = items.Select(c => new CalendarioEventoDto
            {
                Id = c.Id,
                EleicaoId = c.EleicaoId,
                EleicaoNome = c.Eleicao?.Nome ?? string.Empty,
                Titulo = c.Nome,
                Descricao = c.Descricao,
                Tipo = c.Tipo,
                Status = c.Status,
                DataInicio = c.DataInicio,
                DataFim = c.DataFim,
                DiaInteiro = !c.HoraInicio.HasValue,
                Obrigatorio = c.Obrigatorio,
                CreatedAt = c.CreatedAt,
                UpdatedAt = c.UpdatedAt
            });

            return Ok(dtos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar eventos do calendario");
            return InternalError("Erro ao listar eventos");
        }
    }

    /// <summary>
    /// Obtem um evento pelo ID
    /// </summary>
    /// <param name="id">ID do evento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados do evento</returns>
    [HttpGet("{id:guid}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(CalendarioEventoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<CalendarioEventoDto>> GetById(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var evento = await _calendarioService.GetByIdAsync(id, cancellationToken);
            if (evento == null)
                return NotFound(new { message = "Evento nao encontrado" });

            return Ok(evento);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter evento {Id}", id);
            return InternalError("Erro ao obter evento");
        }
    }

    /// <summary>
    /// Lista eventos por eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de eventos</returns>
    [HttpGet("eleicao/{eleicaoId:guid}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<CalendarioEventoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<CalendarioEventoDto>>> GetByEleicao(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var eventos = await _calendarioService.GetByEleicaoAsync(eleicaoId, cancellationToken);
            return Ok(eventos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar eventos da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao listar eventos");
        }
    }

    /// <summary>
    /// Lista eventos proximos (dentro dos proximos dias)
    /// </summary>
    /// <param name="dias">Numero de dias a frente (padrao: 7)</param>
    /// <param name="eleicaoId">Filtro opcional por eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de eventos proximos</returns>
    [HttpGet("proximos")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<CalendarioEventoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<CalendarioEventoDto>>> GetProximos(
        [FromQuery] int dias = 7,
        [FromQuery] Guid? eleicaoId = null,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var eventos = await _calendarioService.GetProximosAsync(dias, eleicaoId, cancellationToken);
            return Ok(eventos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar proximos eventos");
            return InternalError("Erro ao listar eventos");
        }
    }

    /// <summary>
    /// Lista eventos em andamento
    /// </summary>
    /// <param name="eleicaoId">Filtro opcional por eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de eventos em andamento</returns>
    [HttpGet("em-andamento")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<CalendarioEventoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<CalendarioEventoDto>>> GetEmAndamento(
        [FromQuery] Guid? eleicaoId,
        CancellationToken cancellationToken)
    {
        try
        {
            var eventos = await _calendarioService.GetEmAndamentoAsync(eleicaoId, cancellationToken);
            return Ok(eventos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar eventos em andamento");
            return InternalError("Erro ao listar eventos");
        }
    }

    /// <summary>
    /// Lista eventos por periodo
    /// </summary>
    /// <param name="dataInicio">Data de inicio</param>
    /// <param name="dataFim">Data de fim</param>
    /// <param name="eleicaoId">Filtro opcional por eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de eventos no periodo</returns>
    [HttpGet("periodo")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<CalendarioEventoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<CalendarioEventoDto>>> GetByPeriodo(
        [FromQuery] DateTime dataInicio,
        [FromQuery] DateTime dataFim,
        [FromQuery] Guid? eleicaoId,
        CancellationToken cancellationToken)
    {
        try
        {
            var eventos = await _calendarioService.GetByPeriodoAsync(dataInicio, dataFim, eleicaoId, cancellationToken);
            return Ok(eventos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar eventos do periodo");
            return InternalError("Erro ao listar eventos");
        }
    }

    /// <summary>
    /// Cria um novo evento no calendario
    /// </summary>
    /// <param name="dto">Dados do evento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Evento criado</returns>
    [HttpPost]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(CalendarioEventoDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<CalendarioEventoDto>> Create([FromBody] CreateCalendarioEventoDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var evento = await _calendarioService.CreateAsync(dto, userId, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id = evento.Id }, evento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao criar evento");
            return InternalError("Erro ao criar evento");
        }
    }

    /// <summary>
    /// Atualiza um evento existente
    /// </summary>
    /// <param name="id">ID do evento</param>
    /// <param name="dto">Dados atualizados</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Evento atualizado</returns>
    [HttpPut("{id:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(CalendarioEventoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<CalendarioEventoDto>> Update(Guid id, [FromBody] UpdateCalendarioEventoDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var evento = await _calendarioService.UpdateAsync(id, dto, cancellationToken);
            return Ok(evento);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Evento nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar evento {Id}", id);
            return InternalError("Erro ao atualizar evento");
        }
    }

    /// <summary>
    /// Remove um evento
    /// </summary>
    /// <param name="id">ID do evento</param>
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
            await _calendarioService.DeleteAsync(id, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Evento nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao excluir evento {Id}", id);
            return InternalError("Erro ao excluir evento");
        }
    }

    /// <summary>
    /// Inicia um evento
    /// </summary>
    /// <param name="id">ID do evento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Evento iniciado</returns>
    [HttpPost("{id:guid}/iniciar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(CalendarioEventoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<CalendarioEventoDto>> Iniciar(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var evento = await _calendarioService.IniciarAsync(id, cancellationToken);
            return Ok(evento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao iniciar evento {Id}", id);
            return InternalError("Erro ao iniciar evento");
        }
    }

    /// <summary>
    /// Conclui um evento
    /// </summary>
    /// <param name="id">ID do evento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Evento concluido</returns>
    [HttpPost("{id:guid}/concluir")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(CalendarioEventoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<CalendarioEventoDto>> Concluir(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var evento = await _calendarioService.ConcluirAsync(id, cancellationToken);
            return Ok(evento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao concluir evento {Id}", id);
            return InternalError("Erro ao concluir evento");
        }
    }

    /// <summary>
    /// Cancela um evento
    /// </summary>
    /// <param name="id">ID do evento</param>
    /// <param name="request">Dados do cancelamento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Evento cancelado</returns>
    [HttpPost("{id:guid}/cancelar")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(CalendarioEventoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<CalendarioEventoDto>> Cancelar(
        Guid id,
        [FromBody] MotivoRequest request,
        CancellationToken cancellationToken)
    {
        try
        {
            var evento = await _calendarioService.CancelarAsync(id, request.Motivo, cancellationToken);
            return Ok(evento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao cancelar evento {Id}", id);
            return InternalError("Erro ao cancelar evento");
        }
    }

    /// <summary>
    /// Gera calendario padrao para uma eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de eventos criados</returns>
    [HttpPost("gerar/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(IEnumerable<CalendarioEventoDto>), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<IEnumerable<CalendarioEventoDto>>> GerarCalendarioPadrao(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var eventos = await _calendarioService.GerarCalendarioPadraoAsync(eleicaoId, userId, cancellationToken);
            return CreatedAtAction(nameof(GetByEleicao), new { eleicaoId }, eventos);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao gerar calendario para eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao gerar calendario");
        }
    }
}

// DTOs para Calendario
public record CalendarioEventoDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public string Titulo { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public TipoCalendario Tipo { get; init; }
    public StatusCalendario Status { get; init; }
    public DateTime DataInicio { get; init; }
    public DateTime DataFim { get; init; }
    public bool DiaInteiro { get; init; }
    public string? Local { get; init; }
    public string? Observacoes { get; init; }
    public bool Obrigatorio { get; init; }
    public DateTime CreatedAt { get; init; }
    public DateTime? UpdatedAt { get; init; }
}

public record CreateCalendarioEventoDto
{
    public Guid EleicaoId { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public TipoCalendario Tipo { get; init; }
    public DateTime DataInicio { get; init; }
    public DateTime DataFim { get; init; }
    public bool DiaInteiro { get; init; }
    public string? Local { get; init; }
    public string? Observacoes { get; init; }
    public bool Obrigatorio { get; init; }
}

public record UpdateCalendarioEventoDto
{
    public string? Titulo { get; init; }
    public string? Descricao { get; init; }
    public TipoCalendario? Tipo { get; init; }
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
    public bool? DiaInteiro { get; init; }
    public string? Local { get; init; }
    public string? Observacoes { get; init; }
    public bool? Obrigatorio { get; init; }
}

// Interface do servico (a ser implementada)
public interface ICalendarioService
{
    Task<IEnumerable<CalendarioEventoDto>> GetAllAsync(Guid? eleicaoId, TipoCalendario? tipo, CancellationToken cancellationToken = default);
    Task<CalendarioEventoDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioEventoDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioEventoDto>> GetProximosAsync(int dias, Guid? eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioEventoDto>> GetEmAndamentoAsync(Guid? eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioEventoDto>> GetByPeriodoAsync(DateTime dataInicio, DateTime dataFim, Guid? eleicaoId, CancellationToken cancellationToken = default);
    Task<CalendarioEventoDto> CreateAsync(CreateCalendarioEventoDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<CalendarioEventoDto> UpdateAsync(Guid id, UpdateCalendarioEventoDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);
    Task<CalendarioEventoDto> IniciarAsync(Guid id, CancellationToken cancellationToken = default);
    Task<CalendarioEventoDto> ConcluirAsync(Guid id, CancellationToken cancellationToken = default);
    Task<CalendarioEventoDto> CancelarAsync(Guid id, string motivo, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioEventoDto>> GerarCalendarioPadraoAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);
}
