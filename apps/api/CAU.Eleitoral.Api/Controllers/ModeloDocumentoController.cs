using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace CAU.Eleitoral.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class ModeloDocumentoController : ControllerBase
{
    [HttpGet]
    public async Task<ActionResult> GetAll([FromQuery] int pageNumber = 1, [FromQuery] int pageSize = 20, [FromQuery] string? tipo = null)
    {
        return Ok(new { items = new object[] { }, totalCount = 0 });
    }

    [HttpGet("{id:guid}")]
    public async Task<ActionResult> GetById(Guid id)
    {
        return Ok(new { id, nome = "Modelo", tipo = "pdf" });
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

    [HttpDelete("{id:guid}")]
    public async Task<ActionResult> Delete(Guid id)
    {
        return NoContent();
    }

    [HttpGet("tipo/{tipo}")]
    public async Task<ActionResult> GetByTipo(string tipo)
    {
        return Ok(new { items = new object[] { } });
    }
}