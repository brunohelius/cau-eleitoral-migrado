using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Application.DTOs;

namespace CAU.Eleitoral.Api.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class ChapaController : BaseController
{
    private readonly IChapaService _chapaService;
    private readonly ILogger<ChapaController> _logger;

    public ChapaController(IChapaService chapaService, ILogger<ChapaController> logger)
    {
        _chapaService = chapaService;
        _logger = logger;
    }

    /// <summary>
    /// Lista todas as chapas
    /// </summary>
    [HttpGet]
    [AllowAnonymous]
    public async Task<ActionResult<IEnumerable<ChapaDto>>> GetAll([FromQuery] Guid? eleicaoId)
    {
        try
        {
            var chapas = await _chapaService.GetAllAsync(eleicaoId);
            return Ok(chapas);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar chapas");
            return InternalError("Erro ao listar chapas");
        }
    }

    /// <summary>
    /// Obtem uma chapa pelo ID
    /// </summary>
    [HttpGet("{id:guid}")]
    [AllowAnonymous]
    public async Task<ActionResult<ChapaDto>> GetById(Guid id)
    {
        try
        {
            var chapa = await _chapaService.GetByIdAsync(id);
            if (chapa == null)
                return NotFound("Chapa nao encontrada");

            return Ok(chapa);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter chapa {Id}", id);
            return InternalError("Erro ao obter chapa");
        }
    }

    /// <summary>
    /// Lista chapas por eleicao
    /// </summary>
    [HttpGet("eleicao/{eleicaoId:guid}")]
    [AllowAnonymous]
    public async Task<ActionResult<IEnumerable<ChapaDto>>> GetByEleicao(Guid eleicaoId)
    {
        try
        {
            var chapas = await _chapaService.GetByEleicaoAsync(eleicaoId);
            return Ok(chapas);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar chapas da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao listar chapas da eleicao");
        }
    }

    /// <summary>
    /// Cria uma nova chapa
    /// </summary>
    [HttpPost]
    public async Task<ActionResult<ChapaDto>> Create([FromBody] CreateChapaDto dto)
    {
        try
        {
            var chapa = await _chapaService.CreateAsync(dto);
            return CreatedAtAction(nameof(GetById), new { id = chapa.Id }, chapa);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ex.Message);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao criar chapa");
            return InternalError("Erro ao criar chapa");
        }
    }

    /// <summary>
    /// Atualiza uma chapa existente
    /// </summary>
    [HttpPut("{id:guid}")]
    public async Task<ActionResult<ChapaDto>> Update(Guid id, [FromBody] UpdateChapaDto dto)
    {
        try
        {
            var chapa = await _chapaService.UpdateAsync(id, dto);
            return Ok(chapa);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ex.Message);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar chapa {Id}", id);
            return InternalError("Erro ao atualizar chapa");
        }
    }

    /// <summary>
    /// Remove uma chapa
    /// </summary>
    [HttpDelete("{id:guid}")]
    public async Task<IActionResult> Delete(Guid id)
    {
        try
        {
            await _chapaService.DeleteAsync(id);
            return NoContent();
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ex.Message);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao excluir chapa {Id}", id);
            return InternalError("Erro ao excluir chapa");
        }
    }

    /// <summary>
    /// Submete uma chapa para analise
    /// </summary>
    [HttpPost("{id:guid}/submeter")]
    public async Task<ActionResult<ChapaDto>> Submeter(Guid id)
    {
        try
        {
            var chapa = await _chapaService.SubmeterParaAnaliseAsync(id);
            return Ok(chapa);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ex.Message);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao submeter chapa {Id}", id);
            return InternalError("Erro ao submeter chapa");
        }
    }

    /// <summary>
    /// Defere uma chapa
    /// </summary>
    [HttpPost("{id:guid}/deferir")]
    [Authorize(Roles = "Admin,Analista")]
    public async Task<ActionResult<ChapaDto>> Deferir(Guid id, [FromBody] DeferirChapaDto dto)
    {
        try
        {
            var userId = GetUserId();
            var chapa = await _chapaService.DeferirAsync(id, dto.Parecer, userId);
            return Ok(chapa);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ex.Message);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao deferir chapa {Id}", id);
            return InternalError("Erro ao deferir chapa");
        }
    }

    /// <summary>
    /// Indefere uma chapa
    /// </summary>
    [HttpPost("{id:guid}/indeferir")]
    [Authorize(Roles = "Admin,Analista")]
    public async Task<ActionResult<ChapaDto>> Indeferir(Guid id, [FromBody] IndeferirChapaDto dto)
    {
        try
        {
            var userId = GetUserId();
            var chapa = await _chapaService.IndeferirAsync(id, dto.Motivo, userId);
            return Ok(chapa);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ex.Message);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao indeferir chapa {Id}", id);
            return InternalError("Erro ao indeferir chapa");
        }
    }

    /// <summary>
    /// Adiciona um membro a chapa
    /// </summary>
    [HttpPost("{id:guid}/membros")]
    public async Task<ActionResult<MembroChapaDto>> AddMembro(Guid id, [FromBody] CreateMembroChapaDto dto)
    {
        try
        {
            var membro = await _chapaService.AddMembroAsync(id, dto);
            return CreatedAtAction(nameof(GetById), new { id }, membro);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ex.Message);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao adicionar membro a chapa {Id}", id);
            return InternalError("Erro ao adicionar membro a chapa");
        }
    }

    /// <summary>
    /// Remove um membro da chapa
    /// </summary>
    [HttpDelete("{id:guid}/membros/{membroId:guid}")]
    public async Task<IActionResult> RemoveMembro(Guid id, Guid membroId)
    {
        try
        {
            await _chapaService.RemoveMembroAsync(id, membroId);
            return NoContent();
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ex.Message);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao remover membro {MembroId} da chapa {Id}", membroId, id);
            return InternalError("Erro ao remover membro da chapa");
        }
    }
}

public record DeferirChapaDto(string Parecer);
public record IndeferirChapaDto(string Motivo);
