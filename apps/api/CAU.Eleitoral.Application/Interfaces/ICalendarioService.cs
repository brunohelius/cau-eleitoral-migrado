using CAU.Eleitoral.Application.DTOs.Calendario;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.Interfaces;

/// <summary>
/// Result of period validation
/// </summary>
public class PeriodoValidacaoResult
{
    public bool IsValid { get; set; }
    public string? Message { get; set; }
    public TipoCalendario? PeriodoAtual { get; set; }
    public DateTime? DataInicioPeriodo { get; set; }
    public DateTime? DataFimPeriodo { get; set; }
}

public interface ICalendarioService
{
    #region Period Validation Methods

    /// <summary>
    /// Checks if the current date is within a specific calendar period for an election
    /// </summary>
    Task<bool> IsWithinPeriodAsync(Guid eleicaoId, TipoCalendario tipo, CancellationToken cancellationToken = default);

    /// <summary>
    /// Checks if the current date is within any of the specified calendar periods
    /// </summary>
    Task<bool> IsWithinAnyPeriodAsync(Guid eleicaoId, TipoCalendario[] tipos, CancellationToken cancellationToken = default);

    /// <summary>
    /// Gets the current active period for an election
    /// </summary>
    Task<CalendarioDto?> GetPeriodoAtualAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Gets the next upcoming period for an election
    /// </summary>
    Task<CalendarioDto?> GetProximoPeriodoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Validates if an action is allowed based on permitted calendar periods.
    /// Throws InvalidOperationException if validation fails.
    /// </summary>
    Task<PeriodoValidacaoResult> ValidarPeriodoAsync(Guid eleicaoId, TipoCalendario[] tiposPermitidos, string nomeAcao, CancellationToken cancellationToken = default);

    /// <summary>
    /// Checks if a specific period has already passed (completed)
    /// </summary>
    Task<bool> PeriodoJaPassouAsync(Guid eleicaoId, TipoCalendario tipo, CancellationToken cancellationToken = default);

    /// <summary>
    /// Checks if a specific period has not started yet
    /// </summary>
    Task<bool> PeriodoAindaNaoIniciouAsync(Guid eleicaoId, TipoCalendario tipo, CancellationToken cancellationToken = default);

    /// <summary>
    /// Gets all active periods for an election at the current moment
    /// </summary>
    Task<IEnumerable<CalendarioDto>> GetPeriodosAtivosAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    #endregion

    #region CRUD Operations

    Task<CalendarioDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioDto>> GetAllAsync(CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioDto>> GetByFaseAsync(FaseEleicao fase, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioDto>> GetByTipoAsync(TipoCalendario tipo, CancellationToken cancellationToken = default);
    Task<IEnumerable<CalendarioDto>> GetByStatusAsync(StatusCalendario status, CancellationToken cancellationToken = default);
    Task<CalendarioDto> CreateAsync(CreateCalendarioDto dto, CancellationToken cancellationToken = default);
    Task<CalendarioDto> UpdateAsync(Guid id, UpdateCalendarioDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);

    #endregion

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
