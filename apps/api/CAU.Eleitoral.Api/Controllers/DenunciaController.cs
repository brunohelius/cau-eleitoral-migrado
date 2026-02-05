using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para gerenciamento de denuncias eleitorais
/// </summary>
[Authorize]
public class DenunciaController : BaseController
{
    private readonly IDenunciaService _denunciaService;
    private readonly ILogger<DenunciaController> _logger;

    public DenunciaController(IDenunciaService denunciaService, ILogger<DenunciaController> logger)
    {
        _denunciaService = denunciaService;
        _logger = logger;
    }

    /// <summary>
    /// Lista todas as denuncias
    /// </summary>
    /// <param name="eleicaoId">Filtro opcional por eleicao</param>
    /// <param name="status">Filtro opcional por status</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de denuncias</returns>
    [HttpGet]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(IEnumerable<DenunciaDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<DenunciaDto>>> GetAll(
        [FromQuery] Guid? eleicaoId,
        [FromQuery] StatusDenuncia? status,
        CancellationToken cancellationToken)
    {
        try
        {
            var denuncias = await _denunciaService.GetAllAsync(eleicaoId, status, cancellationToken);
            return Ok(denuncias);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar denuncias");
            return InternalError("Erro ao listar denuncias");
        }
    }

    /// <summary>
    /// Obtem uma denuncia pelo ID
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados da denuncia</returns>
    [HttpGet("{id:guid}")]
    [ProducesResponseType(typeof(DenunciaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<DenunciaDto>> GetById(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var denuncia = await _denunciaService.GetByIdAsync(id, cancellationToken);
            if (denuncia == null)
                return NotFound(new { message = "Denuncia nao encontrada" });

            return Ok(denuncia);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter denuncia {Id}", id);
            return InternalError("Erro ao obter denuncia");
        }
    }

    /// <summary>
    /// Lista denuncias por eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de denuncias</returns>
    [HttpGet("eleicao/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(IEnumerable<DenunciaDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<DenunciaDto>>> GetByEleicao(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var denuncias = await _denunciaService.GetByEleicaoAsync(eleicaoId, cancellationToken);
            return Ok(denuncias);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar denuncias da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao listar denuncias");
        }
    }

    /// <summary>
    /// Lista denuncias do usuario logado (denunciante)
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de denuncias</returns>
    [HttpGet("minhas")]
    [ProducesResponseType(typeof(IEnumerable<DenunciaDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<DenunciaDto>>> GetMinhasDenuncias(CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var denuncias = await _denunciaService.GetByDenuncianteAsync(userId, cancellationToken);
            return Ok(denuncias);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar denuncias do usuario");
            return InternalError("Erro ao listar denuncias");
        }
    }

    /// <summary>
    /// Cria uma nova denuncia
    /// </summary>
    /// <param name="dto">Dados da denuncia</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Denuncia criada</returns>
    [HttpPost]
    [ProducesResponseType(typeof(DenunciaDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DenunciaDto>> Create([FromBody] CreateDenunciaDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var denuncia = await _denunciaService.CreateAsync(dto, userId, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id = denuncia.Id }, denuncia);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao criar denuncia");
            return InternalError("Erro ao criar denuncia");
        }
    }

    /// <summary>
    /// Atualiza uma denuncia existente
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="dto">Dados atualizados</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Denuncia atualizada</returns>
    [HttpPut("{id:guid}")]
    [ProducesResponseType(typeof(DenunciaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DenunciaDto>> Update(Guid id, [FromBody] UpdateDenunciaDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var denuncia = await _denunciaService.UpdateAsync(id, dto, cancellationToken);
            return Ok(denuncia);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Denuncia nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar denuncia {Id}", id);
            return InternalError("Erro ao atualizar denuncia");
        }
    }

    /// <summary>
    /// Remove uma denuncia
    /// </summary>
    /// <param name="id">ID da denuncia</param>
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
            await _denunciaService.DeleteAsync(id, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Denuncia nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao excluir denuncia {Id}", id);
            return InternalError("Erro ao excluir denuncia");
        }
    }

    /// <summary>
    /// Aceita a admissibilidade de uma denuncia
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="request">Dados do parecer</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Denuncia atualizada</returns>
    [HttpPost("{id:guid}/aceitar-admissibilidade")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(DenunciaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DenunciaDto>> AceitarAdmissibilidade(
        Guid id,
        [FromBody] ParecerRequest request,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var denuncia = await _denunciaService.AceitarAdmissibilidadeAsync(id, request.Parecer, userId, cancellationToken);
            return Ok(denuncia);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao aceitar admissibilidade da denuncia {Id}", id);
            return InternalError("Erro ao aceitar admissibilidade");
        }
    }

    /// <summary>
    /// Rejeita a admissibilidade de uma denuncia
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="request">Dados do motivo</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Denuncia atualizada</returns>
    [HttpPost("{id:guid}/rejeitar-admissibilidade")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(DenunciaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DenunciaDto>> RejeitarAdmissibilidade(
        Guid id,
        [FromBody] MotivoRequest request,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var denuncia = await _denunciaService.RejeitarAdmissibilidadeAsync(id, request.Motivo, userId, cancellationToken);
            return Ok(denuncia);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao rejeitar admissibilidade da denuncia {Id}", id);
            return InternalError("Erro ao rejeitar admissibilidade");
        }
    }

    /// <summary>
    /// Solicita defesa do denunciado
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="request">Dados da solicitacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Denuncia atualizada</returns>
    [HttpPost("{id:guid}/solicitar-defesa")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(DenunciaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DenunciaDto>> SolicitarDefesa(
        Guid id,
        [FromBody] SolicitarDefesaRequest request,
        CancellationToken cancellationToken)
    {
        try
        {
            var denuncia = await _denunciaService.SolicitarDefesaAsync(id, request.PrazoEmDias, cancellationToken);
            return Ok(denuncia);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao solicitar defesa da denuncia {Id}", id);
            return InternalError("Erro ao solicitar defesa");
        }
    }

    /// <summary>
    /// Registra a defesa apresentada
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="dto">Dados da defesa</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Denuncia atualizada</returns>
    [HttpPost("{id:guid}/apresentar-defesa")]
    [ProducesResponseType(typeof(DenunciaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DenunciaDto>> ApresentarDefesa(
        Guid id,
        [FromBody] ApresentarDefesaDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var denuncia = await _denunciaService.ApresentarDefesaAsync(id, dto, userId, cancellationToken);
            return Ok(denuncia);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao apresentar defesa da denuncia {Id}", id);
            return InternalError("Erro ao apresentar defesa");
        }
    }

    /// <summary>
    /// Envia denuncia para julgamento
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Denuncia atualizada</returns>
    [HttpPost("{id:guid}/enviar-julgamento")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(DenunciaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DenunciaDto>> EnviarParaJulgamento(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var denuncia = await _denunciaService.EnviarParaJulgamentoAsync(id, cancellationToken);
            return Ok(denuncia);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao enviar denuncia para julgamento {Id}", id);
            return InternalError("Erro ao enviar para julgamento");
        }
    }

    /// <summary>
    /// Registra o resultado do julgamento
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="dto">Dados do julgamento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Denuncia atualizada</returns>
    [HttpPost("{id:guid}/julgar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(DenunciaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DenunciaDto>> Julgar(
        Guid id,
        [FromBody] JulgarDenunciaDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var denuncia = await _denunciaService.JulgarAsync(id, dto, userId, cancellationToken);
            return Ok(denuncia);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao julgar denuncia {Id}", id);
            return InternalError("Erro ao julgar denuncia");
        }
    }

    /// <summary>
    /// Arquiva uma denuncia
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="request">Dados do arquivamento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Denuncia arquivada</returns>
    [HttpPost("{id:guid}/arquivar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(DenunciaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DenunciaDto>> Arquivar(
        Guid id,
        [FromBody] MotivoRequest request,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var denuncia = await _denunciaService.ArquivarAsync(id, request.Motivo, userId, cancellationToken);
            return Ok(denuncia);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao arquivar denuncia {Id}", id);
            return InternalError("Erro ao arquivar denuncia");
        }
    }

    /// <summary>
    /// Adiciona arquivo de prova a denuncia
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="dto">Dados do arquivo</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Arquivo adicionado</returns>
    [HttpPost("{id:guid}/arquivos")]
    [ProducesResponseType(typeof(ArquivoDenunciaDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ArquivoDenunciaDto>> AddArquivo(
        Guid id,
        [FromBody] CreateArquivoDenunciaDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var arquivo = await _denunciaService.AddArquivoAsync(id, dto, userId, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id }, arquivo);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao adicionar arquivo a denuncia {Id}", id);
            return InternalError("Erro ao adicionar arquivo");
        }
    }

    /// <summary>
    /// Remove arquivo de prova da denuncia
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="arquivoId">ID do arquivo</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>NoContent em caso de sucesso</returns>
    [HttpDelete("{id:guid}/arquivos/{arquivoId:guid}")]
    [ProducesResponseType(StatusCodes.Status204NoContent)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<IActionResult> RemoveArquivo(Guid id, Guid arquivoId, CancellationToken cancellationToken)
    {
        try
        {
            await _denunciaService.RemoveArquivoAsync(id, arquivoId, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Arquivo nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao remover arquivo {ArquivoId} da denuncia {Id}", arquivoId, id);
            return InternalError("Erro ao remover arquivo");
        }
    }
}

// DTOs para Denuncia
public record DenunciaDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public Guid DenuncianteId { get; init; }
    public string DenuncianteNome { get; init; } = string.Empty;
    public Guid? DenunciadoId { get; init; }
    public string? DenunciadoNome { get; init; }
    public Guid? ChapaId { get; init; }
    public string? ChapaNome { get; init; }
    public TipoDenuncia Tipo { get; init; }
    public StatusDenuncia Status { get; init; }
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
    public DateTime? PrazoDefesa { get; init; }
    public StatusDefesa StatusDefesa { get; init; }
    public string? DefesaTexto { get; init; }
    public DateTime? DataDefesa { get; init; }
    public string? Parecer { get; init; }
    public string? Decisao { get; init; }
    public DateTime? DataJulgamento { get; init; }
    public List<ArquivoDenunciaDto> Arquivos { get; init; } = new();
    public DateTime CreatedAt { get; init; }
    public DateTime? UpdatedAt { get; init; }
}

public record CreateDenunciaDto
{
    public Guid EleicaoId { get; init; }
    public Guid? DenunciadoId { get; init; }
    public Guid? ChapaId { get; init; }
    public TipoDenuncia Tipo { get; init; }
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
}

public record UpdateDenunciaDto
{
    public TipoDenuncia? Tipo { get; init; }
    public string? Descricao { get; init; }
    public string? Fundamentacao { get; init; }
}

public record ApresentarDefesaDto
{
    public string Texto { get; init; } = string.Empty;
    public List<CreateArquivoDenunciaDto>? Arquivos { get; init; }
}

public record JulgarDenunciaDto
{
    public StatusDenuncia Resultado { get; init; }
    public string Decisao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
}

public record ArquivoDenunciaDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string Tipo { get; init; } = string.Empty;
    public string Url { get; init; } = string.Empty;
    public long Tamanho { get; init; }
    public DateTime CreatedAt { get; init; }
}

public record CreateArquivoDenunciaDto
{
    public string Nome { get; init; } = string.Empty;
    public TipoArquivoDenuncia Tipo { get; init; }
    public string Url { get; init; } = string.Empty;
    public long Tamanho { get; init; }
}

public record ParecerRequest(string Parecer);
public record MotivoRequest(string Motivo);
public record SolicitarDefesaRequest(int PrazoEmDias);

// Interface do servico (a ser implementada)
public interface IDenunciaService
{
    Task<IEnumerable<DenunciaDto>> GetAllAsync(Guid? eleicaoId, StatusDenuncia? status, CancellationToken cancellationToken = default);
    Task<DenunciaDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<DenunciaDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<DenunciaDto>> GetByDenuncianteAsync(Guid denuncianteId, CancellationToken cancellationToken = default);
    Task<DenunciaDto> CreateAsync(CreateDenunciaDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<DenunciaDto> UpdateAsync(Guid id, UpdateDenunciaDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);
    Task<DenunciaDto> AceitarAdmissibilidadeAsync(Guid id, string parecer, Guid userId, CancellationToken cancellationToken = default);
    Task<DenunciaDto> RejeitarAdmissibilidadeAsync(Guid id, string motivo, Guid userId, CancellationToken cancellationToken = default);
    Task<DenunciaDto> SolicitarDefesaAsync(Guid id, int prazoEmDias, CancellationToken cancellationToken = default);
    Task<DenunciaDto> ApresentarDefesaAsync(Guid id, ApresentarDefesaDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<DenunciaDto> EnviarParaJulgamentoAsync(Guid id, CancellationToken cancellationToken = default);
    Task<DenunciaDto> JulgarAsync(Guid id, JulgarDenunciaDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<DenunciaDto> ArquivarAsync(Guid id, string motivo, Guid userId, CancellationToken cancellationToken = default);
    Task<ArquivoDenunciaDto> AddArquivoAsync(Guid denunciaId, CreateArquivoDenunciaDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task RemoveArquivoAsync(Guid denunciaId, Guid arquivoId, CancellationToken cancellationToken = default);
}
