using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Application.DTOs.Chapas;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para gerenciamento de chapas eleitorais
/// </summary>
[ApiController]
[Route("api/chapas")]
[Authorize]
[Produces("application/json")]
public class ChapasController : BaseController
{
    private readonly IChapaService _chapaService;
    private readonly ILogger<ChapasController> _logger;

    public ChapasController(IChapaService chapaService, ILogger<ChapasController> logger)
    {
        _chapaService = chapaService;
        _logger = logger;
    }

    /// <summary>
    /// Lista todas as chapas com paginacao e filtros
    /// </summary>
    /// <param name="eleicaoId">Filtrar por eleicao (opcional)</param>
    /// <param name="status">Filtrar por status (opcional)</param>
    /// <param name="search">Buscar por nome ou sigla (opcional)</param>
    /// <param name="page">Numero da pagina (default: 1)</param>
    /// <param name="pageSize">Tamanho da pagina (default: 10, max: 100)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista paginada de chapas</returns>
    [HttpGet]
    [AllowAnonymous]
    [ProducesResponseType(typeof(PagedResultDto<ChapaDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<PagedResultDto<ChapaDto>>> GetAll(
        [FromQuery] Guid? eleicaoId,
        [FromQuery] int? status,
        [FromQuery] string? search,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 10,
        CancellationToken cancellationToken = default)
    {
        try
        {
            // Validar parametros de paginacao
            if (page < 1) page = 1;
            if (pageSize < 1) pageSize = 10;
            if (pageSize > 100) pageSize = 100;

            var filter = new ChapaFilterDto
            {
                EleicaoId = eleicaoId,
                Status = status,
                Search = search,
                Page = page,
                PageSize = pageSize
            };

            var result = await _chapaService.GetPagedAsync(filter, cancellationToken);
            return Ok(result);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar chapas");
            return InternalError("Erro ao listar chapas");
        }
    }

    /// <summary>
    /// Lista todas as chapas (sem paginacao) - para compatibilidade
    /// </summary>
    [HttpGet("all")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<ChapaDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<ChapaDto>>> GetAllSimple(
        [FromQuery] Guid? eleicaoId,
        CancellationToken cancellationToken = default)
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
    /// Obtem uma chapa pelo ID com todos os membros
    /// </summary>
    /// <param name="id">ID da chapa</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados completos da chapa</returns>
    [HttpGet("{id:guid}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(ChapaDetailDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ChapaDetailDto>> GetById(Guid id, CancellationToken cancellationToken = default)
    {
        try
        {
            var chapa = await _chapaService.GetByIdDetailedAsync(id, cancellationToken);
            if (chapa == null)
                return NotFound(new { message = "Chapa nao encontrada" });

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
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="apenasAtivas">Retornar apenas chapas ativas/deferidas</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de chapas da eleicao</returns>
    [HttpGet("eleicao/{eleicaoId:guid}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<ChapaDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<ChapaDto>>> GetByEleicao(
        Guid eleicaoId,
        [FromQuery] bool apenasAtivas = false,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var chapas = await _chapaService.GetByEleicaoAsync(eleicaoId, apenasAtivas, cancellationToken);
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
    /// <param name="dto">Dados da chapa</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Chapa criada</returns>
    [HttpPost]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Candidato")]
    [ProducesResponseType(typeof(ChapaDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    [ProducesResponseType(StatusCodes.Status401Unauthorized)]
    [ProducesResponseType(StatusCodes.Status403Forbidden)]
    public async Task<ActionResult<ChapaDto>> Create(
        [FromBody] CreateChapaDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            if (!ModelState.IsValid)
                return BadRequest(ModelState);

            var userId = GetUserId();
            var chapa = await _chapaService.CreateAsync(dto, userId, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id = chapa.Id }, chapa);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
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
    /// <param name="id">ID da chapa</param>
    /// <param name="dto">Dados atualizados</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Chapa atualizada</returns>
    [HttpPut("{id:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Candidato")]
    [ProducesResponseType(typeof(ChapaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status401Unauthorized)]
    [ProducesResponseType(StatusCodes.Status403Forbidden)]
    public async Task<ActionResult<ChapaDto>> Update(
        Guid id,
        [FromBody] UpdateChapaDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            if (!ModelState.IsValid)
                return BadRequest(ModelState);

            var userId = GetUserId();
            var chapa = await _chapaService.UpdateAsync(id, dto, userId, cancellationToken);
            return Ok(chapa);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Chapa nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar chapa {Id}", id);
            return InternalError("Erro ao atualizar chapa");
        }
    }

    /// <summary>
    /// Remove uma chapa (apenas rascunhos)
    /// </summary>
    /// <param name="id">ID da chapa</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>NoContent em caso de sucesso</returns>
    [HttpDelete("{id:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Candidato")]
    [ProducesResponseType(StatusCodes.Status204NoContent)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status401Unauthorized)]
    [ProducesResponseType(StatusCodes.Status403Forbidden)]
    public async Task<IActionResult> Delete(Guid id, CancellationToken cancellationToken = default)
    {
        try
        {
            var userId = GetUserId();
            await _chapaService.DeleteAsync(id, userId, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Chapa nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao excluir chapa {Id}", id);
            return InternalError("Erro ao excluir chapa");
        }
    }

    /// <summary>
    /// Adiciona um membro a chapa
    /// </summary>
    /// <param name="id">ID da chapa</param>
    /// <param name="dto">Dados do membro</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Membro adicionado</returns>
    [HttpPost("{id:guid}/membros")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Candidato")]
    [ProducesResponseType(typeof(MembroChapaDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status401Unauthorized)]
    [ProducesResponseType(StatusCodes.Status403Forbidden)]
    public async Task<ActionResult<MembroChapaDto>> AddMembro(
        Guid id,
        [FromBody] CreateMembroChapaDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            if (!ModelState.IsValid)
                return BadRequest(ModelState);

            var userId = GetUserId();
            var membro = await _chapaService.AddMembroAsync(id, dto, userId, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id }, membro);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Chapa nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao adicionar membro a chapa {Id}", id);
            return InternalError("Erro ao adicionar membro a chapa");
        }
    }

    /// <summary>
    /// Atualiza um membro da chapa
    /// </summary>
    /// <param name="id">ID da chapa</param>
    /// <param name="membroId">ID do membro</param>
    /// <param name="dto">Dados atualizados</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Membro atualizado</returns>
    [HttpPut("{id:guid}/membros/{membroId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Candidato")]
    [ProducesResponseType(typeof(MembroChapaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status401Unauthorized)]
    [ProducesResponseType(StatusCodes.Status403Forbidden)]
    public async Task<ActionResult<MembroChapaDto>> UpdateMembro(
        Guid id,
        Guid membroId,
        [FromBody] CAU.Eleitoral.Application.DTOs.Chapas.UpdateMembroChapaDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            if (!ModelState.IsValid)
                return BadRequest(ModelState);

            var userId = GetUserId();
            var membro = await _chapaService.UpdateMembroAsync(id, membroId, dto, userId, cancellationToken);
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
            _logger.LogError(ex, "Erro ao atualizar membro {MembroId} da chapa {Id}", membroId, id);
            return InternalError("Erro ao atualizar membro");
        }
    }

    /// <summary>
    /// Remove um membro da chapa
    /// </summary>
    /// <param name="id">ID da chapa</param>
    /// <param name="membroId">ID do membro</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>NoContent em caso de sucesso</returns>
    [HttpDelete("{id:guid}/membros/{membroId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Candidato")]
    [ProducesResponseType(StatusCodes.Status204NoContent)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status401Unauthorized)]
    [ProducesResponseType(StatusCodes.Status403Forbidden)]
    public async Task<IActionResult> RemoveMembro(
        Guid id,
        Guid membroId,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var userId = GetUserId();
            await _chapaService.RemoveMembroAsync(id, membroId, userId, cancellationToken);
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
            _logger.LogError(ex, "Erro ao remover membro {MembroId} da chapa {Id}", membroId, id);
            return InternalError("Erro ao remover membro da chapa");
        }
    }

    /// <summary>
    /// Lista membros de uma chapa
    /// </summary>
    /// <param name="id">ID da chapa</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de membros</returns>
    [HttpGet("{id:guid}/membros")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<MembroChapaDto>), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<IEnumerable<MembroChapaDto>>> GetMembros(
        Guid id,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var membros = await _chapaService.GetMembrosAsync(id, cancellationToken);
            return Ok(membros);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Chapa nao encontrada" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar membros da chapa {Id}", id);
            return InternalError("Erro ao listar membros");
        }
    }

    /// <summary>
    /// Submete uma chapa para analise
    /// </summary>
    /// <param name="id">ID da chapa</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Chapa atualizada</returns>
    [HttpPost("{id:guid}/submeter")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Candidato")]
    [ProducesResponseType(typeof(ChapaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ChapaDto>> Submeter(Guid id, CancellationToken cancellationToken = default)
    {
        try
        {
            var userId = GetUserId();
            var chapa = await _chapaService.SubmeterParaAnaliseAsync(id, userId, cancellationToken);
            return Ok(chapa);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Chapa nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao submeter chapa {Id}", id);
            return InternalError("Erro ao submeter chapa");
        }
    }

    /// <summary>
    /// Inicia analise de uma chapa
    /// </summary>
    /// <param name="id">ID da chapa</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Chapa atualizada</returns>
    [HttpPost("{id:guid}/iniciar-analise")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(ChapaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ChapaDto>> IniciarAnalise(Guid id, CancellationToken cancellationToken = default)
    {
        try
        {
            var userId = GetUserId();
            var chapa = await _chapaService.IniciarAnaliseAsync(id, userId, cancellationToken);
            return Ok(chapa);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Chapa nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao iniciar analise da chapa {Id}", id);
            return InternalError("Erro ao iniciar analise");
        }
    }

    /// <summary>
    /// Defere uma chapa (aprova)
    /// </summary>
    /// <param name="id">ID da chapa</param>
    /// <param name="dto">Dados do deferimento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Chapa atualizada</returns>
    [HttpPost("{id:guid}/deferir")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(ChapaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ChapaDto>> Deferir(
        Guid id,
        [FromBody] DeferirChapaRequestDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var userId = GetUserId();
            var chapa = await _chapaService.DeferirAsync(id, dto.Parecer, userId, cancellationToken);
            return Ok(chapa);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Chapa nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao deferir chapa {Id}", id);
            return InternalError("Erro ao deferir chapa");
        }
    }

    /// <summary>
    /// Indefere uma chapa (reprova)
    /// </summary>
    /// <param name="id">ID da chapa</param>
    /// <param name="dto">Dados do indeferimento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Chapa atualizada</returns>
    [HttpPost("{id:guid}/indeferir")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(ChapaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ChapaDto>> Indeferir(
        Guid id,
        [FromBody] IndeferirChapaRequestDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            if (string.IsNullOrWhiteSpace(dto.Motivo))
                return BadRequest(new { message = "Motivo do indeferimento e obrigatorio" });

            var userId = GetUserId();
            var chapa = await _chapaService.IndeferirAsync(id, dto.Motivo, userId, cancellationToken);
            return Ok(chapa);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Chapa nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao indeferir chapa {Id}", id);
            return InternalError("Erro ao indeferir chapa");
        }
    }

    /// <summary>
    /// Solicita documentos pendentes
    /// </summary>
    /// <param name="id">ID da chapa</param>
    /// <param name="dto">Lista de documentos solicitados</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Chapa atualizada</returns>
    [HttpPost("{id:guid}/solicitar-documentos")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(ChapaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ChapaDto>> SolicitarDocumentos(
        Guid id,
        [FromBody] SolicitarDocumentosRequestDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var userId = GetUserId();
            var chapa = await _chapaService.SolicitarDocumentosAsync(id, dto.Documentos, dto.Observacao, userId, cancellationToken);
            return Ok(chapa);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Chapa nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao solicitar documentos da chapa {Id}", id);
            return InternalError("Erro ao solicitar documentos");
        }
    }

    /// <summary>
    /// Lista status disponiveis para chapas
    /// </summary>
    /// <returns>Lista de status</returns>
    [HttpGet("status")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<StatusOptionDto>), StatusCodes.Status200OK)]
    public ActionResult<IEnumerable<StatusOptionDto>> GetStatusOptions()
    {
        var status = new List<StatusOptionDto>
        {
            new(0, "Rascunho", "Chapa em elaboracao"),
            new(1, "PendenteDocumentos", "Aguardando envio de documentos"),
            new(2, "AguardandoAnalise", "Submetida para analise"),
            new(3, "EmAnalise", "Em processo de analise"),
            new(4, "Deferida", "Chapa aprovada"),
            new(5, "Indeferida", "Chapa reprovada"),
            new(6, "Impugnada", "Chapa impugnada"),
            new(7, "AguardandoJulgamento", "Aguardando julgamento"),
            new(8, "Registrada", "Chapa registrada oficialmente"),
            new(9, "Cancelada", "Chapa cancelada")
        };

        return Ok(status);
    }

    /// <summary>
    /// Lista tipos de membros disponiveis
    /// </summary>
    /// <returns>Lista de tipos de membros</returns>
    [HttpGet("tipos-membro")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<TipoMembroOptionDto>), StatusCodes.Status200OK)]
    public ActionResult<IEnumerable<TipoMembroOptionDto>> GetTiposMembroOptions()
    {
        var tipos = new List<TipoMembroOptionDto>
        {
            new(0, "Presidente", true),
            new(1, "VicePresidente", true),
            new(2, "PrimeiroSecretario", true),
            new(3, "SegundoSecretario", true),
            new(4, "PrimeiroTesoureiro", true),
            new(5, "SegundoTesoureiro", true),
            new(6, "ConselheiroTitular", true),
            new(7, "ConselheiroSuplente", false),
            new(8, "Delegado", true),
            new(9, "DelegadoSuplente", false)
        };

        return Ok(tipos);
    }

    /// <summary>
    /// Obtém estatisticas de chapas por eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Estatisticas das chapas</returns>
    [HttpGet("eleicao/{eleicaoId:guid}/estatisticas")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(ChapaEstatisticasDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<ChapaEstatisticasDto>> GetEstatisticas(
        Guid eleicaoId,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var estatisticas = await _chapaService.GetEstatisticasAsync(eleicaoId, cancellationToken);
            return Ok(estatisticas);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter estatisticas de chapas da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao obter estatisticas");
        }
    }
}

// Request DTOs
public record DeferirChapaRequestDto
{
    public string? Parecer { get; init; }
}

public record IndeferirChapaRequestDto
{
    public string Motivo { get; init; } = string.Empty;
}

public record SolicitarDocumentosRequestDto
{
    public List<int> Documentos { get; init; } = new();
    public string? Observacao { get; init; }
}

// Response DTOs
public record StatusOptionDto(int Codigo, string Nome, string Descricao);
public record TipoMembroOptionDto(int Codigo, string Nome, bool Titular);
