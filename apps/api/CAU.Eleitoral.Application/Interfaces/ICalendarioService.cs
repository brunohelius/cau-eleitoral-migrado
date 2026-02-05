using CAU.Eleitoral.Application.DTOs.Calendario;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.Interfaces;

public interface ICalendarioService
{
    // CRUD Operations
    Task<CalendarioDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioDto>> GetAllAsync(CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioDto>> GetByFaseAsync(FaseEleicao fase, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioDto>> GetByTipoAsync(TipoCalendario tipo, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioDto>> GetByStatusAsync(StatusCalendario status, CancellationToken cancellationToken = default);
    Task<CalendarioDto> CreateAsync(CreateCalendarioDto dto, CancellationToken cancellationToken = default);
    Task<CalendarioDto> UpdateAsync(Guid id, UpdateCalendarioDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);

    // Status Operations
    Task<CalendarioDto> IniciarAsync(Guid id, CancellationToken cancellationToken = default);
    Task<CalendarioDto> ConcluirAsync(Guid id, CancellationToken cancellationToken = default);
    Task<CalendarioDto> CancelarAsync(Guid id, string motivo, CancellationToken cancellationToken = default);

    // Query Operations
    Task<CalendarioDto?> GetEventoAtualAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<CalendarioDto?> GetProximoEventoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioDto>> GetEventosPendentesAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioDto>> GetEventosHojeAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioDto>> GetEventosSemanaAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioDto>> GetEventosPorPeriodoAsync(Guid eleicaoId, DateTime inicio, DateTime fim, CancellationToken cancellationToken = default);
    Task<CalendarioResumoDto> GetResumoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    // Atividades
    Task<AtividadeCalendarioDto> AddAtividadePrincipalAsync(Guid calendarioId, CreateAtividadeCalendarioDto dto, CancellationToken cancellationToken = default);
    Task<AtividadeCalendarioDto> AddAtividadeSecundariaAsync(Guid calendarioId, CreateAtividadeCalendarioDto dto, CancellationToken cancellationToken = default);
    Task RemoveAtividadeAsync(Guid calendarioId, Guid atividadeId, bool principal, CancellationToken cancellationToken = default);
    Task<AtividadeCalendarioDto> ConcluirAtividadeAsync(Guid calendarioId, Guid atividadeId, bool principal, CancellationToken cancellationToken = default);

    // Ordering
    Task ReordenarEventosAsync(Guid eleicaoId, IEnumerable<(Guid Id, int Ordem)> novaOrdem, CancellationToken cancellationToken = default);

    // Template
    Task<IEnumerable<CalendarioDto>> CriarCalendarioPadraoAsync(Guid eleicaoId, DateTime dataInicioEleicao, CancellationToken cancellationToken = default);

    // Notifications
    Task VerificarNotificacoesAsync(CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioDto>> GetEventosParaNotificarAsync(CancellationToken cancellationToken = default);
}
