using CAU.Eleitoral.Application.DTOs.Denuncias;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IDenunciaService
{
    // CRUD Operations
    Task<DenunciaDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<DenunciaDto?> GetByProtocoloAsync(string protocolo, CancellationToken cancellationToken = default);
    Task<IEnumerable<DenunciaDto>> GetAllAsync(CancellationToken cancellationToken = default);
    Task<IEnumerable<DenunciaDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<DenunciaDto>> GetByStatusAsync(StatusDenuncia status, CancellationToken cancellationToken = default);
    Task<IEnumerable<DenunciaDto>> GetByChapaAsync(Guid chapaId, CancellationToken cancellationToken = default);
    Task<DenunciaDto> CreateAsync(CreateDenunciaDto dto, CancellationToken cancellationToken = default);
    Task<DenunciaDto> UpdateAsync(Guid id, UpdateDenunciaDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);

    // Workflow Operations
    Task<DenunciaDto> ReceberAsync(Guid id, CancellationToken cancellationToken = default);
    Task<DenunciaDto> IniciarAnaliseAsync(Guid id, CancellationToken cancellationToken = default);
    Task<DenunciaDto> RegistrarAdmissibilidadeAsync(Guid id, bool admissivel, string parecer, Guid relatorId, CancellationToken cancellationToken = default);
    Task<DenunciaDto> AbrirPrazoDefesaAsync(Guid id, DateTime prazoDefesa, CancellationToken cancellationToken = default);
    Task<DenunciaDto> RegistrarDefesaAsync(Guid id, string defesa, Guid autorId, CancellationToken cancellationToken = default);
    Task<DenunciaDto> EncaminharParaJulgamentoAsync(Guid id, CancellationToken cancellationToken = default);
    Task<DenunciaDto> RegistrarJulgamentoAsync(Guid id, StatusDenuncia decisao, string fundamentacao, CancellationToken cancellationToken = default);
    Task<DenunciaDto> ArquivarAsync(Guid id, string motivo, CancellationToken cancellationToken = default);
    Task<DenunciaDto> AbrirPrazoRecursoAsync(Guid id, DateTime prazoRecurso, CancellationToken cancellationToken = default);
    Task<DenunciaDto> RegistrarRecursoAsync(Guid id, string recurso, Guid autorId, CancellationToken cancellationToken = default);

    // Provas
    Task<ProvaDenunciaDto> AddProvaAsync(Guid denunciaId, CreateProvaDenunciaDto dto, CancellationToken cancellationToken = default);
    Task RemoveProvaAsync(Guid denunciaId, Guid provaId, CancellationToken cancellationToken = default);
    Task<IEnumerable<ProvaDenunciaDto>> GetProvasAsync(Guid denunciaId, CancellationToken cancellationToken = default);

    // Statistics
    Task<int> CountByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<int> CountByStatusAsync(StatusDenuncia status, CancellationToken cancellationToken = default);
}
