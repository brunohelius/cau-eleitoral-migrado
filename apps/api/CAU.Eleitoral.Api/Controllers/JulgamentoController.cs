using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para gerenciamento de julgamentos eleitorais
/// </summary>
[Authorize]
public class JulgamentoController : BaseController
{
    private readonly IJulgamentoService _julgamentoService;
    private readonly ILogger<JulgamentoController> _logger;

    public JulgamentoController(IJulgamentoService julgamentoService, ILogger<JulgamentoController> logger)
    {
        _julgamentoService = julgamentoService;
        _logger = logger;
    }

    /// <summary>
    /// Lista todos os julgamentos
    /// </summary>
    /// <param name="eleicaoId">Filtro opcional por eleicao</param>
    /// <param name="status">Filtro opcional por status</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de julgamentos</returns>
    [HttpGet]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(IEnumerable<JulgamentoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<JulgamentoDto>>> GetAll(
        [FromQuery] Guid? eleicaoId,
        [FromQuery] StatusJulgamento? status,
        CancellationToken cancellationToken)
    {
        try
        {
            var julgamentos = await _julgamentoService.GetAllAsync(eleicaoId, status, cancellationToken);
            return Ok(julgamentos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar julgamentos");
            return InternalError("Erro ao listar julgamentos");
        }
    }

    /// <summary>
    /// Obtem um julgamento pelo ID
    /// </summary>
    /// <param name="id">ID do julgamento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados do julgamento</returns>
    [HttpGet("{id:guid}")]
    [ProducesResponseType(typeof(JulgamentoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<JulgamentoDto>> GetById(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var julgamento = await _julgamentoService.GetByIdAsync(id, cancellationToken);
            if (julgamento == null)
                return NotFound(new { message = "Julgamento nao encontrado" });

            return Ok(julgamento);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter julgamento {Id}", id);
            return InternalError("Erro ao obter julgamento");
        }
    }

    /// <summary>
    /// Lista julgamentos por eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de julgamentos</returns>
    [HttpGet("eleicao/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(IEnumerable<JulgamentoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<JulgamentoDto>>> GetByEleicao(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var julgamentos = await _julgamentoService.GetByEleicaoAsync(eleicaoId, cancellationToken);
            return Ok(julgamentos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar julgamentos da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao listar julgamentos");
        }
    }

    /// <summary>
    /// Lista julgamentos agendados
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de julgamentos agendados</returns>
    [HttpGet("agendados")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(IEnumerable<JulgamentoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<JulgamentoDto>>> GetAgendados(CancellationToken cancellationToken)
    {
        try
        {
            var julgamentos = await _julgamentoService.GetAgendadosAsync(cancellationToken);
            return Ok(julgamentos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar julgamentos agendados");
            return InternalError("Erro ao listar julgamentos");
        }
    }

    /// <summary>
    /// Cria um novo julgamento
    /// </summary>
    /// <param name="dto">Dados do julgamento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Julgamento criado</returns>
    [HttpPost]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(JulgamentoDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<JulgamentoDto>> Create([FromBody] CreateJulgamentoDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var julgamento = await _julgamentoService.CreateAsync(dto, userId, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id = julgamento.Id }, julgamento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao criar julgamento");
            return InternalError("Erro ao criar julgamento");
        }
    }

    /// <summary>
    /// Atualiza um julgamento existente
    /// </summary>
    /// <param name="id">ID do julgamento</param>
    /// <param name="dto">Dados atualizados</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Julgamento atualizado</returns>
    [HttpPut("{id:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(JulgamentoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<JulgamentoDto>> Update(Guid id, [FromBody] UpdateJulgamentoDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var julgamento = await _julgamentoService.UpdateAsync(id, dto, cancellationToken);
            return Ok(julgamento);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Julgamento nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar julgamento {Id}", id);
            return InternalError("Erro ao atualizar julgamento");
        }
    }

    /// <summary>
    /// Remove um julgamento
    /// </summary>
    /// <param name="id">ID do julgamento</param>
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
            await _julgamentoService.DeleteAsync(id, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Julgamento nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao excluir julgamento {Id}", id);
            return InternalError("Erro ao excluir julgamento");
        }
    }

    /// <summary>
    /// Inicia um julgamento
    /// </summary>
    /// <param name="id">ID do julgamento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Julgamento iniciado</returns>
    [HttpPost("{id:guid}/iniciar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(JulgamentoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<JulgamentoDto>> Iniciar(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var julgamento = await _julgamentoService.IniciarAsync(id, cancellationToken);
            return Ok(julgamento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao iniciar julgamento {Id}", id);
            return InternalError("Erro ao iniciar julgamento");
        }
    }

    /// <summary>
    /// Suspende um julgamento
    /// </summary>
    /// <param name="id">ID do julgamento</param>
    /// <param name="request">Dados da suspensao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Julgamento suspenso</returns>
    [HttpPost("{id:guid}/suspender")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(JulgamentoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<JulgamentoDto>> Suspender(
        Guid id,
        [FromBody] MotivoRequest request,
        CancellationToken cancellationToken)
    {
        try
        {
            var julgamento = await _julgamentoService.SuspenderAsync(id, request.Motivo, cancellationToken);
            return Ok(julgamento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao suspender julgamento {Id}", id);
            return InternalError("Erro ao suspender julgamento");
        }
    }

    /// <summary>
    /// Retoma um julgamento suspenso
    /// </summary>
    /// <param name="id">ID do julgamento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Julgamento retomado</returns>
    [HttpPost("{id:guid}/retomar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(JulgamentoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<JulgamentoDto>> Retomar(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var julgamento = await _julgamentoService.RetomarAsync(id, cancellationToken);
            return Ok(julgamento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao retomar julgamento {Id}", id);
            return InternalError("Erro ao retomar julgamento");
        }
    }

    /// <summary>
    /// Registra voto de um membro da comissao
    /// </summary>
    /// <param name="id">ID do julgamento</param>
    /// <param name="dto">Dados do voto</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Julgamento atualizado</returns>
    [HttpPost("{id:guid}/votos")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(JulgamentoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<JulgamentoDto>> RegistrarVoto(
        Guid id,
        [FromBody] VotoJulgamentoDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var julgamento = await _julgamentoService.RegistrarVotoAsync(id, dto, userId, cancellationToken);
            return Ok(julgamento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao registrar voto no julgamento {Id}", id);
            return InternalError("Erro ao registrar voto");
        }
    }

    /// <summary>
    /// Conclui um julgamento
    /// </summary>
    /// <param name="id">ID do julgamento</param>
    /// <param name="dto">Dados da conclusao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Julgamento concluido</returns>
    [HttpPost("{id:guid}/concluir")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(JulgamentoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<JulgamentoDto>> Concluir(
        Guid id,
        [FromBody] ConcluirJulgamentoDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var julgamento = await _julgamentoService.ConcluirAsync(id, dto, userId, cancellationToken);
            return Ok(julgamento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao concluir julgamento {Id}", id);
            return InternalError("Erro ao concluir julgamento");
        }
    }

    /// <summary>
    /// Cancela um julgamento
    /// </summary>
    /// <param name="id">ID do julgamento</param>
    /// <param name="request">Dados do cancelamento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Julgamento cancelado</returns>
    [HttpPost("{id:guid}/cancelar")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(JulgamentoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<JulgamentoDto>> Cancelar(
        Guid id,
        [FromBody] MotivoRequest request,
        CancellationToken cancellationToken)
    {
        try
        {
            var julgamento = await _julgamentoService.CancelarAsync(id, request.Motivo, cancellationToken);
            return Ok(julgamento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao cancelar julgamento {Id}", id);
            return InternalError("Erro ao cancelar julgamento");
        }
    }

    /// <summary>
    /// Lista sessoes de julgamento
    /// </summary>
    /// <param name="eleicaoId">Filtro opcional por eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de sessoes</returns>
    [HttpGet("sessoes")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(IEnumerable<SessaoJulgamentoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<SessaoJulgamentoDto>>> GetSessoes(
        [FromQuery] Guid? eleicaoId,
        CancellationToken cancellationToken)
    {
        try
        {
            var sessoes = await _julgamentoService.GetSessoesAsync(eleicaoId, cancellationToken);
            return Ok(sessoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar sessoes de julgamento");
            return InternalError("Erro ao listar sessoes");
        }
    }

    /// <summary>
    /// Cria uma nova sessao de julgamento
    /// </summary>
    /// <param name="dto">Dados da sessao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Sessao criada</returns>
    [HttpPost("sessoes")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(SessaoJulgamentoDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<SessaoJulgamentoDto>> CreateSessao(
        [FromBody] CreateSessaoJulgamentoDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var sessao = await _julgamentoService.CreateSessaoAsync(dto, userId, cancellationToken);
            return CreatedAtAction(nameof(GetSessoes), sessao);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao criar sessao de julgamento");
            return InternalError("Erro ao criar sessao");
        }
    }
}

// DTOs para Julgamento
public record JulgamentoDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public Guid? SessaoId { get; init; }
    public Guid? DenunciaId { get; init; }
    public Guid? ImpugnacaoId { get; init; }
    public TipoJulgamento Tipo { get; init; }
    public StatusJulgamento Status { get; init; }
    public DateTime DataAgendada { get; init; }
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
    public string? Ementa { get; init; }
    public string? Relatorio { get; init; }
    public TipoDecisao? TipoDecisao { get; init; }
    public string? Decisao { get; init; }
    public string? Fundamentacao { get; init; }
    public Guid? RelatorId { get; init; }
    public string? RelatorNome { get; init; }
    public List<VotoJulgamentoResultadoDto> Votos { get; init; } = new();
    public DateTime CreatedAt { get; init; }
    public DateTime? UpdatedAt { get; init; }
}

public record CreateJulgamentoDto
{
    public Guid EleicaoId { get; init; }
    public Guid? SessaoId { get; init; }
    public Guid? DenunciaId { get; init; }
    public Guid? ImpugnacaoId { get; init; }
    public TipoJulgamento Tipo { get; init; }
    public DateTime DataAgendada { get; init; }
    public string? Ementa { get; init; }
    public Guid RelatorId { get; init; }
}

public record UpdateJulgamentoDto
{
    public Guid? SessaoId { get; init; }
    public DateTime? DataAgendada { get; init; }
    public string? Ementa { get; init; }
    public Guid? RelatorId { get; init; }
}

public record VotoJulgamentoDto
{
    public Guid MembroId { get; init; }
    public TipoVotoJulgamento Voto { get; init; }
    public string? Fundamentacao { get; init; }
}

public record VotoJulgamentoResultadoDto
{
    public Guid Id { get; init; }
    public Guid MembroId { get; init; }
    public string MembroNome { get; init; } = string.Empty;
    public TipoVotoJulgamento Voto { get; init; }
    public string? Fundamentacao { get; init; }
    public DateTime DataVoto { get; init; }
}

public record ConcluirJulgamentoDto
{
    public TipoDecisao TipoDecisao { get; init; }
    public string Decisao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
}

public record SessaoJulgamentoDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public TipoSessao Tipo { get; init; }
    public StatusSessao Status { get; init; }
    public DateTime Data { get; init; }
    public string? Local { get; init; }
    public string? Pauta { get; init; }
    public int TotalJulgamentos { get; init; }
    public DateTime CreatedAt { get; init; }
}

public record CreateSessaoJulgamentoDto
{
    public Guid EleicaoId { get; init; }
    public TipoSessao Tipo { get; init; }
    public DateTime Data { get; init; }
    public string? Local { get; init; }
    public string? Pauta { get; init; }
}

// Interface do servico (a ser implementada)
public interface IJulgamentoService
{
    Task<IEnumerable<JulgamentoDto>> GetAllAsync(Guid? eleicaoId, StatusJulgamento? status, CancellationToken cancellationToken = default);
    Task<JulgamentoDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<JulgamentoDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<JulgamentoDto>> GetAgendadosAsync(CancellationToken cancellationToken = default);
    Task<JulgamentoDto> CreateAsync(CreateJulgamentoDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<JulgamentoDto> UpdateAsync(Guid id, UpdateJulgamentoDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);
    Task<JulgamentoDto> IniciarAsync(Guid id, CancellationToken cancellationToken = default);
    Task<JulgamentoDto> SuspenderAsync(Guid id, string motivo, CancellationToken cancellationToken = default);
    Task<JulgamentoDto> RetomarAsync(Guid id, CancellationToken cancellationToken = default);
    Task<JulgamentoDto> RegistrarVotoAsync(Guid id, VotoJulgamentoDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<JulgamentoDto> ConcluirAsync(Guid id, ConcluirJulgamentoDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<JulgamentoDto> CancelarAsync(Guid id, string motivo, CancellationToken cancellationToken = default);
    Task<IEnumerable<SessaoJulgamentoDto>> GetSessoesAsync(Guid? eleicaoId, CancellationToken cancellationToken = default);
    Task<SessaoJulgamentoDto> CreateSessaoAsync(CreateSessaoJulgamentoDto dto, Guid userId, CancellationToken cancellationToken = default);
}
