using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para gerenciamento de impugnacoes eleitorais
/// </summary>
[Authorize]
public class ImpugnacaoController : BaseController
{
    private readonly IImpugnacaoService _impugnacaoService;
    private readonly ILogger<ImpugnacaoController> _logger;

    public ImpugnacaoController(IImpugnacaoService impugnacaoService, ILogger<ImpugnacaoController> logger)
    {
        _impugnacaoService = impugnacaoService;
        _logger = logger;
    }

    /// <summary>
    /// Lista todas as impugnacoes
    /// </summary>
    /// <param name="eleicaoId">Filtro opcional por eleicao</param>
    /// <param name="status">Filtro opcional por status</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de impugnacoes</returns>
    [HttpGet]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(IEnumerable<ImpugnacaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<ImpugnacaoDto>>> GetAll(
        [FromQuery] Guid? eleicaoId,
        [FromQuery] StatusImpugnacao? status,
        CancellationToken cancellationToken)
    {
        try
        {
            var impugnacoes = await _impugnacaoService.GetAllAsync(eleicaoId, status, cancellationToken);
            return Ok(impugnacoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar impugnacoes");
            return InternalError("Erro ao listar impugnacoes");
        }
    }

    /// <summary>
    /// Obtem uma impugnacao pelo ID
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados da impugnacao</returns>
    [HttpGet("{id:guid}")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ImpugnacaoDto>> GetById(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var impugnacao = await _impugnacaoService.GetByIdAsync(id, cancellationToken);
            if (impugnacao == null)
                return NotFound(new { message = "Impugnacao nao encontrada" });

            return Ok(impugnacao);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter impugnacao {Id}", id);
            return InternalError("Erro ao obter impugnacao");
        }
    }

    /// <summary>
    /// Lista impugnacoes por eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de impugnacoes</returns>
    [HttpGet("eleicao/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(IEnumerable<ImpugnacaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<ImpugnacaoDto>>> GetByEleicao(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var impugnacoes = await _impugnacaoService.GetByEleicaoAsync(eleicaoId, cancellationToken);
            return Ok(impugnacoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar impugnacoes da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao listar impugnacoes");
        }
    }

    /// <summary>
    /// Lista impugnacoes por chapa
    /// </summary>
    /// <param name="chapaId">ID da chapa</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de impugnacoes</returns>
    [HttpGet("chapa/{chapaId:guid}")]
    [ProducesResponseType(typeof(IEnumerable<ImpugnacaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<ImpugnacaoDto>>> GetByChapa(Guid chapaId, CancellationToken cancellationToken)
    {
        try
        {
            var impugnacoes = await _impugnacaoService.GetByChapaAsync(chapaId, cancellationToken);
            return Ok(impugnacoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar impugnacoes da chapa {ChapaId}", chapaId);
            return InternalError("Erro ao listar impugnacoes");
        }
    }

    /// <summary>
    /// Lista impugnacoes do usuario logado (impugnante)
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de impugnacoes</returns>
    [HttpGet("minhas")]
    [ProducesResponseType(typeof(IEnumerable<ImpugnacaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<ImpugnacaoDto>>> GetMinhasImpugnacoes(CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var impugnacoes = await _impugnacaoService.GetByImpugnanteAsync(userId, cancellationToken);
            return Ok(impugnacoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar impugnacoes do usuario");
            return InternalError("Erro ao listar impugnacoes");
        }
    }

    /// <summary>
    /// Cria uma nova impugnacao
    /// </summary>
    /// <param name="dto">Dados da impugnacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao criada</returns>
    [HttpPost]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> Create([FromBody] CreateImpugnacaoDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var impugnacao = await _impugnacaoService.CreateAsync(dto, userId, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id = impugnacao.Id }, impugnacao);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao criar impugnacao");
            return InternalError("Erro ao criar impugnacao");
        }
    }

    /// <summary>
    /// Atualiza uma impugnacao existente
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="dto">Dados atualizados</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPut("{id:guid}")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> Update(Guid id, [FromBody] UpdateImpugnacaoDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var impugnacao = await _impugnacaoService.UpdateAsync(id, dto, cancellationToken);
            return Ok(impugnacao);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Impugnacao nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar impugnacao {Id}", id);
            return InternalError("Erro ao atualizar impugnacao");
        }
    }

    /// <summary>
    /// Remove uma impugnacao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>NoContent em caso de sucesso</returns>
    [HttpDelete("{id:guid}")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(StatusCodes.Status204NoContent)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<IActionResult> Delete(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            await _impugnacaoService.DeleteAsync(id, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Impugnacao nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao excluir impugnacao {Id}", id);
            return InternalError("Erro ao excluir impugnacao");
        }
    }

    /// <summary>
    /// Inicia a analise de uma impugnacao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/iniciar-analise")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> IniciarAnalise(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var impugnacao = await _impugnacaoService.IniciarAnaliseAsync(id, userId, cancellationToken);
            return Ok(impugnacao);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao iniciar analise da impugnacao {Id}", id);
            return InternalError("Erro ao iniciar analise");
        }
    }

    /// <summary>
    /// Solicita alegacoes do impugnado
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="request">Dados da solicitacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/solicitar-alegacoes")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> SolicitarAlegacoes(
        Guid id,
        [FromBody] SolicitarAlegacoesRequest request,
        CancellationToken cancellationToken)
    {
        try
        {
            var impugnacao = await _impugnacaoService.SolicitarAlegacoesAsync(id, request.PrazoEmDias, cancellationToken);
            return Ok(impugnacao);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao solicitar alegacoes da impugnacao {Id}", id);
            return InternalError("Erro ao solicitar alegacoes");
        }
    }

    /// <summary>
    /// Apresenta alegacoes
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="dto">Dados das alegacoes</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/apresentar-alegacoes")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> ApresentarAlegacoes(
        Guid id,
        [FromBody] ApresentarAlegacoesDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var impugnacao = await _impugnacaoService.ApresentarAlegacoesAsync(id, dto, userId, cancellationToken);
            return Ok(impugnacao);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao apresentar alegacoes da impugnacao {Id}", id);
            return InternalError("Erro ao apresentar alegacoes");
        }
    }

    /// <summary>
    /// Solicita contra-alegacoes do impugnante
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="request">Dados da solicitacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/solicitar-contra-alegacoes")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> SolicitarContraAlegacoes(
        Guid id,
        [FromBody] SolicitarAlegacoesRequest request,
        CancellationToken cancellationToken)
    {
        try
        {
            var impugnacao = await _impugnacaoService.SolicitarContraAlegacoesAsync(id, request.PrazoEmDias, cancellationToken);
            return Ok(impugnacao);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao solicitar contra-alegacoes da impugnacao {Id}", id);
            return InternalError("Erro ao solicitar contra-alegacoes");
        }
    }

    /// <summary>
    /// Apresenta contra-alegacoes
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="dto">Dados das contra-alegacoes</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/apresentar-contra-alegacoes")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> ApresentarContraAlegacoes(
        Guid id,
        [FromBody] ApresentarAlegacoesDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var impugnacao = await _impugnacaoService.ApresentarContraAlegacoesAsync(id, dto, userId, cancellationToken);
            return Ok(impugnacao);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao apresentar contra-alegacoes da impugnacao {Id}", id);
            return InternalError("Erro ao apresentar contra-alegacoes");
        }
    }

    /// <summary>
    /// Envia impugnacao para julgamento
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/enviar-julgamento")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> EnviarParaJulgamento(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var impugnacao = await _impugnacaoService.EnviarParaJulgamentoAsync(id, cancellationToken);
            return Ok(impugnacao);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao enviar impugnacao para julgamento {Id}", id);
            return InternalError("Erro ao enviar para julgamento");
        }
    }

    /// <summary>
    /// Registra o resultado do julgamento
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="dto">Dados do julgamento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/julgar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> Julgar(
        Guid id,
        [FromBody] JulgarImpugnacaoDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var impugnacao = await _impugnacaoService.JulgarAsync(id, dto, userId, cancellationToken);
            return Ok(impugnacao);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao julgar impugnacao {Id}", id);
            return InternalError("Erro ao julgar impugnacao");
        }
    }

    /// <summary>
    /// Arquiva uma impugnacao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="request">Dados do arquivamento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao arquivada</returns>
    [HttpPost("{id:guid}/arquivar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> Arquivar(
        Guid id,
        [FromBody] MotivoRequest request,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var impugnacao = await _impugnacaoService.ArquivarAsync(id, request.Motivo, userId, cancellationToken);
            return Ok(impugnacao);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao arquivar impugnacao {Id}", id);
            return InternalError("Erro ao arquivar impugnacao");
        }
    }

    /// <summary>
    /// Registra recurso contra decisao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="dto">Dados do recurso</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/recurso")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> Recurso(
        Guid id,
        [FromBody] RecursoImpugnacaoDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var impugnacao = await _impugnacaoService.ApresentarRecursoAsync(id, dto, userId, cancellationToken);
            return Ok(impugnacao);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao apresentar recurso da impugnacao {Id}", id);
            return InternalError("Erro ao apresentar recurso");
        }
    }
}

// DTOs para Impugnacao
public record ImpugnacaoDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public Guid ImpugnanteId { get; init; }
    public string ImpugnanteNome { get; init; } = string.Empty;
    public Guid? ChapaId { get; init; }
    public string? ChapaNome { get; init; }
    public Guid? MembroId { get; init; }
    public string? MembroNome { get; init; }
    public TipoImpugnacao Tipo { get; init; }
    public StatusImpugnacao Status { get; init; }
    public TipoAlegacao TipoAlegacao { get; init; }
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
    public DateTime? PrazoAlegacoes { get; init; }
    public string? Alegacoes { get; init; }
    public DateTime? DataAlegacoes { get; init; }
    public DateTime? PrazoContraAlegacoes { get; init; }
    public string? ContraAlegacoes { get; init; }
    public DateTime? DataContraAlegacoes { get; init; }
    public string? Parecer { get; init; }
    public string? Decisao { get; init; }
    public DateTime? DataJulgamento { get; init; }
    public DateTime CreatedAt { get; init; }
    public DateTime? UpdatedAt { get; init; }
}

public record CreateImpugnacaoDto
{
    public Guid EleicaoId { get; init; }
    public Guid? ChapaId { get; init; }
    public Guid? MembroId { get; init; }
    public TipoImpugnacao Tipo { get; init; }
    public TipoAlegacao TipoAlegacao { get; init; }
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
}

public record UpdateImpugnacaoDto
{
    public TipoAlegacao? TipoAlegacao { get; init; }
    public string? Descricao { get; init; }
    public string? Fundamentacao { get; init; }
}

public record ApresentarAlegacoesDto
{
    public string Texto { get; init; } = string.Empty;
    public List<Guid>? DocumentosIds { get; init; }
}

public record JulgarImpugnacaoDto
{
    public StatusImpugnacao Resultado { get; init; }
    public string Decisao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
}

public record RecursoImpugnacaoDto
{
    public string Fundamentacao { get; init; } = string.Empty;
    public List<Guid>? DocumentosIds { get; init; }
}

public record SolicitarAlegacoesRequest(int PrazoEmDias);

// Interface do servico (a ser implementada)
public interface IImpugnacaoService
{
    Task<IEnumerable<ImpugnacaoDto>> GetAllAsync(Guid? eleicaoId, StatusImpugnacao? status, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<ImpugnacaoDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<ImpugnacaoDto>> GetByChapaAsync(Guid chapaId, CancellationToken cancellationToken = default);
    Task<IEnumerable<ImpugnacaoDto>> GetByImpugnanteAsync(Guid impugnanteId, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> CreateAsync(CreateImpugnacaoDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> UpdateAsync(Guid id, UpdateImpugnacaoDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> IniciarAnaliseAsync(Guid id, Guid userId, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> SolicitarAlegacoesAsync(Guid id, int prazoEmDias, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> ApresentarAlegacoesAsync(Guid id, ApresentarAlegacoesDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> SolicitarContraAlegacoesAsync(Guid id, int prazoEmDias, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> ApresentarContraAlegacoesAsync(Guid id, ApresentarAlegacoesDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> EnviarParaJulgamentoAsync(Guid id, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> JulgarAsync(Guid id, JulgarImpugnacaoDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> ArquivarAsync(Guid id, string motivo, Guid userId, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> ApresentarRecursoAsync(Guid id, RecursoImpugnacaoDto dto, Guid userId, CancellationToken cancellationToken = default);
}
