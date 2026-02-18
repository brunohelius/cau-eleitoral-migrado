using CAU.Eleitoral.Application.DTOs.Chapas;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IPedidoService
{
    Task<PagedResultDto<PedidoDto>> GetAllAsync(int pageNumber, int pageSize, Guid? eleicaoId, int? status, CancellationToken cancellationToken = default);
    Task<PedidoDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<PedidoDto>> GetBySolicitanteAsync(CancellationToken cancellationToken = default);
    Task<PedidoDto> CreateAsync(CreatePedidoDto dto, CancellationToken cancellationToken = default);
    Task<PedidoDto> RespondAsync(Guid id, RespondPedidoDto dto, CancellationToken cancellationToken = default);
    Task<PedidoDto> CancelAsync(Guid id, CancellationToken cancellationToken = default);
    Task<PedidoEstatisticasDto> GetEstatisticasAsync(CancellationToken cancellationToken = default);
}

public class PedidoDto
{
    public Guid Id { get; set; }
    public Guid? EleicaoId { get; set; }
    public string? EleicaoNome { get; set; }
    public Guid SolicitanteId { get; set; }
    public string? SolicitanteNome { get; set; }
    public string? SolicitanteEmail { get; set; }
    public int Tipo { get; set; }
    public string TipoNome { get; set; } = string.Empty;
    public string Assunto { get; set; } = string.Empty;
    public string Descricao { get; set; } = string.Empty;
    public int Status { get; set; }
    public string StatusNome { get; set; } = string.Empty;
    public DateTime DataSolicitacao { get; set; }
    public DateTime? DataResposta { get; set; }
    public string? Resposta { get; set; }
    public Guid? RespondidoPorId { get; set; }
    public string? RespondidoPorNome { get; set; }
    public string? Observacoes { get; set; }
}

public class CreatePedidoDto
{
    public Guid? EleicaoId { get; set; }
    public Guid SolicitanteId { get; set; }
    public string? SolicitanteNome { get; set; }
    public string? SolicitanteEmail { get; set; }
    public int Tipo { get; set; }
    public string Assunto { get; set; } = string.Empty;
    public string Descricao { get; set; } = string.Empty;
}

public class RespondPedidoDto
{
    public string Resposta { get; set; } = string.Empty;
    public string? Observacoes { get; set; }
}

public class PedidoEstatisticasDto
{
    public int Total { get; set; }
    public int Pendentes { get; set; }
    public int EmAnalise { get; set; }
    public int Respondidos { get; set; }
    public int Encerrados { get; set; }
    public int Cancelados { get; set; }
}