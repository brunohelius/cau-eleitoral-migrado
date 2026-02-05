using CAU.Eleitoral.Application.DTOs.Auditoria;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IAuditoriaService
{
    // Audit Logging
    Task RegistrarAsync(RegistrarAuditoriaDto dto, CancellationToken cancellationToken = default);
    Task RegistrarCriacaoAsync<T>(T entidade, Guid? usuarioId, string? ipAddress = null, CancellationToken cancellationToken = default);
    Task RegistrarAtualizacaoAsync<T>(T entidadeAnterior, T entidadeNova, Guid? usuarioId, string? ipAddress = null, CancellationToken cancellationToken = default);
    Task RegistrarExclusaoAsync<T>(T entidade, Guid? usuarioId, string? ipAddress = null, CancellationToken cancellationToken = default);
    Task RegistrarAcessoAsync(Guid usuarioId, string acao, string? recurso = null, bool sucesso = true, string? mensagem = null, string? ipAddress = null, CancellationToken cancellationToken = default);
    Task RegistrarLoginAsync(Guid usuarioId, bool sucesso, string? ipAddress = null, string? userAgent = null, string? mensagem = null, CancellationToken cancellationToken = default);
    Task RegistrarLogoutAsync(Guid usuarioId, string? ipAddress = null, CancellationToken cancellationToken = default);
    Task RegistrarVotoAsync(Guid eleicaoId, string hashVoto, string? ipAddress = null, CancellationToken cancellationToken = default);

    // Query Operations
    Task<LogAuditoriaDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<PaginatedResultDto<LogAuditoriaDto>> GetLogsAsync(FiltroAuditoriaDto filtro, CancellationToken cancellationToken = default);
    Task<IEnumerable<LogAuditoriaDto>> GetByUsuarioAsync(Guid usuarioId, int quantidade = 100, CancellationToken cancellationToken = default);
    Task<IEnumerable<LogAuditoriaDto>> GetByEntidadeAsync(string entidade, Guid? entidadeId = null, int quantidade = 100, CancellationToken cancellationToken = default);
    Task<IEnumerable<LogAuditoriaDto>> GetByPeriodoAsync(DateTime inicio, DateTime fim, CancellationToken cancellationToken = default);

    // Statistics
    Task<AuditoriaResumoDto> GetResumoAsync(CancellationToken cancellationToken = default);
    Task<AuditoriaPorUsuarioDto> GetResumoUsuarioAsync(Guid usuarioId, CancellationToken cancellationToken = default);
    Task<AuditoriaPorEntidadeDto> GetResumoEntidadeAsync(string entidade, Guid? entidadeId = null, CancellationToken cancellationToken = default);

    // Access Log Operations
    Task<PaginatedResultDto<LogAcessoDto>> GetLogsAcessoAsync(FiltroLogAcessoDto filtro, CancellationToken cancellationToken = default);
    Task<IEnumerable<LogAcessoDto>> GetUltimosAcessosUsuarioAsync(Guid usuarioId, int quantidade = 10, CancellationToken cancellationToken = default);

    // Export
    Task<byte[]> ExportarLogsAsync(ExportarAuditoriaDto dto, CancellationToken cancellationToken = default);
    Task<Stream> ExportarLogsStreamAsync(ExportarAuditoriaDto dto, CancellationToken cancellationToken = default);

    // Cleanup
    Task LimparLogsAntigosAsync(int diasRetencao = 365, CancellationToken cancellationToken = default);
    Task ArquivarLogsAsync(DateTime dataLimite, CancellationToken cancellationToken = default);
}
