using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace CAU.Eleitoral.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class SubstituicaoChapaController : ControllerBase
{
    [HttpGet]
    public async Task<ActionResult> GetAll([FromQuery] int pageNumber = 1, [FromQuery] int pageSize = 20)
    {
        return Ok(new { items = new object[] { }, totalCount = 0 });
    }

    [HttpGet("{id:guid}")]
    public async Task<ActionResult> GetById(Guid id)
    {
        return Ok(new { id, status = "not_implemented" });
    }

    [HttpPost]
    public async Task<ActionResult> Create([FromBody] object dto)
    {
        return Ok(new { status = "created" });
    }

    [HttpPut("{id:guid}/aprovar")]
    public async Task<ActionResult> Aprovar(Guid id)
    {
        return Ok(new { status = "approved" });
    }

    [HttpPut("{id:guid}/rejeitar")]
    public async Task<ActionResult> Rejeitar(Guid id, [FromBody] object motivo)
    {
        return Ok(new { status = "rejected" });
    }

    [HttpGet("chapa/{chapaId:guid}")]
    public async Task<ActionResult> GetByChapa(Guid chapaId)
    {
        return Ok(new { items = new object[] { } });
    }
}