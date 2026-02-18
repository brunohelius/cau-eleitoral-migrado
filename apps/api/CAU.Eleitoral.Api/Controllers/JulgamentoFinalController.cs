using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.DTOs.Denuncias;
using CAU.Eleitoral.Application.Interfaces;

namespace CAU.Eleitoral.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class JulgamentoFinalController : ControllerBase
{
    private readonly IJulgamentoFinalService _service;
    private readonly ILogger<JulgamentoFinalController> _logger;

    public JulgamentoFinalController(IJulgamentoFinalService service, ILogger<JulgamentoFinalController> logger)
    {
        _service = service;
        _logger = logger;
    }

    [HttpGet]
    public async Task<ActionResult> GetAll([FromQuery] int pageNumber = 1, [FromQuery] int pageSize = 20, CancellationToken cancellationToken = default)
    {
        var result = await _service.GetAllAsync(pageNumber, pageSize, cancellationToken);
        return Ok(result);
    }

    [HttpGet("{id:guid}")]
    public async Task<ActionResult> GetById(Guid id, CancellationToken cancellationToken = default)
    {
        var entity = await _service.GetByIdAsync(id, cancellationToken);
        if (entity == null) return NotFound();
        return Ok(entity);
    }

    [HttpGet("denuncia/{denunciaId:guid}")]
    public async Task<ActionResult> GetByDenuncia(Guid denunciaId, CancellationToken cancellationToken = default)
    {
        var entity = await _service.GetByDenunciaAsync(denunciaId, cancellationToken);
        return Ok(entity);
    }

    [HttpPost]
    public async Task<ActionResult> Create([FromBody] CreateJulgamentoFinalDto dto, CancellationToken cancellationToken)
    {
        var entity = await _service.CreateAsync(dto, cancellationToken);
        return CreatedAtAction(nameof(GetById), new { id = entity.Id }, entity);
    }

    [HttpPut("{id:guid}")]
    public async Task<ActionResult> Update(Guid id, [FromBody] UpdateJulgamentoFinalDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var entity = await _service.UpdateAsync(id, dto, cancellationToken);
            return Ok(entity);
        }
        catch (KeyNotFoundException) { return NotFound(); }
    }

    [HttpPost("{id:guid}/recurso")]
    public async Task<ActionResult> InterporRecurso(Guid id, [FromBody] CreateRecursoJulgamentoFinalDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var recurso = await _service.InterporRecursoAsync(id, dto, cancellationToken);
            return Ok(recurso);
        }
        catch (KeyNotFoundException) { return NotFound(); }
    }

    [HttpGet("{id:guid}/recursos")]
    public async Task<ActionResult> GetRecursos(Guid id, CancellationToken cancellationToken = default)
    {
        var recursos = await _service.GetRecursosAsync(id, cancellationToken);
        return Ok(recursos);
    }

    [HttpPost("{id:guid}/votacao")]
    public async Task<ActionResult> RegistrarVotacao(Guid id, [FromBody] RegistrarVotacaoDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var result = await _service.RegistrarVotacaoAsync(id, dto, cancellationToken);
            return Ok(result);
        }
        catch (KeyNotFoundException) { return NotFound(); }
    }
}

public class CreateJulgamentoFinalDto
{
    public Guid DenunciaId { get; set; }
    public DateTime DataJulgamento { get; set; }
    public int Decisao { get; set; }
    public string? Motivacao { get; set; }
    public string? Sentenca { get; set; }
    public Guid? ComissaoJulgadoraId { get; set; }
    public bool JulgamentoUnanime { get; set; }
}

public class UpdateJulgamentoFinalDto
{
    public DateTime? DataJulgamento { get; set; }
    public int? Decisao { get; set; }
    public string? Motivacao { get; set; }
    public string? Sentenca { get; set; }
    public bool? JulgamentoUnanime { get; set; }
    public int? VotosFavoraveis { get; set; }
    public int? VotosContrarios { get; set; }
}

public class CreateRecursoJulgamentoFinalDto
{
    public Guid RecorrenteId { get; set; }
    public string? RecorrenteNome { get; set; }
    public string? TextoRecurso { get; set; }
}

public class RegistrarVotacaoDto
{
    public int VotosFavoraveis { get; set; }
    public int VotosContrarios { get; set; }
    public bool JulgamentoUnanime { get; set; }
}

public interface IJulgamentoFinalService
{
    Task<object> GetAllAsync(int pageNumber, int pageSize, CancellationToken cancellationToken = default);
    Task<object?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<object?> GetByDenunciaAsync(Guid denunciaId, CancellationToken cancellationToken = default);
    Task<object> CreateAsync(CreateJulgamentoFinalDto dto, CancellationToken cancellationToken = default);
    Task<object> UpdateAsync(Guid id, UpdateJulgamentoFinalDto dto, CancellationToken cancellationToken = default);
    Task<object> InterporRecursoAsync(Guid id, CreateRecursoJulgamentoFinalDto dto, CancellationToken cancellationToken = default);
    Task<object> GetRecursosAsync(Guid julgamentoId, CancellationToken cancellationToken = default);
    Task<object> RegistrarVotacaoAsync(Guid id, RegistrarVotacaoDto dto, CancellationToken cancellationToken = default);
}