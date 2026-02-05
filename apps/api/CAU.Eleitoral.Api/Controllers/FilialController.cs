using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para gerenciamento de filiais/regionais do CAU
/// </summary>
[Authorize]
public class FilialController : BaseController
{
    private readonly IFilialService _filialService;
    private readonly ILogger<FilialController> _logger;

    public FilialController(IFilialService filialService, ILogger<FilialController> logger)
    {
        _filialService = filialService;
        _logger = logger;
    }

    /// <summary>
    /// Lista todas as filiais/regionais
    /// </summary>
    /// <param name="ativa">Filtro opcional por status ativo</param>
    /// <param name="uf">Filtro opcional por UF</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de filiais</returns>
    [HttpGet]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<FilialDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<FilialDto>>> GetAll(
        [FromQuery] bool? ativa,
        [FromQuery] string? uf,
        CancellationToken cancellationToken)
    {
        try
        {
            var filiais = await _filialService.GetAllAsync(ativa, uf, cancellationToken);
            return Ok(filiais);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar filiais");
            return InternalError("Erro ao listar filiais");
        }
    }

    /// <summary>
    /// Obtem uma filial pelo ID
    /// </summary>
    /// <param name="id">ID da filial</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados da filial</returns>
    [HttpGet("{id:guid}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(FilialDetalheDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<FilialDetalheDto>> GetById(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var filial = await _filialService.GetByIdAsync(id, cancellationToken);
            if (filial == null)
                return NotFound(new { message = "Filial nao encontrada" });

            return Ok(filial);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter filial {Id}", id);
            return InternalError("Erro ao obter filial");
        }
    }

    /// <summary>
    /// Obtem uma filial pelo codigo
    /// </summary>
    /// <param name="codigo">Codigo da filial</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados da filial</returns>
    [HttpGet("codigo/{codigo}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(FilialDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<FilialDto>> GetByCodigo(string codigo, CancellationToken cancellationToken)
    {
        try
        {
            var filial = await _filialService.GetByCodigoAsync(codigo, cancellationToken);
            if (filial == null)
                return NotFound(new { message = "Filial nao encontrada" });

            return Ok(filial);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter filial {Codigo}", codigo);
            return InternalError("Erro ao obter filial");
        }
    }

    /// <summary>
    /// Obtem uma filial pela UF
    /// </summary>
    /// <param name="uf">Sigla da UF</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados da filial</returns>
    [HttpGet("uf/{uf}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(FilialDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<FilialDto>> GetByUF(string uf, CancellationToken cancellationToken)
    {
        try
        {
            var filial = await _filialService.GetByUFAsync(uf, cancellationToken);
            if (filial == null)
                return NotFound(new { message = "Filial nao encontrada" });

            return Ok(filial);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter filial da UF {UF}", uf);
            return InternalError("Erro ao obter filial");
        }
    }

    /// <summary>
    /// Cria uma nova filial
    /// </summary>
    /// <param name="dto">Dados da filial</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Filial criada</returns>
    [HttpPost]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(FilialDetalheDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<FilialDetalheDto>> Create([FromBody] CreateFilialDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var filial = await _filialService.CreateAsync(dto, userId, cancellationToken);
            return CreatedAtAction(nameof(GetById), new { id = filial.Id }, filial);
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao criar filial");
            return InternalError("Erro ao criar filial");
        }
    }

    /// <summary>
    /// Atualiza uma filial
    /// </summary>
    /// <param name="id">ID da filial</param>
    /// <param name="dto">Dados atualizados</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Filial atualizada</returns>
    [HttpPut("{id:guid}")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(FilialDetalheDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<FilialDetalheDto>> Update(Guid id, [FromBody] UpdateFilialDto dto, CancellationToken cancellationToken)
    {
        try
        {
            var filial = await _filialService.UpdateAsync(id, dto, cancellationToken);
            return Ok(filial);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Filial nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao atualizar filial {Id}", id);
            return InternalError("Erro ao atualizar filial");
        }
    }

    /// <summary>
    /// Remove uma filial
    /// </summary>
    /// <param name="id">ID da filial</param>
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
            await _filialService.DeleteAsync(id, cancellationToken);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Filial nao encontrada" });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao excluir filial {Id}", id);
            return InternalError("Erro ao excluir filial");
        }
    }

    /// <summary>
    /// Ativa uma filial
    /// </summary>
    /// <param name="id">ID da filial</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Filial ativada</returns>
    [HttpPost("{id:guid}/ativar")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(FilialDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<FilialDto>> Ativar(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var filial = await _filialService.AtivarAsync(id, cancellationToken);
            return Ok(filial);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Filial nao encontrada" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao ativar filial {Id}", id);
            return InternalError("Erro ao ativar filial");
        }
    }

    /// <summary>
    /// Desativa uma filial
    /// </summary>
    /// <param name="id">ID da filial</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Filial desativada</returns>
    [HttpPost("{id:guid}/desativar")]
    [Authorize(Roles = "Admin")]
    [ProducesResponseType(typeof(FilialDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<FilialDto>> Desativar(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var filial = await _filialService.DesativarAsync(id, cancellationToken);
            return Ok(filial);
        }
        catch (KeyNotFoundException)
        {
            return NotFound(new { message = "Filial nao encontrada" });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao desativar filial {Id}", id);
            return InternalError("Erro ao desativar filial");
        }
    }

    /// <summary>
    /// Obtem estatisticas de uma filial
    /// </summary>
    /// <param name="id">ID da filial</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Estatisticas da filial</returns>
    [HttpGet("{id:guid}/estatisticas")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(EstatisticasFilialDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<EstatisticasFilialDto>> GetEstatisticas(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var estatisticas = await _filialService.GetEstatisticasAsync(id, cancellationToken);
            return Ok(estatisticas);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter estatisticas da filial {Id}", id);
            return InternalError("Erro ao obter estatisticas");
        }
    }

    /// <summary>
    /// Lista profissionais de uma filial
    /// </summary>
    /// <param name="id">ID da filial</param>
    /// <param name="page">Pagina</param>
    /// <param name="pageSize">Tamanho da pagina</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de profissionais</returns>
    [HttpGet("{id:guid}/profissionais")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(PagedResult<ProfissionalFilialDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<PagedResult<ProfissionalFilialDto>>> GetProfissionais(
        Guid id,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 50,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var profissionais = await _filialService.GetProfissionaisAsync(id, page, pageSize, cancellationToken);
            return Ok(profissionais);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar profissionais da filial {Id}", id);
            return InternalError("Erro ao listar profissionais");
        }
    }

    /// <summary>
    /// Lista eleicoes de uma filial
    /// </summary>
    /// <param name="id">ID da filial</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de eleicoes</returns>
    [HttpGet("{id:guid}/eleicoes")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<EleicaoFilialDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<EleicaoFilialDto>>> GetEleicoes(Guid id, CancellationToken cancellationToken)
    {
        try
        {
            var eleicoes = await _filialService.GetEleicoesAsync(id, cancellationToken);
            return Ok(eleicoes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar eleicoes da filial {Id}", id);
            return InternalError("Erro ao listar eleicoes");
        }
    }

    /// <summary>
    /// Lista UFs disponiveis
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Lista de UFs</returns>
    [HttpGet("ufs")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<UFDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<UFDto>>> GetUFs(CancellationToken cancellationToken)
    {
        try
        {
            var ufs = await _filialService.GetUFsAsync(cancellationToken);
            return Ok(ufs);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar UFs");
            return InternalError("Erro ao listar UFs");
        }
    }
}

// DTOs para Filial
public record FilialDto
{
    public Guid Id { get; init; }
    public string Codigo { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string NomeCompleto { get; init; } = string.Empty;
    public string Sigla { get; init; } = string.Empty;
    public string UF { get; init; } = string.Empty;
    public TipoFilial Tipo { get; init; }
    public bool Ativa { get; init; }
}

public record FilialDetalheDto : FilialDto
{
    public string? Endereco { get; init; }
    public string? Complemento { get; init; }
    public string? Bairro { get; init; }
    public string? Cidade { get; init; }
    public string? CEP { get; init; }
    public string? Telefone { get; init; }
    public string? Email { get; init; }
    public string? Site { get; init; }
    public string? LogoUrl { get; init; }
    public double? Latitude { get; init; }
    public double? Longitude { get; init; }
    public int TotalProfissionais { get; init; }
    public int TotalEleitores { get; init; }
    public DateTime CreatedAt { get; init; }
    public DateTime? UpdatedAt { get; init; }
}

public record CreateFilialDto
{
    public string Codigo { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string NomeCompleto { get; init; } = string.Empty;
    public string Sigla { get; init; } = string.Empty;
    public string UF { get; init; } = string.Empty;
    public TipoFilial Tipo { get; init; }
    public string? Endereco { get; init; }
    public string? Complemento { get; init; }
    public string? Bairro { get; init; }
    public string? Cidade { get; init; }
    public string? CEP { get; init; }
    public string? Telefone { get; init; }
    public string? Email { get; init; }
    public string? Site { get; init; }
    public string? LogoUrl { get; init; }
    public double? Latitude { get; init; }
    public double? Longitude { get; init; }
}

public record UpdateFilialDto
{
    public string? Nome { get; init; }
    public string? NomeCompleto { get; init; }
    public string? Endereco { get; init; }
    public string? Complemento { get; init; }
    public string? Bairro { get; init; }
    public string? Cidade { get; init; }
    public string? CEP { get; init; }
    public string? Telefone { get; init; }
    public string? Email { get; init; }
    public string? Site { get; init; }
    public string? LogoUrl { get; init; }
    public double? Latitude { get; init; }
    public double? Longitude { get; init; }
}

public record EstatisticasFilialDto
{
    public Guid FilialId { get; init; }
    public string FilialNome { get; init; } = string.Empty;
    public int TotalProfissionais { get; init; }
    public int ProfissionaisAtivos { get; init; }
    public int ProfissionaisInativos { get; init; }
    public int TotalEleitores { get; init; }
    public int EleitoresAptos { get; init; }
    public int TotalEleicoes { get; init; }
    public int EleicoesRealizadas { get; init; }
    public decimal MediaParticipacaoEleicoes { get; init; }
    public int TotalConselheiros { get; init; }
    public int ConselheirosAtivos { get; init; }
}

public record ProfissionalFilialDto
{
    public Guid Id { get; init; }
    public string RegistroCAU { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string? Email { get; init; }
    public string Status { get; init; } = string.Empty;
    public bool EleitorApto { get; init; }
}

public record EleicaoFilialDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public int Ano { get; init; }
    public string Status { get; init; } = string.Empty;
    public DateTime DataInicio { get; init; }
    public DateTime DataFim { get; init; }
    public int TotalVotos { get; init; }
    public decimal PercentualParticipacao { get; init; }
}

public record UFDto
{
    public string Sigla { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string Regiao { get; init; } = string.Empty;
    public bool PossuiFilial { get; init; }
    public Guid? FilialId { get; init; }
}

public enum TipoFilial
{
    Federal = 0,
    Estadual = 1
}

// Interface do servico (a ser implementada)
public interface IFilialService
{
    Task<IEnumerable<FilialDto>> GetAllAsync(bool? ativa, string? uf, CancellationToken cancellationToken = default);
    Task<FilialDetalheDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<FilialDto?> GetByCodigoAsync(string codigo, CancellationToken cancellationToken = default);
    Task<FilialDto?> GetByUFAsync(string uf, CancellationToken cancellationToken = default);
    Task<FilialDetalheDto> CreateAsync(CreateFilialDto dto, Guid userId, CancellationToken cancellationToken = default);
    Task<FilialDetalheDto> UpdateAsync(Guid id, UpdateFilialDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);
    Task<FilialDto> AtivarAsync(Guid id, CancellationToken cancellationToken = default);
    Task<FilialDto> DesativarAsync(Guid id, CancellationToken cancellationToken = default);
    Task<EstatisticasFilialDto> GetEstatisticasAsync(Guid id, CancellationToken cancellationToken = default);
    Task<PagedResult<ProfissionalFilialDto>> GetProfissionaisAsync(Guid id, int page, int pageSize, CancellationToken cancellationToken = default);
    Task<IEnumerable<EleicaoFilialDto>> GetEleicoesAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<UFDto>> GetUFsAsync(CancellationToken cancellationToken = default);
}
