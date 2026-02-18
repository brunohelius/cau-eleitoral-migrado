using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace CAU.Eleitoral.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class CabecalhoEmailController : ControllerBase
{
    [HttpGet]
    public async Task<ActionResult> GetAll([FromQuery] int pageNumber = 1, [FromQuery] int pageSize = 20)
    {
        return Ok(new { items = new object[] { }, totalCount = 0, pageNumber, pageSize });
    }

    [HttpGet("{id:guid}")]
    public async Task<ActionResult> GetById(Guid id)
    {
        return Ok(new { id, nome = "Padrão", conteudoHtml = "<div>...</div>" });
    }

    [HttpPost]
    public async Task<ActionResult> Create([FromBody] CreateCabecalhoEmailDto dto)
    {
        return Ok(new { id = Guid.NewGuid(), nome = dto.Nome, conteudoHtml = dto.ConteudoHtml, createdAt = DateTime.UtcNow });
    }

    [HttpPut("{id:guid}")]
    public async Task<ActionResult> Update(Guid id, [FromBody] UpdateCabecalhoEmailDto dto)
    {
        return Ok(new { id, nome = dto.Nome ?? "Nome", conteudoHtml = dto.ConteudoHtml ?? "<div>...</div>" });
    }

    [HttpDelete("{id:guid}")]
    public async Task<ActionResult> Delete(Guid id)
    {
        return NoContent();
    }
}

public class CreateCabecalhoEmailDto
{
    public string Nome { get; set; } = string.Empty;
    public string ConteudoHtml { get; set; } = string.Empty;
    public int? Tipo { get; set; }
}

public class UpdateCabecalhoEmailDto
{
    public string? Nome { get; set; }
    public string? ConteudoHtml { get; set; }
}