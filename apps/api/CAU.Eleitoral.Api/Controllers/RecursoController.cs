using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace CAU.Eleitoral.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class RecursoController : ControllerBase
{
    [HttpGet]
    public async Task<ActionResult> GetAll([FromQuery] int pageNumber = 1, [FromQuery] int pageSize = 20, [FromQuery] int? tipo = null)
    {
        return Ok(new { items = new object[] { }, totalCount = 0 });
    }

    [HttpGet("{id:guid}")]
    public async Task<ActionResult> GetById(Guid id)
    {
        return Ok(new { id, tipoRecurso = tipo, status = "not_implemented" });
    }

    [HttpPost]
    public async Task<ActionResult> Create([FromBody] object dto)
    {
        return Ok(new { id = Guid.NewGuid(), status = "created" });
    }

    [HttpPut("{id:guid}")]
    public async Task<ActionResult> Update(Guid id, [FromBody] object dto)
    {
        return Ok(new { status = "updated" });
    }

    [HttpGet("tipo/{tipo:int}")]
    public async Task<ActionResult> GetByTipo(int tipo, [FromQuery] Guid referenciaId)
    {
        return Ok(new { items = new object[] { } });
    }

    [HttpPost("{id:guid}/contra-razoes")]
    public async Task<ActionResult> EnviarContraRazoes(Guid id, [FromBody] object dto)
    {
        return Ok(new { status = "sent" });
    }
}