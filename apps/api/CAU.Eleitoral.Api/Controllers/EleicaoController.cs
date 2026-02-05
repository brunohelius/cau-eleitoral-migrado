using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.DTOs;
using CAU.Eleitoral.Application.Interfaces;

namespace CAU.Eleitoral.Api.Controllers;

public class EleicaoController : BaseController
{
    private readonly IEleicaoService _eleicaoService;
    private readonly ILogger<EleicaoController> _logger;

    public EleicaoController(IEleicaoService eleicaoService, ILogger<EleicaoController> logger)
    {
        _eleicaoService = eleicaoService;
        _logger = logger;
    }

    /// <summary>
    /// Lista todas as eleições
    /// </summary>
    [HttpGet]
    [ProducesResponseType(typeof(IEnumerable<EleicaoDto>), StatusCodes.Status200OK)]
    public async Task<IActionResult> GetAll(CancellationToken cancellationToken)
    {
        var eleicoes = await _eleicaoService.GetAllAsync(cancellationToken);
        return Ok(eleicoes);
    }

    /// <summary>
    /// Obtém uma eleição pelo ID
    /// </summary>
    [HttpGet("{id:guid}")]
    [ProducesResponseType(typeof(EleicaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<IActionResult> GetById(Guid id, CancellationToken cancellationToken)
    {
        var eleicao = await _eleicaoService.GetByIdAsync(id, cancellationToken);
        if (eleicao == null)
            return NotFound(new { message = "Eleição não encontrada" });

        return Ok(eleicao);
    }

    /// <summary>
    /// Lista eleições por status
    /// </summary>
    [HttpGet("status/{status:int}")]
    [ProducesResponseType(typeof(IEnumerable<EleicaoDto>), StatusCodes.Status200OK)]
    public async Task<IActionResult> GetByStatus(int status, CancellationToken cancellationToken)
    {
        var eleicoes = await _eleicaoService.GetByStatusAsync(status, cancellationToken);
        return Ok(eleicoes);
    }

    /// <summary>
    /// Lista eleições ativas
    /// </summary>
    [HttpGet("ativas")]
    [ProducesResponseType(typeof(IEnumerable<EleicaoDto>), StatusCodes.Status200OK)]
    public async Task<IActionResult> GetAtivas(CancellationToken cancellationToken)
    {
        var eleicoes = await _eleicaoService.GetAtivasAsync(cancellationToken);
        return Ok(eleicoes);
    }

    /// <summary>
    /// Cria uma nova eleição
    /// </summary>
    [HttpPost]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(EleicaoDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> Create([FromBody] CreateEleicaoDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var eleicao = await _eleicaoService.CreateAsync(dto, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id = eleicao.Id }, eleicao);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao criar eleição");
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Atualiza uma eleição
    /// </summary>
    [HttpPut("{id:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(EleicaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<IActionResult> Update(Guid id, [FromBody] UpdateEleicaoDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var eleicao = await _eleicaoService.UpdateAsync(id, dto, cancellationToken);
            return Ok(eleicao);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar eleição {EleicaoId}", id);
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Exclui uma eleição
    /// </summary>
    [HttpDelete("{id:guid}")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(StatusCodes.Status204NoContent)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<IActionResult> Delete(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            await _eleicaoService.DeleteAsync(id, cancellationToken);
            return NoContent();
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao excluir eleição {EleicaoId}", id);
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Inicia uma eleição
    /// </summary>
    [HttpPost("{id:guid}/iniciar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(EleicaoDto), StatusCodes.Status200OK)]
    public async Task<IActionResult> Iniciar(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var eleicao = await _eleicaoService.IniciarAsync(id, cancellationToken);
            return Ok(eleicao);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao iniciar eleição {EleicaoId}", id);
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Encerra uma eleição
    /// </summary>
    [HttpPost("{id:guid}/encerrar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(EleicaoDto), StatusCodes.Status200OK)]
    public async Task<IActionResult> Encerrar(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var eleicao = await _eleicaoService.EncerrarAsync(id, cancellationToken);
            return Ok(eleicao);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao encerrar eleição {EleicaoId}", id);
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Suspende uma eleição
    /// </summary>
    [HttpPost("{id:guid}/suspender")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(EleicaoDto), StatusCodes.Status200OK)]
    public async Task<IActionResult> Suspender(Guid id, [FromBody] SuspenderEleicaoRequest request, CancellationToken cancellationToken)
    {
        try
        {
            var eleicao = await _eleicaoService.SuspenderAsync(id, request.Motivo, cancellationToken);
            return Ok(eleicao);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao suspender eleição {EleicaoId}", id);
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Cancela uma eleição
    /// </summary>
    [HttpPost("{id:guid}/cancelar")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(EleicaoDto), StatusCodes.Status200OK)]
    public async Task<IActionResult> Cancelar(Guid id, [FromBody] CancelarEleicaoRequest request, CancellationToken cancellationToken)
    {
        try
        {
            var eleicao = await _eleicaoService.CancelarAsync(id, request.Motivo, cancellationToken);
            return Ok(eleicao);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao cancelar eleição {EleicaoId}", id);
            return HandleException(ex);
        }
    }
}

public record SuspenderEleicaoRequest(string Motivo);
public record CancelarEleicaoRequest(string Motivo);
