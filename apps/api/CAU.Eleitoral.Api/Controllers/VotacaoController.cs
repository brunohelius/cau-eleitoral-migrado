using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Application.DTOs.Votacao;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para gerenciamento de votacao eleitoral
/// </summary>
[Authorize]
public class VotacaoController : BaseController
{
    private readonly IVotacaoService _votacaoService;
    private readonly ILogger<VotacaoController> _logger;

    public VotacaoController(IVotacaoService votacaoService, ILogger<VotacaoController> logger)
    {
        _votacaoService = votacaoService;
        _logger = logger;
    }

    /// <summary>
    /// Verifica se o eleitor pode votar na eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Status de elegibilidade</returns>
    [HttpGet("elegibilidade/{eleicaoId:guid}")]
    [ProducesResponseType(typeof(ElegibilidadeVotoDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<ElegibilidadeVotoDto>> VerificarElegibilidade(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var elegibilidade = await _votacaoService.VerificarElegibilidadeAsync(eleicaoId, userId, cancellationToken);
            return Ok(elegibilidade);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao verificar elegibilidade para eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao verificar elegibilidade");
        }
    }

    /// <summary>
    /// Verifica se o eleitor ja votou na eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Status do voto</returns>
    [HttpGet("status/{eleicaoId:guid}")]
    [ProducesResponseType(typeof(StatusVotoDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<StatusVotoDto>> VerificarStatusVoto(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var status = await _votacaoService.VerificarStatusVotoAsync(eleicaoId, userId, cancellationToken);
            return Ok(status);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao verificar status de voto para eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao verificar status do voto");
        }
    }

    /// <summary>
    /// Obtem a cedula de votacao (chapas disponiveis)
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Cedula de votacao</returns>
    [HttpGet("cedula/{eleicaoId:guid}")]
    [ProducesResponseType(typeof(CedulaVotacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<CedulaVotacaoDto>> ObterCedula(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var cedula = await _votacaoService.ObterCedulaAsync(eleicaoId, userId, cancellationToken);
            return Ok(cedula);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter cedula para eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao obter cedula");
        }
    }

    /// <summary>
    /// Registra um voto
    /// </summary>
    /// <param name="dto">Dados do voto</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Comprovante de voto</returns>
    [HttpPost("votar")]
    [ProducesResponseType(typeof(ComprovanteVotoDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ComprovanteVotoDto>> Votar([FromBody] RegistrarVotoDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var comprovante = await _votacaoService.RegistrarVotoAsync(dto, userId, cancellationToken);
            return CreatedAtAction(nameof(VerificarStatusVoto), new { eleicaoId = dto.EleicaoId }, comprovante);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao registrar voto");
            return InternalError("Erro ao registrar voto");
        }
    }

    /// <summary>
    /// Obtem o comprovante de voto
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Comprovante de voto</returns>
    [HttpGet("comprovante/{eleicaoId:guid}")]
    [ProducesResponseType(typeof(ComprovanteVotoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ComprovanteVotoDto>> ObterComprovante(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var comprovante = await _votacaoService.ObterComprovanteAsync(eleicaoId, userId, cancellationToken);
            if (comprovante == null)
                return NotFound(new { message = "Comprovante nao encontrado" });

            return Ok(comprovante);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter comprovante para eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao obter comprovante");
        }
    }

    /// <summary>
    /// Obtem as chapas de uma eleicao para votacao (formato simplificado)
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de chapas</returns>
    [HttpGet("eleicao/{eleicaoId:guid}/chapas")]
    [ProducesResponseType(StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> GetChapasEleicao(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var cedula = await _votacaoService.ObterCedulaAsync(eleicaoId, userId, cancellationToken);

            // Map to simplified format expected by frontend
            var chapas = cedula.Opcoes.Select(o => new
            {
                id = o.ChapaId.ToString(),
                numero = o.Numero,
                nome = o.Nome,
                sigla = o.Sigla,
                slogan = o.Lema,
                presidente = o.Membros.FirstOrDefault(m => m.Cargo == "Presidente")?.Nome ?? o.Membros.FirstOrDefault()?.Nome ?? "",
                vicePresidente = o.Membros.FirstOrDefault(m => m.Cargo == "Vice-Presidente")?.Nome,
                membros = o.Membros.Where(m => m.Cargo != "Presidente" && m.Cargo != "Vice-Presidente")
                    .Select(m => new { nome = m.Nome, cargo = m.Cargo })
                    .ToList()
            });

            return Ok(chapas);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter chapas para eleicao {EleicaoId}", eleicaoId);
            return StatusCode(500, new { message = "Erro ao obter chapas" });
        }
    }

    /// <summary>
    /// Lista eleicoes disponiveis para votacao
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de eleicoes</returns>
    [HttpGet("eleicoes-disponiveis")]
    [ProducesResponseType(typeof(IEnumerable<EleicaoVotacaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<EleicaoVotacaoDto>>> GetEleicoesDisponiveis(CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var eleicoes = await _votacaoService.GetEleicoesDisponiveisAsync(userId, cancellationToken);
            return Ok(eleicoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar eleicoes disponiveis");
            return InternalError("Erro ao listar eleicoes");
        }
    }

    /// <summary>
    /// Lista historico de votos do usuario
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de votos</returns>
    [HttpGet("historico")]
    [ProducesResponseType(typeof(IEnumerable<HistoricoVotoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<HistoricoVotoDto>>> GetHistorico(CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var historico = await _votacaoService.GetHistoricoAsync(userId, cancellationToken);
            return Ok(historico);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar historico de votos");
            return InternalError("Erro ao listar historico");
        }
    }

    /// <summary>
    /// Obtem estatisticas de votacao de uma eleicao (Admin)
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Estatisticas de votacao</returns>
    [HttpGet("estatisticas/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(EstatisticasVotacaoDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<EstatisticasVotacaoDto>> GetEstatisticas(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var estatisticas = await _votacaoService.GetEstatisticasAsync(eleicaoId, cancellationToken);
            return Ok(estatisticas);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter estatisticas da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao obter estatisticas");
        }
    }

    /// <summary>
    /// Lista eleitores que votaram em uma eleicao (Admin)
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="page">Pagina</param>
    /// <param name="pageSize">Tamanho da pagina</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de eleitores que votaram</returns>
    [HttpGet("eleitores-votaram/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(PagedResultDto<EleitorVotouDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<PagedResultDto<EleitorVotouDto>>> GetEleitoresQueVotaram(
        Guid eleicaoId,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 50,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var eleitores = await _votacaoService.GetEleitoresQueVotaramAsync(eleicaoId, page, pageSize, cancellationToken);
            return Ok(eleitores);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar eleitores que votaram na eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao listar eleitores");
        }
    }

    /// <summary>
    /// Anula um voto (Admin)
    /// </summary>
    /// <param name="id">ID do registro de voto</param>
    /// <param name="request">Dados da anulacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Resultado da anulacao</returns>
    [HttpPost("{id:guid}/anular")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> AnularVoto(
        Guid id,
        [FromBody] AnularVotoRequest request,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            await _votacaoService.AnularVotoAsync(id, request.Motivo, userId, cancellationToken);
            return Ok(new { message = "Voto anulado com sucesso" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (KeyNotFoundException ex)
        {
            return NotFound(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao anular voto {Id}", id);
            return InternalError("Erro ao anular voto");
        }
    }

    /// <summary>
    /// Abre a votacao de uma eleicao (Admin)
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Resultado da operacao</returns>
    [HttpPost("abrir/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> AbrirVotacao(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            await _votacaoService.AbrirVotacaoAsync(eleicaoId, cancellationToken);
            return Ok(new { message = "Votacao aberta com sucesso" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (KeyNotFoundException ex)
        {
            return NotFound(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao abrir votacao da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao abrir votacao");
        }
    }

    /// <summary>
    /// Fecha a votacao de uma eleicao (Admin)
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Resultado da operacao</returns>
    [HttpPost("fechar/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<IActionResult> FecharVotacao(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            await _votacaoService.FecharVotacaoAsync(eleicaoId, cancellationToken);
            return Ok(new { message = "Votacao fechada com sucesso" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (KeyNotFoundException ex)
        {
            return NotFound(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao fechar votacao da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao fechar votacao");
        }
    }
}

// Request DTOs (simple records for request bodies)
public record AnularVotoRequest(string Motivo);
