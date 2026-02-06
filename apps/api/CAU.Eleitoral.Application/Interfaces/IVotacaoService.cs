using CAU.Eleitoral.Application.DTOs.Votacao;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IVotacaoService
{
    // ===== Core Voting Operations (Public User) =====

    /// <summary>
    /// Verifica se o usuario (eleitor) pode votar na eleicao
    /// </summary>
    Task<ElegibilidadeVotoDto> VerificarElegibilidadeAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Verifica se o usuario ja votou na eleicao
    /// </summary>
    Task<StatusVotoDto> VerificarStatusVotoAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem a cedula de votacao com as chapas disponiveis
    /// </summary>
    Task<CedulaVotacaoDto> ObterCedulaAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Registra o voto do usuario
    /// </summary>
    Task<ComprovanteVotoDto> RegistrarVotoAsync(RegistrarVotoDto dto, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem o comprovante de voto do usuario
    /// </summary>
    Task<ComprovanteVotoDto?> ObterComprovanteAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Lista eleicoes disponiveis para o usuario votar
    /// </summary>
    Task<IEnumerable<EleicaoVotacaoDto>> GetEleicoesDisponiveisAsync(Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem historico de votos do usuario
    /// </summary>
    Task<IEnumerable<HistoricoVotoDto>> GetHistoricoAsync(Guid userId, CancellationToken cancellationToken = default);

    // ===== Administrative Operations =====

    /// <summary>
    /// Obtem estatisticas de votacao de uma eleicao
    /// </summary>
    Task<EstatisticasVotacaoDto> GetEstatisticasAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Lista eleitores que votaram em uma eleicao (paginado)
    /// </summary>
    Task<PagedResultDto<EleitorVotouDto>> GetEleitoresQueVotaramAsync(Guid eleicaoId, int page, int pageSize, CancellationToken cancellationToken = default);

    /// <summary>
    /// Anula um voto (operacao administrativa)
    /// </summary>
    Task AnularVotoAsync(Guid votoId, string motivo, Guid adminUserId, CancellationToken cancellationToken = default);

    // ===== Legacy/Extended Operations =====

    /// <summary>
    /// Registra um voto usando os DTOs legados
    /// </summary>
    Task<ComprovanteVotoLegadoDto> VotarAsync(RegistrarVotoLegadoDto dto, CancellationToken cancellationToken = default);

    /// <summary>
    /// Valida se um eleitor pode votar
    /// </summary>
    Task<ValidacaoVotoResultDto> ValidarEleitorAsync(ValidarVotoDto dto, CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem a cedula eleitoral usando DTOs legados
    /// </summary>
    Task<CedulaEleitoralDto> ObterCedulaEleitoralAsync(Guid eleicaoId, Guid eleitorId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem comprovante pelo hash do voto
    /// </summary>
    Task<ComprovanteVotoLegadoDto?> ObterComprovantePorHashAsync(string hashVoto, CancellationToken cancellationToken = default);

    /// <summary>
    /// Verifica se um voto existe pelo hash
    /// </summary>
    Task<bool> VerificarVotoAsync(string hashVoto, CancellationToken cancellationToken = default);

    // ===== Eleitor Management =====

    Task<EleitorDto?> GetEleitorByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<EleitorDto?> GetEleitorByProfissionalAsync(Guid eleicaoId, Guid profissionalId, CancellationToken cancellationToken = default);
    Task<IEnumerable<EleitorDto>> GetEleitoresByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<EleitorDto>> GetEleitoresAptosAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<EleitorDto>> GetEleitoresQueVotaramListaAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<EleitorDto>> GetEleitoresQueNaoVotaramAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<EleitorDto> RegistrarEleitorAsync(Guid eleicaoId, Guid profissionalId, CancellationToken cancellationToken = default);
    Task<EleitorDto> AtualizarAptidaoEleitorAsync(Guid eleitorId, bool apto, string? motivo, CancellationToken cancellationToken = default);

    // ===== Statistics =====

    Task<int> CountVotosAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<int> CountVotosByChapaAsync(Guid eleicaoId, Guid chapaId, CancellationToken cancellationToken = default);
    Task<int> CountVotosByTipoAsync(Guid eleicaoId, TipoVoto tipo, CancellationToken cancellationToken = default);
    Task<int> CountEleitoresAptosAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<int> CountEleitoresQueVotaramAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<double> GetPercentualComparecimentoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    // ===== Election Status Control =====

    Task<bool> EleicaoAbertaParaVotacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task AbrirVotacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task FecharVotacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    // ===== Import/Export =====

    Task<int> ImportarEleitoresAsync(Guid eleicaoId, IEnumerable<Guid> profissionalIds, CancellationToken cancellationToken = default);
    Task<IEnumerable<EleitorDto>> ExportarEleitoresAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
}
