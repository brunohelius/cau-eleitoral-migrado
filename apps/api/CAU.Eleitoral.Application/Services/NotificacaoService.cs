using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Notificacoes;
using CAU.Eleitoral.Application.Interfaces;

namespace CAU.Eleitoral.Application.Services;

public class NotificacaoService : INotificacaoService
{
    private readonly ILogger<NotificacaoService> _logger;

    // In-memory storage for notifications (in production, use a database)
    private static readonly Dictionary<Guid, NotificacaoDto> _notificacoes = new();
    private static readonly Dictionary<Guid, ConfiguracaoNotificacaoUsuarioDto> _configuracoes = new();

    public NotificacaoService(ILogger<NotificacaoService> logger)
    {
        _logger = logger;
    }

    public async Task<NotificacaoDto> EnviarAsync(EnviarNotificacaoDto dto, CancellationToken cancellationToken = default)
    {
        var notificacao = new NotificacaoDto
        {
            Id = Guid.NewGuid(),
            UsuarioId = dto.UsuarioId,
            UsuarioNome = "",
            Tipo = dto.Tipo,
            TipoNome = dto.Tipo.ToString(),
            Canal = dto.Canal,
            CanalNome = dto.Canal.ToString(),
            Status = StatusNotificacao.Enviada,
            StatusNome = StatusNotificacao.Enviada.ToString(),
            Titulo = dto.Titulo,
            Mensagem = dto.Mensagem,
            Link = dto.Link,
            Dados = dto.Dados != null ? System.Text.Json.JsonSerializer.Serialize(dto.Dados) : null,
            Lida = false,
            DataEnvio = DateTime.UtcNow,
            DataEntrega = DateTime.UtcNow,
            CreatedAt = DateTime.UtcNow
        };

        _notificacoes[notificacao.Id] = notificacao;

        _logger.LogInformation("Notificacao enviada: {NotificacaoId} para usuario {UsuarioId}", notificacao.Id, dto.UsuarioId);

        return notificacao;
    }

    public async Task<IEnumerable<NotificacaoDto>> EnviarEmMassaAsync(EnviarNotificacaoEmMassaDto dto, CancellationToken cancellationToken = default)
    {
        var notificacoes = new List<NotificacaoDto>();

        foreach (var usuarioId in dto.UsuarioIds)
        {
            var notificacao = await EnviarAsync(new EnviarNotificacaoDto
            {
                UsuarioId = usuarioId,
                Tipo = dto.Tipo,
                Canal = dto.Canal,
                Titulo = dto.Titulo,
                Mensagem = dto.Mensagem,
                Link = dto.Link,
                Dados = dto.Dados
            }, cancellationToken);

            notificacoes.Add(notificacao);
        }

        _logger.LogInformation("Notificacao em massa enviada para {Count} usuarios", notificacoes.Count);

        return notificacoes;
    }

    public async Task<IEnumerable<NotificacaoDto>> EnviarPorFiltroAsync(EnviarNotificacaoPorFiltroDto dto, CancellationToken cancellationToken = default)
    {
        // In real implementation, query users based on filter
        // For now, return empty list
        _logger.LogInformation("Notificacao por filtro solicitada");
        return new List<NotificacaoDto>();
    }

    public async Task<NotificacaoDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        _notificacoes.TryGetValue(id, out var notificacao);
        return notificacao;
    }

    public async Task<IEnumerable<NotificacaoDto>> GetByUsuarioAsync(Guid usuarioId, CancellationToken cancellationToken = default)
    {
        return _notificacoes.Values
            .Where(n => n.UsuarioId == usuarioId)
            .OrderByDescending(n => n.DataEnvio)
            .ToList();
    }

    public async Task<IEnumerable<NotificacaoDto>> GetNaoLidasByUsuarioAsync(Guid usuarioId, CancellationToken cancellationToken = default)
    {
        return _notificacoes.Values
            .Where(n => n.UsuarioId == usuarioId && !n.Lida)
            .OrderByDescending(n => n.DataEnvio)
            .ToList();
    }

    public async Task<NotificacoesResumoDto> GetResumoByUsuarioAsync(Guid usuarioId, CancellationToken cancellationToken = default)
    {
        var notificacoesUsuario = _notificacoes.Values
            .Where(n => n.UsuarioId == usuarioId)
            .ToList();

        var hoje = DateTime.UtcNow.Date;
        var inicioSemana = hoje.AddDays(-(int)hoje.DayOfWeek);

        return new NotificacoesResumoDto
        {
            TotalNaoLidas = notificacoesUsuario.Count(n => !n.Lida),
            TotalHoje = notificacoesUsuario.Count(n => n.DataEnvio.Date == hoje),
            TotalSemana = notificacoesUsuario.Count(n => n.DataEnvio.Date >= inicioSemana),
            UltimasNotificacoes = notificacoesUsuario
                .OrderByDescending(n => n.DataEnvio)
                .Take(10)
                .ToList()
        };
    }

    public async Task DeleteAsync(Guid id, CancellationToken cancellationToken = default)
    {
        _notificacoes.Remove(id);
        _logger.LogInformation("Notificacao excluida: {NotificacaoId}", id);
    }

    public async Task DeleteAllByUsuarioAsync(Guid usuarioId, CancellationToken cancellationToken = default)
    {
        var idsParaRemover = _notificacoes.Values
            .Where(n => n.UsuarioId == usuarioId)
            .Select(n => n.Id)
            .ToList();

        foreach (var id in idsParaRemover)
        {
            _notificacoes.Remove(id);
        }

        _logger.LogInformation("Todas as notificacoes do usuario {UsuarioId} foram excluidas", usuarioId);
    }

    public async Task<NotificacaoDto> MarcarComoLidaAsync(Guid id, CancellationToken cancellationToken = default)
    {
        if (_notificacoes.TryGetValue(id, out var notificacao))
        {
            notificacao = notificacao with
            {
                Lida = true,
                DataLeitura = DateTime.UtcNow
            };
            _notificacoes[id] = notificacao;

            _logger.LogInformation("Notificacao {NotificacaoId} marcada como lida", id);

            return notificacao;
        }

        throw new KeyNotFoundException($"Notificacao {id} nao encontrada");
    }

    public async Task MarcarTodasComoLidasAsync(Guid usuarioId, CancellationToken cancellationToken = default)
    {
        var notificacoesUsuario = _notificacoes.Values
            .Where(n => n.UsuarioId == usuarioId && !n.Lida)
            .ToList();

        foreach (var notificacao in notificacoesUsuario)
        {
            _notificacoes[notificacao.Id] = notificacao with
            {
                Lida = true,
                DataLeitura = DateTime.UtcNow
            };
        }

        _logger.LogInformation("Todas as notificacoes do usuario {UsuarioId} foram marcadas como lidas", usuarioId);
    }

    public async Task<ConfiguracaoNotificacaoUsuarioDto?> GetConfiguracaoUsuarioAsync(Guid usuarioId, CancellationToken cancellationToken = default)
    {
        if (_configuracoes.TryGetValue(usuarioId, out var config))
            return config;

        // Return default configuration
        return new ConfiguracaoNotificacaoUsuarioDto
        {
            UsuarioId = usuarioId,
            ReceberEmail = true,
            ReceberSMS = false,
            ReceberPush = true,
            NotificacoesEleicao = true,
            NotificacoesVotacao = true,
            NotificacoesResultado = true,
            NotificacoesCalendario = true,
            NotificacoesDenuncia = false
        };
    }

    public async Task<ConfiguracaoNotificacaoUsuarioDto> AtualizarConfiguracaoUsuarioAsync(Guid usuarioId, AtualizarConfiguracaoNotificacaoDto dto, CancellationToken cancellationToken = default)
    {
        var config = await GetConfiguracaoUsuarioAsync(usuarioId, cancellationToken)
            ?? new ConfiguracaoNotificacaoUsuarioDto { UsuarioId = usuarioId };

        config = config with
        {
            ReceberEmail = dto.ReceberEmail ?? config.ReceberEmail,
            ReceberSMS = dto.ReceberSMS ?? config.ReceberSMS,
            ReceberPush = dto.ReceberPush ?? config.ReceberPush,
            NotificacoesEleicao = dto.NotificacoesEleicao ?? config.NotificacoesEleicao,
            NotificacoesVotacao = dto.NotificacoesVotacao ?? config.NotificacoesVotacao,
            NotificacoesResultado = dto.NotificacoesResultado ?? config.NotificacoesResultado,
            NotificacoesCalendario = dto.NotificacoesCalendario ?? config.NotificacoesCalendario,
            NotificacoesDenuncia = dto.NotificacoesDenuncia ?? config.NotificacoesDenuncia
        };

        _configuracoes[usuarioId] = config;

        _logger.LogInformation("Configuracao de notificacao atualizada para usuario {UsuarioId}", usuarioId);

        return config;
    }

    public async Task<int> CountNaoLidasAsync(Guid usuarioId, CancellationToken cancellationToken = default)
    {
        return _notificacoes.Values.Count(n => n.UsuarioId == usuarioId && !n.Lida);
    }

    public async Task<int> CountByTipoAsync(TipoNotificacao tipo, CancellationToken cancellationToken = default)
    {
        return _notificacoes.Values.Count(n => n.Tipo == tipo);
    }

    public async Task ProcessarNotificacoesAgendadasAsync(CancellationToken cancellationToken = default)
    {
        _logger.LogInformation("Processamento de notificacoes agendadas executado");
    }

    public async Task ReprocessarNotificacoesFalhasAsync(CancellationToken cancellationToken = default)
    {
        var falhas = _notificacoes.Values
            .Where(n => n.Status == StatusNotificacao.Falha)
            .ToList();

        foreach (var notificacao in falhas)
        {
            // Retry sending
            _notificacoes[notificacao.Id] = notificacao with
            {
                Status = StatusNotificacao.Enviada,
                StatusNome = StatusNotificacao.Enviada.ToString(),
                DataEntrega = DateTime.UtcNow
            };
        }

        _logger.LogInformation("{Count} notificacoes com falha reprocessadas", falhas.Count);
    }

    public async Task<TemplateNotificacaoDto?> GetTemplateAsync(string codigo, CancellationToken cancellationToken = default)
    {
        // In real implementation, get from database
        return new TemplateNotificacaoDto
        {
            Id = Guid.NewGuid(),
            Codigo = codigo,
            Tipo = TipoNotificacao.Sistema,
            Titulo = "Template Padrao",
            Corpo = "Conteudo do template {0}",
            Ativo = true
        };
    }

    public async Task<IEnumerable<TemplateNotificacaoDto>> GetTemplatesAsync(TipoNotificacao? tipo = null, CancellationToken cancellationToken = default)
    {
        // In real implementation, get from database
        return new List<TemplateNotificacaoDto>
        {
            new TemplateNotificacaoDto
            {
                Id = Guid.NewGuid(),
                Codigo = "ELEICAO_INICIADA",
                Tipo = TipoNotificacao.Eleicao,
                Titulo = "Eleicao Iniciada",
                Corpo = "A eleicao {0} foi iniciada.",
                Ativo = true
            },
            new TemplateNotificacaoDto
            {
                Id = Guid.NewGuid(),
                Codigo = "VOTACAO_ABERTA",
                Tipo = TipoNotificacao.Votacao,
                Titulo = "Votacao Aberta",
                Corpo = "A votacao para a eleicao {0} esta aberta.",
                Ativo = true
            }
        };
    }

    public async Task EnviarEmailAsync(string destinatario, string assunto, string corpo, CancellationToken cancellationToken = default)
    {
        // In real implementation, send email via SMTP or email service
        _logger.LogInformation("Email enviado para {Destinatario}: {Assunto}", destinatario, assunto);
    }

    public async Task EnviarEmailTemplateAsync(string destinatario, string templateCodigo, Dictionary<string, string> parametros, CancellationToken cancellationToken = default)
    {
        var template = await GetTemplateAsync(templateCodigo, cancellationToken);

        if (template != null)
        {
            var corpo = template.Corpo;
            foreach (var param in parametros)
            {
                corpo = corpo.Replace($"{{{param.Key}}}", param.Value);
            }

            await EnviarEmailAsync(destinatario, template.Titulo, corpo, cancellationToken);
        }
    }

    public async Task LimparNotificacoesAntigasAsync(int diasRetencao = 90, CancellationToken cancellationToken = default)
    {
        var dataLimite = DateTime.UtcNow.AddDays(-diasRetencao);
        var antigas = _notificacoes.Values
            .Where(n => n.DataEnvio < dataLimite)
            .Select(n => n.Id)
            .ToList();

        foreach (var id in antigas)
        {
            _notificacoes.Remove(id);
        }

        _logger.LogInformation("{Count} notificacoes antigas removidas", antigas.Count);
    }
}
