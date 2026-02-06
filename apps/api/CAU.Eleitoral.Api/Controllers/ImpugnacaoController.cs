using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Application.DTOs.Impugnacoes;
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
    /// Lista todas as impugnacoes com paginacao e filtros
    /// </summary>
    /// <param name="eleicaoId">Filtro opcional por eleicao</param>
    /// <param name="status">Filtro opcional por status</param>
    /// <param name="page">Pagina atual (default: 1)</param>
    /// <param name="pageSize">Tamanho da pagina (default: 20)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista paginada de impugnacoes</returns>
    [HttpGet]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(PaginatedResult<ImpugnacaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<PaginatedResult<ImpugnacaoDto>>> GetAll(
        [FromQuery] Guid? eleicaoId,
        [FromQuery] StatusImpugnacao? status,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 20,
        CancellationToken cancellationToken = default)
    {
        try
        {
            IEnumerable<ImpugnacaoDto> impugnacoes;

            if (eleicaoId.HasValue)
            {
                impugnacoes = await _impugnacaoService.GetByEleicaoAsync(eleicaoId.Value, cancellationToken);
            }
            else if (status.HasValue)
            {
                impugnacoes = await _impugnacaoService.GetByStatusAsync(status.Value, cancellationToken);
            }
            else
            {
                impugnacoes = await _impugnacaoService.GetAllAsync(cancellationToken);
            }

            // Apply pagination
            var totalCount = impugnacoes.Count();
            var paginatedItems = impugnacoes
                .Skip((page - 1) * pageSize)
                .Take(pageSize)
                .ToList();

            var result = new PaginatedResult<ImpugnacaoDto>
            {
                Items = paginatedItems,
                TotalCount = totalCount,
                Page = page,
                PageSize = pageSize,
                TotalPages = (int)Math.Ceiling((double)totalCount / pageSize)
            };

            return Ok(result);
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
    public async Task<ActionResult<ImpugnacaoDto>> GetById(Guid id, CancellationToken cancellationToken = default)
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
    /// Obtem uma impugnacao pelo protocolo
    /// </summary>
    /// <param name="protocolo">Protocolo da impugnacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados da impugnacao</returns>
    [HttpGet("protocolo/{protocolo}")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ImpugnacaoDto>> GetByProtocolo(string protocolo, CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacao = await _impugnacaoService.GetByProtocoloAsync(protocolo, cancellationToken);
            if (impugnacao == null)
                return NotFound(new { message = "Impugnacao nao encontrada" });

            return Ok(impugnacao);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter impugnacao por protocolo {Protocolo}", protocolo);
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
    public async Task<ActionResult<IEnumerable<ImpugnacaoDto>>> GetByEleicao(Guid eleicaoId, CancellationToken cancellationToken = default)
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
    /// Lista impugnacoes por chapa impugnada
    /// </summary>
    /// <param name="chapaId">ID da chapa</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de impugnacoes</returns>
    [HttpGet("chapa/{chapaId:guid}")]
    [ProducesResponseType(typeof(IEnumerable<ImpugnacaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<ImpugnacaoDto>>> GetByChapa(Guid chapaId, CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacoes = await _impugnacaoService.GetByChapaImpugnadaAsync(chapaId, cancellationToken);
            return Ok(impugnacoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar impugnacoes da chapa {ChapaId}", chapaId);
            return InternalError("Erro ao listar impugnacoes");
        }
    }

    /// <summary>
    /// Lista impugnacoes por status
    /// </summary>
    /// <param name="status">Status da impugnacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de impugnacoes</returns>
    [HttpGet("status/{status}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(IEnumerable<ImpugnacaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<ImpugnacaoDto>>> GetByStatus(StatusImpugnacao status, CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacoes = await _impugnacaoService.GetByStatusAsync(status, cancellationToken);
            return Ok(impugnacoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar impugnacoes por status {Status}", status);
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
    public async Task<ActionResult<ImpugnacaoDto>> Create([FromBody] CreateImpugnacaoDto dto, CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacao = await _impugnacaoService.CreateAsync(dto, cancellationToken);
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
    public async Task<ActionResult<ImpugnacaoDto>> Update(Guid id, [FromBody] UpdateImpugnacaoDto dto, CancellationToken cancellationToken = default)
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
    public async Task<IActionResult> Delete(Guid id, CancellationToken cancellationToken = default)
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
    /// Recebe a impugnacao para analise
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/receber")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> Receber(Guid id, CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacao = await _impugnacaoService.ReceberAsync(id, cancellationToken);
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
            _logger.LogError(ex, "Erro ao receber impugnacao {Id}", id);
            return InternalError("Erro ao receber impugnacao");
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
    public async Task<ActionResult<ImpugnacaoDto>> IniciarAnalise(Guid id, CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacao = await _impugnacaoService.IniciarAnaliseAsync(id, null, cancellationToken);
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
            _logger.LogError(ex, "Erro ao iniciar analise da impugnacao {Id}", id);
            return InternalError("Erro ao iniciar analise");
        }
    }

    /// <summary>
    /// Abre prazo para alegacoes
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="request">Dados do prazo</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/abrir-prazo-alegacoes")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> AbrirPrazoAlegacoes(
        Guid id,
        [FromBody] PrazoRequest request,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var prazo = DateTime.UtcNow.AddDays(request.PrazoEmDias);
            var impugnacao = await _impugnacaoService.AbrirPrazoAlegacoesAsync(id, prazo, cancellationToken);
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
            _logger.LogError(ex, "Erro ao abrir prazo de alegacoes da impugnacao {Id}", id);
            return InternalError("Erro ao abrir prazo de alegacoes");
        }
    }

    /// <summary>
    /// Registra alegacao na impugnacao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="dto">Dados da alegacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/alegacoes")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> RegistrarAlegacao(
        Guid id,
        [FromBody] CreateAlegacaoDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacao = await _impugnacaoService.RegistrarAlegacaoAsync(id, dto, cancellationToken);
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
            _logger.LogError(ex, "Erro ao registrar alegacao na impugnacao {Id}", id);
            return InternalError("Erro ao registrar alegacao");
        }
    }

    /// <summary>
    /// Lista alegacoes de uma impugnacao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de alegacoes</returns>
    [HttpGet("{id:guid}/alegacoes")]
    [ProducesResponseType(typeof(IEnumerable<AlegacaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<AlegacaoDto>>> GetAlegacoes(Guid id, CancellationToken cancellationToken = default)
    {
        try
        {
            var alegacoes = await _impugnacaoService.GetAlegacoesAsync(id, cancellationToken);
            return Ok(alegacoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar alegacoes da impugnacao {Id}", id);
            return InternalError("Erro ao listar alegacoes");
        }
    }

    /// <summary>
    /// Remove uma alegacao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="alegacaoId">ID da alegacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>NoContent em caso de sucesso</returns>
    [HttpDelete("{id:guid}/alegacoes/{alegacaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(StatusCodes.Status204NoContent)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<IActionResult> RemoveAlegacao(Guid id, Guid alegacaoId, CancellationToken cancellationToken = default)
    {
        try
        {
            await _impugnacaoService.RemoveAlegacaoAsync(id, alegacaoId, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Alegacao nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao remover alegacao {AlegacaoId} da impugnacao {Id}", alegacaoId, id);
            return InternalError("Erro ao remover alegacao");
        }
    }

    /// <summary>
    /// Abre prazo para contra-alegacoes
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="request">Dados do prazo</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/abrir-prazo-contra-alegacoes")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> AbrirPrazoContraAlegacoes(
        Guid id,
        [FromBody] PrazoRequest request,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var prazo = DateTime.UtcNow.AddDays(request.PrazoEmDias);
            var impugnacao = await _impugnacaoService.AbrirPrazoContraAlegacoesAsync(id, prazo, cancellationToken);
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
            _logger.LogError(ex, "Erro ao abrir prazo de contra-alegacoes da impugnacao {Id}", id);
            return InternalError("Erro ao abrir prazo de contra-alegacoes");
        }
    }

    /// <summary>
    /// Registra contra-alegacao na impugnacao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="dto">Dados da contra-alegacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/contra-alegacoes")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> RegistrarContraAlegacao(
        Guid id,
        [FromBody] ContraAlegacaoRequest request,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var userId = GetUserId();
            var impugnacao = await _impugnacaoService.RegistrarContraAlegacaoAsync(id, request.Texto, userId, cancellationToken);
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
            _logger.LogError(ex, "Erro ao registrar contra-alegacao na impugnacao {Id}", id);
            return InternalError("Erro ao registrar contra-alegacao");
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
    public async Task<ActionResult<ImpugnacaoDto>> EnviarParaJulgamento(Guid id, CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacao = await _impugnacaoService.EncaminharParaJulgamentoAsync(id, cancellationToken);
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
        [FromBody] JulgarImpugnacaoRequest dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacao = await _impugnacaoService.RegistrarJulgamentoAsync(id, dto.Decisao, dto.Fundamentacao, cancellationToken);
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
            _logger.LogError(ex, "Erro ao julgar impugnacao {Id}", id);
            return InternalError("Erro ao julgar impugnacao");
        }
    }

    /// <summary>
    /// Defere a impugnacao (julga procedente)
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="request">Dados do deferimento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao deferida</returns>
    [HttpPost("{id:guid}/deferir")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ImpugnacaoDto>> Deferir(
        Guid id,
        [FromBody] DeferirIndeferirRequest request,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacao = await _impugnacaoService.RegistrarJulgamentoAsync(
                id,
                StatusImpugnacao.Procedente,
                request.Fundamentacao,
                cancellationToken);
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
            _logger.LogError(ex, "Erro ao deferir impugnacao {Id}", id);
            return InternalError("Erro ao deferir impugnacao");
        }
    }

    /// <summary>
    /// Indefere a impugnacao (julga improcedente)
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="request">Dados do indeferimento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao indeferida</returns>
    [HttpPost("{id:guid}/indeferir")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ImpugnacaoDto>> Indeferir(
        Guid id,
        [FromBody] DeferirIndeferirRequest request,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacao = await _impugnacaoService.RegistrarJulgamentoAsync(
                id,
                StatusImpugnacao.Improcedente,
                request.Fundamentacao,
                cancellationToken);
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
            _logger.LogError(ex, "Erro ao indeferir impugnacao {Id}", id);
            return InternalError("Erro ao indeferir impugnacao");
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
        CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacao = await _impugnacaoService.ArquivarAsync(id, request.Motivo, cancellationToken);
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
        [FromBody] RecursoRequest dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var userId = GetUserId();
            var impugnacao = await _impugnacaoService.RegistrarRecursoAsync(id, dto.Fundamentacao, userId, cancellationToken);
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
            _logger.LogError(ex, "Erro ao apresentar recurso da impugnacao {Id}", id);
            return InternalError("Erro ao apresentar recurso");
        }
    }

    /// <summary>
    /// Adiciona pedido a impugnacao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="dto">Dados do pedido</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Pedido criado</returns>
    [HttpPost("{id:guid}/pedidos")]
    [ProducesResponseType(typeof(PedidoImpugnacaoDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<PedidoImpugnacaoDto>> AddPedido(
        Guid id,
        [FromBody] CreatePedidoImpugnacaoDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var pedido = await _impugnacaoService.AddPedidoAsync(id, dto, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id }, pedido);
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
            _logger.LogError(ex, "Erro ao adicionar pedido a impugnacao {Id}", id);
            return InternalError("Erro ao adicionar pedido");
        }
    }

    /// <summary>
    /// Lista pedidos de uma impugnacao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de pedidos</returns>
    [HttpGet("{id:guid}/pedidos")]
    [ProducesResponseType(typeof(IEnumerable<PedidoImpugnacaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<PedidoImpugnacaoDto>>> GetPedidos(Guid id, CancellationToken cancellationToken = default)
    {
        try
        {
            var pedidos = await _impugnacaoService.GetPedidosAsync(id, cancellationToken);
            return Ok(pedidos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar pedidos da impugnacao {Id}", id);
            return InternalError("Erro ao listar pedidos");
        }
    }

    /// <summary>
    /// Remove um pedido da impugnacao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="pedidoId">ID do pedido</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>NoContent em caso de sucesso</returns>
    [HttpDelete("{id:guid}/pedidos/{pedidoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(StatusCodes.Status204NoContent)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<IActionResult> RemovePedido(Guid id, Guid pedidoId, CancellationToken cancellationToken = default)
    {
        try
        {
            await _impugnacaoService.RemovePedidoAsync(id, pedidoId, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Pedido nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao remover pedido {PedidoId} da impugnacao {Id}", pedidoId, id);
            return InternalError("Erro ao remover pedido");
        }
    }

    /// <summary>
    /// Obtem estatisticas gerais de impugnacoes (com filtro opcional por eleicao)
    /// </summary>
    /// <param name="eleicaoId">Filtro opcional por eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Estatisticas de impugnacoes</returns>
    [HttpGet("estatisticas")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(EstatisticasImpugnacaoResponse), StatusCodes.Status200OK)]
    public async Task<ActionResult<EstatisticasImpugnacaoResponse>> GetEstatisticas(
        [FromQuery] Guid? eleicaoId,
        CancellationToken cancellationToken = default)
    {
        try
        {
            IEnumerable<ImpugnacaoDto> impugnacoes;
            if (eleicaoId.HasValue)
            {
                impugnacoes = await _impugnacaoService.GetByEleicaoAsync(eleicaoId.Value, cancellationToken);
            }
            else
            {
                impugnacoes = await _impugnacaoService.GetAllAsync(cancellationToken);
            }

            var list = impugnacoes.ToList();
            var response = new EstatisticasImpugnacaoResponse
            {
                Total = list.Count,
                Pendentes = list.Count(i => i.Status == StatusImpugnacao.Recebida),
                EmAnalise = list.Count(i => i.Status == StatusImpugnacao.EmAnalise),
                Julgadas = list.Count(i => i.Status == StatusImpugnacao.Julgada),
                Procedentes = list.Count(i => i.Status == StatusImpugnacao.Procedente),
                Improcedentes = list.Count(i => i.Status == StatusImpugnacao.Improcedente)
            };

            return Ok(response);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter estatisticas de impugnacoes");
            return InternalError("Erro ao obter estatisticas");
        }
    }

    /// <summary>
    /// Obtem estatisticas de impugnacoes por eleicao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Contagem de impugnacoes</returns>
    [HttpGet("estatisticas/eleicao/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(EstatisticasImpugnacaoResponse), StatusCodes.Status200OK)]
    public async Task<ActionResult<EstatisticasImpugnacaoResponse>> GetEstatisticasByEleicao(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        try
        {
            var total = await _impugnacaoService.CountByEleicaoAsync(eleicaoId, cancellationToken);
            var pendentes = await _impugnacaoService.CountByStatusAsync(StatusImpugnacao.Recebida, cancellationToken);
            var emAnalise = await _impugnacaoService.CountByStatusAsync(StatusImpugnacao.EmAnalise, cancellationToken);
            var julgadas = await _impugnacaoService.CountByStatusAsync(StatusImpugnacao.Julgada, cancellationToken);
            var procedentes = await _impugnacaoService.CountByStatusAsync(StatusImpugnacao.Procedente, cancellationToken);
            var improcedentes = await _impugnacaoService.CountByStatusAsync(StatusImpugnacao.Improcedente, cancellationToken);

            var response = new EstatisticasImpugnacaoResponse
            {
                Total = total,
                Pendentes = pendentes,
                EmAnalise = emAnalise,
                Julgadas = julgadas,
                Procedentes = procedentes,
                Improcedentes = improcedentes
            };

            return Ok(response);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter estatisticas de impugnacoes da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao obter estatisticas");
        }
    }

    #region New Workflow Endpoints

    /// <summary>
    /// Solicita alegacoes do impugnante (novo workflow)
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="dto">Dados do prazo</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/solicitar-alegacoes")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> SolicitarAlegacoes(
        Guid id,
        [FromBody] AbrirPrazoAlegacoesDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacao = await _impugnacaoService.SolicitarAlegacoesAsync(id, dto.PrazoEmDias, dto.Observacoes, cancellationToken);
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
            _logger.LogError(ex, "Erro ao solicitar alegacoes da impugnacao {Id}", id);
            return InternalError("Erro ao solicitar alegacoes");
        }
    }

    /// <summary>
    /// Apresenta alegacoes do impugnante (novo workflow)
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="dto">Dados da alegacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/apresentar-alegacoes")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> ApresentarAlegacoes(
        Guid id,
        [FromBody] CreateAlegacaoDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacao = await _impugnacaoService.ApresentarAlegacoesAsync(id, dto, cancellationToken);
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
            _logger.LogError(ex, "Erro ao apresentar alegacoes da impugnacao {Id}", id);
            return InternalError("Erro ao apresentar alegacoes");
        }
    }

    /// <summary>
    /// Solicita contra-alegacoes do impugnado (novo workflow)
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="dto">Dados do prazo</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/solicitar-contra-alegacoes")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> SolicitarContraAlegacoes(
        Guid id,
        [FromBody] AbrirPrazoAlegacoesDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacao = await _impugnacaoService.SolicitarContraAlegacoesAsync(id, dto.PrazoEmDias, dto.Observacoes, cancellationToken);
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
            _logger.LogError(ex, "Erro ao solicitar contra-alegacoes da impugnacao {Id}", id);
            return InternalError("Erro ao solicitar contra-alegacoes");
        }
    }

    /// <summary>
    /// Apresenta contra-alegacoes do impugnado (novo workflow)
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="dto">Dados da contra-alegacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/apresentar-contra-alegacoes")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> ApresentarContraAlegacoes(
        Guid id,
        [FromBody] CreateContraAlegacaoDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var userId = GetUserId();
            var impugnacao = await _impugnacaoService.ApresentarContraAlegacoesAsync(id, dto, userId, cancellationToken);
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
            _logger.LogError(ex, "Erro ao apresentar contra-alegacoes da impugnacao {Id}", id);
            return InternalError("Erro ao apresentar contra-alegacoes");
        }
    }

    /// <summary>
    /// Lista contra-alegacoes de uma impugnacao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de contra-alegacoes</returns>
    [HttpGet("{id:guid}/contra-alegacoes")]
    [ProducesResponseType(typeof(IEnumerable<ContraAlegacaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<ContraAlegacaoDto>>> GetContraAlegacoes(Guid id, CancellationToken cancellationToken = default)
    {
        try
        {
            var contraAlegacoes = await _impugnacaoService.GetContraAlegacoesAsync(id, cancellationToken);
            return Ok(contraAlegacoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar contra-alegacoes da impugnacao {Id}", id);
            return InternalError("Erro ao listar contra-alegacoes");
        }
    }

    /// <summary>
    /// Encaminha impugnacao para julgamento (novo workflow)
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="dto">Dados do encaminhamento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/encaminhar-julgamento")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> EncaminharJulgamento(
        Guid id,
        [FromBody] EncaminharJulgamentoRequest? dto = null,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacao = await _impugnacaoService.EncaminharJulgamentoAsync(id, dto?.ComissaoId, cancellationToken);
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
            _logger.LogError(ex, "Erro ao encaminhar impugnacao para julgamento {Id}", id);
            return InternalError("Erro ao encaminhar para julgamento");
        }
    }

    /// <summary>
    /// Julga a impugnacao (novo workflow com DTO completo)
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="dto">Dados do julgamento</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao julgada</returns>
    [HttpPost("{id:guid}/julgar-completo")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> JulgarCompleto(
        Guid id,
        [FromBody] JulgarImpugnacaoDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacao = await _impugnacaoService.JulgarAsync(id, dto, cancellationToken);
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
            _logger.LogError(ex, "Erro ao julgar impugnacao {Id}", id);
            return InternalError("Erro ao julgar impugnacao");
        }
    }

    /// <summary>
    /// Interpoe recurso contra decisao (novo workflow)
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="dto">Dados do recurso</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/interpor-recurso")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> InterporRecurso(
        Guid id,
        [FromBody] CreateRecursoImpugnacaoDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var userId = GetUserId();
            var impugnacao = await _impugnacaoService.InterporRecursoAsync(id, dto, userId, cancellationToken);
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
            _logger.LogError(ex, "Erro ao interpor recurso da impugnacao {Id}", id);
            return InternalError("Erro ao interpor recurso");
        }
    }

    /// <summary>
    /// Lista recursos de uma impugnacao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de recursos</returns>
    [HttpGet("{id:guid}/recursos")]
    [ProducesResponseType(typeof(IEnumerable<RecursoImpugnacaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<RecursoImpugnacaoDto>>> GetRecursos(Guid id, CancellationToken cancellationToken = default)
    {
        try
        {
            var recursos = await _impugnacaoService.GetRecursosAsync(id, cancellationToken);
            return Ok(recursos);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar recursos da impugnacao {Id}", id);
            return InternalError("Erro ao listar recursos");
        }
    }

    /// <summary>
    /// Julga recurso de uma impugnacao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="recursoId">ID do recurso</param>
    /// <param name="dto">Dados do julgamento do recurso</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Impugnacao atualizada</returns>
    [HttpPost("{id:guid}/recursos/{recursoId:guid}/julgar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(ImpugnacaoDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ImpugnacaoDto>> JulgarRecurso(
        Guid id,
        Guid recursoId,
        [FromBody] JulgarRecursoImpugnacaoDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var impugnacao = await _impugnacaoService.JulgarRecursoAsync(id, recursoId, dto, cancellationToken);
            return Ok(impugnacao);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Impugnacao ou recurso nao encontrado" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao julgar recurso {RecursoId} da impugnacao {Id}", recursoId, id);
            return InternalError("Erro ao julgar recurso");
        }
    }

    /// <summary>
    /// Apresenta defesa na impugnacao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="dto">Dados da defesa</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Defesa criada</returns>
    [HttpPost("{id:guid}/defesas")]
    [ProducesResponseType(typeof(DefesaImpugnacaoDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DefesaImpugnacaoDto>> ApresentarDefesa(
        Guid id,
        [FromBody] CreateDefesaImpugnacaoDto dto,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var userId = GetUserId();
            var defesa = await _impugnacaoService.ApresentarDefesaAsync(id, dto, userId, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id }, defesa);
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
            _logger.LogError(ex, "Erro ao apresentar defesa na impugnacao {Id}", id);
            return InternalError("Erro ao apresentar defesa");
        }
    }

    /// <summary>
    /// Lista defesas de uma impugnacao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de defesas</returns>
    [HttpGet("{id:guid}/defesas")]
    [ProducesResponseType(typeof(IEnumerable<DefesaImpugnacaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<DefesaImpugnacaoDto>>> GetDefesas(Guid id, CancellationToken cancellationToken = default)
    {
        try
        {
            var defesas = await _impugnacaoService.GetDefesasAsync(id, cancellationToken);
            return Ok(defesas);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar defesas da impugnacao {Id}", id);
            return InternalError("Erro ao listar defesas");
        }
    }

    /// <summary>
    /// Obtem historico de uma impugnacao
    /// </summary>
    /// <param name="id">ID da impugnacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de eventos do historico</returns>
    [HttpGet("{id:guid}/historico")]
    [ProducesResponseType(typeof(IEnumerable<HistoricoImpugnacaoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<HistoricoImpugnacaoDto>>> GetHistorico(Guid id, CancellationToken cancellationToken = default)
    {
        try
        {
            var historico = await _impugnacaoService.GetHistoricoAsync(id, cancellationToken);
            return Ok(historico);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar historico da impugnacao {Id}", id);
            return InternalError("Erro ao listar historico");
        }
    }

    /// <summary>
    /// Obtem estatisticas detalhadas de impugnacoes
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Estatisticas detalhadas</returns>
    [HttpGet("estatisticas/eleicao/{eleicaoId:guid}/detalhado")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(ImpugnacaoEstatisticasDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<ImpugnacaoEstatisticasDto>> GetEstatisticasDetalhadas(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        try
        {
            var estatisticas = await _impugnacaoService.GetEstatisticasAsync(eleicaoId, cancellationToken);
            return Ok(estatisticas);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter estatisticas detalhadas de impugnacoes da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao obter estatisticas");
        }
    }

    /// <summary>
    /// Valida se esta dentro do periodo de impugnacao
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>True se pode impugnar</returns>
    [HttpGet("validar-periodo/{eleicaoId:guid}")]
    [ProducesResponseType(typeof(ValidacaoPeriodoResponse), StatusCodes.Status200OK)]
    public async Task<ActionResult<ValidacaoPeriodoResponse>> ValidarPeriodo(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        try
        {
            var podeImpugnar = await _impugnacaoService.ValidarPeriodoImpugnacaoAsync(eleicaoId, cancellationToken);
            return Ok(new ValidacaoPeriodoResponse { PodeImpugnar = podeImpugnar });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao validar periodo de impugnacao da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao validar periodo");
        }
    }

    #endregion
}

// Request/Response DTOs
public record PaginatedResult<T>
{
    public List<T> Items { get; init; } = new();
    public int TotalCount { get; init; }
    public int Page { get; init; }
    public int PageSize { get; init; }
    public int TotalPages { get; init; }
}

public record PrazoRequest(int PrazoEmDias);

public record ContraAlegacaoRequest(string Texto);

public record JulgarImpugnacaoRequest
{
    public StatusImpugnacao Decisao { get; init; }
    public string Fundamentacao { get; init; } = string.Empty;
}

public record DeferirIndeferirRequest
{
    public string Fundamentacao { get; init; } = string.Empty;
}

public record RecursoRequest
{
    public string Fundamentacao { get; init; } = string.Empty;
    public List<Guid>? DocumentosIds { get; init; }
}

public record MotivoRequest(string Motivo);

public record EstatisticasImpugnacaoResponse
{
    public int Total { get; init; }
    public int Pendentes { get; init; }
    public int EmAnalise { get; init; }
    public int Julgadas { get; init; }
    public int Procedentes { get; init; }
    public int Improcedentes { get; init; }
}

public record EncaminharJulgamentoRequest
{
    public Guid? ComissaoId { get; init; }
}

public record ValidacaoPeriodoResponse
{
    public bool PodeImpugnar { get; init; }
}
