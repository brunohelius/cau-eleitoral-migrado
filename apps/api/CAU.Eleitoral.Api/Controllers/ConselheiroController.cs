using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para gerenciamento de conselheiros
/// </summary>
[Authorize]
public class ConselheiroController : BaseController
{
    private readonly IConselheiroService _conselheiroService;
    private readonly ILogger<ConselheiroController> _logger;

    public ConselheiroController(IConselheiroService conselheiroService, ILogger<ConselheiroController> logger)
    {
        _conselheiroService = conselheiroService;
        _logger = logger;
    }

    /// <summary>
    /// Lista todos os conselheiros
    /// </summary>
    /// <param name="status">Filtro opcional por status</param>
    /// <param name="tipo">Filtro opcional por tipo</param>
    /// <param name="regionalId">Filtro opcional por regional</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de conselheiros</returns>
    [HttpGet]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<ConselheiroDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<ConselheiroDto>>> GetAll(
        [FromQuery] StatusConselheiro? status,
        [FromQuery] TipoConselheiro? tipo,
        [FromQuery] Guid? regionalId,
        CancellationToken cancellationToken)
    {
        try
        {
            var conselheiros = await _conselheiroService.GetAllAsync(status, tipo, regionalId, cancellationToken);
            return Ok(conselheiros);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar conselheiros");
            return InternalError("Erro ao listar conselheiros");
        }
    }

    /// <summary>
    /// Obtem um conselheiro pelo ID
    /// </summary>
    /// <param name="id">ID do conselheiro</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados do conselheiro</returns>
    [HttpGet("{id:guid}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(ConselheiroDetalheDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ConselheiroDetalheDto>> GetById(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var conselheiro = await _conselheiroService.GetByIdAsync(id, cancellationToken);
            if (conselheiro == null)
                return NotFound(new { message = "Conselheiro nao encontrado" });

            return Ok(conselheiro);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter conselheiro {Id}", id);
            return InternalError("Erro ao obter conselheiro");
        }
    }

    /// <summary>
    /// Lista conselheiros por regional
    /// </summary>
    /// <param name="regionalId">ID da regional</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de conselheiros</returns>
    [HttpGet("regional/{regionalId:guid}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<ConselheiroDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<ConselheiroDto>>> GetByRegional(Guid regionalId, CancellationToken cancellationToken)
    {
        try
        {
            var conselheiros = await _conselheiroService.GetByRegionalAsync(regionalId, cancellationToken);
            return Ok(conselheiros);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar conselheiros da regional {RegionalId}", regionalId);
            return InternalError("Erro ao listar conselheiros");
        }
    }

    /// <summary>
    /// Lista conselheiros ativos em um mandato
    /// </summary>
    /// <param name="mandato">Ano do mandato</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de conselheiros</returns>
    [HttpGet("mandato/{mandato:int}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<ConselheiroDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<ConselheiroDto>>> GetByMandato(int mandato, CancellationToken cancellationToken)
    {
        try
        {
            var conselheiros = await _conselheiroService.GetByMandatoAsync(mandato, cancellationToken);
            return Ok(conselheiros);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar conselheiros do mandato {Mandato}", mandato);
            return InternalError("Erro ao listar conselheiros");
        }
    }

    /// <summary>
    /// Cria um novo conselheiro (empossamento)
    /// </summary>
    /// <param name="dto">Dados do conselheiro</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Conselheiro criado</returns>
    [HttpPost]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ConselheiroDetalheDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ConselheiroDetalheDto>> Create([FromBody] CreateConselheiroDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var conselheiro = await _conselheiroService.CreateAsync(dto, userId, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id = conselheiro.Id }, conselheiro);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao criar conselheiro");
            return InternalError("Erro ao criar conselheiro");
        }
    }

    /// <summary>
    /// Atualiza um conselheiro
    /// </summary>
    /// <param name="id">ID do conselheiro</param>
    /// <param name="dto">Dados atualizados</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Conselheiro atualizado</returns>
    [HttpPut("{id:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ConselheiroDetalheDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ConselheiroDetalheDto>> Update(Guid id, [FromBody] UpdateConselheiroDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var conselheiro = await _conselheiroService.UpdateAsync(id, dto, cancellationToken);
            return Ok(conselheiro);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Conselheiro nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar conselheiro {Id}", id);
            return InternalError("Erro ao atualizar conselheiro");
        }
    }

    /// <summary>
    /// Remove um conselheiro
    /// </summary>
    /// <param name="id">ID do conselheiro</param>
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
            await _conselheiroService.DeleteAsync(id, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Conselheiro nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao excluir conselheiro {Id}", id);
            return InternalError("Erro ao excluir conselheiro");
        }
    }

    /// <summary>
    /// Empossa conselheiros de uma eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="dto">Dados do empossamento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de conselheiros empossados</returns>
    [HttpPost("empossar/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(IEnumerable<ConselheiroDto>), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<IEnumerable<ConselheiroDto>>> Empossar(
        Guid eleicaoId,
        [FromBody] EmpossarConselheirosDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var conselheiros = await _conselheiroService.EmpossarAsync(eleicaoId, dto, userId, cancellationToken);
            return CreatedAtAction(nameof(GetByMandato), new { mandato = dto.Mandato }, conselheiros);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao empossar conselheiros da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao empossar conselheiros");
        }
    }

    /// <summary>
    /// Afasta um conselheiro
    /// </summary>
    /// <param name="id">ID do conselheiro</param>
    /// <param name="dto">Dados do afastamento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Conselheiro afastado</returns>
    [HttpPost("{id:guid}/afastar")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(ConselheiroDetalheDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ConselheiroDetalheDto>> Afastar(
        Guid id,
        [FromBody] AfastarConselheiroDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var conselheiro = await _conselheiroService.AfastarAsync(id, dto, userId, cancellationToken);
            return Ok(conselheiro);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao afastar conselheiro {Id}", id);
            return InternalError("Erro ao afastar conselheiro");
        }
    }

    /// <summary>
    /// Reintegra um conselheiro
    /// </summary>
    /// <param name="id">ID do conselheiro</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Conselheiro reintegrado</returns>
    [HttpPost("{id:guid}/reintegrar")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(ConselheiroDetalheDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ConselheiroDetalheDto>> Reintegrar(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var conselheiro = await _conselheiroService.ReintegrarAsync(id, userId, cancellationToken);
            return Ok(conselheiro);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao reintegrar conselheiro {Id}", id);
            return InternalError("Erro ao reintegrar conselheiro");
        }
    }

    /// <summary>
    /// Renova mandato de um conselheiro
    /// </summary>
    /// <param name="id">ID do conselheiro</param>
    /// <param name="dto">Dados da renovacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Conselheiro com mandato renovado</returns>
    [HttpPost("{id:guid}/renovar-mandato")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ConselheiroDetalheDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ConselheiroDetalheDto>> RenovarMandato(
        Guid id,
        [FromBody] RenovarMandatoDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var conselheiro = await _conselheiroService.RenovarMandatoAsync(id, dto, userId, cancellationToken);
            return Ok(conselheiro);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao renovar mandato do conselheiro {Id}", id);
            return InternalError("Erro ao renovar mandato");
        }
    }

    /// <summary>
    /// Encerra mandato de um conselheiro
    /// </summary>
    /// <param name="id">ID do conselheiro</param>
    /// <param name="dto">Dados do encerramento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Conselheiro com mandato encerrado</returns>
    [HttpPost("{id:guid}/encerrar-mandato")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(ConselheiroDetalheDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ConselheiroDetalheDto>> EncerrarMandato(
        Guid id,
        [FromBody] EncerrarMandatoDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var conselheiro = await _conselheiroService.EncerrarMandatoAsync(id, dto, userId, cancellationToken);
            return Ok(conselheiro);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao encerrar mandato do conselheiro {Id}", id);
            return InternalError("Erro ao encerrar mandato");
        }
    }

    /// <summary>
    /// Lista composicao atual do conselho
    /// </summary>
    /// <param name="regionalId">ID da regional (opcional)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Composicao do conselho</returns>
    [HttpGet("composicao")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(ComposicaoConselhoDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<ComposicaoConselhoDto>> GetComposicao(
        [FromQuery] Guid? regionalId,
        CancellationToken cancellationToken)
    {
        try
        {
            var composicao = await _conselheiroService.GetComposicaoAsync(regionalId, cancellationToken);
            return Ok(composicao);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter composicao do conselho");
            return InternalError("Erro ao obter composicao");
        }
    }
}

// DTOs para Conselheiro
public record ConselheiroDto
{
    public Guid Id { get; init; }
    public Guid ProfissionalId { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string RegistroCAU { get; init; } = string.Empty;
    public TipoConselheiro Tipo { get; init; }
    public StatusConselheiro Status { get; init; }
    public string? Cargo { get; init; }
    public Guid? RegionalId { get; init; }
    public string? RegionalNome { get; init; }
    public int Mandato { get; init; }
    public DateTime DataPosse { get; init; }
    public DateTime? DataFimMandato { get; init; }
}

public record ConselheiroDetalheDto : ConselheiroDto
{
    public string? Email { get; init; }
    public string? Telefone { get; init; }
    public string? Cpf { get; init; }
    public Guid? EleicaoOrigemId { get; init; }
    public string? EleicaoOrigemNome { get; init; }
    public Guid? ChapaOrigemId { get; init; }
    public string? ChapaOrigemNome { get; init; }
    public bool Titular { get; init; }
    public DateTime? DataAfastamento { get; init; }
    public string? MotivoAfastamento { get; init; }
    public string? Observacoes { get; init; }
    public List<MandatoConselheiroDto> HistoricoMandatos { get; init; } = new();
    public DateTime CreatedAt { get; init; }
    public DateTime? UpdatedAt { get; init; }
}

public record CreateConselheiroDto
{
    public Guid ProfissionalId { get; init; }
    public TipoConselheiro Tipo { get; init; }
    public string? Cargo { get; init; }
    public Guid? RegionalId { get; init; }
    public int Mandato { get; init; }
    public DateTime DataPosse { get; init; }
    public DateTime? DataFimMandato { get; init; }
    public Guid? EleicaoOrigemId { get; init; }
    public Guid? ChapaOrigemId { get; init; }
    public bool Titular { get; init; }
    public string? Observacoes { get; init; }
}

public record UpdateConselheiroDto
{
    public string? Cargo { get; init; }
    public DateTime? DataFimMandato { get; init; }
    public string? Observacoes { get; init; }
}

public record EmpossarConselheirosDto
{
    public int Mandato { get; init; }
    public DateTime DataPosse { get; init; }
    public DateTime? DataFimMandato { get; init; }
}

public record AfastarConselheiroDto
{
    public string Motivo { get; init; } = string.Empty;
    public DateTime DataAfastamento { get; init; }
    public bool Temporario { get; init; }
}

public record RenovarMandatoDto
{
    public int NovoMandato { get; init; }
    public DateTime DataInicioMandato { get; init; }
    public DateTime? DataFimMandato { get; init; }
}

public record EncerrarMandatoDto
{
    public DateTime DataEncerramento { get; init; }
    public string? Motivo { get; init; }
}

public record MandatoConselheiroDto
{
    public int Mandato { get; init; }
    public DateTime DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
    public string? Cargo { get; init; }
    public string? MotivoEncerramento { get; init; }
}

public record ComposicaoConselhoDto
{
    public int MandatoAtual { get; init; }
    public int TotalConselheiros { get; init; }
    public int TotalTitulares { get; init; }
    public int TotalSuplentes { get; init; }
    public List<ConselheiroDto> Diretoria { get; init; } = new();
    public List<ConselheiroDto> Conselheiros { get; init; } = new();
    public List<ConselheiroDto> Suplentes { get; init; } = new();
}

public enum TipoConselheiro
{
    Federal = 0,
    Estadual = 1
}

public enum StatusConselheiro
{
    Ativo = 0,
    Afastado = 1,
    Suspenso = 2,
    MandatoEncerrado = 3,
    Renunciou = 4,
    Falecido = 5
}

// Interface do servico (a ser implementada)
public interface IConselheiroService
{
    Task<IEnumerable<ConselheiroDto>> GetAllAsync(StatusConselheiro? status, TipoConselheiro? tipo, Guid? regionalId, CancellationToken cancellationToken = default);
    Task<ConselheiroDetalheDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<ConselheiroDto>> GetByRegionalAsync(Guid regionalId, CancellationToken cancellationToken = default);
    Task<IEnumerable<ConselheiroDto>> GetByMandatoAsync(int mandato, CancellationToken cancellationToken = default);
    Task<ConselheiroDetalheDto> CreateAsync(CreateConselheiroDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<ConselheiroDetalheDto> UpdateAsync(Guid id, UpdateConselheiroDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<ConselheiroDto>> EmpossarAsync(Guid eleicaoId, EmpossarConselheirosDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<ConselheiroDetalheDto> AfastarAsync(Guid id, AfastarConselheiroDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<ConselheiroDetalheDto> ReintegrarAsync(Guid id, Guid userId, CancellationToken cancellationToken = default);
    Task<ConselheiroDetalheDto> RenovarMandatoAsync(Guid id, RenovarMandatoDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<ConselheiroDetalheDto> EncerrarMandatoAsync(Guid id, EncerrarMandatoDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<ComposicaoConselhoDto> GetComposicaoAsync(Guid? regionalId, CancellationToken cancellationToken = default);
}
