using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para gerenciamento de documentos
/// </summary>
[Authorize]
public class DocumentoController : BaseController
{
    private readonly IDocumentoService _documentoService;
    private readonly ILogger<DocumentoController> _logger;

    public DocumentoController(IDocumentoService documentoService, ILogger<DocumentoController> logger)
    {
        _documentoService = documentoService;
        _logger = logger;
    }

    /// <summary>
    /// Lista todos os documentos
    /// </summary>
    /// <param name="eleicaoId">Filtro opcional por eleicao</param>
    /// <param name="tipo">Filtro opcional por tipo</param>
    /// <param name="categoria">Filtro opcional por categoria</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de documentos</returns>
    [HttpGet]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<DocumentoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<DocumentoDto>>> GetAll(
        [FromQuery] Guid? eleicaoId,
        [FromQuery] TipoDocumento? tipo,
        [FromQuery] CategoriaDocumento? categoria,
        CancellationToken cancellationToken)
    {
        try
        {
            var documentos = await _documentoService.GetAllAsync(eleicaoId, tipo, categoria, cancellationToken);
            return Ok(documentos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar documentos");
            return InternalError("Erro ao listar documentos");
        }
    }

    /// <summary>
    /// Obtem um documento pelo ID
    /// </summary>
    /// <param name="id">ID do documento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados do documento</returns>
    [HttpGet("{id:guid}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(DocumentoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<DocumentoDto>> GetById(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var documento = await _documentoService.GetByIdAsync(id, cancellationToken);
            if (documento == null)
                return NotFound(new { message = "Documento nao encontrado" });

            return Ok(documento);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter documento {Id}", id);
            return InternalError("Erro ao obter documento");
        }
    }

    /// <summary>
    /// Lista documentos por eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de documentos</returns>
    [HttpGet("eleicao/{eleicaoId:guid}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<DocumentoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<DocumentoDto>>> GetByEleicao(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var documentos = await _documentoService.GetByEleicaoAsync(eleicaoId, cancellationToken);
            return Ok(documentos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar documentos da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao listar documentos");
        }
    }

    /// <summary>
    /// Lista documentos publicados
    /// </summary>
    /// <param name="eleicaoId">Filtro opcional por eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de documentos publicados</returns>
    [HttpGet("publicados")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<DocumentoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<DocumentoDto>>> GetPublicados(
        [FromQuery] Guid? eleicaoId,
        CancellationToken cancellationToken)
    {
        try
        {
            var documentos = await _documentoService.GetPublicadosAsync(eleicaoId, cancellationToken);
            return Ok(documentos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar documentos publicados");
            return InternalError("Erro ao listar documentos");
        }
    }

    /// <summary>
    /// Faz upload de um novo documento
    /// </summary>
    /// <param name="dto">Dados do documento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Documento criado</returns>
    [HttpPost]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(DocumentoDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DocumentoDto>> Upload([FromBody] CreateDocumentoDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var documento = await _documentoService.CreateAsync(dto, userId, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id = documento.Id }, documento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao fazer upload de documento");
            return InternalError("Erro ao fazer upload");
        }
    }

    /// <summary>
    /// Faz upload de arquivo
    /// </summary>
    /// <param name="file">Arquivo</param>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="tipo">Tipo do documento</param>
    /// <param name="categoria">Categoria do documento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Documento criado</returns>
    [HttpPost("upload")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(DocumentoDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DocumentoDto>> UploadFile(
        IFormFile file,
        [FromQuery] Guid eleicaoId,
        [FromQuery] TipoDocumento tipo,
        [FromQuery] CategoriaDocumento categoria,
        CancellationToken cancellationToken)
    {
        try
        {
            if (file == null || file.Length == 0)
                return BadRequest(new { message = "Arquivo nao enviado" });

            var userId = GetUserId();
            var documento = await _documentoService.UploadAsync(file, eleicaoId, tipo, categoria, userId, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id = documento.Id }, documento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao fazer upload de arquivo");
            return InternalError("Erro ao fazer upload");
        }
    }

    /// <summary>
    /// Atualiza um documento existente
    /// </summary>
    /// <param name="id">ID do documento</param>
    /// <param name="dto">Dados atualizados</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Documento atualizado</returns>
    [HttpPut("{id:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(DocumentoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DocumentoDto>> Update(Guid id, [FromBody] UpdateDocumentoDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var documento = await _documentoService.UpdateAsync(id, dto, cancellationToken);
            return Ok(documento);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Documento nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar documento {Id}", id);
            return InternalError("Erro ao atualizar documento");
        }
    }

    /// <summary>
    /// Remove um documento
    /// </summary>
    /// <param name="id">ID do documento</param>
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
            await _documentoService.DeleteAsync(id, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Documento nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao excluir documento {Id}", id);
            return InternalError("Erro ao excluir documento");
        }
    }

    /// <summary>
    /// Faz download de um documento
    /// </summary>
    /// <param name="id">ID do documento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Arquivo do documento</returns>
    [HttpGet("{id:guid}/download")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(FileContentResult), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<IActionResult> Download(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var (content, contentType, fileName) = await _documentoService.DownloadAsync(id, cancellationToken);
            return File(content, contentType, fileName);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Documento nao encontrado" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao fazer download do documento {Id}", id);
            return InternalError("Erro ao fazer download");
        }
    }

    /// <summary>
    /// Envia documento para revisao
    /// </summary>
    /// <param name="id">ID do documento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Documento atualizado</returns>
    [HttpPost("{id:guid}/enviar-revisao")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(DocumentoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DocumentoDto>> EnviarParaRevisao(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var documento = await _documentoService.EnviarParaRevisaoAsync(id, cancellationToken);
            return Ok(documento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao enviar documento para revisao {Id}", id);
            return InternalError("Erro ao enviar para revisao");
        }
    }

    /// <summary>
    /// Aprova um documento
    /// </summary>
    /// <param name="id">ID do documento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Documento aprovado</returns>
    [HttpPost("{id:guid}/aprovar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(DocumentoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DocumentoDto>> Aprovar(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var documento = await _documentoService.AprovarAsync(id, userId, cancellationToken);
            return Ok(documento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao aprovar documento {Id}", id);
            return InternalError("Erro ao aprovar documento");
        }
    }

    /// <summary>
    /// Publica um documento
    /// </summary>
    /// <param name="id">ID do documento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Documento publicado</returns>
    [HttpPost("{id:guid}/publicar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(DocumentoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DocumentoDto>> Publicar(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var documento = await _documentoService.PublicarAsync(id, userId, cancellationToken);
            return Ok(documento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao publicar documento {Id}", id);
            return InternalError("Erro ao publicar documento");
        }
    }

    /// <summary>
    /// Revoga um documento
    /// </summary>
    /// <param name="id">ID do documento</param>
    /// <param name="request">Dados da revogacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Documento revogado</returns>
    [HttpPost("{id:guid}/revogar")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(DocumentoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DocumentoDto>> Revogar(
        Guid id,
        [FromBody] MotivoRequest request,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var documento = await _documentoService.RevogarAsync(id, request.Motivo, userId, cancellationToken);
            return Ok(documento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao revogar documento {Id}", id);
            return InternalError("Erro ao revogar documento");
        }
    }

    /// <summary>
    /// Arquiva um documento
    /// </summary>
    /// <param name="id">ID do documento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Documento arquivado</returns>
    [HttpPost("{id:guid}/arquivar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(DocumentoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DocumentoDto>> Arquivar(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var documento = await _documentoService.ArquivarAsync(id, cancellationToken);
            return Ok(documento);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao arquivar documento {Id}", id);
            return InternalError("Erro ao arquivar documento");
        }
    }
}

// DTOs para Documento
public record DocumentoDto
{
    public Guid Id { get; init; }
    public Guid? EleicaoId { get; init; }
    public string? EleicaoNome { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public TipoDocumento Tipo { get; init; }
    public CategoriaDocumento Categoria { get; init; }
    public StatusDocumento Status { get; init; }
    public string? Numero { get; init; }
    public DateTime? DataDocumento { get; init; }
    public DateTime? DataPublicacao { get; init; }
    public DateTime? DataRevogacao { get; init; }
    public string? MotivoRevogacao { get; init; }
    public string? Url { get; init; }
    public string? NomeArquivo { get; init; }
    public string? TipoArquivo { get; init; }
    public long? Tamanho { get; init; }
    public Guid? CriadoPor { get; init; }
    public string? CriadoPorNome { get; init; }
    public DateTime CreatedAt { get; init; }
    public DateTime? UpdatedAt { get; init; }
}

public record CreateDocumentoDto
{
    public Guid? EleicaoId { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public TipoDocumento Tipo { get; init; }
    public CategoriaDocumento Categoria { get; init; }
    public string? Numero { get; init; }
    public DateTime? DataDocumento { get; init; }
    public string? Url { get; init; }
}

public record UpdateDocumentoDto
{
    public string? Titulo { get; init; }
    public string? Descricao { get; init; }
    public TipoDocumento? Tipo { get; init; }
    public CategoriaDocumento? Categoria { get; init; }
    public string? Numero { get; init; }
    public DateTime? DataDocumento { get; init; }
}

// Interface do servico (a ser implementada)
public interface IDocumentoService
{
    Task<IEnumerable<DocumentoDto>> GetAllAsync(Guid? eleicaoId, TipoDocumento? tipo, CategoriaDocumento? categoria, CancellationToken cancellationToken = default);
    Task<DocumentoDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<DocumentoDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<DocumentoDto>> GetPublicadosAsync(Guid? eleicaoId, CancellationToken cancellationToken = default);
    Task<DocumentoDto> CreateAsync(CreateDocumentoDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<DocumentoDto> UploadAsync(IFormFile file, Guid eleicaoId, TipoDocumento tipo, CategoriaDocumento categoria, Guid userId, CancellationToken cancellationToken = default);
    Task<DocumentoDto> UpdateAsync(Guid id, UpdateDocumentoDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);
    Task<(byte[] Content, string ContentType, string FileName)> DownloadAsync(Guid id, CancellationToken cancellationToken = default);
    Task<DocumentoDto> EnviarParaRevisaoAsync(Guid id, CancellationToken cancellationToken = default);
    Task<DocumentoDto> AprovarAsync(Guid id, Guid userId, CancellationToken cancellationToken = default);
    Task<DocumentoDto> PublicarAsync(Guid id, Guid userId, CancellationToken cancellationToken = default);
    Task<DocumentoDto> RevogarAsync(Guid id, string motivo, Guid userId, CancellationToken cancellationToken = default);
    Task<DocumentoDto> ArquivarAsync(Guid id, CancellationToken cancellationToken = default);
}
