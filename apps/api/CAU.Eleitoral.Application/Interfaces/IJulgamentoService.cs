using CAU.Eleitoral.Application.DTOs.Julgamentos;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IJulgamentoService
{
    // Comissao Julgadora
    Task<ComissaoJulgadoraDto?> GetComissaoByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<ComissaoJulgadoraDto>> GetComissoesByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<ComissaoJulgadoraDto>> GetComissoesAtivasAsync(CancellationToken cancellationToken = default);
    Task<ComissaoJulgadoraDto> CreateComissaoAsync(CreateComissaoJulgadoraDto dto, CancellationToken cancellationToken = default);
    Task<ComissaoJulgadoraDto> UpdateComissaoAsync(Guid id, UpdateComissaoJulgadoraDto dto, CancellationToken cancellationToken = default);
    Task DeleteComissaoAsync(Guid id, CancellationToken cancellationToken = default);
    Task<ComissaoJulgadoraDto> AtivarComissaoAsync(Guid id, CancellationToken cancellationToken = default);
    Task<ComissaoJulgadoraDto> DesativarComissaoAsync(Guid id, CancellationToken cancellationToken = default);

    // Membros da Comissao
    Task<MembroComissaoDto> AddMembroComissaoAsync(Guid comissaoId, CreateMembroComissaoDto dto, CancellationToken cancellationToken = default);
    Task RemoveMembroComissaoAsync(Guid comissaoId, Guid membroId, CancellationToken cancellationToken = default);
    Task<IEnumerable<MembroComissaoDto>> GetMembrosComissaoAsync(Guid comissaoId, CancellationToken cancellationToken = default);
    Task<MembroComissaoDto> AtualizarMembroComissaoAsync(Guid comissaoId, Guid membroId, TipoMembroComissao novoTipo, CancellationToken cancellationToken = default);

    // Sessoes de Julgamento
    Task<SessaoJulgamentoDto?> GetSessaoByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<SessaoJulgamentoDto>> GetSessoesByComissaoAsync(Guid comissaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<SessaoJulgamentoDto>> GetSessoesAgendadasAsync(CancellationToken cancellationToken = default);
    Task<SessaoJulgamentoDto> CreateSessaoAsync(CreateSessaoJulgamentoDto dto, CancellationToken cancellationToken = default);
    Task<SessaoJulgamentoDto> UpdateSessaoAsync(Guid id, UpdateSessaoJulgamentoDto dto, CancellationToken cancellationToken = default);
    Task DeleteSessaoAsync(Guid id, CancellationToken cancellationToken = default);
    Task<SessaoJulgamentoDto> IniciarSessaoAsync(Guid id, CancellationToken cancellationToken = default);
    Task<SessaoJulgamentoDto> EncerrarSessaoAsync(Guid id, CancellationToken cancellationToken = default);
    Task<SessaoJulgamentoDto> CancelarSessaoAsync(Guid id, string motivo, CancellationToken cancellationToken = default);
    Task<SessaoJulgamentoDto> AdiarSessaoAsync(Guid id, DateTime novaData, string motivo, CancellationToken cancellationToken = default);

    // Pauta
    Task<PautaSessaoDto> AddPautaAsync(Guid sessaoId, CreatePautaSessaoDto dto, CancellationToken cancellationToken = default);
    Task RemovePautaAsync(Guid sessaoId, Guid pautaId, CancellationToken cancellationToken = default);
    Task<IEnumerable<PautaSessaoDto>> GetPautasSessaoAsync(Guid sessaoId, CancellationToken cancellationToken = default);
    Task<PautaSessaoDto> MarcarPautaComoJulgadaAsync(Guid sessaoId, Guid pautaId, CancellationToken cancellationToken = default);
    Task<PautaSessaoDto> ReordenarPautaAsync(Guid sessaoId, Guid pautaId, int novaOrdem, CancellationToken cancellationToken = default);

    // Convocacao
    Task<string> GerarConvocacaoAsync(Guid sessaoId, CancellationToken cancellationToken = default);
    Task EnviarConvocacaoAsync(Guid sessaoId, CancellationToken cancellationToken = default);

    // Ata
    Task<string> GerarAtaSessaoAsync(Guid sessaoId, CancellationToken cancellationToken = default);
}
