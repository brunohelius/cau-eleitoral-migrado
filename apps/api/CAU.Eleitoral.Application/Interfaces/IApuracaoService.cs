using CAU.Eleitoral.Application.DTOs.Apuracao;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IApuracaoService
{
    // Apuracao Operations
    Task<ApuracaoResumoDto> IniciarApuracaoAsync(IniciarApuracaoDto dto, CancellationToken cancellationToken = default);
    Task<ApuracaoResumoDto> GetResumoApuracaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<ResultadoEleicaoDto> FinalizarApuracaoAsync(FinalizarApuracaoDto dto, CancellationToken cancellationToken = default);
    Task<ResultadoEleicaoDto> ReprocessarApuracaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    // Result Operations
    Task<ResultadoEleicaoDto?> GetResultadoByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<ResultadoEleicaoDto?> GetResultadoByEleicaoAsync(Guid eleicaoId, bool final = true, CancellationToken cancellationToken = default);
    Task<IEnumerable<ResultadoEleicaoDto>> GetResultadosParciaisAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<ResultadoEleicaoDto> PublicarResultadoAsync(PublicarResultadoDto dto, CancellationToken cancellationToken = default);
    Task DespublicarResultadoAsync(Guid resultadoId, CancellationToken cancellationToken = default);

    // Vote Details
    Task<IEnumerable<VotoChapaDto>> GetVotosPorChapaAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<VotoChapaDto?> GetChapaVencedoraAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<VotoChapaDto>> GetClassificacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    // Anulacao
    Task<VotoChapaDto> AnularVotoAsync(AnularVotoDto dto, CancellationToken cancellationToken = default);
    Task<VotoChapaDto> ReanalisarVotoAsync(ReanalizarVotoDto dto, CancellationToken cancellationToken = default);
    Task<IEnumerable<VotoChapaDto>> GetVotosAnuladosAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    // Ata
    Task<AtaApuracaoDto> GerarAtaApuracaoAsync(GerarAtaApuracaoDto dto, CancellationToken cancellationToken = default);
    Task<AtaApuracaoDto?> GetAtaApuracaoAsync(Guid resultadoId, CancellationToken cancellationToken = default);
    Task<AtaApuracaoDto> AssinarAtaAsync(Guid ataId, Guid membroId, CancellationToken cancellationToken = default);
    Task<string> ExportarAtaAsync(Guid ataId, string formato, CancellationToken cancellationToken = default);

    // Statistics
    Task<ApuracaoResumoDto> GetEstatisticasApuracaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<bool> ApuracaoFinalizadaAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<double> GetProgressoApuracaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
}
