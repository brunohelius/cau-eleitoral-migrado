using CAU.Eleitoral.Application.DTOs;
using CAU.Eleitoral.Domain.Enums;

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

    /// <summary>
    /// Advances the election to the next phase based on calendar
    /// </summary>
    Task<EleicaoDto> AvancarFaseAsync(Guid id, CancellationToken cancellationToken = default);

    /// <summary>
    /// Sets the election to a specific phase (admin only)
    /// </summary>
    Task<EleicaoDto> DefinirFaseAsync(Guid id, FaseEleicao fase, CancellationToken cancellationToken = default);

    /// <summary>
    /// Validates if the election can transition to a specific phase
    /// </summary>
    Task<EleicaoValidationResult> CanTransitionToPhaseAsync(Guid id, FaseEleicao fase, CancellationToken cancellationToken = default);

    /// <summary>
    /// Checks if an election can be deleted and returns validation info
    /// </summary>
    Task<EleicaoValidationResult> CanDeleteAsync(Guid id, CancellationToken cancellationToken = default);

    /// <summary>
    /// Checks if an election can be edited and returns validation info
    /// </summary>
    Task<EleicaoValidationResult> CanEditAsync(Guid id, CancellationToken cancellationToken = default);
}
