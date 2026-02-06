using Microsoft.EntityFrameworkCore;
using CAU.Eleitoral.Api.Controllers;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Infrastructure.Data;

namespace CAU.Eleitoral.Api.Services;

public class DashboardService : IDashboardService
{
    private readonly AppDbContext _context;

    public DashboardService(AppDbContext context)
    {
        _context = context;
    }

    public async Task<DashboardGeralDto> GetDashboardGeralAsync(CancellationToken cancellationToken = default)
    {
        var totalEleicoes = await _context.Eleicoes.CountAsync(cancellationToken);
        var eleicoesAtivas = await _context.Eleicoes.CountAsync(e => e.Status == StatusEleicao.EmAndamento, cancellationToken);
        var totalUsuarios = await _context.Usuarios.CountAsync(cancellationToken);
        var totalProfissionais = await _context.Profissionais.CountAsync(cancellationToken);
        var totalVotos = await _context.Votos.CountAsync(cancellationToken);
        var denunciasPendentes = await _context.Denuncias.CountAsync(d => d.Status == StatusDenuncia.Recebida || d.Status == StatusDenuncia.EmAnalise, cancellationToken);
        var impugnacoesPendentes = await _context.Impugnacoes.CountAsync(i => i.Status == StatusImpugnacao.Recebida || i.Status == StatusImpugnacao.EmAnalise, cancellationToken);

        var eleicoesRecentes = await _context.Eleicoes
            .OrderByDescending(e => e.DataInicio)
            .Take(5)
            .Select(e => new EleicaoResumoDto
            {
                Id = e.Id,
                Nome = e.Nome,
                Status = e.Status.ToString(),
                DataInicio = e.DataInicio,
                DataFim = e.DataFim,
                TotalVotos = _context.Votos.Count(v => v.EleicaoId == e.Id)
            })
            .ToListAsync(cancellationToken);

        return new DashboardGeralDto
        {
            TotalEleicoes = totalEleicoes,
            EleicoesAtivas = eleicoesAtivas,
            TotalUsuarios = totalUsuarios,
            TotalProfissionais = totalProfissionais,
            TotalVotosGeral = totalVotos,
            DenunciasPendentes = denunciasPendentes,
            ImpugnacoesPendentes = impugnacoesPendentes,
            EleicoesRecentes = eleicoesRecentes
        };
    }

    public async Task<DashboardEleicaoDto> GetDashboardEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await _context.Eleicoes.FirstOrDefaultAsync(e => e.Id == eleicaoId, cancellationToken);
        if (eleicao == null) return new DashboardEleicaoDto();

        var totalChapas = await _context.Chapas.CountAsync(c => c.EleicaoId == eleicaoId, cancellationToken);
        var chapasAprovadas = await _context.Chapas.CountAsync(c => c.EleicaoId == eleicaoId && (c.Status == StatusChapa.Deferida || c.Status == StatusChapa.Registrada), cancellationToken);
        var totalEleitores = await _context.Eleitores.CountAsync(e => e.EleicaoId == eleicaoId, cancellationToken);
        var votaram = await _context.Eleitores.CountAsync(e => e.EleicaoId == eleicaoId && e.Votou, cancellationToken);
        var totalDenuncias = await _context.Denuncias.CountAsync(d => d.EleicaoId == eleicaoId, cancellationToken);
        var totalImpugnacoes = await _context.Impugnacoes.CountAsync(i => i.EleicaoId == eleicaoId, cancellationToken);

        return new DashboardEleicaoDto
        {
            EleicaoId = eleicaoId,
            EleicaoNome = eleicao.Nome,
            Status = eleicao.Status.ToString(),
            Fase = eleicao.FaseAtual.ToString(),
            TotalChapas = totalChapas,
            ChapasAprovadas = chapasAprovadas,
            ChapasPendentes = totalChapas - chapasAprovadas,
            TotalEleitores = totalEleitores,
            EleitoresQueVotaram = votaram,
            PercentualParticipacao = totalEleitores > 0 ? Math.Round((decimal)votaram / totalEleitores * 100, 2) : 0,
            TotalDenuncias = totalDenuncias,
            TotalImpugnacoes = totalImpugnacoes,
            DiasRestantes = (eleicao.DataFim - DateTime.UtcNow).Days
        };
    }

    public async Task<DashboardUsuarioDto> GetDashboardUsuarioAsync(Guid userId, CancellationToken cancellationToken = default)
    {
        var eleicoesAtivas = await _context.Eleicoes.CountAsync(e => e.Status == StatusEleicao.EmAndamento, cancellationToken);
        var denuncias = await _context.Denuncias.CountAsync(d => d.DenuncianteId != null, cancellationToken);

        return new DashboardUsuarioDto
        {
            EleicoesDisponiveis = eleicoesAtivas,
            DenunciasCriadas = denuncias
        };
    }

    public async Task<DashboardVotacaoDto> GetDashboardVotacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await _context.Eleicoes.FirstOrDefaultAsync(e => e.Id == eleicaoId, cancellationToken);
        if (eleicao == null) return new DashboardVotacaoDto();

        var totalEleitores = await _context.Eleitores.CountAsync(e => e.EleicaoId == eleicaoId, cancellationToken);
        var votosComputados = await _context.Votos.CountAsync(v => v.EleicaoId == eleicaoId, cancellationToken);

        return new DashboardVotacaoDto
        {
            EleicaoId = eleicaoId,
            EleicaoNome = eleicao.Nome,
            VotacaoAberta = eleicao.FaseAtual == FaseEleicao.Votacao,
            InicioVotacao = eleicao.DataVotacaoInicio,
            FimVotacao = eleicao.DataVotacaoFim,
            TotalEleitores = totalEleitores,
            VotosComputados = votosComputados,
            PercentualParticipacao = totalEleitores > 0 ? Math.Round((decimal)votosComputados / totalEleitores * 100, 2) : 0
        };
    }

    public async Task<DashboardProcessosDto> GetDashboardProcessosAsync(Guid? eleicaoId, CancellationToken cancellationToken = default)
    {
        var denuncias = _context.Denuncias.AsQueryable();
        var impugnacoes = _context.Impugnacoes.AsQueryable();

        if (eleicaoId.HasValue)
        {
            denuncias = denuncias.Where(d => d.EleicaoId == eleicaoId.Value);
            impugnacoes = impugnacoes.Where(i => i.EleicaoId == eleicaoId.Value);
        }

        return new DashboardProcessosDto
        {
            TotalDenuncias = await denuncias.CountAsync(cancellationToken),
            DenunciasRecebidas = await denuncias.CountAsync(d => d.Status == StatusDenuncia.Recebida, cancellationToken),
            DenunciasEmAnalise = await denuncias.CountAsync(d => d.Status == StatusDenuncia.EmAnalise, cancellationToken),
            DenunciasJulgadas = await denuncias.CountAsync(d => d.Status == StatusDenuncia.Procedente || d.Status == StatusDenuncia.Improcedente, cancellationToken),
            DenunciasArquivadas = await denuncias.CountAsync(d => d.Status == StatusDenuncia.Arquivada, cancellationToken),
            TotalImpugnacoes = await impugnacoes.CountAsync(cancellationToken),
            ImpugnacoesRecebidas = await impugnacoes.CountAsync(i => i.Status == StatusImpugnacao.Recebida, cancellationToken),
            ImpugnacoesEmAnalise = await impugnacoes.CountAsync(i => i.Status == StatusImpugnacao.EmAnalise, cancellationToken),
            ImpugnacoesJulgadas = await impugnacoes.CountAsync(i => i.Status == StatusImpugnacao.Julgada, cancellationToken),
            ImpugnacoesArquivadas = await impugnacoes.CountAsync(i => i.Status == StatusImpugnacao.Arquivada, cancellationToken)
        };
    }

    public Task<IEnumerable<TimelineEventoDto>> GetTimelineAsync(Guid? eleicaoId, int dias, CancellationToken cancellationToken = default)
    {
        return Task.FromResult<IEnumerable<TimelineEventoDto>>(new List<TimelineEventoDto>());
    }

    public Task<GraficoParticipacaoDto> GetGraficoParticipacaoAsync(Guid eleicaoId, string intervalo, CancellationToken cancellationToken = default)
    {
        return Task.FromResult(new GraficoParticipacaoDto { EleicaoId = eleicaoId, Intervalo = intervalo });
    }

    public Task<IEnumerable<AtividadeRecenteDto>> GetAtividadesRecentesAsync(int limit, CancellationToken cancellationToken = default)
    {
        return Task.FromResult<IEnumerable<AtividadeRecenteDto>>(new List<AtividadeRecenteDto>());
    }

    public Task<KpisDto> GetKpisAsync(Guid? eleicaoId, CancellationToken cancellationToken = default)
    {
        return Task.FromResult(new KpisDto());
    }
}
