using CAU.Eleitoral.Application.DTOs;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IChapaService
{
    Task<IEnumerable<ChapaDto>> GetAllAsync(Guid? eleicaoId = null);
    Task<ChapaDto?> GetByIdAsync(Guid id);
    Task<IEnumerable<ChapaDto>> GetByEleicaoAsync(Guid eleicaoId);
    Task<ChapaDto> CreateAsync(CreateChapaDto dto);
    Task<ChapaDto> UpdateAsync(Guid id, UpdateChapaDto dto);
    Task DeleteAsync(Guid id);
    Task<ChapaDto> SubmeterParaAnaliseAsync(Guid id);
    Task<ChapaDto> DeferirAsync(Guid id, string parecer, Guid analistId);
    Task<ChapaDto> IndeferirAsync(Guid id, string motivo, Guid analistId);
    Task<MembroChapaDto> AddMembroAsync(Guid chapaId, CreateMembroChapaDto dto);
    Task RemoveMembroAsync(Guid chapaId, Guid membroId);
}
