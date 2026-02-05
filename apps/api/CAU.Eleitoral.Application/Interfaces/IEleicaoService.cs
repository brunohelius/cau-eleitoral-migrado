using CAU.Eleitoral.Application.DTOs;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IEleicaoService
{
    Task<EleicaoDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<EleicaoDto>> GetAllAsync(CancellationToken cancellationToken = default);
    Task<IEnumerable<EleicaoDto>> GetByStatusAsync(int status, CancellationToken cancellationToken = default);
    Task<IEnumerable<EleicaoDto>> GetAtivasAsync(CancellationToken cancellationToken = default);
    Task<EleicaoDto> CreateAsync(CreateEleicaoDto dto, CancellationToken cancellationToken = default);
    Task<EleicaoDto> UpdateAsync(Guid id, UpdateEleicaoDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);
    Task<EleicaoDto> IniciarAsync(Guid id, CancellationToken cancellationToken = default);
    Task<EleicaoDto> EncerrarAsync(Guid id, CancellationToken cancellationToken = default);
    Task<EleicaoDto> SuspenderAsync(Guid id, string motivo, CancellationToken cancellationToken = default);
    Task<EleicaoDto> CancelarAsync(Guid id, string motivo, CancellationToken cancellationToken = default);
}
