using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.DTOs.Comissoes;
using CAU.Eleitoral.Application.Interfaces;

namespace CAU.Eleitoral.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class ComissaoController : ControllerBase
{
    private readonly IComissaoService _comissaoService;
    private readonly ILogger<ComissaoController> _logger;

    public ComissaoController(IComissaoService comissaoService, ILogger<ComissaoController> logger)
    {
        _comissaoService = comissaoService;
        _logger = logger;
    }

    [HttpGet]
    public async Task<ActionResult<IEnumerable<ComissaoEleitoralDto>>> GetAll([FromQuery] ComissaoEleitoralFilter filter, CancellationToken cancellationToken)
    {
        var result = await _comissaoService.GetAllAsync(filter, cancellationToken);
        return Ok(result);
    }

    [HttpGet("{id:guid}")]
    public async Task<ActionResult<ComissaoEleitoralDto>> GetById(Guid id, CancellationToken cancellationToken)
    {
        var comissao = await _comissaoService.GetByIdAsync(id, cancellationToken);
        if (comissao == null) return NotFound();
        return Ok(comissao);
    }

    [HttpGet("calendario/{calendarioId:guid}")]
    public async Task<ActionResult<IEnumerable<ComissaoEleitoralDto>>> GetByCalendario(Guid calendarioId, CancellationToken cancellationToken)
    {
        var comissoes = await _comissaoService.GetByCalendarioAsync(calendarioId, cancellationToken);
        return Ok(comissoes);
    }

    [HttpGet("ativas")]
    public async Task<ActionResult<IEnumerable<ComissaoEleitoralDto>>> GetAtivas(CancellationToken cancellationToken)
    {
        var comissoes = await _comissaoService.GetAtivasAsync(cancellationToken);
        return Ok(comissoes);
    }

    [HttpPost]
    public async Task<ActionResult<ComissaoEleitoralDto>> Create([FromBody] CreateComissaoEleitoralDto dto, CancellationToken cancellationToken)
    {
        var comissao = await _comissaoService.CreateAsync(dto, cancellationToken);
        return CreatedAtAction(nameof(GetById), new { id = comissao.Id }, comissao);
    }

    [HttpPut("{id:guid}")]
    public async Task<ActionResult<ComissaoEleitoralDto>> Update(Guid id, [FromBody] UpdateComissaoEleitoralDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var comissao = await _comissaoService.UpdateAsync(id, dto, cancellationToken);
            return Ok(comissao);
        }
        catch (KeyNotFoundException)
        {
            return NotFound();
        }
    }

    [HttpDelete("{id:guid}")]
    public async Task<ActionResult> Delete(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            await _comissaoService.DeleteAsync(id, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound();
        }
    }

    [HttpPost("{id:guid}/ativar")]
    public async Task<ActionResult<ComissaoEleitoralDto>> Ativar(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var comissao = await _comissaoService.AtivarAsync(id, cancellationToken);
            return Ok(comissao);
        }
        catch (KeyNotFoundException)
        {
            return NotFound();
        }
    }

    [HttpPost("{id:guid}/encerrar")]
    public async Task<ActionResult<ComissaoEleitoralDto>> Encerrar(Guid id, [FromBody] string motivo, CancellationToken cancellationToken)
    {
        try
        {
            var comissao = await _comissaoService.EncerrarAsync(id, motivo, cancellationToken);
            return Ok(comissao);
        }
        catch (KeyNotFoundException)
        {
            return NotFound();
        }
    }

    // MembroComissao Endpoints
    [HttpGet("membros")]
    public async Task<ActionResult<PagedResult<MembroComissaoDto>>> GetAllMembros([FromQuery] MembroComissaoFilter filter, CancellationToken cancellationToken)
    {
        var result = await _comissaoService.GetAllMembrosAsync(filter, cancellationToken);
        return Ok(result);
    }

    [HttpGet("membros/{id:guid}")]
    public async Task<ActionResult<MembroComissaoDto>> GetMembroById(Guid id, CancellationToken cancellationToken)
    {
        var membro = await _comissaoService.GetMembroByIdAsync(id, cancellationToken);
        if (membro == null) return NotFound();
        return Ok(membro);
    }

    [HttpGet("{comissaoId:guid}/membros")]
    public async Task<ActionResult<IEnumerable<MembroComissaoDto>>> GetMembrosByComissao(Guid comissaoId, CancellationToken cancellationToken)
    {
        var membros = await _comissaoService.GetMembrosByComissaoAsync(comissaoId, cancellationToken);
        return Ok(membros);
    }

    [HttpPost("membros")]
    public async Task<ActionResult<MembroComissaoDto>> CreateMembro([FromBody] CreateMembroComissaoDto dto, CancellationToken cancellationToken)
    {
        var membro = await _comissaoService.CreateMembroAsync(dto, cancellationToken);
        return CreatedAtAction(nameof(GetMembroById), new { id = membro.Id }, membro);
    }

    [HttpPut("membros/{id:guid}")]
    public async Task<ActionResult<MembroComissaoDto>> UpdateMembro(Guid id, [FromBody] UpdateMembroComissaoDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var membro = await _comissaoService.UpdateMembroAsync(id, dto, cancellationToken);
            return Ok(membro);
        }
        catch (KeyNotFoundException)
        {
            return NotFound();
        }
    }

    [HttpDelete("membros/{id:guid}")]
    public async Task<ActionResult> DeleteMembro(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            await _comissaoService.DeleteMembroAsync(id, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound();
        }
    }

    [HttpPost("membros/{id:guid}/ativar")]
    public async Task<ActionResult<MembroComissaoDto>> AtivarMembro(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var membro = await _comissaoService.AtivarMembroAsync(id, cancellationToken);
            return Ok(membro);
        }
        catch (KeyNotFoundException)
        {
            return NotFound();
        }
    }

    [HttpPost("membros/{id:guid}/inativar")]
    public async Task<ActionResult<MembroComissaoDto>> InativarMembro(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var membro = await _comissaoService.InativarMembroAsync(id, cancellationToken);
            return Ok(membro);
        }
        catch (KeyNotFoundException)
        {
            return NotFound();
        }
    }

    [HttpPost("membros/{id:guid}/declaracao")]
    public async Task<ActionResult<MembroComissaoDto>> ResponderDeclaracao(Guid id, [FromBody] bool resposta, CancellationToken cancellationToken)
    {
        try
        {
            var membro = await _comissaoService.ResponderDeclaracaoAsync(id, resposta, cancellationToken);
            return Ok(membro);
        }
        catch (KeyNotFoundException)
        {
            return NotFound();
        }
    }

    // Situacao
    [HttpGet("membros/{membroId:guid}/situacoes")]
    public async Task<ActionResult<IEnumerable<MembroComissaoSituacaoDto>>> GetHistoricoSituacoes(Guid membroId, CancellationToken cancellationToken)
    {
        var situacoes = await _comissaoService.GetHistoricoSituacoesAsync(membroId, cancellationToken);
        return Ok(situacoes);
    }

    [HttpPost("membros/situacoes")]
    public async Task<ActionResult<MembroComissaoSituacaoDto>> AddSituacao([FromBody] CreateMembroComissaoSituacaoDto dto, CancellationToken cancellationToken)
    {
        var situacao = await _comissaoService.AddSituacaoAsync(dto, cancellationToken);
        return Ok(situacao);
    }

    // Validacoes
    [HttpGet("validar-membro-comissao")]
    [AllowAnonymous]
    public async Task<ActionResult<bool>> ValidarMembroComissaoEleicaoVigente(CancellationToken cancellationToken)
    {
        var validacao = await _comissaoService.ValidarMembroComissaoEleicaoVigenteAsync(cancellationToken);
        return Ok(validacao);
    }
}