using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace CAU.Eleitoral.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class DiplomaEleitoralController : ControllerBase
{
    [HttpGet]
    public async Task<ActionResult> GetAll([FromQuery] int pageNumber = 1, [FromQuery] int pageSize = 20, [FromQuery] Guid? eleicaoId = null)
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

    [HttpGet("eleicao/{eleicaoId:guid}")]
    public async Task<ActionResult> GetByEleicao(Guid eleicaoId)
    {
        return Ok(new { items = new object[] { } });
    }

    [HttpGet("chapa/{chapaId:guid}")]
    public async Task<ActionResult> GetByChapa(Guid chapaId)
    {
        return Ok(new { });
    }

    [HttpPost("{id:guid}/gerar-pdf")]
    public async Task<ActionResult> GerarPdf(Guid id)
    {
        return Ok(new { url = $"/documentos/diploma-{id}.pdf" });
    }

    [HttpPost("gerar-todos/{eleicaoId:guid}")]
    public async Task<ActionResult> GerarTodos(Guid eleicaoId)
    {
        return Ok(new { status = "processing", message = "Diplomas sendo gerados" });
    }
}