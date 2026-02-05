using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para gerenciamento de apuracao de votos
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
    /// Obtem resultado da apuracao de uma eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Resultado da apuracao</returns>
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
    /// Obtem resultado parcial da apuracao (em tempo real)
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Resultado parcial</returns>
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
    /// Inicia o processo de apuracao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Status da apuracao</returns>
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
    /// Pausa o processo de apuracao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="request">Dados da pausa</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Status da apuracao</returns>
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
    /// Retoma o processo de apuracao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Status da apuracao</returns>
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
    /// Finaliza o processo de apuracao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Resultado final</returns>
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
    /// Homologa o resultado da eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Resultado homologado</returns>
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
    /// Publica o resultado da eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Resultado publicado</returns>
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
    /// Obtem status da apuracao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Status da apuracao</returns>
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
    /// Gera ata de apuracao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Ata de apuracao</returns>
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
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao gerar ata de apuracao da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao gerar ata");
        }
    }

    /// <summary>
    /// Gera boletim de urna
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
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
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao gerar boletim de urna da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao gerar boletim");
        }
    }

    /// <summary>
    /// Lista eleitos de uma eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de eleitos</returns>
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
    /// Reprocessa a apuracao (em caso de erro)
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Status da apuracao</returns>
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
}

// DTOs para Apuracao
public record ResultadoApuracaoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public StatusApuracao StatusApuracao { get; init; }
    public bool Homologado { get; init; }
    public bool Publicado { get; init; }
    public DateTime? DataApuracao { get; init; }
    public DateTime? DataHomologacao { get; init; }
    public DateTime? DataPublicacao { get; init; }
    public int TotalEleitores { get; init; }
    public int TotalVotos { get; init; }
    public int VotosValidos { get; init; }
    public int VotosBrancos { get; init; }
    public int VotosNulos { get; init; }
    public int VotosAnulados { get; init; }
    public decimal PercentualParticipacao { get; init; }
    public List<ResultadoChapaDto> ResultadosChapas { get; init; } = new();
    public Guid? ChapaVencedoraId { get; init; }
    public string? ChapaVencedoraNome { get; init; }
}

public record ResultadoParcialDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public int VotosApurados { get; init; }
    public int TotalVotos { get; init; }
    public decimal PercentualApurado { get; init; }
    public DateTime UltimaAtualizacao { get; init; }
    public List<ResultadoChapaDto> ResultadosChapas { get; init; } = new();
}

public record ResultadoChapaDto
{
    public Guid ChapaId { get; init; }
    public int Numero { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Sigla { get; init; }
    public int TotalVotos { get; init; }
    public decimal Percentual { get; init; }
    public int Posicao { get; init; }
    public bool Vencedora { get; init; }
}

public record StatusApuracaoDto
{
    public Guid EleicaoId { get; init; }
    public StatusApuracao Status { get; init; }
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
    public DateTime? DataPausa { get; init; }
    public string? MotivoPausa { get; init; }
    public int VotosApurados { get; init; }
    public int TotalVotos { get; init; }
    public decimal PercentualApurado { get; init; }
    public Guid? ResponsavelId { get; init; }
    public string? ResponsavelNome { get; init; }
}

public record AtaApuracaoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public DateTime DataApuracao { get; init; }
    public string Numero { get; init; } = string.Empty;
    public string Conteudo { get; init; } = string.Empty;
    public List<MembroComissaoDto> MembrosComissao { get; init; } = new();
    public ResultadoApuracaoDto Resultado { get; init; } = new();
}

public record MembroComissaoDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string Cargo { get; init; } = string.Empty;
}

public record BoletimUrnaDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public DateTime DataEmissao { get; init; }
    public string HashIntegridade { get; init; } = string.Empty;
    public int TotalVotos { get; init; }
    public int VotosValidos { get; init; }
    public int VotosBrancos { get; init; }
    public int VotosNulos { get; init; }
    public List<ResultadoChapaDto> ResultadosChapas { get; init; } = new();
}

public record EleitoDto
{
    public Guid Id { get; init; }
    public Guid ChapaId { get; init; }
    public string ChapaNome { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string Cargo { get; init; } = string.Empty;
    public bool Titular { get; init; }
    public int Ordem { get; init; }
}

public enum StatusApuracao
{
    NaoIniciada = 0,
    EmAndamento = 1,
    Pausada = 2,
    Finalizada = 3,
    Homologada = 4,
    Publicada = 5
}

// Interface do servico (a ser implementada)
public interface IApuracaoService
{
    Task<ResultadoApuracaoDto?> GetResultadoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<ResultadoParcialDto> GetResultadoParcialAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<StatusApuracaoDto> IniciarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);
    Task<StatusApuracaoDto> PausarAsync(Guid eleicaoId, string motivo, Guid userId, CancellationToken cancellationToken = default);
    Task<StatusApuracaoDto> RetomarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);
    Task<ResultadoApuracaoDto> FinalizarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);
    Task<ResultadoApuracaoDto> HomologarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);
    Task<ResultadoApuracaoDto> PublicarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);
    Task<StatusApuracaoDto> GetStatusAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<AtaApuracaoDto> GetAtaAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<BoletimUrnaDto> GetBoletimUrnaAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<EleitoDto>> GetEleitosAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<StatusApuracaoDto> ReprocessarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);
}
