using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.DTOs.Denuncias;
using CAU.Eleitoral.Application.Interfaces;

namespace CAU.Eleitoral.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class JulgamentoAdmissibilidadeController : ControllerBase
{
    private readonly IJulgamentoAdmissibilidadeService _service;
    private readonly ILogger<JulgamentoAdmissibilidadeController> _logger;

    public JulgamentoAdmissibilidadeController(IJulgamentoAdmissibilidadeService service, ILogger<JulgamentoAdmissibilidadeController> logger)
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
    public async Task<ActionResult> Create([FromBody] CreateJulgamentoAdmissibilidadeDto dto, CancellationToken cancellationToken)
    {
        var entity = await _service.CreateAsync(dto, cancellationToken);
        return CreatedAtAction(nameof(GetById), new { id = entity.Id }, entity);
    }

    [HttpPut("{id:guid}")]
    public async Task<ActionResult> Update(Guid id, [FromBody] UpdateJulgamentoAdmissibilidadeDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var entity = await _service.UpdateAsync(id, dto, cancellationToken);
            return Ok(entity);
        }
        catch (KeyNotFoundException) { return NotFound(); }
    }

    [HttpPost("{id:guid}/recurso")]
    public async Task<ActionResult> InterporRecurso(Guid id, [FromBody] CreateRecursoDto dto, CancellationToken cancellationToken)
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
}

public class CreateJulgamentoAdmissibilidadeDto
{
    public Guid DenunciaId { get; set; }
    public DateTime DataJulgamento { get; set; }
    public int Decisao { get; set; }
    public string? Motivacao { get; set; }
    public string? Parecer { get; set; }
    public Guid? ComissaoJulgadoraId { get; set; }
    public Guid? MembroComissaoId { get; set; }
}

public class UpdateJulgamentoAdmissibilidadeDto
{
    public DateTime? DataJulgamento { get; set; }
    public int? Decisao { get; set; }
    public string? Motivacao { get; set; }
    public string? Parecer { get; set; }
    public bool? RecursoDisponivel { get; set; }
}

public class CreateRecursoDto
{
    public Guid RecorrenteId { get; set; }
    public string? RecorrenteNome { get; set; }
    public string? TextoRecurso { get; set; }
}

public interface IJulgamentoAdmissibilidadeService
{
    Task<object> GetAllAsync(int pageNumber, int pageSize, CancellationToken cancellationToken = default);
    Task<object?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<object?> GetByDenunciaAsync(Guid denunciaId, CancellationToken cancellationToken = default);
    Task<object> CreateAsync(CreateJulgamentoAdmissibilidadeDto dto, CancellationToken cancellationToken = default);
    Task<object> UpdateAsync(Guid id, UpdateJulgamentoAdmissibilidadeDto dto, CancellationToken cancellationToken = default);
    Task<object> InterporRecursoAsync(Guid id, CreateRecursoDto dto, CancellationToken cancellationToken = default);
    Task<object> GetRecursosAsync(Guid julgamentoId, CancellationToken cancellationToken = default);
}