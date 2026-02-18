using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace CAU.Eleitoral.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class TermoPosseController : ControllerBase
{
    [HttpGet]
    public async Task<ActionResult> GetAll([FromQuery] int pageNumber = 1, [FromQuery] int pageSize = 20)
    {
        return Ok(new { items = new object[] { }, totalCount = 0, pageNumber, pageSize });
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

    [HttpPut("{id:guid}")]
    public async Task<ActionResult> Update(Guid id, [FromBody] object dto)
    {
        return Ok(new { status = "updated" });
    }

    [HttpGet("eleicao/{eleicaoId:guid}")]
    public async Task<ActionResult> GetByEleicao(Guid eleicaoId)
    {
        return Ok(new { items = new object[] { } });
    }

    [HttpGet("membro/{membroId:guid}")]
    public async Task<ActionResult> GetByMembro(Guid membroId)
    {
        return Ok(new { });
    }

    [HttpPost("{id:guid}/gerar-pdf")]
    public async Task<ActionResult> GerarPdf(Guid id)
    {
        return Ok(new { url = $"/documentos/termo-posse-{id}.pdf" });
    }
}