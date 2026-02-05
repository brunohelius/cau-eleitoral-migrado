using CAU.Eleitoral.Application.DTOs.Impugnacoes;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IImpugnacaoService
{
    // CRUD Operations
    Task<ImpugnacaoDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto?> GetByProtocoloAsync(string protocolo, CancellationToken cancellationToken = default);
    Task<IEnumerable<ImpugnacaoDto>> GetAllAsync(CancellationToken cancellationToken = default);
    Task<IEnumerable<ImpugnacaoDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<ImpugnacaoDto>> GetByStatusAsync(StatusImpugnacao status, CancellationToken cancellationToken = default);
    Task<IEnumerable<ImpugnacaoDto>> GetByChapaImpugnadaAsync(Guid chapaId, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> CreateAsync(CreateImpugnacaoDto dto, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> UpdateAsync(Guid id, UpdateImpugnacaoDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);

    // Workflow Operations
    Task<ImpugnacaoDto> ReceberAsync(Guid id, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> IniciarAnaliseAsync(Guid id, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> AbrirPrazoAlegacoesAsync(Guid id, DateTime prazo, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> RegistrarAlegacaoAsync(Guid id, CreateAlegacaoDto dto, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> AbrirPrazoContraAlegacoesAsync(Guid id, DateTime prazo, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> RegistrarContraAlegacaoAsync(Guid id, string contraAlegacao, Guid autorId, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> EncaminharParaJulgamentoAsync(Guid id, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> RegistrarJulgamentoAsync(Guid id, StatusImpugnacao decisao, string fundamentacao, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> ArquivarAsync(Guid id, string motivo, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> RegistrarRecursoAsync(Guid id, string recurso, Guid autorId, CancellationToken cancellationToken = default);

    // Pedidos
    Task<PedidoImpugnacaoDto> AddPedidoAsync(Guid impugnacaoId, CreatePedidoImpugnacaoDto dto, CancellationToken cancellationToken = default);
    Task RemovePedidoAsync(Guid impugnacaoId, Guid pedidoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<PedidoImpugnacaoDto>> GetPedidosAsync(Guid impugnacaoId, CancellationToken cancellationToken = default);

    // Alegacoes
    Task<IEnumerable<AlegacaoDto>> GetAlegacoesAsync(Guid impugnacaoId, CancellationToken cancellationToken = default);
    Task RemoveAlegacaoAsync(Guid impugnacaoId, Guid alegacaoId, CancellationToken cancellationToken = default);

    // Statistics
    Task<int> CountByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<int> CountByStatusAsync(StatusImpugnacao status, CancellationToken cancellationToken = default);
}
