using CAU.Eleitoral.Application.DTOs.Apuracao;

namespace CAU.Eleitoral.Application.Interfaces;

/// <summary>
/// Service interface for vote tallying and election results management
/// </summary>
public interface IApuracaoService
{
    // Main vote counting
    /// <summary>
    /// Count and tally all votes for an election
    /// </summary>
    Task<ResultadoApuracaoDto> ApurarVotosAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Get full election results
    /// </summary>
    Task<ResultadoApuracaoDto?> GetResultadoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Get partial/real-time results during voting
    /// </summary>
    Task<ResultadoParcialDto> GetResultadoParcialAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Get final official results
    /// </summary>
    Task<ResultadoFinalDto> GetResultadoFinalAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    // Apuracao lifecycle
    /// <summary>
    /// Start the vote tallying process
    /// </summary>
    Task<StatusApuracaoDto> IniciarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Pause the vote tallying process
    /// </summary>
    Task<StatusApuracaoDto> PausarAsync(Guid eleicaoId, string motivo, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Resume the vote tallying process
    /// </summary>
    Task<StatusApuracaoDto> RetomarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Finalize the vote tallying process
    /// </summary>
    Task<ResultadoApuracaoDto> FinalizarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Homologate (officially validate) the results
    /// </summary>
    Task<ResultadoApuracaoDto> HomologarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Publish the results officially
    /// </summary>
    Task<ResultadoApuracaoDto> PublicarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);

    // Status and monitoring
    /// <summary>
    /// Get current status of the vote tallying process
    /// </summary>
    Task<StatusApuracaoDto> GetStatusAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Reprocess the vote tallying (in case of errors)
    /// </summary>
    Task<StatusApuracaoDto> ReprocessarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);

    // Winner determination
    /// <summary>
    /// Determine the winning ticket/chapa
    /// </summary>
    Task<ChapaVencedoraDto> DeterminarVencedorAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    // Statistics
    /// <summary>
    /// Get detailed voting statistics
    /// </summary>
    Task<EstatisticasVotacaoDto> GetEstatisticasAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Get vote distribution by region/UF
    /// </summary>
    Task<IEnumerable<VotosPorRegiaoDto>> GetVotosPorRegiaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Get votes per chapa
    /// </summary>
    Task<IEnumerable<VotosPorChapaDto>> GetVotosPorChapaAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    // Documents
    /// <summary>
    /// Get or generate the ata (minutes) of the vote tallying
    /// </summary>
    Task<AtaApuracaoDto> GetAtaAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Get the urna (ballot box) bulletin
    /// </summary>
    Task<BoletimUrnaDto> GetBoletimUrnaAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    // Eleitos (elected officials)
    /// <summary>
    /// Get list of elected officials
    /// </summary>
    Task<IEnumerable<EleitoDto>> GetEleitosAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
}
