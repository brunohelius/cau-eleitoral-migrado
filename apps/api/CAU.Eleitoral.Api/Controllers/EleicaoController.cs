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
    /// Atualiza uma eleicao existente
    /// </summary>
    /// <remarks>
    /// Regras de validacao:
    /// - Eleicoes finalizadas ou canceladas nao podem ser editadas
    /// - Eleicoes com votos registrados nao podem ter datas de inicio ou modo de votacao alterados
    /// - As datas devem ser consistentes (fim maior que inicio)
    /// </remarks>
    [HttpPut("{id:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(EleicaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status409Conflict)]
    public async Task<IActionResult> Update(Guid id, [FromBody] UpdateEleicaoDto dto, CancellationToken cancellationToken)
    {
        if (dto == null)
            return BadRequest(new { message = "Dados de atualizacao nao fornecidos" });

        try
        {
            var eleicao = await _eleicaoService.UpdateAsync(id, dto, cancellationToken);
            return Ok(eleicao);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar eleicao {EleicaoId}", id);
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Exclui uma eleicao (soft delete)
    /// </summary>
    /// <remarks>
    /// Regras de validacao:
    /// - Eleicoes em andamento ou em apuracao nao podem ser excluidas (cancele primeiro)
    /// - Eleicoes em periodo de votacao nao podem ser excluidas
    /// - Eleicoes com votos registrados nao podem ser excluidas (utilize cancelar)
    /// - Chapas associadas serao soft-deleted automaticamente
    /// </remarks>
    [HttpDelete("{id:guid}")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(StatusCodes.Status204NoContent)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status409Conflict)]
    public async Task<IActionResult> Delete(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            await _eleicaoService.DeleteAsync(id, cancellationToken);
            return NoContent();
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao excluir eleicao {EleicaoId}", id);
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
    /// Cancela uma eleicao
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
            _logger.LogError(ex, "Erro ao cancelar eleicao {EleicaoId}", id);
            return HandleException(ex);
        }
    }

    /// <summary>
    /// Verifica se uma eleicao pode ser excluida
    /// </summary>
    /// <remarks>
    /// Retorna informacoes de validacao incluindo:
    /// - Se a exclusao e permitida
    /// - Mensagem de erro (se nao for permitida)
    /// - Warnings (se houver consideracoes)
    /// - Quantidade de votos e chapas associadas
    /// </remarks>
    [HttpGet("{id:guid}/can-delete")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(EleicaoValidationResult), StatusCodes.Status200OK)]
    public async Task<IActionResult> CanDelete(Guid id, CancellationToken cancellationToken)
    {
        var result = await _eleicaoService.CanDeleteAsync(id, cancellationToken);
        return Ok(result);
    }

    /// <summary>
    /// Verifica se uma eleicao pode ser editada
    /// </summary>
    /// <remarks>
    /// Retorna informacoes de validacao incluindo:
    /// - Se a edicao e permitida
    /// - Mensagem de erro (se nao for permitida)
    /// - Warnings sobre restricoes (ex: campos que nao podem ser alterados)
    /// - Quantidade de votos e chapas associadas
    /// </remarks>
    [HttpGet("{id:guid}/can-edit")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(EleicaoValidationResult), StatusCodes.Status200OK)]
    public async Task<IActionResult> CanEdit(Guid id, CancellationToken cancellationToken)
    {
        var result = await _eleicaoService.CanEditAsync(id, cancellationToken);
        return Ok(result);
    }
}

public record SuspenderEleicaoRequest(string Motivo);
public record CancelarEleicaoRequest(string Motivo);
