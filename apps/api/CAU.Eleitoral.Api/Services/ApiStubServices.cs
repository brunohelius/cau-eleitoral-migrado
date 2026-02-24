using CAU.Eleitoral.Api.Controllers;
using CAU.Eleitoral.Application.DTOs;
using CAU.Eleitoral.Domain.Entities.Chapas;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Documentos;
using CAU.Eleitoral.Domain.Entities.Julgamentos;
using CAU.Eleitoral.Domain.Entities.Usuarios;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Infrastructure.Data;
using CAU.Eleitoral.Infrastructure.Services.Email;
using CAU.Eleitoral.Infrastructure.Services.Pdf;
using CAU.Eleitoral.Infrastructure.Services.Storage;
using Microsoft.AspNetCore.Http;
using Microsoft.EntityFrameworkCore;
using System.Collections.Concurrent;
using System.Text;
using System.Text.Json;

namespace CAU.Eleitoral.Api.Services;

// ── AuditoriaService ──
public class AuditoriaApiService : Controllers.IAuditoriaService
{
    private readonly AppDbContext _db;
    public AuditoriaApiService(AppDbContext db) => _db = db;

    private static AuditoriaLogDto MapToDto(AuditoriaLog l) => new()
    {
        Id = l.Id,
        DataHora = l.DataAcao,
        Acao = l.Acao,
        EntidadeTipo = l.EntidadeTipo,
        EntidadeId = l.EntidadeId,
        EntidadeNome = l.EntidadeNome,
        UsuarioId = l.UsuarioId,
        UsuarioNome = l.UsuarioNome,
        UsuarioEmail = l.UsuarioEmail,
        IpAddress = l.IpAddress,
        UserAgent = l.UserAgent,
        Sucesso = l.Sucesso,
        Mensagem = l.Mensagem
    };

    public async Task<PagedResult<AuditoriaLogDto>> GetAllAsync(FiltroAuditoriaDto filtro, int page, int pageSize, CancellationToken ct = default)
    {
        var query = _db.AuditoriaLogs.IgnoreQueryFilters().Where(l => !l.IsDeleted).AsQueryable();
        if (filtro.UsuarioId.HasValue) query = query.Where(l => l.UsuarioId == filtro.UsuarioId.Value);
        if (!string.IsNullOrEmpty(filtro.Acao)) query = query.Where(l => l.Acao == filtro.Acao);
        if (!string.IsNullOrEmpty(filtro.EntidadeTipo)) query = query.Where(l => l.EntidadeTipo == filtro.EntidadeTipo);
        if (filtro.DataInicio.HasValue) query = query.Where(l => l.DataAcao >= filtro.DataInicio.Value);
        if (filtro.DataFim.HasValue) query = query.Where(l => l.DataAcao <= filtro.DataFim.Value);

        var total = await query.CountAsync(ct);
        var items = await query.OrderByDescending(l => l.DataAcao).Skip((page - 1) * pageSize).Take(pageSize).ToListAsync(ct);
        return new PagedResult<AuditoriaLogDto> { Items = items.Select(MapToDto).ToList(), TotalCount = total, Page = page, PageSize = pageSize };
    }

    public async Task<AuditoriaLogDetalheDto?> GetByIdAsync(Guid id, CancellationToken ct = default)
    {
        var l = await _db.AuditoriaLogs.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct);
        if (l == null) return null;
        return new AuditoriaLogDetalheDto
        {
            Id = l.Id, DataHora = l.DataAcao, Acao = l.Acao, EntidadeTipo = l.EntidadeTipo, EntidadeId = l.EntidadeId,
            EntidadeNome = l.EntidadeNome, UsuarioId = l.UsuarioId, UsuarioNome = l.UsuarioNome, UsuarioEmail = l.UsuarioEmail,
            IpAddress = l.IpAddress, UserAgent = l.UserAgent, Sucesso = l.Sucesso, Mensagem = l.Mensagem,
            DadosAnteriores = l.ValorAnterior, DadosNovos = l.ValorNovo,
            RequestPath = l.Recurso, RequestMethod = l.Metodo, ResponseStatusCode = l.StatusCode
        };
    }

    public async Task<PagedResult<AuditoriaLogDto>> GetByUsuarioAsync(Guid usuarioId, int page, int pageSize, CancellationToken ct = default)
        => await GetAllAsync(new FiltroAuditoriaDto { UsuarioId = usuarioId }, page, pageSize, ct);

    public async Task<PagedResult<AuditoriaLogDto>> GetByEntidadeAsync(string entidadeTipo, Guid entidadeId, int page, int pageSize, CancellationToken ct = default)
    {
        var query = _db.AuditoriaLogs.IgnoreQueryFilters().Where(l => !l.IsDeleted && l.EntidadeTipo == entidadeTipo && l.EntidadeId == entidadeId);
        var total = await query.CountAsync(ct);
        var items = await query.OrderByDescending(l => l.DataAcao).Skip((page - 1) * pageSize).Take(pageSize).ToListAsync(ct);
        return new PagedResult<AuditoriaLogDto> { Items = items.Select(MapToDto).ToList(), TotalCount = total, Page = page, PageSize = pageSize };
    }

    public async Task<PagedResult<AuditoriaLogDto>> GetByAcaoAsync(string acao, int page, int pageSize, CancellationToken ct = default)
        => await GetAllAsync(new FiltroAuditoriaDto { Acao = acao }, page, pageSize, ct);

    public async Task<PagedResult<AuditoriaLogDto>> GetByPeriodoAsync(DateTime dataInicio, DateTime dataFim, int page, int pageSize, CancellationToken ct = default)
        => await GetAllAsync(new FiltroAuditoriaDto { DataInicio = dataInicio, DataFim = dataFim }, page, pageSize, ct);

    public async Task<EstatisticasAuditoriaDto> GetEstatisticasAsync(DateTime? dataInicio, DateTime? dataFim, CancellationToken ct = default)
    {
        var query = _db.AuditoriaLogs.IgnoreQueryFilters().Where(l => !l.IsDeleted);
        if (dataInicio.HasValue) query = query.Where(l => l.DataAcao >= dataInicio.Value);
        if (dataFim.HasValue) query = query.Where(l => l.DataAcao <= dataFim.Value);

        var logs = await query.ToListAsync(ct);
        var hoje = DateTime.UtcNow.Date;
        return new EstatisticasAuditoriaDto
        {
            TotalLogs = logs.Count,
            LogsHoje = logs.Count(l => l.DataAcao.Date == hoje),
            LogsSemana = logs.Count(l => l.DataAcao >= hoje.AddDays(-7)),
            LogsMes = logs.Count(l => l.DataAcao >= hoje.AddDays(-30)),
            ErrosTotal = logs.Count(l => !l.Sucesso),
            LogsPorAcao = logs.GroupBy(l => l.Acao).Select(g => new AcaoContadorDto { Acao = g.Key, Total = g.Count() }).ToList(),
            LogsPorEntidade = logs.GroupBy(l => l.EntidadeTipo).Select(g => new EntidadeContadorDto { EntidadeTipo = g.Key, Total = g.Count() }).ToList(),
            UsuariosMaisAtivos = logs.Where(l => l.UsuarioId.HasValue).GroupBy(l => l.UsuarioId!.Value)
                .Select(g => new UsuarioAtividadeDto { UsuarioId = g.Key, UsuarioNome = g.First().UsuarioNome ?? "Sistema", TotalAcoes = g.Count() })
                .OrderByDescending(u => u.TotalAcoes).Take(10).ToList(),
            LogsPorDia = logs.GroupBy(l => l.DataAcao.Date).OrderByDescending(g => g.Key).Take(30)
                .Select(g => new LogPorDiaDto { Data = g.Key, Total = g.Count(), Erros = g.Count(l => !l.Sucesso) }).ToList(),
        };
    }

    public async Task<IEnumerable<string>> GetAcoesAsync(CancellationToken ct = default)
    {
        var acoes = await _db.AuditoriaLogs.IgnoreQueryFilters().Where(l => !l.IsDeleted).Select(l => l.Acao).Distinct().ToListAsync(ct);
        return acoes.Any() ? acoes : new List<string> { "Login", "Logout", "Criacao", "Atualizacao", "Exclusao", "Votacao" };
    }

    public async Task<IEnumerable<string>> GetTiposEntidadeAsync(CancellationToken ct = default)
    {
        var tipos = await _db.AuditoriaLogs.IgnoreQueryFilters().Where(l => !l.IsDeleted).Select(l => l.EntidadeTipo).Distinct().ToListAsync(ct);
        return tipos.Any() ? tipos : new List<string> { "Eleicao", "Chapa", "Denuncia", "Impugnacao", "Usuario", "Voto" };
    }

    public async Task<(byte[] Content, string ContentType, string FileName)> ExportarAsync(FiltroAuditoriaDto filtro, string formato, CancellationToken ct = default)
    {
        var result = await GetAllAsync(filtro, 1, 10000, ct);
        var sb = new StringBuilder();
        sb.AppendLine("DataHora;Acao;Entidade;Usuario;IP;Sucesso;Mensagem");
        foreach (var l in result.Items)
            sb.AppendLine($"{l.DataHora:yyyy-MM-dd HH:mm:ss};{l.Acao};{l.EntidadeTipo};{l.UsuarioNome};{l.IpAddress};{l.Sucesso};{l.Mensagem}");
        return (Encoding.UTF8.GetBytes(sb.ToString()), "text/csv", $"auditoria_{DateTime.UtcNow:yyyyMMdd}.csv");
    }

    public async Task<ResultadoLimpezaDto> LimparLogsAntigosAsync(int diasRetencao, CancellationToken ct = default)
    {
        var dataCorte = DateTime.UtcNow.AddDays(-diasRetencao);
        var logs = await _db.AuditoriaLogs.IgnoreQueryFilters().Where(l => l.DataAcao < dataCorte).ToListAsync(ct);
        foreach (var l in logs) l.IsDeleted = true;
        await _db.SaveChangesAsync(ct);
        return new ResultadoLimpezaDto { LogsRemovidos = logs.Count, DataCorte = dataCorte, DataExecucao = DateTime.UtcNow };
    }

    public async Task<IEnumerable<HistoricoAlteracaoDto>> GetHistoricoAlteracoesAsync(string entidadeTipo, Guid entidadeId, CancellationToken ct = default)
    {
        var logs = await _db.AuditoriaLogs.IgnoreQueryFilters()
            .Where(l => !l.IsDeleted && l.EntidadeTipo == entidadeTipo && l.EntidadeId == entidadeId)
            .OrderByDescending(l => l.DataAcao).Take(50).ToListAsync(ct);
        return logs.Select(l =>
        {
            var campos = new List<CampoAlteradoDto>();
            if (!string.IsNullOrEmpty(l.ValorAnterior) || !string.IsNullOrEmpty(l.ValorNovo))
                campos.Add(new CampoAlteradoDto { Campo = l.Acao, ValorAnterior = l.ValorAnterior, ValorNovo = l.ValorNovo });
            return new HistoricoAlteracaoDto
            {
                Id = l.Id, DataHora = l.DataAcao, Acao = l.Acao,
                UsuarioId = l.UsuarioId, UsuarioNome = l.UsuarioNome ?? "Sistema",
                CamposAlterados = campos
            };
        });
    }
}

// ── FilialService ──
public class FilialApiService : Controllers.IFilialService
{
    private readonly AppDbContext _db;
    public FilialApiService(AppDbContext db) => _db = db;

    private static FilialDto MapToDto(Filial f) => new()
    {
        Id = f.Id, Codigo = f.Codigo, Nome = f.Nome, UF = f.UF ?? string.Empty, Ativa = f.Ativo
    };

    public async Task<IEnumerable<FilialDto>> GetAllAsync(bool? ativa, string? uf, CancellationToken ct = default)
    {
        var query = _db.Filiais.IgnoreQueryFilters().Where(f => !f.IsDeleted);
        if (ativa.HasValue) query = query.Where(f => f.Ativo == ativa.Value);
        if (!string.IsNullOrEmpty(uf)) query = query.Where(f => f.UF == uf);
        var items = await query.OrderBy(f => f.Nome).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<FilialDetalheDto?> GetByIdAsync(Guid id, CancellationToken ct = default)
    {
        var f = await _db.Filiais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct);
        if (f == null) return null;
        var totalProf = await _db.Profissionais.IgnoreQueryFilters().Where(p => !p.IsDeleted && (p.FilialId == id || p.RegionalId == f.RegionalId)).CountAsync(ct);
        var totalEleitores = await _db.Profissionais.IgnoreQueryFilters().Where(p => !p.IsDeleted && (p.FilialId == id || p.RegionalId == f.RegionalId) && p.EleitorApto).CountAsync(ct);
        return new FilialDetalheDto
        {
            Id = f.Id, Codigo = f.Codigo, Nome = f.Nome, UF = f.UF ?? string.Empty,
            Ativa = f.Ativo, Endereco = f.Endereco, Cidade = f.Cidade, CEP = f.Cep,
            Telefone = f.Telefone, Email = f.Email,
            TotalProfissionais = totalProf, TotalEleitores = totalEleitores,
            CreatedAt = f.CreatedAt, UpdatedAt = f.UpdatedAt
        };
    }

    public async Task<FilialDto?> GetByCodigoAsync(string codigo, CancellationToken ct = default)
    {
        var f = await _db.Filiais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Codigo == codigo, ct);
        return f == null ? null : MapToDto(f);
    }

    public async Task<FilialDto?> GetByUFAsync(string uf, CancellationToken ct = default)
    {
        var f = await _db.Filiais.IgnoreQueryFilters().Where(x => !x.IsDeleted && x.UF == uf).FirstOrDefaultAsync(ct);
        return f == null ? null : MapToDto(f);
    }

    public async Task<FilialDetalheDto> CreateAsync(CreateFilialDto dto, Guid userId, CancellationToken ct = default)
    {
        var f = new Filial
        {
            Codigo = dto.Codigo, Nome = dto.Nome, Cidade = dto.Cidade, UF = dto.UF,
            Endereco = dto.Endereco, Cep = dto.CEP, Telefone = dto.Telefone, Email = dto.Email,
            Ativo = true, RegionalId = Guid.Empty
        };
        await _db.Filiais.AddAsync(f, ct);
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(f.Id, ct))!;
    }

    public async Task<FilialDetalheDto> UpdateAsync(Guid id, UpdateFilialDto dto, CancellationToken ct = default)
    {
        var f = await _db.Filiais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException("Filial nao encontrada");
        if (dto.Nome != null) f.Nome = dto.Nome;
        if (dto.Endereco != null) f.Endereco = dto.Endereco;
        if (dto.Cidade != null) f.Cidade = dto.Cidade;
        if (dto.Telefone != null) f.Telefone = dto.Telefone;
        if (dto.Email != null) f.Email = dto.Email;
        if (dto.CEP != null) f.Cep = dto.CEP;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task DeleteAsync(Guid id, CancellationToken ct = default)
    {
        var f = await _db.Filiais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException("Filial nao encontrada");
        f.IsDeleted = true;
        await _db.SaveChangesAsync(ct);
    }

    public async Task<FilialDto> AtivarAsync(Guid id, CancellationToken ct = default)
    {
        var f = await _db.Filiais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        f.Ativo = true;
        await _db.SaveChangesAsync(ct);
        return MapToDto(f);
    }

    public async Task<FilialDto> DesativarAsync(Guid id, CancellationToken ct = default)
    {
        var f = await _db.Filiais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        f.Ativo = false;
        await _db.SaveChangesAsync(ct);
        return MapToDto(f);
    }

    public async Task<EstatisticasFilialDto> GetEstatisticasAsync(Guid id, CancellationToken ct = default)
    {
        var f = await _db.Filiais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct);
        var profQuery = _db.Profissionais.IgnoreQueryFilters().Where(p => !p.IsDeleted && (p.FilialId == id || p.RegionalId == (f != null ? f.RegionalId : id)));
        var totalProf = await profQuery.CountAsync(ct);
        var profAtivos = await profQuery.Where(p => p.Status == StatusProfissional.Ativo).CountAsync(ct);
        var totalEleitores = await profQuery.Where(p => p.EleitorApto).CountAsync(ct);
        var totalEleicoes = f != null
            ? await _db.Eleicoes.IgnoreQueryFilters().Where(e => !e.IsDeleted && e.RegionalId == f.RegionalId).CountAsync(ct)
            : 0;

        return new EstatisticasFilialDto
        {
            FilialId = id, FilialNome = f?.Nome ?? string.Empty,
            TotalProfissionais = totalProf, ProfissionaisAtivos = profAtivos,
            ProfissionaisInativos = totalProf - profAtivos,
            TotalEleitores = totalEleitores, EleitoresAptos = totalEleitores,
            TotalEleicoes = totalEleicoes
        };
    }

    public async Task<PagedResult<ProfissionalFilialDto>> GetProfissionaisAsync(Guid id, int page, int pageSize, CancellationToken ct = default)
    {
        var f = await _db.Filiais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct);
        var query = _db.Profissionais.IgnoreQueryFilters()
            .Where(p => !p.IsDeleted && (p.FilialId == id || p.RegionalId == (f != null ? f.RegionalId : id)));
        var total = await query.CountAsync(ct);
        var items = await query.OrderBy(p => p.Nome).Skip((page - 1) * pageSize).Take(pageSize).ToListAsync(ct);
        return new PagedResult<ProfissionalFilialDto>
        {
            Items = items.Select(p => new ProfissionalFilialDto
            {
                Id = p.Id, RegistroCAU = p.RegistroCAU, Nome = p.NomeCompleto ?? p.Nome,
                Email = p.Email, Status = p.Status.ToString(), EleitorApto = p.EleitorApto
            }).ToList(),
            TotalCount = total, Page = page, PageSize = pageSize
        };
    }

    public async Task<IEnumerable<EleicaoFilialDto>> GetEleicoesAsync(Guid id, CancellationToken ct = default)
    {
        var f = await _db.Filiais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct);
        if (f == null) return new List<EleicaoFilialDto>();

        var eleicoes = await _db.Eleicoes.IgnoreQueryFilters()
            .Where(e => !e.IsDeleted && (e.RegionalId == f.RegionalId || e.RegionalId == null))
            .OrderByDescending(e => e.Ano).ToListAsync(ct);

        var result = new List<EleicaoFilialDto>();
        foreach (var e in eleicoes)
        {
            var totalVotos = await _db.Votos.IgnoreQueryFilters().Where(v => !v.IsDeleted && v.EleicaoId == e.Id).CountAsync(ct);
            var totalAptos = await _db.Eleitores.IgnoreQueryFilters().Where(el => !el.IsDeleted && el.EleicaoId == e.Id && el.Apto).CountAsync(ct);
            result.Add(new EleicaoFilialDto
            {
                Id = e.Id, Nome = e.Nome, Ano = e.Ano, Status = e.Status.ToString(),
                DataInicio = e.DataInicio, DataFim = e.DataFim, TotalVotos = totalVotos,
                PercentualParticipacao = totalAptos > 0 ? Math.Round((decimal)totalVotos / totalAptos * 100, 2) : 0
            });
        }
        return result;
    }

    public Task<IEnumerable<UFDto>> GetUFsAsync(CancellationToken ct = default)
    {
        var ufs = new List<UFDto>
        {
            new() { Sigla = "AC", Nome = "Acre", Regiao = "Norte" }, new() { Sigla = "AL", Nome = "Alagoas", Regiao = "Nordeste" },
            new() { Sigla = "AM", Nome = "Amazonas", Regiao = "Norte" }, new() { Sigla = "AP", Nome = "Amapa", Regiao = "Norte" },
            new() { Sigla = "BA", Nome = "Bahia", Regiao = "Nordeste" }, new() { Sigla = "CE", Nome = "Ceara", Regiao = "Nordeste" },
            new() { Sigla = "DF", Nome = "Distrito Federal", Regiao = "Centro-Oeste" }, new() { Sigla = "ES", Nome = "Espirito Santo", Regiao = "Sudeste" },
            new() { Sigla = "GO", Nome = "Goias", Regiao = "Centro-Oeste" }, new() { Sigla = "MA", Nome = "Maranhao", Regiao = "Nordeste" },
            new() { Sigla = "MG", Nome = "Minas Gerais", Regiao = "Sudeste" }, new() { Sigla = "MS", Nome = "Mato Grosso do Sul", Regiao = "Centro-Oeste" },
            new() { Sigla = "MT", Nome = "Mato Grosso", Regiao = "Centro-Oeste" }, new() { Sigla = "PA", Nome = "Para", Regiao = "Norte" },
            new() { Sigla = "PB", Nome = "Paraiba", Regiao = "Nordeste" }, new() { Sigla = "PE", Nome = "Pernambuco", Regiao = "Nordeste" },
            new() { Sigla = "PI", Nome = "Piaui", Regiao = "Nordeste" }, new() { Sigla = "PR", Nome = "Parana", Regiao = "Sul" },
            new() { Sigla = "RJ", Nome = "Rio de Janeiro", Regiao = "Sudeste" }, new() { Sigla = "RN", Nome = "Rio Grande do Norte", Regiao = "Nordeste" },
            new() { Sigla = "RO", Nome = "Rondonia", Regiao = "Norte" }, new() { Sigla = "RR", Nome = "Roraima", Regiao = "Norte" },
            new() { Sigla = "RS", Nome = "Rio Grande do Sul", Regiao = "Sul" }, new() { Sigla = "SC", Nome = "Santa Catarina", Regiao = "Sul" },
            new() { Sigla = "SE", Nome = "Sergipe", Regiao = "Nordeste" }, new() { Sigla = "SP", Nome = "Sao Paulo", Regiao = "Sudeste" },
            new() { Sigla = "TO", Nome = "Tocantins", Regiao = "Norte" }
        };
        return Task.FromResult<IEnumerable<UFDto>>(ufs);
    }
}

// ── NotificacaoService ──
public class NotificacaoApiService : Controllers.INotificacaoService
{
    private readonly AppDbContext _db;
    private readonly IEmailService _email;
    private readonly ILogger<NotificacaoApiService> _logger;
    private static readonly ConcurrentDictionary<Guid, ConfiguracaoNotificacaoDto> _configuracoesUsuario = new();
    public NotificacaoApiService(AppDbContext db, IEmailService email, ILogger<NotificacaoApiService> logger) { _db = db; _email = email; _logger = logger; }

    private static TipoNotificacao ParseTipoNotificacao(string? tipo) => tipo switch
    {
        "Sucesso" => TipoNotificacao.Sucesso,
        "Alerta" => TipoNotificacao.Alerta,
        "Erro" => TipoNotificacao.Erro,
        "Sistema" => TipoNotificacao.Sistema,
        _ => TipoNotificacao.Info
    };

    private static NotificacaoDto MapToDto(Notificacao n) => new()
    {
        Id = n.Id, UsuarioId = n.UsuarioId, Titulo = n.Titulo, Mensagem = n.Mensagem,
        Tipo = ParseTipoNotificacao(n.Tipo), Lida = n.Lida,
        DataLeitura = n.DataLeitura, Link = n.Link, CreatedAt = n.CreatedAt
    };

    public async Task<PagedResult<NotificacaoDto>> GetByUsuarioAsync(Guid userId, bool apenasNaoLidas, int page, int pageSize, CancellationToken ct = default)
    {
        var query = _db.Notificacoes.IgnoreQueryFilters().Where(n => !n.IsDeleted && n.UsuarioId == userId);
        if (apenasNaoLidas) query = query.Where(n => !n.Lida);
        var total = await query.CountAsync(ct);
        var items = await query.OrderByDescending(n => n.DataEnvio).Skip((page - 1) * pageSize).Take(pageSize).ToListAsync(ct);
        return new PagedResult<NotificacaoDto> { Items = items.Select(MapToDto).ToList(), TotalCount = total, Page = page, PageSize = pageSize };
    }

    public async Task<NotificacaoDto?> GetByIdAsync(Guid id, Guid userId, CancellationToken ct = default)
    {
        var n = await _db.Notificacoes.IgnoreQueryFilters().Where(x => !x.IsDeleted && x.UsuarioId == userId).FirstOrDefaultAsync(x => x.Id == id, ct);
        return n == null ? null : MapToDto(n);
    }

    public async Task<ContagemNotificacoesDto> GetContagemNaoLidasAsync(Guid userId, CancellationToken ct = default)
    {
        var total = await _db.Notificacoes.IgnoreQueryFilters().Where(n => !n.IsDeleted && n.UsuarioId == userId).CountAsync(ct);
        var naoLidas = await _db.Notificacoes.IgnoreQueryFilters().Where(n => !n.IsDeleted && n.UsuarioId == userId && !n.Lida).CountAsync(ct);
        return new ContagemNotificacoesDto { Total = total, NaoLidas = naoLidas, AltaPrioridade = 0 };
    }

    private static string BuildEmailBody(string titulo, string mensagem, string? link)
    {
        return $"<h2>{titulo}</h2><p>{mensagem}</p>" +
               (string.IsNullOrEmpty(link) ? "" : $"<p><a href=\"{link}\">Acessar</a></p>") +
               "<hr/><p style=\"font-size:12px;color:#888;\">CAU Sistema Eleitoral</p>";
    }

    private async Task EnviarEmailSeDisponivelAsync(Guid userId, string titulo, string mensagem, string? link, CancellationToken ct)
    {
        var user = await _db.Usuarios.IgnoreQueryFilters()
            .Where(u => !u.IsDeleted && u.Id == userId)
            .FirstOrDefaultAsync(ct);

        if (user == null || string.IsNullOrWhiteSpace(user.Email))
            return;

        var htmlBody = BuildEmailBody(titulo, mensagem, link);
        await _email.SendAsync(user.Email, $"[CAU Eleitoral] {titulo}", htmlBody, ct);
    }

    public async Task<NotificacaoDto> MarcarComoLidaAsync(Guid id, Guid userId, CancellationToken ct = default)
    {
        var n = await _db.Notificacoes.IgnoreQueryFilters().Where(x => !x.IsDeleted && x.UsuarioId == userId).FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new KeyNotFoundException("Notificacao nao encontrada");
        n.Lida = true;
        n.DataLeitura = DateTime.UtcNow;
        await _db.SaveChangesAsync(ct);
        return MapToDto(n);
    }

    public async Task<int> MarcarTodasComoLidasAsync(Guid userId, CancellationToken ct = default)
    {
        var naoLidas = await _db.Notificacoes.IgnoreQueryFilters().Where(n => !n.IsDeleted && n.UsuarioId == userId && !n.Lida).ToListAsync(ct);
        foreach (var n in naoLidas) { n.Lida = true; n.DataLeitura = DateTime.UtcNow; }
        await _db.SaveChangesAsync(ct);
        return naoLidas.Count;
    }

    public async Task DeleteAsync(Guid id, Guid userId, CancellationToken ct = default)
    {
        var n = await _db.Notificacoes.IgnoreQueryFilters().Where(x => !x.IsDeleted && x.UsuarioId == userId).FirstOrDefaultAsync(x => x.Id == id, ct);
        if (n != null) { n.IsDeleted = true; await _db.SaveChangesAsync(ct); }
    }

    public async Task<int> DeleteLidasAsync(Guid userId, CancellationToken ct = default)
    {
        var lidas = await _db.Notificacoes.IgnoreQueryFilters().Where(n => !n.IsDeleted && n.UsuarioId == userId && n.Lida).ToListAsync(ct);
        foreach (var n in lidas) n.IsDeleted = true;
        await _db.SaveChangesAsync(ct);
        return lidas.Count;
    }

    public async Task<NotificacaoDto> EnviarAsync(CreateNotificacaoDto dto, CancellationToken ct = default)
    {
        var n = new Notificacao
        {
            UsuarioId = dto.UsuarioId, Titulo = dto.Titulo, Mensagem = dto.Mensagem,
            Tipo = dto.Tipo.ToString(), Canal = "InApp", Status = "Enviada",
            DataEnvio = DateTime.UtcNow, Link = dto.Link
        };
        await _db.Notificacoes.AddAsync(n, ct);
        await _db.SaveChangesAsync(ct);

        await EnviarEmailSeDisponivelAsync(dto.UsuarioId, dto.Titulo, dto.Mensagem, dto.Link, ct);

        return MapToDto(n);
    }

    public async Task<ResultadoEnvioMassaDto> EnviarEmMassaAsync(CreateNotificacaoMassaDto dto, CancellationToken ct = default)
    {
        var userIds = new List<Guid>();

        if (dto.UsuarioIds != null && dto.UsuarioIds.Any())
        {
            userIds.AddRange(dto.UsuarioIds);
        }
        else if (!string.IsNullOrEmpty(dto.Role))
        {
            var role = await _db.Roles.IgnoreQueryFilters().FirstOrDefaultAsync(r => !r.IsDeleted && r.Nome == dto.Role, ct);
            if (role != null)
            {
                userIds = await _db.UsuarioRoles.IgnoreQueryFilters()
                    .Where(ur => !ur.IsDeleted && ur.RoleId == role.Id)
                    .Select(ur => ur.UsuarioId).ToListAsync(ct);
            }
        }
        else if (dto.EleicaoId.HasValue)
        {
            userIds = await _db.Eleitores.IgnoreQueryFilters()
                .Where(e => !e.IsDeleted && e.EleicaoId == dto.EleicaoId.Value && e.Apto)
                .Join(_db.Profissionais.IgnoreQueryFilters().Where(p => !p.IsDeleted && p.UsuarioId.HasValue),
                    e => e.ProfissionalId, p => p.Id, (e, p) => p.UsuarioId!.Value)
                .Distinct().ToListAsync(ct);
        }

        var sucesso = 0;
        var falhas = 0;
        var erros = new List<string>();

        foreach (var userId in userIds)
        {
            try
            {
                var n = new Notificacao
                {
                    UsuarioId = userId, Titulo = dto.Titulo, Mensagem = dto.Mensagem,
                    Tipo = dto.Tipo.ToString(), Canal = "InApp", Status = "Enviada",
                    DataEnvio = DateTime.UtcNow, Link = dto.Link
                };
                await _db.Notificacoes.AddAsync(n, ct);
                sucesso++;
            }
            catch (Exception ex)
            {
                falhas++;
                erros.Add($"Erro para usuario {userId}: {ex.Message}");
            }
        }

        if (sucesso > 0)
        {
            await _db.SaveChangesAsync(ct);

            foreach (var userId in userIds)
            {
                try
                {
                    await EnviarEmailSeDisponivelAsync(userId, dto.Titulo, dto.Mensagem, dto.Link, ct);
                }
                catch (Exception ex)
                {
                    _logger.LogWarning(ex, "Falha no envio de email para notificacao em massa (usuario {UserId})", userId);
                }
            }
        }

        return new ResultadoEnvioMassaDto
        {
            TotalEnviadas = userIds.Count, Sucesso = sucesso, Falhas = falhas,
            Erros = erros.Any() ? erros : null
        };
    }

    public Task<ConfiguracaoNotificacaoDto> GetConfiguracoesAsync(Guid userId, CancellationToken ct = default)
    {
        if (_configuracoesUsuario.TryGetValue(userId, out var config))
            return Task.FromResult(config);

        var padrao = new ConfiguracaoNotificacaoDto
        {
            EmailHabilitado = true,
            PushHabilitado = false,
            SmsHabilitado = false,
            NotificacaoEleicao = true,
            NotificacaoVotacao = true,
            NotificacaoResultado = true,
            NotificacaoSistema = true,
            NotificacaoDenuncia = true,
            NotificacaoImpugnacao = true,
            ResumoDigital = false,
            FrequenciaResumo = "diario"
        };

        _configuracoesUsuario[userId] = padrao;
        return Task.FromResult(padrao);
    }

    public async Task<ConfiguracaoNotificacaoDto> UpdateConfiguracoesAsync(Guid userId, UpdateConfiguracaoNotificacaoDto dto, CancellationToken ct = default)
    {
        var atual = await GetConfiguracoesAsync(userId, ct);
        var config = new ConfiguracaoNotificacaoDto
        {
            EmailHabilitado = dto.EmailHabilitado ?? atual.EmailHabilitado,
            PushHabilitado = dto.PushHabilitado ?? atual.PushHabilitado,
            SmsHabilitado = dto.SmsHabilitado ?? atual.SmsHabilitado,
            NotificacaoEleicao = dto.NotificacaoEleicao ?? atual.NotificacaoEleicao,
            NotificacaoDenuncia = dto.NotificacaoDenuncia ?? atual.NotificacaoDenuncia,
            NotificacaoImpugnacao = dto.NotificacaoImpugnacao ?? atual.NotificacaoImpugnacao,
            NotificacaoVotacao = dto.NotificacaoVotacao ?? atual.NotificacaoVotacao,
            NotificacaoResultado = dto.NotificacaoResultado ?? atual.NotificacaoResultado,
            NotificacaoSistema = dto.NotificacaoSistema ?? atual.NotificacaoSistema,
            ResumoDigital = dto.ResumoDigital ?? atual.ResumoDigital,
            FrequenciaResumo = dto.FrequenciaResumo ?? atual.FrequenciaResumo
        };
        _configuracoesUsuario[userId] = config;
        return config;
    }
}

// ── DocumentoService ──
public class DocumentoApiService : Controllers.IDocumentoService
{
    private readonly AppDbContext _db;
    private readonly IS3StorageService _s3;
    private readonly ILogger<DocumentoApiService> _logger;
    public DocumentoApiService(AppDbContext db, IS3StorageService s3, ILogger<DocumentoApiService> logger) { _db = db; _s3 = s3; _logger = logger; }

    private static string GetLocalDocumentDirectory(Guid docId)
    {
        var directory = Path.Combine(AppContext.BaseDirectory, "documentos", docId.ToString("N"));
        Directory.CreateDirectory(directory);
        return directory;
    }

    private static Controllers.DocumentoDto MapToDto(CAU.Eleitoral.Domain.Entities.Documentos.Documento d) => new()
    {
        Id = d.Id, EleicaoId = d.EleicaoId, EleicaoNome = d.Eleicao?.Nome ?? string.Empty,
        Titulo = d.Titulo, Descricao = d.Ementa, Tipo = d.Tipo, Categoria = d.Categoria,
        Status = d.Status, Numero = d.Numero, DataDocumento = d.DataDocumento,
        DataPublicacao = d.DataPublicacao, DataRevogacao = d.DataRevogacao,
        Url = d.ArquivoUrl, NomeArquivo = d.ArquivoNome, TipoArquivo = d.ArquivoTipo,
        Tamanho = d.ArquivoTamanho, CreatedAt = d.CreatedAt, UpdatedAt = d.UpdatedAt
    };

    public async Task<IEnumerable<Controllers.DocumentoDto>> GetAllAsync(Guid? eleicaoId, TipoDocumento? tipo, CategoriaDocumento? categoria, CancellationToken ct = default)
    {
        var query = _db.Documentos.IgnoreQueryFilters().Include(d => d.Eleicao).Where(d => !d.IsDeleted).AsQueryable();
        if (eleicaoId.HasValue) query = query.Where(d => d.EleicaoId == eleicaoId.Value);
        if (tipo.HasValue) query = query.Where(d => d.Tipo == tipo.Value);
        if (categoria.HasValue) query = query.Where(d => d.Categoria == categoria.Value);
        var items = await query.OrderByDescending(d => d.DataPublicacao ?? d.CreatedAt).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<Controllers.DocumentoDto?> GetByIdAsync(Guid id, CancellationToken ct = default)
    {
        var d = await _db.Documentos.IgnoreQueryFilters().Include(x => x.Eleicao).Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct);
        return d == null ? null : MapToDto(d);
    }

    public async Task<IEnumerable<Controllers.DocumentoDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken ct = default)
    {
        var items = await _db.Documentos.IgnoreQueryFilters().Include(d => d.Eleicao)
            .Where(d => !d.IsDeleted && d.EleicaoId == eleicaoId).OrderByDescending(d => d.DataPublicacao ?? d.CreatedAt).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<IEnumerable<Controllers.DocumentoDto>> GetPublicadosAsync(Guid? eleicaoId, CancellationToken ct = default)
    {
        var query = _db.Documentos.IgnoreQueryFilters().Include(d => d.Eleicao).Where(d => !d.IsDeleted && d.Status == StatusDocumento.Publicado);
        if (eleicaoId.HasValue) query = query.Where(d => d.EleicaoId == eleicaoId.Value);
        var items = await query.OrderByDescending(d => d.DataPublicacao ?? d.CreatedAt).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<Controllers.DocumentoDto> CreateAsync(Controllers.CreateDocumentoDto dto, Guid userId, CancellationToken ct = default)
    {
        var doc = new CAU.Eleitoral.Domain.Entities.Documentos.Documento
        {
            EleicaoId = dto.EleicaoId ?? Guid.Empty, Titulo = dto.Titulo, Ementa = dto.Descricao,
            Tipo = dto.Tipo, Categoria = dto.Categoria, Status = StatusDocumento.Rascunho,
            Numero = dto.Numero ?? string.Empty, DataDocumento = dto.DataDocumento ?? DateTime.UtcNow
        };
        await _db.Documentos.AddAsync(doc, ct);
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(doc.Id, ct))!;
    }

    public async Task<Controllers.DocumentoDto> UploadAsync(IFormFile file, Guid eleicaoId, TipoDocumento tipo, CategoriaDocumento categoria, Guid userId, CancellationToken ct = default)
    {
        var docId = Guid.NewGuid();
        var safeFileName = Path.GetFileName(file.FileName);
        var s3Key = $"documentos/{docId}/{safeFileName}";
        string arquivoUrl;

        try
        {
            using var stream = file.OpenReadStream();
            arquivoUrl = await _s3.UploadAsync(stream, s3Key, file.ContentType, ct);
            _logger.LogInformation("Document uploaded to S3: {Key}", s3Key);
        }
        catch (Exception ex)
        {
            _logger.LogWarning(ex, "Falha no upload S3 para documento {DocumentId}. Aplicando fallback para arquivo local.", docId);

            var localDirectory = GetLocalDocumentDirectory(docId);
            var localPath = Path.Combine(localDirectory, safeFileName);
            await using var output = File.Create(localPath);
            await using var input = file.OpenReadStream();
            await input.CopyToAsync(output, ct);
            arquivoUrl = localPath;
        }

        var doc = new CAU.Eleitoral.Domain.Entities.Documentos.Documento
        {
            Id = docId, EleicaoId = eleicaoId, Titulo = Path.GetFileNameWithoutExtension(file.FileName),
            Tipo = tipo, Categoria = categoria, Status = StatusDocumento.Rascunho,
            DataDocumento = DateTime.UtcNow, ArquivoUrl = arquivoUrl,
            ArquivoNome = safeFileName, ArquivoTipo = file.ContentType, ArquivoTamanho = file.Length
        };
        await _db.Documentos.AddAsync(doc, ct);
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(doc.Id, ct))!;
    }

    public async Task<Controllers.DocumentoDto> UpdateAsync(Guid id, Controllers.UpdateDocumentoDto dto, CancellationToken ct = default)
    {
        var d = await _db.Documentos.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException("Documento nao encontrado");
        if (dto.Titulo != null) d.Titulo = dto.Titulo;
        if (dto.Descricao != null) d.Ementa = dto.Descricao;
        if (dto.Numero != null) d.Numero = dto.Numero;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task DeleteAsync(Guid id, CancellationToken ct = default)
    {
        var d = await _db.Documentos.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException("Documento nao encontrado");
        d.IsDeleted = true;
        await _db.SaveChangesAsync(ct);
    }

    public async Task<(byte[] Content, string ContentType, string FileName)> DownloadAsync(Guid id, CancellationToken ct = default)
    {
        var d = await _db.Documentos.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new KeyNotFoundException("Documento nao encontrado");

        if (string.IsNullOrEmpty(d.ArquivoUrl))
            throw new KeyNotFoundException("Arquivo do documento nao encontrado");

        if (Path.IsPathRooted(d.ArquivoUrl) && File.Exists(d.ArquivoUrl))
        {
            var localBytes = await File.ReadAllBytesAsync(d.ArquivoUrl, ct);
            return (localBytes, d.ArquivoTipo ?? "application/octet-stream", d.ArquivoNome ?? "documento");
        }

        var s3Key = d.ArquivoUrl.StartsWith("s3://", StringComparison.OrdinalIgnoreCase)
            ? d.ArquivoUrl.Split('/', 4).Last()
            : d.ArquivoUrl;

        using var stream = await _s3.DownloadAsync(s3Key, ct);
        using var ms = new MemoryStream();
        await stream.CopyToAsync(ms, ct);
        return (ms.ToArray(), d.ArquivoTipo ?? "application/octet-stream", d.ArquivoNome ?? "documento");
    }

    public async Task<Controllers.DocumentoDto> EnviarParaRevisaoAsync(Guid id, CancellationToken ct = default)
    {
        var d = await _db.Documentos.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        d.Status = StatusDocumento.EmRevisao;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<Controllers.DocumentoDto> AprovarAsync(Guid id, Guid userId, CancellationToken ct = default)
    {
        var d = await _db.Documentos.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        d.Status = StatusDocumento.Aprovado;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<Controllers.DocumentoDto> PublicarAsync(Guid id, Guid userId, CancellationToken ct = default)
    {
        var d = await _db.Documentos.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        d.Status = StatusDocumento.Publicado;
        d.DataPublicacao = DateTime.UtcNow;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<Controllers.DocumentoDto> RevogarAsync(Guid id, string motivo, Guid userId, CancellationToken ct = default)
    {
        var d = await _db.Documentos.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        d.Status = StatusDocumento.Revogado;
        d.DataRevogacao = DateTime.UtcNow;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<Controllers.DocumentoDto> ArquivarAsync(Guid id, CancellationToken ct = default)
    {
        var d = await _db.Documentos.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        d.Status = StatusDocumento.Arquivado;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }
}

// ── JulgamentoService ──
public class JulgamentoApiService : Controllers.IJulgamentoService
{
    private readonly AppDbContext _db;
    public JulgamentoApiService(AppDbContext db) => _db = db;

    private JulgamentoDto MapToDto(JulgamentoFinal j) => new()
    {
        Id = j.Id, EleicaoId = j.EleicaoId, EleicaoNome = j.Eleicao?.Nome ?? string.Empty,
        SessaoId = j.SessaoId, Tipo = j.Tipo, Status = j.Status,
        DataAgendada = j.DataJulgamento ?? j.CreatedAt,
        DataInicio = j.Status != StatusJulgamento.Agendado ? j.DataJulgamento : null,
        DataFim = j.Status == StatusJulgamento.Concluido ? j.DataPublicacao : null,
        Ementa = j.Ementa, Relatorio = j.Relatorio, TipoDecisao = j.TipoDecisao,
        Decisao = j.Dispositivo, Fundamentacao = j.Fundamentacao,
        Votos = j.Votos?.Select(v => new VotoJulgamentoResultadoDto
        {
            Id = v.Id, MembroId = v.MembroComissaoId,
            MembroNome = v.MembroComissao?.Conselheiro?.Profissional?.Nome ?? "Membro",
            Voto = v.Voto, Fundamentacao = v.Fundamentacao, DataVoto = v.DataVoto
        }).ToList() ?? new(),
        CreatedAt = j.CreatedAt, UpdatedAt = j.UpdatedAt
    };

    public async Task<IEnumerable<JulgamentoDto>> GetAllAsync(Guid? eleicaoId, StatusJulgamento? status, CancellationToken ct = default)
    {
        var query = _db.JulgamentosFinais.IgnoreQueryFilters()
            .Include(j => j.Eleicao).Include(j => j.Votos).ThenInclude(v => v.MembroComissao)
            .Where(j => !j.IsDeleted);
        if (eleicaoId.HasValue) query = query.Where(j => j.EleicaoId == eleicaoId.Value);
        if (status.HasValue) query = query.Where(j => j.Status == status.Value);
        var items = await query.OrderByDescending(j => j.DataJulgamento ?? j.CreatedAt).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<JulgamentoDto?> GetByIdAsync(Guid id, CancellationToken ct = default)
    {
        var j = await _db.JulgamentosFinais.IgnoreQueryFilters()
            .Include(x => x.Eleicao).Include(x => x.Votos).ThenInclude(v => v.MembroComissao)
            .Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct);
        return j == null ? null : MapToDto(j);
    }

    public async Task<IEnumerable<JulgamentoDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken ct = default)
        => await GetAllAsync(eleicaoId, null, ct);

    public async Task<IEnumerable<JulgamentoDto>> GetAgendadosAsync(CancellationToken ct = default)
        => await GetAllAsync(null, StatusJulgamento.Agendado, ct);

    public async Task<JulgamentoDto> CreateAsync(CreateJulgamentoDto dto, Guid userId, CancellationToken ct = default)
    {
        var j = new JulgamentoFinal
        {
            EleicaoId = dto.EleicaoId, SessaoId = dto.SessaoId, Tipo = dto.Tipo,
            Status = StatusJulgamento.Agendado, DataJulgamento = dto.DataAgendada,
            Ementa = dto.Ementa, Protocolo = $"JLG-{DateTime.UtcNow:yyyyMMdd}-{Guid.NewGuid().ToString()[..8].ToUpper()}"
        };
        await _db.JulgamentosFinais.AddAsync(j, ct);
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(j.Id, ct))!;
    }

    public async Task<JulgamentoDto> UpdateAsync(Guid id, UpdateJulgamentoDto dto, CancellationToken ct = default)
    {
        var j = await _db.JulgamentosFinais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        if (dto.SessaoId.HasValue) j.SessaoId = dto.SessaoId;
        if (dto.DataAgendada.HasValue) j.DataJulgamento = dto.DataAgendada;
        if (dto.Ementa != null) j.Ementa = dto.Ementa;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task DeleteAsync(Guid id, CancellationToken ct = default)
    {
        var j = await _db.JulgamentosFinais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        j.IsDeleted = true;
        await _db.SaveChangesAsync(ct);
    }

    public async Task<JulgamentoDto> IniciarAsync(Guid id, CancellationToken ct = default)
    {
        var j = await _db.JulgamentosFinais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        j.Status = StatusJulgamento.EmAndamento;
        j.DataJulgamento = DateTime.UtcNow;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<JulgamentoDto> SuspenderAsync(Guid id, string motivo, CancellationToken ct = default)
    {
        var j = await _db.JulgamentosFinais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        j.Status = StatusJulgamento.Suspenso;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<JulgamentoDto> RetomarAsync(Guid id, CancellationToken ct = default)
    {
        var j = await _db.JulgamentosFinais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        j.Status = StatusJulgamento.EmAndamento;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<JulgamentoDto> RegistrarVotoAsync(Guid id, VotoJulgamentoDto dto, Guid userId, CancellationToken ct = default)
    {
        var j = await _db.JulgamentosFinais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        var voto = new VotoJulgamentoFinal
        {
            JulgamentoId = id, MembroComissaoId = dto.MembroId, Voto = dto.Voto,
            Fundamentacao = dto.Fundamentacao, DataVoto = DateTime.UtcNow
        };
        await _db.VotosJulgamentoFinal.AddAsync(voto, ct);
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<JulgamentoDto> ConcluirAsync(Guid id, ConcluirJulgamentoDto dto, Guid userId, CancellationToken ct = default)
    {
        var j = await _db.JulgamentosFinais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        j.Status = StatusJulgamento.Concluido;
        j.TipoDecisao = dto.TipoDecisao;
        j.Dispositivo = dto.Decisao;
        j.Fundamentacao = dto.Fundamentacao;
        j.DataPublicacao = DateTime.UtcNow;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<JulgamentoDto> CancelarAsync(Guid id, string motivo, CancellationToken ct = default)
    {
        var j = await _db.JulgamentosFinais.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        j.Status = StatusJulgamento.Cancelado;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<IEnumerable<SessaoJulgamentoDto>> GetSessoesAsync(Guid? eleicaoId, CancellationToken ct = default)
    {
        var query = _db.SessoesJulgamento.IgnoreQueryFilters()
            .Include(s => s.Comissao).ThenInclude(c => c.Eleicao)
            .Where(s => !s.IsDeleted);
        if (eleicaoId.HasValue) query = query.Where(s => s.Comissao.EleicaoId == eleicaoId.Value);
        var items = await query.OrderByDescending(s => s.DataSessao).ToListAsync(ct);

        var julgamentoCounts = await _db.JulgamentosFinais.IgnoreQueryFilters()
            .Where(j => !j.IsDeleted && j.SessaoId != null)
            .GroupBy(j => j.SessaoId!.Value)
            .Select(g => new { SessaoId = g.Key, Count = g.Count() })
            .ToDictionaryAsync(g => g.SessaoId, g => g.Count, ct);

        return items.Select(s => new SessaoJulgamentoDto
        {
            Id = s.Id, EleicaoId = s.Comissao?.EleicaoId ?? Guid.Empty,
            EleicaoNome = s.Comissao?.Eleicao?.Nome ?? string.Empty,
            Tipo = s.Tipo, Status = s.Status, Data = s.DataSessao,
            Local = s.Local, Pauta = s.Observacao,
            TotalJulgamentos = julgamentoCounts.GetValueOrDefault(s.Id, 0),
            CreatedAt = s.CreatedAt
        });
    }

    public async Task<SessaoJulgamentoDto> CreateSessaoAsync(CreateSessaoJulgamentoDto dto, Guid userId, CancellationToken ct = default)
    {
        var comissao = await _db.ComissoesJulgadoras.IgnoreQueryFilters()
            .Where(c => !c.IsDeleted && c.EleicaoId == dto.EleicaoId && c.Ativa).FirstOrDefaultAsync(ct)
            ?? throw new InvalidOperationException("Nenhuma comissao julgadora ativa encontrada para esta eleicao");
        var sessao = new SessaoJulgamento
        {
            ComissaoId = comissao.Id, Tipo = dto.Tipo, Status = StatusSessao.Agendada,
            DataSessao = dto.Data, Local = dto.Local, Observacao = dto.Pauta,
            Numero = $"{DateTime.UtcNow:yyyyMMdd}-{Guid.NewGuid().ToString()[..4].ToUpper()}"
        };
        await _db.SessoesJulgamento.AddAsync(sessao, ct);
        await _db.SaveChangesAsync(ct);
        var sessoes = await GetSessoesAsync(dto.EleicaoId, ct);
        return sessoes.First(s => s.Id == sessao.Id);
    }

    public async Task<IEnumerable<MembroComissaoJulgamentoDto>> GetMembrosAsync(Guid julgamentoId, CancellationToken ct = default)
    {
        var julgamento = await _db.JulgamentosFinais.IgnoreQueryFilters()
            .Include(j => j.Sessao)
            .ThenInclude(s => s!.Comissao)
            .Where(j => !j.IsDeleted)
            .FirstOrDefaultAsync(j => j.Id == julgamentoId, ct)
            ?? throw new KeyNotFoundException();

        Guid? comissaoId = julgamento.Sessao?.ComissaoId;
        if (!comissaoId.HasValue)
        {
            comissaoId = await _db.ComissoesJulgadoras.IgnoreQueryFilters()
                .Where(c => !c.IsDeleted && c.EleicaoId == julgamento.EleicaoId && c.Ativa)
                .Select(c => (Guid?)c.Id)
                .FirstOrDefaultAsync(ct);
        }

        if (!comissaoId.HasValue)
            return Array.Empty<MembroComissaoJulgamentoDto>();

        var membros = await _db.MembrosComissaoJulgadora.IgnoreQueryFilters()
            .Include(m => m.Conselheiro)
            .ThenInclude(c => c.Profissional)
            .Where(m => !m.IsDeleted && m.ComissaoId == comissaoId.Value && m.Ativo)
            .OrderBy(m => m.Ordem)
            .ToListAsync(ct);

        return membros.Select(m => new MembroComissaoJulgamentoDto
        {
            Id = m.Id,
            ConselheiroId = m.ConselheiroId,
            Nome = m.Conselheiro?.Profissional?.Nome ?? "Membro",
            Tipo = m.Tipo,
            Ordem = m.Ordem,
            Ativo = m.Ativo
        });
    }
}

// ── RelatorioService ──
public class RelatorioApiService : Controllers.IRelatorioService
{
    private readonly AppDbContext _db;
    private readonly IPdfExportService _pdf;
    private readonly IExcelExportService _excel;
    private readonly ILogger<RelatorioApiService> _logger;

    private const string ExportDirectoryName = "exports";
    private static readonly ConcurrentDictionary<Guid, ReportArtifact> _artifacts = new();

    private sealed record ReportArtifact(
        Guid Id,
        Guid? EleicaoId,
        string Tipo,
        string Formato,
        string ContentType,
        string FileName,
        string FilePath,
        long Size,
        DateTime GeneratedAt,
        int? TotalRegistros
    );

    public RelatorioApiService(
        AppDbContext db,
        IPdfExportService pdf,
        IExcelExportService excel,
        ILogger<RelatorioApiService> logger)
    {
        _db = db;
        _pdf = pdf;
        _excel = excel;
        _logger = logger;
    }

    public Task<IEnumerable<TipoRelatorioDto>> GetTiposDisponiveisAsync(CancellationToken ct = default)
    {
        var tipos = new List<TipoRelatorioDto>
        {
            new() { Codigo = "participacao", Nome = "Participacao", FormatosDisponiveis = new() { "pdf", "xlsx", "csv", "json" }, RequerEleicao = true },
            new() { Codigo = "resultado", Nome = "Resultado", FormatosDisponiveis = new() { "pdf", "xlsx", "csv", "json" }, RequerEleicao = true },
            new() { Codigo = "chapas", Nome = "Chapas", FormatosDisponiveis = new() { "pdf", "xlsx", "csv", "json" }, RequerEleicao = true },
            new() { Codigo = "eleitores", Nome = "Eleitores", FormatosDisponiveis = new() { "pdf", "xlsx", "csv", "json" }, RequerEleicao = true },
            new() { Codigo = "denuncias", Nome = "Denuncias", FormatosDisponiveis = new() { "pdf", "xlsx", "csv", "json" }, RequerEleicao = true },
            new() { Codigo = "impugnacoes", Nome = "Impugnacoes", FormatosDisponiveis = new() { "pdf", "xlsx", "csv", "json" }, RequerEleicao = true },
            new() { Codigo = "auditoria", Nome = "Auditoria", FormatosDisponiveis = new() { "pdf", "xlsx", "csv", "json" }, RequerEleicao = true },
            new() { Codigo = "consolidado", Nome = "Consolidado", FormatosDisponiveis = new() { "pdf", "xlsx", "csv", "json" }, RequerEleicao = true },
            new() { Codigo = "comparativo", Nome = "Comparativo", FormatosDisponiveis = new() { "pdf", "xlsx", "csv", "json" }, RequerEleicao = true },
        };
        return Task.FromResult<IEnumerable<TipoRelatorioDto>>(tipos);
    }

    private async Task<string> BuildJsonReport(string tipo, Guid eleicaoId, CancellationToken ct)
    {
        var eleicao = await _db.Eleicoes.IgnoreQueryFilters().Where(e => !e.IsDeleted).FirstOrDefaultAsync(e => e.Id == eleicaoId, ct);
        if (eleicao == null) return "{}";

        var votos = await _db.Votos.IgnoreQueryFilters().Where(v => !v.IsDeleted && v.EleicaoId == eleicaoId).ToListAsync(ct);
        var chapas = await _db.Chapas.IgnoreQueryFilters().Where(c => !c.IsDeleted && c.EleicaoId == eleicaoId).ToListAsync(ct);
        var eleitores = await _db.Eleitores.IgnoreQueryFilters().Where(e => !e.IsDeleted && e.EleicaoId == eleicaoId).ToListAsync(ct);

        var totalVotos = votos.Count;
        var totalEleitores = eleitores.Count;
        var totalAptos = eleitores.Count(e => e.Apto);
        var votosValidos = votos.Count(v => v.Tipo == TipoVoto.Chapa);
        var votosBrancos = votos.Count(v => v.Tipo == TipoVoto.Branco);
        var votosNulos = votos.Count(v => v.Tipo == TipoVoto.Nulo);
        var participacao = totalAptos > 0 ? Math.Round((decimal)totalVotos / totalAptos * 100, 2) : 0;

        return JsonSerializer.Serialize(new
        {
            eleicaoId, eleicaoNome = eleicao.Nome, tipo,
            totalEleitores, totalAptos, totalVotos, votosValidos, votosBrancos, votosNulos,
            participacao,
            chapas = chapas.Select(c => new
            {
                id = c.Id, nome = c.Nome, numero = c.Numero,
                votos = votos.Count(v => v.ChapaId == c.Id),
                percentual = totalVotos > 0 ? Math.Round((decimal)votos.Count(v => v.ChapaId == c.Id) / totalVotos * 100, 2) : 0
            }),
            geradoEm = DateTime.UtcNow
        });
    }

    private async Task<string> BuildJsonComparativoReport(IReadOnlyCollection<Guid> eleicaoIds, CancellationToken ct)
    {
        var ids = eleicaoIds.Where(id => id != Guid.Empty).Distinct().ToList();
        if (!ids.Any())
            return "{}";

        var eleicoes = await _db.Eleicoes.IgnoreQueryFilters()
            .Where(e => !e.IsDeleted && ids.Contains(e.Id))
            .OrderBy(e => e.Ano)
            .ThenBy(e => e.Nome)
            .ToListAsync(ct);

        var comparativo = new List<object>();
        var totalAptos = 0;
        var totalVotos = 0;

        foreach (var eleicao in eleicoes)
        {
            var votos = await _db.Votos.IgnoreQueryFilters()
                .Where(v => !v.IsDeleted && v.EleicaoId == eleicao.Id)
                .CountAsync(ct);
            var aptos = await _db.Eleitores.IgnoreQueryFilters()
                .Where(e => !e.IsDeleted && e.EleicaoId == eleicao.Id && e.Apto)
                .CountAsync(ct);
            var chapas = await _db.Chapas.IgnoreQueryFilters()
                .Where(c => !c.IsDeleted && c.EleicaoId == eleicao.Id)
                .CountAsync(ct);

            totalAptos += aptos;
            totalVotos += votos;

            comparativo.Add(new
            {
                eleicaoId = eleicao.Id,
                eleicaoNome = eleicao.Nome,
                ano = eleicao.Ano,
                totalAptos = aptos,
                totalVotos = votos,
                totalChapas = chapas,
                participacao = aptos > 0 ? Math.Round((decimal)votos / aptos * 100, 2) : 0
            });
        }

        var participacaoMedia = totalAptos > 0 ? Math.Round((decimal)totalVotos / totalAptos * 100, 2) : 0;

        return JsonSerializer.Serialize(new
        {
            tipo = "comparativo",
            totalEleicoes = comparativo.Count,
            totalAptos,
            totalVotos,
            participacaoMedia,
            eleicoesComparadas = comparativo,
            geradoEm = DateTime.UtcNow
        });
    }

    private string BuildCsvReport(string jsonData, string tipo)
    {
        var sb = new StringBuilder();
        using var doc = JsonDocument.Parse(jsonData);
        var root = doc.RootElement;

        sb.AppendLine($"Relatorio: {tipo}");
        sb.AppendLine($"Eleicao: {(root.TryGetProperty("eleicaoNome", out var nome) ? nome.GetString() : "N/A")}");
        sb.AppendLine($"Gerado em: {DateTime.UtcNow:yyyy-MM-dd HH:mm:ss}");
        sb.AppendLine();

        if (root.TryGetProperty("totalEleitores", out var te)) sb.AppendLine($"Total Eleitores: {te.GetInt32()}");
        if (root.TryGetProperty("totalAptos", out var ta)) sb.AppendLine($"Eleitores Aptos: {ta.GetInt32()}");
        if (root.TryGetProperty("totalVotos", out var tv)) sb.AppendLine($"Total Votos: {tv.GetInt32()}");
        if (root.TryGetProperty("votosValidos", out var vv)) sb.AppendLine($"Votos Validos: {vv.GetInt32()}");
        if (root.TryGetProperty("votosBrancos", out var vb)) sb.AppendLine($"Votos Brancos: {vb.GetInt32()}");
        if (root.TryGetProperty("votosNulos", out var vn)) sb.AppendLine($"Votos Nulos: {vn.GetInt32()}");
        if (root.TryGetProperty("participacao", out var part)) sb.AppendLine($"Participacao: {part.GetDecimal()}%");
        sb.AppendLine();

        if (root.TryGetProperty("eleicoesComparadas", out var comparativo) && comparativo.ValueKind == JsonValueKind.Array)
        {
            sb.AppendLine("Eleicao;Ano;Aptos;Votos;Chapas;Participacao");
            foreach (var item in comparativo.EnumerateArray())
            {
                var nomeEleicao = item.TryGetProperty("eleicaoNome", out var n) ? n.GetString() : "";
                var ano = item.TryGetProperty("ano", out var a) ? a.GetInt32().ToString() : "";
                var aptos = item.TryGetProperty("totalAptos", out var ap) ? ap.GetInt32().ToString() : "0";
                var votos = item.TryGetProperty("totalVotos", out var v) ? v.GetInt32().ToString() : "0";
                var chapas = item.TryGetProperty("totalChapas", out var c) ? c.GetInt32().ToString() : "0";
                var participacao = item.TryGetProperty("participacao", out var p) ? p.GetDecimal().ToString("F2") : "0.00";
                sb.AppendLine($"{nomeEleicao};{ano};{aptos};{votos};{chapas};{participacao}%");
            }
        }
        else if (root.TryGetProperty("chapas", out var chapas) && chapas.ValueKind == JsonValueKind.Array)
        {
            sb.AppendLine("Nome;Numero;Votos;Percentual");
            foreach (var chapa in chapas.EnumerateArray())
            {
                var cn = chapa.TryGetProperty("nome", out var cnv) ? cnv.GetString() : "";
                var cnum = chapa.TryGetProperty("numero", out var cnumv) ? cnumv.ToString() : "";
                var cv = chapa.TryGetProperty("votos", out var cvv) ? cvv.GetInt32().ToString() : "0";
                var cp = chapa.TryGetProperty("percentual", out var cpv) ? cpv.GetDecimal().ToString("F2") : "0";
                sb.AppendLine($"{cn};{cnum};{cv};{cp}%");
            }
        }
        return sb.ToString();
    }

    private (Dictionary<string, string> Summary, List<string> Headers, List<List<string>> Rows) ParseJsonToTableData(string jsonData)
    {
        var summary = new Dictionary<string, string>();
        var headers = new List<string>();
        var rows = new List<List<string>>();

        using var doc = JsonDocument.Parse(jsonData);
        var root = doc.RootElement;

        if (root.TryGetProperty("eleicaoNome", out var nome)) summary["Eleicao"] = nome.GetString() ?? "";
        if (root.TryGetProperty("totalEleitores", out var te)) summary["Total Eleitores"] = te.GetInt32().ToString();
        if (root.TryGetProperty("totalAptos", out var ta)) summary["Eleitores Aptos"] = ta.GetInt32().ToString();
        if (root.TryGetProperty("totalVotos", out var tv)) summary["Total Votos"] = tv.GetInt32().ToString();
        if (root.TryGetProperty("votosValidos", out var vv)) summary["Votos Validos"] = vv.GetInt32().ToString();
        if (root.TryGetProperty("votosBrancos", out var vb)) summary["Votos Brancos"] = vb.GetInt32().ToString();
        if (root.TryGetProperty("votosNulos", out var vn)) summary["Votos Nulos"] = vn.GetInt32().ToString();
        if (root.TryGetProperty("participacao", out var part)) summary["Participacao"] = $"{part.GetDecimal()}%";

        if (root.TryGetProperty("totalEleicoes", out var totalEleicoes))
            summary["Total Eleicoes"] = totalEleicoes.GetInt32().ToString();
        if (root.TryGetProperty("participacaoMedia", out var participacaoMedia))
            summary["Participacao Media"] = $"{participacaoMedia.GetDecimal():F2}%";

        if (root.TryGetProperty("eleicoesComparadas", out var comparativo) && comparativo.ValueKind == JsonValueKind.Array)
        {
            headers.AddRange(new[] { "Eleicao", "Ano", "Aptos", "Votos", "Chapas", "Participacao" });
            foreach (var item in comparativo.EnumerateArray())
            {
                rows.Add(new List<string>
                {
                    item.TryGetProperty("eleicaoNome", out var e) ? e.GetString() ?? "" : "",
                    item.TryGetProperty("ano", out var a) ? a.GetInt32().ToString() : "",
                    item.TryGetProperty("totalAptos", out var ap) ? ap.GetInt32().ToString() : "0",
                    item.TryGetProperty("totalVotos", out var v) ? v.GetInt32().ToString() : "0",
                    item.TryGetProperty("totalChapas", out var c) ? c.GetInt32().ToString() : "0",
                    item.TryGetProperty("participacao", out var p) ? $"{p.GetDecimal():F2}%" : "0%"
                });
            }
        }
        else if (root.TryGetProperty("chapas", out var chapas) && chapas.ValueKind == JsonValueKind.Array)
        {
            headers.AddRange(new[] { "Nome", "Numero", "Votos", "Percentual" });
            foreach (var chapa in chapas.EnumerateArray())
            {
                rows.Add(new List<string>
                {
                    chapa.TryGetProperty("nome", out var cn) ? cn.GetString() ?? "" : "",
                    chapa.TryGetProperty("numero", out var cnum) ? cnum.ToString() : "",
                    chapa.TryGetProperty("votos", out var cv) ? cv.GetInt32().ToString() : "0",
                    chapa.TryGetProperty("percentual", out var cp) ? $"{cp.GetDecimal():F2}%" : "0%"
                });
            }
        }

        return (summary, headers, rows);
    }

    private static (string Normalized, string ContentType, string Extension, FormatoExportacao EnumValue) ResolveFormato(string formato)
    {
        var normalized = (formato ?? "pdf").Trim().ToLowerInvariant();
        if (normalized == "excel")
            normalized = "xlsx";

        return normalized switch
        {
            "xlsx" => ("xlsx", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "xlsx", FormatoExportacao.Excel),
            "csv" => ("csv", "text/csv; charset=utf-8", "csv", FormatoExportacao.CSV),
            "json" => ("json", "application/json", "json", FormatoExportacao.JSON),
            _ => ("pdf", "application/pdf", "pdf", FormatoExportacao.PDF)
        };
    }

    private static TipoExportacao MapTipoExportacao(string tipo)
    {
        return tipo.ToLowerInvariant() switch
        {
            "participacao" => TipoExportacao.Eleitores,
            "eleitores" => TipoExportacao.Eleitores,
            "chapas" => TipoExportacao.Chapas,
            "resultado" => TipoExportacao.Resultados,
            "denuncias" => TipoExportacao.Resultados,
            "impugnacoes" => TipoExportacao.Resultados,
            "auditoria" => TipoExportacao.Resultados,
            _ => TipoExportacao.Completa
        };
    }

    private static string GetExportDirectoryPath()
    {
        var directory = Path.Combine(AppContext.BaseDirectory, ExportDirectoryName);
        Directory.CreateDirectory(directory);
        return directory;
    }

    private static string GetTipoNome(string tipo) => tipo switch
    {
        "participacao" => "Relatorio de Participacao",
        "resultado" => "Relatorio de Resultado",
        "chapas" => "Relatorio de Chapas",
        "eleitores" => "Relatorio de Eleitores",
        "denuncias" => "Relatorio de Denuncias",
        "impugnacoes" => "Relatorio de Impugnacoes",
        "auditoria" => "Relatorio de Auditoria",
        "consolidado" => "Relatorio Consolidado",
        "comparativo" => "Relatorio Comparativo",
        _ => "Relatorio Personalizado"
    };

    private async Task PersistExportMetadataAsync(ReportArtifact artifact, CancellationToken ct)
    {
        try
        {
            var solicitanteId = await _db.Usuarios.IgnoreQueryFilters()
                .Where(u => !u.IsDeleted)
                .Select(u => u.Id)
                .FirstOrDefaultAsync(ct);

            if (solicitanteId == Guid.Empty)
            {
                _logger.LogWarning("Historico de relatorio {ReportId} nao persistido: usuario solicitante nao encontrado", artifact.Id);
                return;
            }

            var exportacao = new ExportacaoDados
            {
                Id = artifact.Id,
                EleicaoId = artifact.EleicaoId,
                SolicitanteId = solicitanteId,
                Nome = GetTipoNome(artifact.Tipo),
                Descricao = $"Exportacao {artifact.Tipo} em formato {artifact.Formato.ToUpperInvariant()}",
                Tipo = MapTipoExportacao(artifact.Tipo),
                Formato = ResolveFormato(artifact.Formato).EnumValue,
                Status = StatusExportacao.Concluida,
                DataSolicitacao = artifact.GeneratedAt,
                DataInicio = artifact.GeneratedAt,
                DataConclusao = artifact.GeneratedAt,
                DataExpiracao = artifact.GeneratedAt.AddDays(30),
                TotalRegistros = artifact.TotalRegistros,
                RegistrosExportados = artifact.TotalRegistros,
                ArquivoUrl = artifact.FilePath,
                ArquivoNome = artifact.FileName,
                ArquivoTamanho = artifact.Size,
                DownloadsRealizados = 0
            };

            await _db.ExportacoesDados.AddAsync(exportacao, ct);
            await _db.SaveChangesAsync(ct);
        }
        catch (Exception ex)
        {
            _logger.LogWarning(ex, "Falha ao persistir historico do relatorio {ReportId}", artifact.Id);
        }
    }

    private async Task<(byte[] Content, string ContentType, string FileName)> GenerateReport(
        string tipo,
        Guid eleicaoId,
        string formato,
        CancellationToken ct,
        IReadOnlyCollection<Guid>? eleicaoIdsComparativo = null)
    {
        var formatoInfo = ResolveFormato(formato);
        var json = tipo == "comparativo"
            ? await BuildJsonComparativoReport(eleicaoIdsComparativo ?? new[] { eleicaoId }, ct)
            : await BuildJsonReport(tipo, eleicaoId, ct);

        byte[] content;
        string contentType;

        if (formatoInfo.Normalized == "json")
        {
            content = Encoding.UTF8.GetBytes(json);
            contentType = formatoInfo.ContentType;
        }
        else if (formatoInfo.Normalized == "csv")
        {
            content = Encoding.UTF8.GetBytes(BuildCsvReport(json, tipo));
            contentType = formatoInfo.ContentType;
        }
        else
        {
            var eleicaoNome = "Multiplas eleicoes";
            if (tipo != "comparativo")
            {
                var eleicao = await _db.Eleicoes.IgnoreQueryFilters()
                    .Where(e => !e.IsDeleted)
                    .FirstOrDefaultAsync(e => e.Id == eleicaoId, ct);
                eleicaoNome = eleicao?.Nome ?? "Eleicao";
            }

            var titulo = $"Relatorio de {char.ToUpper(tipo[0])}{tipo[1..]}";
            var (summary, headers, rows) = ParseJsonToTableData(json);

            if (formatoInfo.Normalized == "xlsx")
            {
                content = _excel.GenerateReport(titulo, eleicaoNome, DateTime.UtcNow, summary, headers, rows);
                contentType = formatoInfo.ContentType;
            }
            else
            {
                var reportData = new ReportData { Summary = summary, TableHeaders = headers, TableRows = rows };
                content = _pdf.GenerateReport(titulo, eleicaoNome, DateTime.UtcNow, reportData);
                contentType = formatoInfo.ContentType;
            }
        }

        var id = Guid.NewGuid();
        var fileName = $"{tipo}_{id:N}.{formatoInfo.Extension}";
        var filePath = Path.Combine(GetExportDirectoryPath(), fileName);
        await File.WriteAllBytesAsync(filePath, content, ct);

        var totalRegistros = tipo == "comparativo"
            ? eleicaoIdsComparativo?.Count
            : (eleicaoId == Guid.Empty ? null : 1);

        var artifact = new ReportArtifact(
            id,
            eleicaoId == Guid.Empty ? null : eleicaoId,
            tipo,
            formatoInfo.Normalized,
            contentType,
            fileName,
            filePath,
            content.LongLength,
            DateTime.UtcNow,
            totalRegistros);

        _artifacts[id] = artifact;
        await PersistExportMetadataAsync(artifact, ct);

        return (content, contentType, fileName);
    }

    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioParticipacaoAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => GenerateReport("participacao", eleicaoId, formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioResultadoAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => GenerateReport("resultado", eleicaoId, formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioChapasAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => GenerateReport("chapas", eleicaoId, formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioEleitoresAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => GenerateReport("eleitores", eleicaoId, formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioDenunciasAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => GenerateReport("denuncias", eleicaoId, formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioImpugnacoesAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => GenerateReport("impugnacoes", eleicaoId, formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioAuditoriaAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => GenerateReport("auditoria", eleicaoId, formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioConsolidadoAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => GenerateReport("consolidado", eleicaoId, formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioComparativoAsync(List<Guid> eleicaoIds, string formato, CancellationToken ct = default) => GenerateReport("comparativo", Guid.Empty, formato, ct, eleicaoIds);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioPersonalizadoAsync(RelatorioPersonalizadoDto dto, CancellationToken ct = default) => GenerateReport("personalizado", dto.EleicaoId, dto.Formato, ct);

    public async Task<IEnumerable<RelatorioGeradoDto>> GetHistoricoAsync(Guid? eleicaoId, CancellationToken ct = default)
    {
        var query = _db.ExportacoesDados.IgnoreQueryFilters()
            .Include(e => e.Eleicao)
            .Include(e => e.Solicitante)
            .Where(e => !e.IsDeleted);

        if (eleicaoId.HasValue)
            query = query.Where(e => e.EleicaoId == eleicaoId.Value);

        var persisted = await query
            .OrderByDescending(e => e.DataSolicitacao)
            .ToListAsync(ct);

        if (persisted.Any())
        {
            return persisted.Select(e => new RelatorioGeradoDto
            {
                Id = e.Id,
                EleicaoId = e.EleicaoId,
                EleicaoNome = e.Eleicao?.Nome,
                Tipo = e.Tipo.ToString().ToLowerInvariant(),
                TipoNome = e.Nome,
                Formato = e.Formato.ToString().ToLowerInvariant(),
                NomeArquivo = e.ArquivoNome,
                Tamanho = e.ArquivoTamanho,
                GeradoPor = e.SolicitanteId,
                GeradoPorNome = e.Solicitante?.NomeCompleto ?? e.Solicitante?.Nome ?? "Sistema",
                DataGeracao = e.DataConclusao ?? e.DataSolicitacao
            }).ToList();
        }

        return _artifacts.Values
            .OrderByDescending(a => a.GeneratedAt)
            .Where(a => !eleicaoId.HasValue || a.EleicaoId == eleicaoId.Value)
            .Select(a => new RelatorioGeradoDto
            {
                Id = a.Id,
                EleicaoId = a.EleicaoId,
                Tipo = a.Tipo,
                TipoNome = GetTipoNome(a.Tipo),
                Formato = a.Formato,
                NomeArquivo = a.FileName,
                Tamanho = a.Size,
                GeradoPor = Guid.Empty,
                GeradoPorNome = "Sistema",
                DataGeracao = a.GeneratedAt
            })
            .ToList();
    }

    public async Task<(byte[] Content, string ContentType, string FileName)> DownloadAsync(Guid id, CancellationToken ct = default)
    {
        var exportacao = await _db.ExportacoesDados.IgnoreQueryFilters()
            .Where(e => !e.IsDeleted && e.Id == id)
            .FirstOrDefaultAsync(ct);

        if (exportacao != null)
        {
            byte[] persistedBytes;

            if (!string.IsNullOrWhiteSpace(exportacao.ArquivoUrl) && File.Exists(exportacao.ArquivoUrl))
            {
                persistedBytes = await File.ReadAllBytesAsync(exportacao.ArquivoUrl, ct);
            }
            else if (_artifacts.TryGetValue(id, out var fallbackArtifact))
            {
                persistedBytes = await File.ReadAllBytesAsync(fallbackArtifact.FilePath, ct);
            }
            else
            {
                throw new KeyNotFoundException("Arquivo do relatorio nao encontrado");
            }

            exportacao.DownloadsRealizados += 1;
            await _db.SaveChangesAsync(ct);

            var contentType = ResolveFormato(exportacao.Formato.ToString().ToLowerInvariant()).ContentType;
            return (persistedBytes, contentType, exportacao.ArquivoNome ?? $"relatorio_{id:N}");
        }

        if (_artifacts.TryGetValue(id, out var artifact) && File.Exists(artifact.FilePath))
        {
            var bytes = await File.ReadAllBytesAsync(artifact.FilePath, ct);
            return (bytes, artifact.ContentType, artifact.FileName);
        }

        throw new KeyNotFoundException("Relatorio nao encontrado");
    }
}

// ── MembroChapaService ──
public class MembroChapaApiService : Controllers.IMembroChapaService
{
    private readonly AppDbContext _db;
    public MembroChapaApiService(AppDbContext db) => _db = db;

    private static MembroChapaDetalheDto MapToDto(MembroChapa m) => new()
    {
        Id = m.Id, ChapaId = m.ChapaId, ChapaNome = m.Chapa?.Nome ?? string.Empty,
        ProfissionalId = m.ProfissionalId ?? Guid.Empty,
        ProfissionalNome = m.Profissional?.NomeCompleto ?? m.Nome,
        ProfissionalRegistroCAU = m.RegistroCAU ?? m.Profissional?.RegistroCAU,
        ProfissionalCpf = m.Cpf ?? m.Profissional?.Cpf,
        ProfissionalEmail = m.Email ?? m.Profissional?.Email,
        TipoMembro = (int)m.Tipo, TipoMembroNome = m.Tipo.ToString(),
        Cargo = m.Cargo, Status = (int)m.Status, StatusNome = m.Status.ToString(),
        Ordem = m.Ordem, CreatedAt = m.CreatedAt, UpdatedAt = m.UpdatedAt
    };

    public async Task<IEnumerable<MembroChapaDetalheDto>> GetByChapaAsync(Guid chapaId, CancellationToken ct = default)
    {
        var items = await _db.MembrosChapa.IgnoreQueryFilters().Include(m => m.Chapa).Include(m => m.Profissional)
            .Where(m => !m.IsDeleted && m.ChapaId == chapaId).OrderBy(m => m.Ordem).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<MembroChapaDetalheDto?> GetByIdAsync(Guid id, CancellationToken ct = default)
    {
        var m = await _db.MembrosChapa.IgnoreQueryFilters().Include(x => x.Chapa).Include(x => x.Profissional).Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct);
        return m == null ? null : MapToDto(m);
    }

    public async Task<IEnumerable<MembroChapaDetalheDto>> GetByProfissionalAsync(Guid profissionalId, CancellationToken ct = default)
    {
        var items = await _db.MembrosChapa.IgnoreQueryFilters().Include(m => m.Chapa).Include(m => m.Profissional)
            .Where(m => !m.IsDeleted && m.ProfissionalId == profissionalId).OrderBy(m => m.Ordem).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<MembroChapaDetalheDto> CreateAsync(CreateMembroChapaDetalheDto dto, Guid userId, CancellationToken ct = default)
    {
        var maxOrdem = await _db.MembrosChapa.IgnoreQueryFilters().Where(m => !m.IsDeleted && m.ChapaId == dto.ChapaId).MaxAsync(m => (int?)m.Ordem, ct) ?? 0;
        var prof = await _db.Profissionais.IgnoreQueryFilters().Where(p => !p.IsDeleted).FirstOrDefaultAsync(p => p.Id == dto.ProfissionalId, ct);
        var membro = new MembroChapa
        {
            ChapaId = dto.ChapaId, ProfissionalId = dto.ProfissionalId,
            Nome = prof?.NomeCompleto ?? "Membro", Cpf = prof?.Cpf, RegistroCAU = prof?.RegistroCAU, Email = prof?.Email,
            Tipo = (TipoMembroChapa)dto.TipoMembro, Cargo = dto.Cargo,
            Status = StatusMembroChapa.Pendente, Ordem = dto.Ordem ?? maxOrdem + 1, Titular = true
        };
        await _db.MembrosChapa.AddAsync(membro, ct);
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(membro.Id, ct))!;
    }

    public async Task<MembroChapaDetalheDto> UpdateAsync(Guid id, UpdateMembroChapaRequestDto dto, CancellationToken ct = default)
    {
        var m = await _db.MembrosChapa.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        if (dto.TipoMembro.HasValue) m.Tipo = (TipoMembroChapa)dto.TipoMembro.Value;
        if (dto.Cargo != null) m.Cargo = dto.Cargo;
        if (dto.Ordem.HasValue) m.Ordem = dto.Ordem.Value;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task DeleteAsync(Guid id, CancellationToken ct = default)
    {
        var m = await _db.MembrosChapa.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        m.IsDeleted = true;
        await _db.SaveChangesAsync(ct);
    }

    public async Task<IEnumerable<MembroChapaDetalheDto>> ReordenarAsync(Guid chapaId, List<Guid> ordemIds, CancellationToken ct = default)
    {
        var membros = await _db.MembrosChapa.IgnoreQueryFilters().Where(m => !m.IsDeleted && m.ChapaId == chapaId).ToListAsync(ct);
        for (int i = 0; i < ordemIds.Count; i++)
        {
            var m = membros.FirstOrDefault(x => x.Id == ordemIds[i]);
            if (m != null) m.Ordem = i + 1;
        }
        await _db.SaveChangesAsync(ct);
        return await GetByChapaAsync(chapaId, ct);
    }

    public Task<ValidacaoElegibilidadeDto> ValidarElegibilidadeAsync(Guid id, CancellationToken ct = default)
        => Task.FromResult(new ValidacaoElegibilidadeDto { MembroId = id, Elegivel = true, RegistroAtivo = true, AdimplenteAnuidade = true, SemDebitos = true, SemPenalidadesAtivas = true });

    public async Task<MembroChapaDetalheDto> AprovarAsync(Guid id, string? parecer, Guid userId, CancellationToken ct = default)
    {
        var m = await _db.MembrosChapa.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        m.Status = StatusMembroChapa.Confirmado;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<MembroChapaDetalheDto> RejeitarAsync(Guid id, string motivo, Guid userId, CancellationToken ct = default)
    {
        var m = await _db.MembrosChapa.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        m.Status = StatusMembroChapa.Recusado;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public Task<IEnumerable<CargoMembroDto>> GetCargosAsync(CancellationToken ct = default)
    {
        var cargos = new List<CargoMembroDto>
        {
            new() { Codigo = 1, Nome = "Presidente", Principal = true },
            new() { Codigo = 2, Nome = "Vice-Presidente", Principal = true },
            new() { Codigo = 3, Nome = "Secretario" }, new() { Codigo = 4, Nome = "Tesoureiro" },
            new() { Codigo = 5, Nome = "Conselheiro Titular" }, new() { Codigo = 6, Nome = "Conselheiro Suplente" },
        };
        return Task.FromResult<IEnumerable<CargoMembroDto>>(cargos);
    }
}

// ── CalendarioService ──
public class CalendarioApiService : Controllers.ICalendarioService
{
    private readonly AppDbContext _db;
    private readonly ILogger<CalendarioApiService> _logger;
    public CalendarioApiService(AppDbContext db, ILogger<CalendarioApiService> logger) { _db = db; _logger = logger; }

    private static CalendarioEventoDto MapToDto(Calendario c) => new()
    {
        Id = c.Id, EleicaoId = c.EleicaoId, EleicaoNome = c.Eleicao?.Nome ?? string.Empty,
        Titulo = c.Nome, Descricao = c.Descricao, Tipo = c.Tipo, Status = c.Status,
        DataInicio = c.DataInicio, DataFim = c.DataFim, DiaInteiro = !c.HoraInicio.HasValue,
        Obrigatorio = c.Obrigatorio, CreatedAt = c.CreatedAt, UpdatedAt = c.UpdatedAt
    };

    public async Task<IEnumerable<CalendarioEventoDto>> GetAllAsync(Guid? eleicaoId, TipoCalendario? tipo, CancellationToken ct = default)
    {
        var query = _db.Calendarios.IgnoreQueryFilters().Include(c => c.Eleicao).Where(c => !c.IsDeleted).AsQueryable();
        if (eleicaoId.HasValue) query = query.Where(c => c.EleicaoId == eleicaoId.Value);
        if (tipo.HasValue) query = query.Where(c => c.Tipo == tipo.Value);
        var items = await query.OrderBy(c => c.Ordem).ToListAsync(ct);
        _logger.LogInformation("CalendarioApiService.GetAllAsync: found {Count} items", items.Count);

        if (items.Count == 0)
        {
            var totalAll = await _db.Calendarios.IgnoreQueryFilters().CountAsync(ct);
            var totalDeleted = await _db.Calendarios.IgnoreQueryFilters().Where(c => c.IsDeleted).CountAsync(ct);
            if (totalDeleted > 0 && totalAll == totalDeleted)
            {
                _logger.LogWarning("All calendario records are soft-deleted! Restoring...");
                var deletedItems = await _db.Calendarios.IgnoreQueryFilters().Where(c => c.IsDeleted).ToListAsync(ct);
                foreach (var item in deletedItems) item.IsDeleted = false;
                await _db.SaveChangesAsync(ct);
                items = await _db.Calendarios.Include(c => c.Eleicao).OrderBy(c => c.Ordem).ToListAsync(ct);
            }
        }
        return items.Select(MapToDto);
    }

    public async Task<CalendarioEventoDto?> GetByIdAsync(Guid id, CancellationToken ct = default)
    {
        var c = await _db.Calendarios.IgnoreQueryFilters().Include(x => x.Eleicao).Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct);
        return c == null ? null : MapToDto(c);
    }

    public async Task<IEnumerable<CalendarioEventoDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken ct = default)
    {
        var items = await _db.Calendarios.IgnoreQueryFilters().Include(c => c.Eleicao)
            .Where(c => !c.IsDeleted && c.EleicaoId == eleicaoId).OrderBy(c => c.Ordem).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<IEnumerable<CalendarioEventoDto>> GetProximosAsync(int dias, Guid? eleicaoId, CancellationToken ct = default)
    {
        var hoje = DateTime.UtcNow; var limite = hoje.AddDays(dias);
        var query = _db.Calendarios.IgnoreQueryFilters().Include(c => c.Eleicao).Where(c => !c.IsDeleted && c.DataInicio >= hoje && c.DataInicio <= limite);
        if (eleicaoId.HasValue) query = query.Where(c => c.EleicaoId == eleicaoId.Value);
        var items = await query.OrderBy(c => c.DataInicio).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<IEnumerable<CalendarioEventoDto>> GetEmAndamentoAsync(Guid? eleicaoId, CancellationToken ct = default)
    {
        var hoje = DateTime.UtcNow;
        var query = _db.Calendarios.IgnoreQueryFilters().Include(c => c.Eleicao).Where(c => !c.IsDeleted && c.DataInicio <= hoje && c.DataFim >= hoje);
        if (eleicaoId.HasValue) query = query.Where(c => c.EleicaoId == eleicaoId.Value);
        var items = await query.OrderBy(c => c.Ordem).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<IEnumerable<CalendarioEventoDto>> GetByPeriodoAsync(DateTime dataInicio, DateTime dataFim, Guid? eleicaoId, CancellationToken ct = default)
    {
        var query = _db.Calendarios.IgnoreQueryFilters().Include(c => c.Eleicao).Where(c => !c.IsDeleted && c.DataInicio >= dataInicio && c.DataFim <= dataFim);
        if (eleicaoId.HasValue) query = query.Where(c => c.EleicaoId == eleicaoId.Value);
        var items = await query.OrderBy(c => c.DataInicio).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<CalendarioEventoDto> CreateAsync(CreateCalendarioEventoDto dto, Guid userId, CancellationToken ct = default)
    {
        var maxOrdem = await _db.Calendarios.IgnoreQueryFilters().Where(c => !c.IsDeleted && c.EleicaoId == dto.EleicaoId).MaxAsync(c => (int?)c.Ordem, ct) ?? 0;
        var c = new Calendario
        {
            EleicaoId = dto.EleicaoId, Nome = dto.Titulo, Descricao = dto.Descricao,
            Tipo = dto.Tipo, Status = StatusCalendario.Pendente,
            DataInicio = dto.DataInicio, DataFim = dto.DataFim,
            Ordem = maxOrdem + 1, Obrigatorio = dto.Obrigatorio
        };
        await _db.Calendarios.AddAsync(c, ct);
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(c.Id, ct))!;
    }

    public async Task<CalendarioEventoDto> UpdateAsync(Guid id, UpdateCalendarioEventoDto dto, CancellationToken ct = default)
    {
        var c = await _db.Calendarios.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        if (dto.Titulo != null) c.Nome = dto.Titulo;
        if (dto.Descricao != null) c.Descricao = dto.Descricao;
        if (dto.DataInicio.HasValue) c.DataInicio = dto.DataInicio.Value;
        if (dto.DataFim.HasValue) c.DataFim = dto.DataFim.Value;
        if (dto.Obrigatorio.HasValue) c.Obrigatorio = dto.Obrigatorio.Value;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task DeleteAsync(Guid id, CancellationToken ct = default)
    {
        var c = await _db.Calendarios.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        c.IsDeleted = true;
        await _db.SaveChangesAsync(ct);
    }

    public async Task<CalendarioEventoDto> IniciarAsync(Guid id, CancellationToken ct = default)
    {
        var c = await _db.Calendarios.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        c.Status = StatusCalendario.EmAndamento;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<CalendarioEventoDto> ConcluirAsync(Guid id, CancellationToken ct = default)
    {
        var c = await _db.Calendarios.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        c.Status = StatusCalendario.Concluido;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<CalendarioEventoDto> CancelarAsync(Guid id, string motivo, CancellationToken ct = default)
    {
        var c = await _db.Calendarios.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct) ?? throw new KeyNotFoundException();
        c.Status = StatusCalendario.Cancelado;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<IEnumerable<CalendarioEventoDto>> GerarCalendarioPadraoAsync(Guid eleicaoId, Guid userId, CancellationToken ct = default)
    {
        var eleicao = await _db.Eleicoes.IgnoreQueryFilters().Where(e => !e.IsDeleted).FirstOrDefaultAsync(e => e.Id == eleicaoId, ct)
            ?? throw new KeyNotFoundException("Eleicao nao encontrada");
        var votacaoInicio = eleicao.DataVotacaoInicio ?? eleicao.DataInicio;
        var fases = new (string Nome, TipoCalendario Tipo, int DiasAntes, int Duracao)[]
        {
            ("Inscricao de Chapas", TipoCalendario.Inscricao, 90, 30),
            ("Periodo de Impugnacoes", TipoCalendario.Impugnacao, 60, 15),
            ("Propaganda Eleitoral", TipoCalendario.Propaganda, 28, 20),
            ("Periodo de Votacao", TipoCalendario.Votacao, 0, 15),
            ("Apuracao dos Votos", TipoCalendario.Apuracao, -15, 3),
            ("Publicacao dos Resultados", TipoCalendario.Resultado, -18, 5),
            ("Diplomacao dos Eleitos", TipoCalendario.Diplomacao, -30, 1),
        };
        for (int i = 0; i < fases.Length; i++)
        {
            var f = fases[i];
            var dataInicio = votacaoInicio.AddDays(f.DiasAntes * -1);
            await _db.Calendarios.AddAsync(new Calendario
            {
                EleicaoId = eleicaoId, Nome = f.Nome, Descricao = $"{f.Nome} da {eleicao.Nome}",
                Tipo = f.Tipo, Status = StatusCalendario.Pendente,
                DataInicio = dataInicio, DataFim = dataInicio.AddDays(f.Duracao),
                HoraInicio = new TimeSpan(8, 0, 0), HoraFim = new TimeSpan(18, 0, 0),
                Ordem = i + 1, Obrigatorio = true, NotificarInicio = true, NotificarFim = true
            }, ct);
        }
        await _db.SaveChangesAsync(ct);
        return await GetByEleicaoAsync(eleicaoId, ct);
    }
}

// ── ConselheiroService ──
public class ConselheiroApiService : Controllers.IConselheiroService
{
    private readonly AppDbContext _db;
    public ConselheiroApiService(AppDbContext db) => _db = db;

    private static StatusConselheiro InferStatus(Conselheiro c)
    {
        if (c.MandatoAtivo) return StatusConselheiro.Ativo;
        if (c.MotivoFinalizacao != null)
        {
            if (c.MotivoFinalizacao.Contains("afast", StringComparison.OrdinalIgnoreCase)) return StatusConselheiro.Afastado;
            if (c.MotivoFinalizacao.Contains("suspens", StringComparison.OrdinalIgnoreCase)) return StatusConselheiro.Suspenso;
            if (c.MotivoFinalizacao.Contains("renunci", StringComparison.OrdinalIgnoreCase)) return StatusConselheiro.Renunciou;
            if (c.MotivoFinalizacao.Contains("falec", StringComparison.OrdinalIgnoreCase)) return StatusConselheiro.Falecido;
        }
        return StatusConselheiro.MandatoEncerrado;
    }

    private ConselheiroDto MapToDto(Conselheiro c) => new()
    {
        Id = c.Id, ProfissionalId = c.ProfissionalId,
        Nome = c.Profissional?.NomeCompleto ?? c.Profissional?.Nome ?? string.Empty,
        RegistroCAU = c.Profissional?.RegistroCAU ?? string.Empty,
        Tipo = TipoConselheiro.Estadual,
        Status = InferStatus(c),
        Cargo = c.Cargo,
        RegionalId = c.Profissional?.RegionalId,
        RegionalNome = c.Profissional?.Regional?.Nome,
        Mandato = c.InicioMandato?.Year ?? 0,
        DataPosse = c.InicioMandato ?? c.CreatedAt,
        DataFimMandato = c.FimMandato
    };

    private ConselheiroDetalheDto MapToDetalheDto(Conselheiro c) => new()
    {
        Id = c.Id, ProfissionalId = c.ProfissionalId,
        Nome = c.Profissional?.NomeCompleto ?? c.Profissional?.Nome ?? string.Empty,
        RegistroCAU = c.Profissional?.RegistroCAU ?? string.Empty,
        Tipo = TipoConselheiro.Estadual,
        Status = InferStatus(c),
        Cargo = c.Cargo,
        RegionalId = c.Profissional?.RegionalId,
        RegionalNome = c.Profissional?.Regional?.Nome,
        Mandato = c.InicioMandato?.Year ?? 0,
        DataPosse = c.InicioMandato ?? c.CreatedAt,
        DataFimMandato = c.FimMandato,
        Email = c.Profissional?.Email,
        Telefone = c.Profissional?.Telefone ?? c.Profissional?.Celular,
        Cpf = c.Profissional?.Cpf,
        Titular = true,
        MotivoAfastamento = c.MotivoFinalizacao,
        DataAfastamento = c.DataFinalizacao,
        HistoricoMandatos = c.Historicos?.Select(h => new MandatoConselheiroDto
        {
            Mandato = h.DataEvento.Year, DataInicio = h.DataEvento,
            Cargo = h.Tipo, MotivoEncerramento = h.Descricao
        }).ToList() ?? new(),
        CreatedAt = c.CreatedAt, UpdatedAt = c.UpdatedAt
    };

    private IQueryable<Conselheiro> BaseQuery() => _db.Conselheiros.IgnoreQueryFilters()
        .Include(c => c.Profissional).ThenInclude(p => p.Regional)
        .Include(c => c.Historicos)
        .Where(c => !c.IsDeleted);

    public async Task<IEnumerable<ConselheiroDto>> GetAllAsync(StatusConselheiro? status, TipoConselheiro? tipo, Guid? regionalId, CancellationToken ct = default)
    {
        var items = await BaseQuery().ToListAsync(ct);
        IEnumerable<Conselheiro> filtered = items;
        if (status.HasValue) filtered = filtered.Where(c => InferStatus(c) == status.Value);
        if (regionalId.HasValue) filtered = filtered.Where(c => c.Profissional?.RegionalId == regionalId.Value);
        return filtered.Select(MapToDto);
    }

    public async Task<ConselheiroDetalheDto?> GetByIdAsync(Guid id, CancellationToken ct = default)
    {
        var c = await BaseQuery().FirstOrDefaultAsync(x => x.Id == id, ct);
        return c == null ? null : MapToDetalheDto(c);
    }

    public async Task<IEnumerable<ConselheiroDto>> GetByRegionalAsync(Guid regionalId, CancellationToken ct = default)
    {
        var items = await BaseQuery().Where(c => c.Profissional.RegionalId == regionalId).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<IEnumerable<ConselheiroDto>> GetByMandatoAsync(int mandato, CancellationToken ct = default)
    {
        var items = await BaseQuery()
            .Where(c => c.InicioMandato != null && c.InicioMandato.Value.Year <= mandato &&
                        (c.FimMandato == null || c.FimMandato.Value.Year >= mandato))
            .ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<ConselheiroDetalheDto> CreateAsync(CreateConselheiroDto dto, Guid userId, CancellationToken ct = default)
    {
        var prof = await _db.Profissionais.IgnoreQueryFilters().Where(p => !p.IsDeleted).FirstOrDefaultAsync(p => p.Id == dto.ProfissionalId, ct)
            ?? throw new InvalidOperationException("Profissional nao encontrado");

        var existing = await _db.Conselheiros.IgnoreQueryFilters()
            .Where(c => !c.IsDeleted && c.ProfissionalId == dto.ProfissionalId && c.MandatoAtivo)
            .FirstOrDefaultAsync(ct);
        if (existing != null) throw new InvalidOperationException("Profissional ja possui mandato ativo como conselheiro");

        var conselheiro = new Conselheiro
        {
            ProfissionalId = dto.ProfissionalId,
            Cargo = dto.Cargo,
            Comissao = dto.Observacoes,
            InicioMandato = dto.DataPosse,
            FimMandato = dto.DataFimMandato,
            MandatoAtivo = true
        };
        await _db.Conselheiros.AddAsync(conselheiro, ct);

        await _db.HistoricosExtratoConselheiro.AddAsync(new HistoricoExtratoConselheiro
        {
            ConselheiroId = conselheiro.Id, Tipo = "Empossamento",
            Descricao = $"Empossamento como {dto.Cargo ?? "Conselheiro"}", DataEvento = dto.DataPosse
        }, ct);

        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(conselheiro.Id, ct))!;
    }

    public async Task<ConselheiroDetalheDto> UpdateAsync(Guid id, UpdateConselheiroDto dto, CancellationToken ct = default)
    {
        var c = await _db.Conselheiros.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new KeyNotFoundException("Conselheiro nao encontrado");
        if (dto.Cargo != null) c.Cargo = dto.Cargo;
        if (dto.DataFimMandato.HasValue) c.FimMandato = dto.DataFimMandato;
        if (dto.Observacoes != null) c.Comissao = dto.Observacoes;
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task DeleteAsync(Guid id, CancellationToken ct = default)
    {
        var c = await _db.Conselheiros.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new KeyNotFoundException("Conselheiro nao encontrado");
        if (c.MandatoAtivo) throw new InvalidOperationException("Nao e possivel excluir conselheiro com mandato ativo");
        c.IsDeleted = true;
        await _db.SaveChangesAsync(ct);
    }

    public async Task<IEnumerable<ConselheiroDto>> EmpossarAsync(Guid eleicaoId, EmpossarConselheirosDto dto, Guid userId, CancellationToken ct = default)
    {
        var eleicao = await _db.Eleicoes.IgnoreQueryFilters().Where(e => !e.IsDeleted).FirstOrDefaultAsync(e => e.Id == eleicaoId, ct)
            ?? throw new InvalidOperationException("Eleicao nao encontrada");

        // Get winning chapa members - use Registrada status (approved chapas)
        var chapas = await _db.Chapas.IgnoreQueryFilters()
            .Where(c => !c.IsDeleted && c.EleicaoId == eleicaoId && c.Status == StatusChapa.Registrada)
            .ToListAsync(ct);

        if (!chapas.Any())
        {
            // Fallback: pick chapa with most votes
            var chapaVoteCounts = await _db.Votos.IgnoreQueryFilters()
                .Where(v => !v.IsDeleted && v.EleicaoId == eleicaoId && v.ChapaId != null)
                .GroupBy(v => v.ChapaId)
                .Select(g => new { ChapaId = g.Key, Count = g.Count() })
                .OrderByDescending(x => x.Count)
                .FirstOrDefaultAsync(ct);
            if (chapaVoteCounts != null)
                chapas = await _db.Chapas.IgnoreQueryFilters()
                    .Where(c => !c.IsDeleted && c.Id == chapaVoteCounts.ChapaId)
                    .ToListAsync(ct);
        }

        var empossados = new List<ConselheiroDto>();
        foreach (var chapa in chapas)
        {
            var membros = await _db.MembrosChapa.IgnoreQueryFilters().Include(m => m.Profissional)
                .Where(m => !m.IsDeleted && m.ChapaId == chapa.Id).ToListAsync(ct);

            foreach (var membro in membros)
            {
                if (membro.ProfissionalId == null) continue;

                var existing = await _db.Conselheiros.IgnoreQueryFilters()
                    .Where(c => !c.IsDeleted && c.ProfissionalId == membro.ProfissionalId && c.MandatoAtivo)
                    .FirstOrDefaultAsync(ct);
                if (existing != null) continue;

                var conselheiro = new Conselheiro
                {
                    ProfissionalId = membro.ProfissionalId.Value,
                    Cargo = membro.Cargo ?? membro.Tipo.ToString(),
                    InicioMandato = dto.DataPosse,
                    FimMandato = dto.DataFimMandato,
                    MandatoAtivo = true
                };
                await _db.Conselheiros.AddAsync(conselheiro, ct);

                await _db.HistoricosExtratoConselheiro.AddAsync(new HistoricoExtratoConselheiro
                {
                    ConselheiroId = conselheiro.Id, Tipo = "Empossamento",
                    Descricao = $"Empossado via eleicao {eleicao.Nome}", DataEvento = dto.DataPosse
                }, ct);
            }
        }

        await _db.SaveChangesAsync(ct);
        return await GetByMandatoAsync(dto.Mandato, ct);
    }

    public async Task<ConselheiroDetalheDto> AfastarAsync(Guid id, AfastarConselheiroDto dto, Guid userId, CancellationToken ct = default)
    {
        var c = await _db.Conselheiros.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new KeyNotFoundException("Conselheiro nao encontrado");
        if (!c.MandatoAtivo) throw new InvalidOperationException("Conselheiro nao possui mandato ativo");

        c.MandatoAtivo = false;
        c.MotivoFinalizacao = $"Afastamento: {dto.Motivo}";
        c.DataFinalizacao = dto.DataAfastamento;

        await _db.HistoricosExtratoConselheiro.AddAsync(new HistoricoExtratoConselheiro
        {
            ConselheiroId = id, Tipo = "Afastamento",
            Descricao = dto.Motivo, DataEvento = dto.DataAfastamento
        }, ct);

        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<ConselheiroDetalheDto> ReintegrarAsync(Guid id, Guid userId, CancellationToken ct = default)
    {
        var c = await _db.Conselheiros.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new KeyNotFoundException("Conselheiro nao encontrado");
        if (c.MandatoAtivo) throw new InvalidOperationException("Conselheiro ja possui mandato ativo");

        c.MandatoAtivo = true;
        c.MotivoFinalizacao = null;
        c.DataFinalizacao = null;

        await _db.HistoricosExtratoConselheiro.AddAsync(new HistoricoExtratoConselheiro
        {
            ConselheiroId = id, Tipo = "Reintegracao",
            Descricao = "Reintegrado ao conselho", DataEvento = DateTime.UtcNow
        }, ct);

        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<ConselheiroDetalheDto> RenovarMandatoAsync(Guid id, RenovarMandatoDto dto, Guid userId, CancellationToken ct = default)
    {
        var c = await _db.Conselheiros.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new KeyNotFoundException("Conselheiro nao encontrado");

        c.InicioMandato = dto.DataInicioMandato;
        c.FimMandato = dto.DataFimMandato;
        c.MandatoAtivo = true;
        c.MotivoFinalizacao = null;
        c.DataFinalizacao = null;

        await _db.HistoricosExtratoConselheiro.AddAsync(new HistoricoExtratoConselheiro
        {
            ConselheiroId = id, Tipo = "Renovacao",
            Descricao = $"Mandato renovado para {dto.NovoMandato}", DataEvento = dto.DataInicioMandato
        }, ct);

        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<ConselheiroDetalheDto> EncerrarMandatoAsync(Guid id, EncerrarMandatoDto dto, Guid userId, CancellationToken ct = default)
    {
        var c = await _db.Conselheiros.IgnoreQueryFilters().Where(x => !x.IsDeleted).FirstOrDefaultAsync(x => x.Id == id, ct)
            ?? throw new KeyNotFoundException("Conselheiro nao encontrado");

        c.MandatoAtivo = false;
        c.MotivoFinalizacao = dto.Motivo ?? "Encerramento de mandato";
        c.DataFinalizacao = dto.DataEncerramento;
        c.FimMandato = dto.DataEncerramento;

        await _db.HistoricosExtratoConselheiro.AddAsync(new HistoricoExtratoConselheiro
        {
            ConselheiroId = id, Tipo = "Encerramento",
            Descricao = dto.Motivo ?? "Mandato encerrado", DataEvento = dto.DataEncerramento
        }, ct);

        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(id, ct))!;
    }

    public async Task<ComposicaoConselhoDto> GetComposicaoAsync(Guid? regionalId, CancellationToken ct = default)
    {
        var query = BaseQuery().Where(c => c.MandatoAtivo);
        if (regionalId.HasValue) query = query.Where(c => c.Profissional.RegionalId == regionalId.Value);

        var conselheiros = await query.ToListAsync(ct);
        var dtos = conselheiros.Select(MapToDto).ToList();

        var diretoria = dtos.Where(c => c.Cargo != null &&
            (c.Cargo.Contains("Presidente", StringComparison.OrdinalIgnoreCase) ||
             c.Cargo.Contains("Secretario", StringComparison.OrdinalIgnoreCase) ||
             c.Cargo.Contains("Tesoureiro", StringComparison.OrdinalIgnoreCase))).ToList();
        var suplentes = dtos.Where(c => c.Cargo != null && c.Cargo.Contains("Suplente", StringComparison.OrdinalIgnoreCase)).ToList();
        var titulares = dtos.Except(diretoria).Except(suplentes).ToList();

        return new ComposicaoConselhoDto
        {
            MandatoAtual = conselheiros.FirstOrDefault()?.InicioMandato?.Year ?? DateTime.UtcNow.Year,
            TotalConselheiros = dtos.Count,
            TotalTitulares = diretoria.Count + titulares.Count,
            TotalSuplentes = suplentes.Count,
            Diretoria = diretoria,
            Conselheiros = titulares,
            Suplentes = suplentes
        };
    }
}
