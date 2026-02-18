using CAU.Eleitoral.Application.DTOs.Comissoes;
using CAU.Eleitoral.Domain.Entities.Comissoes;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IComissaoService
{
    // ComissaoEleitoral
    Task<ComissaoEleitoralDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<ComissaoEleitoralDto>> GetByCalendarioAsync(Guid calendarioId, CancellationToken cancellationToken = default);
    Task<IEnumerable<ComissaoEleitoralDto>> GetAtivasAsync(CancellationToken cancellationToken = default);
    Task<PagedResult<ComissaoEleitoralDto>> GetAllAsync(ComissaoEleitoralFilter filter, CancellationToken cancellationToken = default);
    Task<ComissaoEleitoralDto> CreateAsync(CreateComissaoEleitoralDto dto, CancellationToken cancellationToken = default);
    Task<ComissaoEleitoralDto> UpdateAsync(Guid id, UpdateComissaoEleitoralDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);
    Task<ComissaoEleitoralDto> AtivarAsync(Guid id, CancellationToken cancellationToken = default);
    Task<ComissaoEleitoralDto> EncerrarAsync(Guid id, string motivo, CancellationToken cancellationToken = default);

    // MembroComissao
    Task<MembroComissaoDto?> GetMembroByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<MembroComissaoDto>> GetMembrosByComissaoAsync(Guid comissaoId, CancellationToken cancellationToken = default);
    Task<PagedResult<MembroComissaoDto>> GetAllMembrosAsync(MembroComissaoFilter filter, CancellationToken cancellationToken = default);
    Task<MembroComissaoDto> CreateMembroAsync(CreateMembroComissaoDto dto, CancellationToken cancellationToken = default);
    Task<MembroComissaoDto> UpdateMembroAsync(Guid id, UpdateMembroComissaoDto dto, CancellationToken cancellationToken = default);
    Task DeleteMembroAsync(Guid id, CancellationToken cancellationToken = default);
    Task<MembroComissaoDto> AtivarMembroAsync(Guid id, CancellationToken cancellationToken = default);
    Task<MembroComissaoDto> InativarMembroAsync(Guid id, CancellationToken cancellationToken = default);
    Task<MembroComissaoDto> ResponderDeclaracaoAsync(Guid id, bool resposta, CancellationToken cancellationToken = default);

    // MembroComissaoSituacao
    Task<MembroComissaoSituacaoDto> AddSituacaoAsync(CreateMembroComissaoSituacaoDto dto, CancellationToken cancellationToken = default);
    Task<IEnumerable<MembroComissaoSituacaoDto>> GetHistoricoSituacoesAsync(Guid membroId, CancellationToken cancellationToken = default);

    // Documentos
    Task<ComissaoDocumentoDto> UploadDocumentoAsync(CreateComissaoDocumentoDto dto, CancellationToken cancellationToken = default);
    Task DeleteDocumentoAsync(Guid id, CancellationToken cancellationToken = default);
    Task<MembroComissaoDocumentoDto> UploadMembroDocumentoAsync(CreateMembroComissaoDocumentoDto dto, CancellationToken cancellationToken = default);
    Task DeleteMembroDocumentoAsync(Guid id, CancellationToken cancellationToken = default);

    // Validações
    Task<bool> ValidarMembroComissaoEleicaoVigenteAsync(CancellationToken cancellationToken = default);
    Task<IEnumerable<MembroComissaoDto>> GetMembrosComissaoLogadoAsync(CancellationToken cancellationToken = default);
}