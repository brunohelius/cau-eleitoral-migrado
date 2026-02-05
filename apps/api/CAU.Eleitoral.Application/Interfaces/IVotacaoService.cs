using CAU.Eleitoral.Application.DTOs.Votacao;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IVotacaoService
{
    // Voting Operations
    Task<ComprovanteVotoDto> VotarAsync(RegistrarVotoDto dto, CancellationToken cancellationToken = default);
    Task<ValidacaoVotoResultDto> ValidarEleitorAsync(ValidarVotoDto dto, CancellationToken cancellationToken = default);
    Task<CedulaEleitoralDto> ObterCedulaAsync(Guid eleicaoId, Guid eleitorId, CancellationToken cancellationToken = default);
    Task<ComprovanteVotoDto?> ObterComprovanteAsync(string hashVoto, CancellationToken cancellationToken = default);
    Task<bool> VerificarVotoAsync(string hashVoto, CancellationToken cancellationToken = default);

    // Eleitor Operations
    Task<EleitorDto?> GetEleitorByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<EleitorDto?> GetEleitorByProfissionalAsync(Guid eleicaoId, Guid profissionalId, CancellationToken cancellationToken = default);
    Task<IEnumerable<EleitorDto>> GetEleitoresByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<EleitorDto>> GetEleitoresAptosAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<EleitorDto>> GetEleitoresQueVotaramAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<EleitorDto>> GetEleitoresQueNaoVotaramAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<EleitorDto> RegistrarEleitorAsync(Guid eleicaoId, Guid profissionalId, CancellationToken cancellationToken = default);
    Task<EleitorDto> AtualizarAptidaoEleitorAsync(Guid eleitorId, bool apto, string? motivo, CancellationToken cancellationToken = default);

    // Statistics
    Task<EstatisticasVotacaoDto> GetEstatisticasAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<int> CountVotosAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<int> CountVotosByChapaAsync(Guid eleicaoId, Guid chapaId, CancellationToken cancellationToken = default);
    Task<int> CountVotosByTipoAsync(Guid eleicaoId, TipoVoto tipo, CancellationToken cancellationToken = default);
    Task<int> CountEleitoresAptosAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<int> CountEleitoresQueVotaramAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<double> GetPercentualComparecimentoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    // Election Status
    Task<bool> EleicaoAbertaParaVotacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task AbrirVotacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task FecharVotacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    // Import/Export
    Task<int> ImportarEleitoresAsync(Guid eleicaoId, IEnumerable<Guid> profissionalIds, CancellationToken cancellationToken = default);
    Task<IEnumerable<EleitorDto>> ExportarEleitoresAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
}
