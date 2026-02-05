using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Application.DTOs.Denuncias;
using CAU.Eleitoral.Application.DTOs.Auditoria;
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

    #region CRUD Endpoints

    /// <summary>
    /// Lista todas as denuncias com paginacao e filtros
    /// </summary>
    /// <param name="filtro">Filtros e paginacao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista paginada de denuncias</returns>
    [HttpGet]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(PaginatedResultDto<DenunciaListDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<PaginatedResultDto<DenunciaListDto>>> GetAll(
        [FromQuery] FiltroDenunciaDto filtro,
        CancellationToken cancellationToken)
    {
        try
        {
            var denuncias = await _denunciaService.GetPaginatedAsync(filtro, cancellationToken);
            return Ok(denuncias);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar denuncias");
            return InternalError("Erro ao listar denuncias");
        }
    }

    /// <summary>
    /// Obtem uma denuncia pelo ID com todos os detalhes
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados completos da denuncia</returns>
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
    /// Obtem uma denuncia pelo protocolo
    /// </summary>
    /// <param name="protocolo">Protocolo da denuncia</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados da denuncia</returns>
    [HttpGet("protocolo/{protocolo}")]
    [ProducesResponseType(typeof(DenunciaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<DenunciaDto>> GetByProtocolo(string protocolo, CancellationToken cancellationToken)
    {
        try
        {
            var denuncia = await _denunciaService.GetByProtocoloAsync(protocolo, cancellationToken);
            if (denuncia == null)
                return NotFound(new { message = "Denuncia nao encontrada" });

            return Ok(denuncia);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter denuncia pelo protocolo {Protocolo}", protocolo);
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
    /// Lista denuncias por status
    /// </summary>
    /// <param name="status">Status da denuncia</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de denuncias</returns>
    [HttpGet("status/{status}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(IEnumerable<DenunciaDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<DenunciaDto>>> GetByStatus(StatusDenuncia status, CancellationToken cancellationToken)
    {
        try
        {
            var denuncias = await _denunciaService.GetByStatusAsync(status, cancellationToken);
            return Ok(denuncias);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar denuncias por status {Status}", status);
            return InternalError("Erro ao listar denuncias");
        }
    }

    /// <summary>
    /// Lista denuncias por chapa
    /// </summary>
    /// <param name="chapaId">ID da chapa</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de denuncias</returns>
    [HttpGet("chapa/{chapaId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(IEnumerable<DenunciaDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<DenunciaDto>>> GetByChapa(Guid chapaId, CancellationToken cancellationToken)
    {
        try
        {
            var denuncias = await _denunciaService.GetByChapaAsync(chapaId, cancellationToken);
            return Ok(denuncias);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar denuncias da chapa {ChapaId}", chapaId);
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

    #endregion

    #region Workflow Endpoints

    /// <summary>
    /// Inicia a analise de uma denuncia
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="dto">Dados opcionais da analise</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Denuncia atualizada</returns>
    [HttpPost("{id:guid}/analisar")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(DenunciaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DenunciaDto>> Analisar(
        Guid id,
        [FromBody] IniciarAnaliseDto? dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var denuncia = await _denunciaService.IniciarAnaliseAsync(id, userId, cancellationToken);
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
            _logger.LogError(ex, "Erro ao iniciar analise da denuncia {Id}", id);
            return InternalError("Erro ao iniciar analise");
        }
    }

    /// <summary>
    /// Conclui a analise de uma denuncia
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="dto">Dados da conclusao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados da analise concluida</returns>
    [HttpPost("{id:guid}/concluir-analise")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(AnaliseDenunciaDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<AnaliseDenunciaDto>> ConcluirAnalise(
        Guid id,
        [FromBody] ConcluirAnaliseDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var analise = await _denunciaService.ConcluirAnaliseAsync(id, dto, userId, cancellationToken);
            return Ok(analise);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Analise nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao concluir analise da denuncia {Id}", id);
            return InternalError("Erro ao concluir analise");
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
        [FromBody] RegistrarAdmissibilidadeDto request,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var denuncia = await _denunciaService.RegistrarAdmissibilidadeAsync(id, true, request.Parecer, userId, cancellationToken);
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
        [FromBody] RegistrarAdmissibilidadeDto request,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var denuncia = await _denunciaService.RegistrarAdmissibilidadeAsync(id, false, request.Parecer, userId, cancellationToken);
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
        [FromBody] SolicitarDefesaDto request,
        CancellationToken cancellationToken)
    {
        try
        {
            var denuncia = await _denunciaService.SolicitarDefesaAsync(id, request.PrazoEmDias, cancellationToken);
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
        [FromBody] CreateDefesaDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var denuncia = await _denunciaService.ApresentarDefesaAsync(id, dto, userId, cancellationToken);
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
            var denuncia = await _denunciaService.EncaminharParaJulgamentoAsync(id, cancellationToken);
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
        [FromBody] ArquivarDenunciaDto request,
        CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var denuncia = await _denunciaService.ArquivarAsync(id, request.Motivo, userId, cancellationToken);
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
            _logger.LogError(ex, "Erro ao arquivar denuncia {Id}", id);
            return InternalError("Erro ao arquivar denuncia");
        }
    }

    #endregion

    #region Provas Endpoints

    /// <summary>
    /// Lista provas de uma denuncia
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de provas</returns>
    [HttpGet("{id:guid}/provas")]
    [ProducesResponseType(typeof(IEnumerable<ProvaDenunciaDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<ProvaDenunciaDto>>> GetProvas(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var provas = await _denunciaService.GetProvasAsync(id, cancellationToken);
            return Ok(provas);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Denuncia nao encontrada" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar provas da denuncia {Id}", id);
            return InternalError("Erro ao listar provas");
        }
    }

    /// <summary>
    /// Adiciona prova a denuncia
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="dto">Dados da prova</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Prova adicionada</returns>
    [HttpPost("{id:guid}/provas")]
    [ProducesResponseType(typeof(ProvaDenunciaDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<ProvaDenunciaDto>> AddProva(
        Guid id,
        [FromBody] CreateProvaDenunciaDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            var prova = await _denunciaService.AddProvaAsync(id, dto, cancellationToken);
            return CreatedAtAction(nameof(GetProvas), new { id }, prova);
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
            _logger.LogError(ex, "Erro ao adicionar prova a denuncia {Id}", id);
            return InternalError("Erro ao adicionar prova");
        }
    }

    /// <summary>
    /// Remove prova da denuncia
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="provaId">ID da prova</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>NoContent em caso de sucesso</returns>
    [HttpDelete("{id:guid}/provas/{provaId:guid}")]
    [ProducesResponseType(StatusCodes.Status204NoContent)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<IActionResult> RemoveProva(Guid id, Guid provaId, CancellationToken cancellationToken)
    {
        try
        {
            await _denunciaService.RemoveProvaAsync(id, provaId, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Prova nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao remover prova {ProvaId} da denuncia {Id}", provaId, id);
            return InternalError("Erro ao remover prova");
        }
    }

    #endregion

    #region Defesas Endpoints

    /// <summary>
    /// Lista defesas de uma denuncia
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de defesas</returns>
    [HttpGet("{id:guid}/defesas")]
    [ProducesResponseType(typeof(IEnumerable<DefesaDenunciaDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<DefesaDenunciaDto>>> GetDefesas(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var defesas = await _denunciaService.GetDefesasAsync(id, cancellationToken);
            return Ok(defesas);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Denuncia nao encontrada" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar defesas da denuncia {Id}", id);
            return InternalError("Erro ao listar defesas");
        }
    }

    #endregion

    #region Historico Endpoints

    /// <summary>
    /// Lista historico de uma denuncia
    /// </summary>
    /// <param name="id">ID da denuncia</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de historico</returns>
    [HttpGet("{id:guid}/historico")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(IEnumerable<HistoricoDenunciaDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<HistoricoDenunciaDto>>> GetHistorico(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var historico = await _denunciaService.GetHistoricoAsync(id, cancellationToken);
            return Ok(historico);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Denuncia nao encontrada" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar historico da denuncia {Id}", id);
            return InternalError("Erro ao listar historico");
        }
    }

    #endregion

    #region Statistics Endpoints

    /// <summary>
    /// Obtem estatisticas de denuncias
    /// </summary>
    /// <param name="eleicaoId">Filtro opcional por eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Estatisticas de denuncias</returns>
    [HttpGet("estatisticas")]
    [Authorize(Roles = "Admin,ComissaoEleitoral,Analista")]
    [ProducesResponseType(typeof(DenunciaEstatisticasDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<DenunciaEstatisticasDto>> GetEstatisticas(
        [FromQuery] Guid? eleicaoId,
        CancellationToken cancellationToken)
    {
        try
        {
            var estatisticas = await _denunciaService.GetEstatisticasAsync(eleicaoId, cancellationToken);
            return Ok(estatisticas);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter estatisticas de denuncias");
            return InternalError("Erro ao obter estatisticas");
        }
    }

    #endregion
}
