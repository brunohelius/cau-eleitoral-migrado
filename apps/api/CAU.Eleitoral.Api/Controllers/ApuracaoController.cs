using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.DTOs.Apuracao;
using CAU.Eleitoral.Application.Interfaces;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller for vote tallying and election results management
/// </summary>
[Authorize]
public class ApuracaoController : BaseController
{
    private readonly IApuracaoService _apuracaoService;
    private readonly ILogger<ApuracaoController> _logger;

    public ApuracaoController(IApuracaoService apuracaoService, ILogger<ApuracaoController> logger)
    {
        _apuracaoService = apuracaoService;
        _logger = logger;
    }

    /// <summary>
    /// Get election results
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Election results</returns>
    [HttpGet("{eleicaoId:guid}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(ResultadoApuracaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ResultadoApuracaoDto>> GetResultado(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var resultado = await _apuracaoService.GetResultadoAsync(eleicaoId, cancellationToken);
            if (resultado == null)
                return NotFound(new { message = "Resultado nao encontrado" });

            return Ok(resultado);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter resultado da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao obter resultado");
        }
    }

    /// <summary>
    /// Get partial/real-time results
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Partial results</returns>
    [HttpGet("{eleicaoId:guid}/parcial")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ResultadoParcialDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<ResultadoParcialDto>> GetResultadoParcial(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var resultado = await _apuracaoService.GetResultadoParcialAsync(eleicaoId, cancellationToken);
            return Ok(resultado);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter resultado parcial da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao obter resultado parcial");
        }
    }

    /// <summary>
    /// Get final official results
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Final results</returns>
    [HttpGet("{eleicaoId:guid}/final")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(ResultadoFinalDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ResultadoFinalDto>> GetResultadoFinal(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var resultado = await _apuracaoService.GetResultadoFinalAsync(eleicaoId, cancellationToken);
            return Ok(resultado);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Resultado final nao encontrado" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter resultado final da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao obter resultado final");
        }
    }

    /// <summary>
    /// Start the vote tallying process
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Tallying status</returns>
    [HttpPost("{eleicaoId:guid}/iniciar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(StatusApuracaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<StatusApuracaoDto>> IniciarApuracao(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var status = await _apuracaoService.IniciarAsync(eleicaoId, userId, cancellationToken);
            return Ok(status);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao iniciar apuracao da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao iniciar apuracao");
        }
    }

    /// <summary>
    /// Pause the vote tallying process
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="request">Pause reason</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Tallying status</returns>
    [HttpPost("{eleicaoId:guid}/pausar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(StatusApuracaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<StatusApuracaoDto>> PausarApuracao(
        Guid eleicaoId,
        [FromBody] MotivoRequest request,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var status = await _apuracaoService.PausarAsync(eleicaoId, request.Motivo, userId, cancellationToken);
            return Ok(status);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao pausar apuracao da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao pausar apuracao");
        }
    }

    /// <summary>
    /// Resume the vote tallying process
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Tallying status</returns>
    [HttpPost("{eleicaoId:guid}/retomar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(StatusApuracaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<StatusApuracaoDto>> RetomarApuracao(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var status = await _apuracaoService.RetomarAsync(eleicaoId, userId, cancellationToken);
            return Ok(status);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao retomar apuracao da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao retomar apuracao");
        }
    }

    /// <summary>
    /// Finalize the vote tallying process
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Final results</returns>
    [HttpPost("{eleicaoId:guid}/finalizar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ResultadoApuracaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ResultadoApuracaoDto>> FinalizarApuracao(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var resultado = await _apuracaoService.FinalizarAsync(eleicaoId, userId, cancellationToken);
            return Ok(resultado);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao finalizar apuracao da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao finalizar apuracao");
        }
    }

    /// <summary>
    /// Homologate (officially validate) the results
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Homologated results</returns>
    [HttpPost("{eleicaoId:guid}/homologar")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(ResultadoApuracaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ResultadoApuracaoDto>> HomologarResultado(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var resultado = await _apuracaoService.HomologarAsync(eleicaoId, userId, cancellationToken);
            return Ok(resultado);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao homologar resultado da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao homologar resultado");
        }
    }

    /// <summary>
    /// Publish the results officially
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Published results</returns>
    [HttpPost("{eleicaoId:guid}/publicar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ResultadoApuracaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ResultadoApuracaoDto>> PublicarResultado(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var resultado = await _apuracaoService.PublicarAsync(eleicaoId, userId, cancellationToken);
            return Ok(resultado);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao publicar resultado da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao publicar resultado");
        }
    }

    /// <summary>
    /// Get tallying status
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Tallying status</returns>
    [HttpGet("{eleicaoId:guid}/status")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(StatusApuracaoDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<StatusApuracaoDto>> GetStatus(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var status = await _apuracaoService.GetStatusAsync(eleicaoId, cancellationToken);
            return Ok(status);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter status da apuracao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao obter status");
        }
    }

    /// <summary>
    /// Get vote tallying minutes (ata)
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Ata document</returns>
    [HttpGet("{eleicaoId:guid}/ata")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(AtaApuracaoDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<AtaApuracaoDto>> GetAta(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var ata = await _apuracaoService.GetAtaAsync(eleicaoId, cancellationToken);
            return Ok(ata);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Ata nao encontrada" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao gerar ata de apuracao da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao gerar ata");
        }
    }

    /// <summary>
    /// Get ballot box bulletin
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Boletim de urna</returns>
    [HttpGet("{eleicaoId:guid}/boletim")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(BoletimUrnaDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<BoletimUrnaDto>> GetBoletimUrna(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var boletim = await _apuracaoService.GetBoletimUrnaAsync(eleicaoId, cancellationToken);
            return Ok(boletim);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Boletim nao encontrado" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao gerar boletim de urna da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao gerar boletim");
        }
    }

    /// <summary>
    /// Get list of elected officials
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>List of elected officials</returns>
    [HttpGet("{eleicaoId:guid}/eleitos")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<EleitoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<EleitoDto>>> GetEleitos(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var eleitos = await _apuracaoService.GetEleitosAsync(eleicaoId, cancellationToken);
            return Ok(eleitos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar eleitos da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao listar eleitos");
        }
    }

    /// <summary>
    /// Reprocess the vote tallying (in case of errors)
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Tallying status</returns>
    [HttpPost("{eleicaoId:guid}/reprocessar")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(StatusApuracaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<StatusApuracaoDto>> Reprocessar(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var status = await _apuracaoService.ReprocessarAsync(eleicaoId, userId, cancellationToken);
            return Ok(status);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao reprocessar apuracao da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao reprocessar apuracao");
        }
    }

    /// <summary>
    /// Determine the winning chapa
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Winning chapa details</returns>
    [HttpGet("{eleicaoId:guid}/vencedor")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(ChapaVencedoraDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ChapaVencedoraDto>> GetVencedor(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var vencedor = await _apuracaoService.DeterminarVencedorAsync(eleicaoId, cancellationToken);
            return Ok(vencedor);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Vencedor nao determinado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao determinar vencedor da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao determinar vencedor");
        }
    }

    /// <summary>
    /// Get voting statistics
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Voting statistics</returns>
    [HttpGet("{eleicaoId:guid}/estatisticas")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(EstatisticasVotacaoDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<EstatisticasVotacaoDto>> GetEstatisticas(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var estatisticas = await _apuracaoService.GetEstatisticasAsync(eleicaoId, cancellationToken);
            return Ok(estatisticas);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter estatisticas da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao obter estatisticas");
        }
    }

    /// <summary>
    /// Get votes per region/UF
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Votes per region</returns>
    [HttpGet("{eleicaoId:guid}/votos-por-regiao")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(IEnumerable<VotosPorRegiaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<VotosPorRegiaoDto>>> GetVotosPorRegiao(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var votos = await _apuracaoService.GetVotosPorRegiaoAsync(eleicaoId, cancellationToken);
            return Ok(votos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter votos por regiao da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao obter votos por regiao");
        }
    }

    /// <summary>
    /// Get votes per chapa
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Votes per chapa</returns>
    [HttpGet("{eleicaoId:guid}/votos-por-chapa")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<VotosPorChapaDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<VotosPorChapaDto>>> GetVotosPorChapa(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var votos = await _apuracaoService.GetVotosPorChapaAsync(eleicaoId, cancellationToken);
            return Ok(votos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter votos por chapa da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao obter votos por chapa");
        }
    }

    /// <summary>
    /// Execute full vote tallying (apurar votos)
    /// </summary>
    /// <param name="eleicaoId">Election ID</param>
    /// <param name="cancellationToken">Cancellation token</param>
    /// <returns>Tallying results</returns>
    [HttpPost("{eleicaoId:guid}/apurar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ResultadoApuracaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ResultadoApuracaoDto>> ApurarVotos(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var resultado = await _apuracaoService.ApurarVotosAsync(eleicaoId, cancellationToken);
            return Ok(resultado);
        }
        catch (KeyNotFoundException ex)
        {
            return NotFound(new { message = ex.Message });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao apurar votos da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao apurar votos");
        }
    }
}
