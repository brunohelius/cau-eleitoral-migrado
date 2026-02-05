using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Enums;

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
    [ProducesResponseType(typeof(PagedResult<EleitorVotouDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<PagedResult<EleitorVotouDto>>> GetEleitoresQueVotaram(
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
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao anular voto {Id}", id);
            return InternalError("Erro ao anular voto");
        }
    }
}

// DTOs para Votacao
public record ElegibilidadeVotoDto
{
    public bool PodeVotar { get; init; }
    public bool JaVotou { get; init; }
    public string? MotivoInelegibilidade { get; init; }
    public bool EleicaoEmAndamento { get; init; }
    public DateTime? DataInicioVotacao { get; init; }
    public DateTime? DataFimVotacao { get; init; }
}

public record StatusVotoDto
{
    public bool Votou { get; init; }
    public DateTime? DataVoto { get; init; }
    public string? HashComprovante { get; init; }
}

public record CedulaVotacaoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public string? Instrucoes { get; init; }
    public List<OpcaoVotoDto> Opcoes { get; init; } = new();
    public bool PermiteBranco { get; init; }
    public bool PermiteNulo { get; init; }
}

public record OpcaoVotoDto
{
    public Guid ChapaId { get; init; }
    public int Numero { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Sigla { get; init; }
    public string? Lema { get; init; }
    public List<MembroChapaResumoDto> Membros { get; init; } = new();
}

public record MembroChapaResumoDto
{
    public string Nome { get; init; } = string.Empty;
    public string Cargo { get; init; } = string.Empty;
}

public record RegistrarVotoDto
{
    public Guid EleicaoId { get; init; }
    public Guid? ChapaId { get; init; }
    public TipoVoto TipoVoto { get; init; }
}

public record ComprovanteVotoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public DateTime DataVoto { get; init; }
    public string HashComprovante { get; init; } = string.Empty;
    public string? Mensagem { get; init; }
}

public record EleicaoVotacaoDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public DateTime DataInicioVotacao { get; init; }
    public DateTime DataFimVotacao { get; init; }
    public bool EmAndamento { get; init; }
    public bool JaVotou { get; init; }
    public int TotalChapas { get; init; }
}

public record HistoricoVotoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public int AnoEleicao { get; init; }
    public DateTime DataVoto { get; init; }
    public string HashComprovante { get; init; } = string.Empty;
}

public record EstatisticasVotacaoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public int TotalEleitores { get; init; }
    public int TotalVotos { get; init; }
    public int VotosValidos { get; init; }
    public int VotosBrancos { get; init; }
    public int VotosNulos { get; init; }
    public int VotosAnulados { get; init; }
    public decimal PercentualParticipacao { get; init; }
    public decimal PercentualAbstencao { get; init; }
    public DateTime? UltimaAtualizacao { get; init; }
}

public record EleitorVotouDto
{
    public Guid EleitorId { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? RegistroCAU { get; init; }
    public DateTime DataVoto { get; init; }
}

public record PagedResult<T>
{
    public IEnumerable<T> Items { get; init; } = new List<T>();
    public int TotalCount { get; init; }
    public int Page { get; init; }
    public int PageSize { get; init; }
    public int TotalPages => (int)Math.Ceiling(TotalCount / (double)PageSize);
    public bool HasNextPage => Page < TotalPages;
    public bool HasPreviousPage => Page > 1;
}

public record AnularVotoRequest(string Motivo);

// Interface do servico (a ser implementada)
public interface IVotacaoService
{
    Task<ElegibilidadeVotoDto> VerificarElegibilidadeAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);
    Task<StatusVotoDto> VerificarStatusVotoAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);
    Task<CedulaVotacaoDto> ObterCedulaAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);
    Task<ComprovanteVotoDto> RegistrarVotoAsync(RegistrarVotoDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<ComprovanteVotoDto?> ObterComprovanteAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);
    Task<IEnumerable<EleicaoVotacaoDto>> GetEleicoesDisponiveisAsync(Guid userId, CancellationToken cancellationToken = default);
    Task<IEnumerable<HistoricoVotoDto>> GetHistoricoAsync(Guid userId, CancellationToken cancellationToken = default);
    Task<EstatisticasVotacaoDto> GetEstatisticasAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<PagedResult<EleitorVotouDto>> GetEleitoresQueVotaramAsync(Guid eleicaoId, int page, int pageSize, CancellationToken cancellationToken = default);
    Task AnularVotoAsync(Guid id, string motivo, Guid userId, CancellationToken cancellationToken = default);
}
