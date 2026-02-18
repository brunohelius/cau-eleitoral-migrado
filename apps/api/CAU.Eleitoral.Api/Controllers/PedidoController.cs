using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.DTOs.Core;
using CAU.Eleitoral.Application.Interfaces;

namespace CAU.Eleitoral.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class PedidoController : ControllerBase
{
    private readonly IPedidoService _pedidoService;
    private readonly ILogger<PedidoController> _logger;

    public PedidoController(IPedidoService pedidoService, ILogger<PedidoController> logger)
    {
        _pedidoService = pedidoService;
        _logger = logger;
    }

    [HttpGet]
    public async Task<ActionResult<PagedResultDto<PedidoDto>>> GetAll(
        [FromQuery] int pageNumber = 1,
        [FromQuery] int pageSize = 20,
        [FromQuery] Guid? eleicaoId = null,
        [FromQuery] int? status = null,
        CancellationToken cancellationToken = default)
    {
        var result = await _pedidoService.GetAllAsync(pageNumber, pageSize, eleicaoId, status, cancellationToken);
        return Ok(result);
    }

    [HttpGet("{id:guid}")]
    public async Task<ActionResult<PedidoDto>> GetById(Guid id, CancellationToken cancellationToken)
    {
        var pedido = await _pedidoService.GetByIdAsync(id, cancellationToken);
        if (pedido == null) return NotFound();
        return Ok(pedido);
    }

    [HttpGet("meus-pedidos")]
    public async Task<ActionResult<IEnumerable<PedidoDto>>> GetMyPedidos(CancellationToken cancellationToken)
    {
        var pedidos = await _pedidoService.GetBySolicitanteAsync(cancellationToken);
        return Ok(pedidos);
    }

    [HttpPost]
    [AllowAnonymous]
    public async Task<ActionResult<PedidoDto>> Create([FromBody] CreatePedidoDto dto, CancellationToken cancellationToken)
    {
        var pedido = await _pedidoService.CreateAsync(dto, cancellationToken);
        return CreatedAtAction(nameof(GetById), new { id = pedido.Id }, pedido);
    }

    [HttpPut("{id:guid}/responder")]
    public async Task<ActionResult<PedidoDto>> Respond(Guid id, [FromBody] RespondPedidoDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var pedido = await _pedidoService.RespondAsync(id, dto, cancellationToken);
            return Ok(pedido);
        }
        catch (KeyNotFoundException)
        {
            return NotFound();
        }
    }

    [HttpPut("{id:guid}/cancelar")]
    public async Task<ActionResult<PedidoDto>> Cancel(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var pedido = await _pedidoService.CancelAsync(id, cancellationToken);
            return Ok(pedido);
        }
        catch (KeyNotFoundException)
        {
            return NotFound();
        }
    }

    [HttpGet("estatisticas")]
    public async Task<ActionResult<PedidoEstatisticasDto>> GetEstatisticas(CancellationToken cancellationToken)
    {
        var estatisticas = await _pedidoService.GetEstatisticasAsync(cancellationToken);
        return Ok(estatisticas);
    }
}