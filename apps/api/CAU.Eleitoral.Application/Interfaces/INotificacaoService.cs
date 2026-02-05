using CAU.Eleitoral.Application.DTOs.Notificacoes;

namespace CAU.Eleitoral.Application.Interfaces;

public interface INotificacaoService
{
    // Send Notifications
    Task<NotificacaoDto> EnviarAsync(EnviarNotificacaoDto dto, CancellationToken cancellationToken = default);
    Task<IEnumerable<NotificacaoDto>> EnviarEmMassaAsync(EnviarNotificacaoEmMassaDto dto, CancellationToken cancellationToken = default);
    Task<IEnumerable<NotificacaoDto>> EnviarPorFiltroAsync(EnviarNotificacaoPorFiltroDto dto, CancellationToken cancellationToken = default);

    // CRUD Operations
    Task<NotificacaoDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<NotificacaoDto>> GetByUsuarioAsync(Guid usuarioId, CancellationToken cancellationToken = default);
    Task<IEnumerable<NotificacaoDto>> GetNaoLidasByUsuarioAsync(Guid usuarioId, CancellationToken cancellationToken = default);
    Task<NotificacoesResumoDto> GetResumoByUsuarioAsync(Guid usuarioId, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);
    Task DeleteAllByUsuarioAsync(Guid usuarioId, CancellationToken cancellationToken = default);

    // Read Operations
    Task<NotificacaoDto> MarcarComoLidaAsync(Guid id, CancellationToken cancellationToken = default);
    Task MarcarTodasComoLidasAsync(Guid usuarioId, CancellationToken cancellationToken = default);

    // User Preferences
    Task<ConfiguracaoNotificacaoUsuarioDto?> GetConfiguracaoUsuarioAsync(Guid usuarioId, CancellationToken cancellationToken = default);
    Task<ConfiguracaoNotificacaoUsuarioDto> AtualizarConfiguracaoUsuarioAsync(Guid usuarioId, AtualizarConfiguracaoNotificacaoDto dto, CancellationToken cancellationToken = default);

    // Statistics
    Task<int> CountNaoLidasAsync(Guid usuarioId, CancellationToken cancellationToken = default);
    Task<int> CountByTipoAsync(TipoNotificacao tipo, CancellationToken cancellationToken = default);

    // Scheduled Notifications
    Task ProcessarNotificacoesAgendadasAsync(CancellationToken cancellationToken = default);
    Task ReprocessarNotificacoesFalhasAsync(CancellationToken cancellationToken = default);

    // Templates
    Task<TemplateNotificacaoDto?> GetTemplateAsync(string codigo, CancellationToken cancellationToken = default);
    Task<IEnumerable<TemplateNotificacaoDto>> GetTemplatesAsync(TipoNotificacao? tipo = null, CancellationToken cancellationToken = default);

    // Email Specific
    Task EnviarEmailAsync(string destinatario, string assunto, string corpo, CancellationToken cancellationToken = default);
    Task EnviarEmailTemplateAsync(string destinatario, string templateCodigo, Dictionary<string, string> parametros, CancellationToken cancellationToken = default);

    // Cleanup
    Task LimparNotificacoesAntigasAsync(int diasRetencao = 90, CancellationToken cancellationToken = default);
}
