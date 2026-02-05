using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Application.Interfaces;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para dashboard e estatisticas
/// </summary>
[Authorize]
public class DashboardController : BaseController
{
    private readonly IDashboardService _dashboardService;
    private readonly ILogger<DashboardController> _logger;

    public DashboardController(IDashboardService dashboardService, ILogger<DashboardController> logger)
    {
        _dashboardService = dashboardService;
        _logger = logger;
    }

    /// <summary>
    /// Obtem estatisticas gerais do sistema
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Estatisticas gerais</returns>
    [HttpGet("geral")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(DashboardGeralDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<DashboardGeralDto>> GetDashboardGeral(CancellationToken cancellationToken)
    {
        try
        {
            var dashboard = await _dashboardService.GetDashboardGeralAsync(cancellationToken);
            return Ok(dashboard);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter dashboard geral");
            return InternalError("Erro ao obter dashboard");
        }
    }

    /// <summary>
    /// Obtem estatisticas de uma eleicao especifica
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Estatisticas da eleicao</returns>
    [HttpGet("eleicao/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(DashboardEleicaoDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<DashboardEleicaoDto>> GetDashboardEleicao(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var dashboard = await _dashboardService.GetDashboardEleicaoAsync(eleicaoId, cancellationToken);
            return Ok(dashboard);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter dashboard da eleicao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao obter dashboard");
        }
    }

    /// <summary>
    /// Obtem dashboard do usuario logado
    /// </summary>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dashboard do usuario</returns>
    [HttpGet("meu")]
    [ProducesResponseType(typeof(DashboardUsuarioDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<DashboardUsuarioDto>> GetMeuDashboard(CancellationToken cancellationToken)
    {
        try
        {
            var userId = GetUserId();
            var dashboard = await _dashboardService.GetDashboardUsuarioAsync(userId, cancellationToken);
            return Ok(dashboard);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter dashboard do usuario");
            return InternalError("Erro ao obter dashboard");
        }
    }

    /// <summary>
    /// Obtem estatisticas de votacao em tempo real
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Estatisticas de votacao</returns>
    [HttpGet("votacao/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(DashboardVotacaoDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<DashboardVotacaoDto>> GetDashboardVotacao(Guid eleicaoId, CancellationToken cancellationToken)
    {
        try
        {
            var dashboard = await _dashboardService.GetDashboardVotacaoAsync(eleicaoId, cancellationToken);
            return Ok(dashboard);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter dashboard de votacao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao obter dashboard");
        }
    }

    /// <summary>
    /// Obtem estatisticas de denuncias e impugnacoes
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao (opcional)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Estatisticas de processos</returns>
    [HttpGet("processos")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(DashboardProcessosDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<DashboardProcessosDto>> GetDashboardProcessos(
        [FromQuery] Guid? eleicaoId,
        CancellationToken cancellationToken)
    {
        try
        {
            var dashboard = await _dashboardService.GetDashboardProcessosAsync(eleicaoId, cancellationToken);
            return Ok(dashboard);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter dashboard de processos");
            return InternalError("Erro ao obter dashboard");
        }
    }

    /// <summary>
    /// Obtem linha do tempo de eventos
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao (opcional)</param>
    /// <param name="dias">Numero de dias (padrao: 30)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Linha do tempo</returns>
    [HttpGet("timeline")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(IEnumerable<TimelineEventoDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<TimelineEventoDto>>> GetTimeline(
        [FromQuery] Guid? eleicaoId,
        [FromQuery] int dias = 30,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var timeline = await _dashboardService.GetTimelineAsync(eleicaoId, dias, cancellationToken);
            return Ok(timeline);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter timeline");
            return InternalError("Erro ao obter timeline");
        }
    }

    /// <summary>
    /// Obtem grafico de participacao ao longo do tempo
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao</param>
    /// <param name="intervalo">Intervalo (hora, dia)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Dados do grafico</returns>
    [HttpGet("grafico-participacao/{eleicaoId:guid}")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(GraficoParticipacaoDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<GraficoParticipacaoDto>> GetGraficoParticipacao(
        Guid eleicaoId,
        [FromQuery] string intervalo = "hora",
        CancellationToken cancellationToken = default)
    {
        try
        {
            var grafico = await _dashboardService.GetGraficoParticipacaoAsync(eleicaoId, intervalo, cancellationToken);
            return Ok(grafico);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter grafico de participacao {EleicaoId}", eleicaoId);
            return InternalError("Erro ao obter grafico");
        }
    }

    /// <summary>
    /// Obtem resumo de atividades recentes
    /// </summary>
    /// <param name="limit">Limite de registros (padrao: 10)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>Atividades recentes</returns>
    [HttpGet("atividades-recentes")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(IEnumerable<AtividadeRecenteDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<AtividadeRecenteDto>>> GetAtividadesRecentes(
        [FromQuery] int limit = 10,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var atividades = await _dashboardService.GetAtividadesRecentesAsync(limit, cancellationToken);
            return Ok(atividades);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter atividades recentes");
            return InternalError("Erro ao obter atividades");
        }
    }

    /// <summary>
    /// Obtem indicadores de performance (KPIs)
    /// </summary>
    /// <param name="eleicaoId">ID da eleicao (opcional)</param>
    /// <param name="cancellationToken">Token de cancelamento</param>
    /// <returns>KPIs</returns>
    [HttpGet("kpis")]
    [Authorize(Roles = "Admin,ComissaoEleitoral")]
    [ProducesResponseType(typeof(KpisDto), StatusCodes.Status200OK)]
    public async Task<ActionResult<KpisDto>> GetKpis(
        [FromQuery] Guid? eleicaoId,
        CancellationToken cancellationToken = default)
    {
        try
        {
            var kpis = await _dashboardService.GetKpisAsync(eleicaoId, cancellationToken);
            return Ok(kpis);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao obter KPIs");
            return InternalError("Erro ao obter KPIs");
        }
    }
}

// DTOs para Dashboard
public record DashboardGeralDto
{
    public int TotalEleicoes { get; init; }
    public int EleicoesAtivas { get; init; }
    public int TotalUsuarios { get; init; }
    public int TotalProfissionais { get; init; }
    public int TotalVotosGeral { get; init; }
    public int DenunciasPendentes { get; init; }
    public int ImpugnacoesPendentes { get; init; }
    public List<EleicaoResumoDto> EleicoesRecentes { get; init; } = new();
    public List<EstatisticaPorTipoDto> EstatisticasPorTipo { get; init; } = new();
}

public record DashboardEleicaoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public string Status { get; init; } = string.Empty;
    public string Fase { get; init; } = string.Empty;
    public int TotalChapas { get; init; }
    public int ChapasAprovadas { get; init; }
    public int ChapasPendentes { get; init; }
    public int TotalEleitores { get; init; }
    public int EleitoresQueVotaram { get; init; }
    public decimal PercentualParticipacao { get; init; }
    public int TotalDenuncias { get; init; }
    public int TotalImpugnacoes { get; init; }
    public int DiasRestantes { get; init; }
    public List<EventoProximoDto> ProximosEventos { get; init; } = new();
    public List<ChapaResumoDto> Chapas { get; init; } = new();
}

public record DashboardUsuarioDto
{
    public int NotificacoesNaoLidas { get; init; }
    public int EleicoesDisponiveis { get; init; }
    public int VotosPendentes { get; init; }
    public int DenunciasCriadas { get; init; }
    public int ImpugnacoesCriadas { get; init; }
    public List<EleicaoUsuarioDto> MinhasEleicoes { get; init; } = new();
    public List<EventoProximoDto> ProximosEventos { get; init; } = new();
}

public record DashboardVotacaoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public bool VotacaoAberta { get; init; }
    public DateTime? InicioVotacao { get; init; }
    public DateTime? FimVotacao { get; init; }
    public int TotalEleitores { get; init; }
    public int VotosComputados { get; init; }
    public decimal PercentualParticipacao { get; init; }
    public int VotosUltimaHora { get; init; }
    public int VotosUltimos15Min { get; init; }
    public decimal TaxaVotacaoPorHora { get; init; }
    public List<VotacaoPorHoraDto> VotacaoPorHora { get; init; } = new();
}

public record DashboardProcessosDto
{
    public int TotalDenuncias { get; init; }
    public int DenunciasRecebidas { get; init; }
    public int DenunciasEmAnalise { get; init; }
    public int DenunciasJulgadas { get; init; }
    public int DenunciasArquivadas { get; init; }
    public int TotalImpugnacoes { get; init; }
    public int ImpugnacoesRecebidas { get; init; }
    public int ImpugnacoesEmAnalise { get; init; }
    public int ImpugnacoesJulgadas { get; init; }
    public int ImpugnacoesArquivadas { get; init; }
    public int JulgamentosAgendados { get; init; }
    public List<ProcessoPorStatusDto> DenunciasPorStatus { get; init; } = new();
    public List<ProcessoPorStatusDto> ImpugnacoesPorStatus { get; init; } = new();
}

public record EleicaoResumoDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string Status { get; init; } = string.Empty;
    public DateTime DataInicio { get; init; }
    public DateTime DataFim { get; init; }
    public int TotalVotos { get; init; }
}

public record EstatisticaPorTipoDto
{
    public string Tipo { get; init; } = string.Empty;
    public int Total { get; init; }
    public int Ativas { get; init; }
}

public record EventoProximoDto
{
    public Guid Id { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string Tipo { get; init; } = string.Empty;
    public DateTime Data { get; init; }
    public int DiasRestantes { get; init; }
}

public record ChapaResumoDto
{
    public Guid Id { get; init; }
    public int Numero { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string Status { get; init; } = string.Empty;
    public int TotalMembros { get; init; }
}

public record EleicaoUsuarioDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string Status { get; init; } = string.Empty;
    public bool PodeVotar { get; init; }
    public bool JaVotou { get; init; }
    public DateTime? DataVotacao { get; init; }
}

public record VotacaoPorHoraDto
{
    public DateTime Hora { get; init; }
    public int Votos { get; init; }
}

public record ProcessoPorStatusDto
{
    public string Status { get; init; } = string.Empty;
    public int Total { get; init; }
}

public record TimelineEventoDto
{
    public Guid Id { get; init; }
    public string Tipo { get; init; } = string.Empty;
    public string Titulo { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public DateTime Data { get; init; }
    public Guid? EntidadeId { get; init; }
    public string? EntidadeTipo { get; init; }
}

public record AtividadeRecenteDto
{
    public string Tipo { get; init; } = string.Empty;
    public string Descricao { get; init; } = string.Empty;
    public string? Usuario { get; init; }
    public DateTime Data { get; init; }
    public string? Link { get; init; }
}

public record KpisDto
{
    public decimal TaxaParticipacao { get; init; }
    public decimal TaxaResolucaoDenuncias { get; init; }
    public decimal TaxaResolucaoImpugnacoes { get; init; }
    public int TempoMedioProcssamentoDenuncia { get; init; }
    public int TempoMedioProcessamentoImpugnacao { get; init; }
    public decimal TaxaCrescimentoVotos { get; init; }
    public decimal SatisfacaoGeral { get; init; }
}

public record GraficoParticipacaoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public string Intervalo { get; init; } = string.Empty;
    public List<PontoGraficoDto> Pontos { get; init; } = new();
}

public record PontoGraficoDto
{
    public DateTime Momento { get; init; }
    public int Valor { get; init; }
    public decimal Percentual { get; init; }
}

// Interface do servico (a ser implementada)
public interface IDashboardService
{
    Task<DashboardGeralDto> GetDashboardGeralAsync(CancellationToken cancellationToken = default);
    Task<DashboardEleicaoDto> GetDashboardEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<DashboardUsuarioDto> GetDashboardUsuarioAsync(Guid userId, CancellationToken cancellationToken = default);
    Task<DashboardVotacaoDto> GetDashboardVotacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<DashboardProcessosDto> GetDashboardProcessosAsync(Guid? eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<TimelineEventoDto>> GetTimelineAsync(Guid? eleicaoId, int dias, CancellationToken cancellationToken = default);
    Task<GraficoParticipacaoDto> GetGraficoParticipacaoAsync(Guid eleicaoId, string intervalo, CancellationToken cancellationToken = default);
    Task<IEnumerable<AtividadeRecenteDto>> GetAtividadesRecentesAsync(int limit, CancellationToken cancellationToken = default);
    Task<KpisDto> GetKpisAsync(Guid? eleicaoId, CancellationToken cancellationToken = default);
}
