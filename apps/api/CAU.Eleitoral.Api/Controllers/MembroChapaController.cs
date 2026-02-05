using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Application.DTOs;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para gerenciamento de membros de chapas
/// </summary>
[Authorize]
public class MembroChapaController : BaseController
{
    private readonly IMembroChapaService _membroChapaService;
    private readonly ILogger<MembroChapaController> _logger;

    public MembroChapaController(IMembroChapaService membroChapaService, ILogger<MembroChapaController> logger)
    {
        _membroChapaService = membroChapaService;
        _logger = logger;
    }

    /// <summary>
    /// Lista todos os membros de uma chapa
    /// </summary>
    /// <param name="chapaId">ID da chapa</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de membros</returns>
    [HttpGet("chapa/{chapaId:guid}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<MembroChapaDetalheDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<MembroChapaDetalheDto>>> GetByChapa(Guid chapaId, CancellationToken cancellationToken)
    {
        try
        {
            var membros = await _membroChapaService.GetByChapaAsync(chapaId, cancellationToken);
            return Ok(membros);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar membros da chapa {ChapaId}", chapaId);
            return InternalError("Erro ao listar membros");
        }
    }

    /// <summary>
    /// Obtem um membro pelo ID
    /// </summary>
    /// <param name="id">ID do membro</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados do membro</returns>
    [HttpGet("{id:guid}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(MembroChapaDetalheDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<MembroChapaDetalheDto>> GetById(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var membro = await _membroChapaService.GetByIdAsync(id, cancellationToken);
            if (membro == null)
                return NotFound(new { message = "Membro nao encontrado" });

            return Ok(membro);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter membro {Id}", id);
            return InternalError("Erro ao obter membro");
        }
    }

    /// <summary>
    /// Lista membros por profissional
    /// </summary>
    /// <param name="profissionalId">ID do profissional</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de chapas em que o profissional participa</returns>
    [HttpGet("profissional/{profissionalId:guid}")]
    [ProducesResponseType(typeof(IEnumerable<MembroChapaDetalheDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<MembroChapaDetalheDto>>> GetByProfissional(Guid profissionalId, CancellationToken cancellationToken)
    {
        try
        {
            var membros = await _membroChapaService.GetByProfissionalAsync(profissionalId, cancellationToken);
            return Ok(membros);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar participacoes do profissional {ProfissionalId}", profissionalId);
            return InternalError("Erro ao listar participacoes");
        }
    }

    /// <summary>
    /// Adiciona um membro a uma chapa
    /// </summary>
    /// <param name="dto">Dados do membro</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Membro criado</returns>
    [HttpPost]
    [ProducesResponseType(typeof(MembroChapaDetalheDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<MembroChapaDetalheDto>> Create([FromBody] CreateMembroChapaDetalheDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var membro = await _membroChapaService.CreateAsync(dto, userId, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id = membro.Id }, membro);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao adicionar membro a chapa");
            return InternalError("Erro ao adicionar membro");
        }
    }

    /// <summary>
    /// Atualiza um membro
    /// </summary>
    /// <param name="id">ID do membro</param>
    /// <param name="dto">Dados atualizados</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Membro atualizado</returns>
    [HttpPut("{id:guid}")]
    [ProducesResponseType(typeof(MembroChapaDetalheDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<MembroChapaDetalheDto>> Update(Guid id, [FromBody] UpdateMembroChapaDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var membro = await _membroChapaService.UpdateAsync(id, dto, cancellationToken);
            return Ok(membro);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Membro nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar membro {Id}", id);
            return InternalError("Erro ao atualizar membro");
        }
    }

    /// <summary>
    /// Remove um membro da chapa
    /// </summary>
    /// <param name="id">ID do membro</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>NoContent em caso de sucesso</returns>
    [HttpDelete("{id:guid}")]
    [ProducesResponseType(StatusCodes.Status204NoContent)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<IActionResult> Delete(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            await _membroChapaService.DeleteAsync(id, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Membro nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao remover membro {Id}", id);
            return InternalError("Erro ao remover membro");
        }
    }

    /// <summary>
    /// Reordena membros de uma chapa
    /// </summary>
    /// <param name="chapaId">ID da chapa</param>
    /// <param name="dto">Nova ordem</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de membros reordenados</returns>
    [HttpPost("chapa/{chapaId:guid}/reordenar")]
    [ProducesResponseType(typeof(IEnumerable<MembroChapaDetalheDto>), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<IEnumerable<MembroChapaDetalheDto>>> Reordenar(
        Guid chapaId,
        [FromBody] ReordenarMembrosDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var membros = await _membroChapaService.ReordenarAsync(chapaId, dto.OrdemIds, cancellationToken);
            return Ok(membros);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao reordenar membros da chapa {ChapaId}", chapaId);
            return InternalError("Erro ao reordenar membros");
        }
    }

    /// <summary>
    /// Valida elegibilidade de um membro
    /// </summary>
    /// <param name="id">ID do membro</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Resultado da validacao</returns>
    [HttpGet("{id:guid}/validar-elegibilidade")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(ValidacaoElegibilidadeDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<ValidacaoElegibilidadeDto>> ValidarElegibilidade(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var validacao = await _membroChapaService.ValidarElegibilidadeAsync(id, cancellationToken);
            return Ok(validacao);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao validar elegibilidade do membro {Id}", id);
            return InternalError("Erro ao validar elegibilidade");
        }
    }

    /// <summary>
    /// Aprova um membro
    /// </summary>
    /// <param name="id">ID do membro</param>
    /// <param name="dto">Dados da aprovacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Membro aprovado</returns>
    [HttpPost("{id:guid}/aprovar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(MembroChapaDetalheDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<MembroChapaDetalheDto>> Aprovar(
        Guid id,
        [FromBody] ParecerMembroDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var membro = await _membroChapaService.AprovarAsync(id, dto.Parecer, userId, cancellationToken);
            return Ok(membro);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao aprovar membro {Id}", id);
            return InternalError("Erro ao aprovar membro");
        }
    }

    /// <summary>
    /// Rejeita um membro
    /// </summary>
    /// <param name="id">ID do membro</param>
    /// <param name="dto">Dados da rejeicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Membro rejeitado</returns>
    [HttpPost("{id:guid}/rejeitar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(MembroChapaDetalheDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<MembroChapaDetalheDto>> Rejeitar(
        Guid id,
        [FromBody] RejeicaoMembroDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var membro = await _membroChapaService.RejeitarAsync(id, dto.Motivo, userId, cancellationToken);
            return Ok(membro);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao rejeitar membro {Id}", id);
            return InternalError("Erro ao rejeitar membro");
        }
    }

    /// <summary>
    /// Lista cargos disponiveis
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de cargos</returns>
    [HttpGet("cargos")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<CargoMembroDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<CargoMembroDto>>> GetCargos(CancellationToken cancellationToken)
    {
        try
        {
            var cargos = await _membroChapaService.GetCargosAsync(cancellationToken);
            return Ok(cargos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar cargos");
            return InternalError("Erro ao listar cargos");
        }
    }
}

// DTOs para MembroChapa
public record MembroChapaDetalheDto
{
    public Guid Id { get; init; }
    public Guid ChapaId { get; init; }
    public string ChapaNome { get; init; } = string.Empty;
    public Guid ProfissionalId { get; init; }
    public string ProfissionalNome { get; init; } = string.Empty;
    public string? ProfissionalRegistroCAU { get; init; }
    public string? ProfissionalCpf { get; init; }
    public string? ProfissionalEmail { get; init; }
    public int TipoMembro { get; init; }
    public string TipoMembroNome { get; init; } = string.Empty;
    public string? Cargo { get; init; }
    public int Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public int Ordem { get; init; }
    public string? Parecer { get; init; }
    public string? MotivoRejeicao { get; init; }
    public DateTime? DataAnalise { get; init; }
    public Guid? AnalisadoPor { get; init; }
    public string? AnalisadoPorNome { get; init; }
    public DateTime CreatedAt { get; init; }
    public DateTime? UpdatedAt { get; init; }
}

public record CreateMembroChapaDetalheDto
{
    public Guid ChapaId { get; init; }
    public Guid ProfissionalId { get; init; }
    public int TipoMembro { get; init; }
    public string? Cargo { get; init; }
    public int? Ordem { get; init; }
}

public record UpdateMembroChapaDto
{
    public int? TipoMembro { get; init; }
    public string? Cargo { get; init; }
    public int? Ordem { get; init; }
}

public record ReordenarMembrosDto
{
    public List<Guid> OrdemIds { get; init; } = new();
}

public record ValidacaoElegibilidadeDto
{
    public Guid MembroId { get; init; }
    public bool Elegivel { get; init; }
    public List<string> Pendencias { get; init; } = new();
    public List<string> Impedimentos { get; init; } = new();
    public bool RegistroAtivo { get; init; }
    public bool AdimplenteAnuidade { get; init; }
    public bool SemDebitos { get; init; }
    public bool SemPenalidadesAtivas { get; init; }
}

public record ParecerMembroDto
{
    public string? Parecer { get; init; }
}

public record RejeicaoMembroDto
{
    public string Motivo { get; init; } = string.Empty;
}

public record CargoMembroDto
{
    public int Codigo { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public bool Principal { get; init; }
}

// Interface do servico (a ser implementada)
public interface IMembroChapaService
{
    Task<IEnumerable<MembroChapaDetalheDto>> GetByChapaAsync(Guid chapaId, CancellationToken cancellationToken = default);
    Task<MembroChapaDetalheDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<MembroChapaDetalheDto>> GetByProfissionalAsync(Guid profissionalId, CancellationToken cancellationToken = default);
    Task<MembroChapaDetalheDto> CreateAsync(CreateMembroChapaDetalheDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<MembroChapaDetalheDto> UpdateAsync(Guid id, UpdateMembroChapaDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<MembroChapaDetalheDto>> ReordenarAsync(Guid chapaId, List<Guid> ordemIds, CancellationToken cancellationToken = default);
    Task<ValidacaoElegibilidadeDto> ValidarElegibilidadeAsync(Guid id, CancellationToken cancellationToken = default);
    Task<MembroChapaDetalheDto> AprovarAsync(Guid id, string? parecer, Guid userId, CancellationToken cancellationToken = default);
    Task<MembroChapaDetalheDto> RejeitarAsync(Guid id, string motivo, Guid userId, CancellationToken cancellationToken = default);
    Task<IEnumerable<CargoMembroDto>> GetCargosAsync(CancellationToken cancellationToken = default);
}
