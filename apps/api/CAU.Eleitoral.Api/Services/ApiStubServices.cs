using CAU.Eleitoral.Api.Controllers;
using CAU.Eleitoral.Application.DTOs;
using CAU.Eleitoral.Domain.Entities.Chapas;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Julgamentos;
using CAU.Eleitoral.Domain.Entities.Usuarios;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Infrastructure.Data;
using Microsoft.AspNetCore.Http;
using Microsoft.EntityFrameworkCore;
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
    public NotificacaoApiService(AppDbContext db) => _db = db;

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
        var count = await _db.Notificacoes.IgnoreQueryFilters().Where(n => !n.IsDeleted && n.UsuarioId == userId && !n.Lida).CountAsync(ct);
        return new ContagemNotificacoesDto { Total = count, NaoLidas = count };
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

        if (sucesso > 0) await _db.SaveChangesAsync(ct);

        return new ResultadoEnvioMassaDto
        {
            TotalEnviadas = userIds.Count, Sucesso = sucesso, Falhas = falhas,
            Erros = erros.Any() ? erros : null
        };
    }

    public Task<ConfiguracaoNotificacaoDto> GetConfiguracoesAsync(Guid userId, CancellationToken ct = default)
        => Task.FromResult(new ConfiguracaoNotificacaoDto
        {
            EmailHabilitado = true, NotificacaoEleicao = true, NotificacaoVotacao = true,
            NotificacaoResultado = true, NotificacaoSistema = true, NotificacaoDenuncia = true,
            NotificacaoImpugnacao = true, FrequenciaResumo = "diario"
        });

    public Task<ConfiguracaoNotificacaoDto> UpdateConfiguracoesAsync(Guid userId, UpdateConfiguracaoNotificacaoDto dto, CancellationToken ct = default)
    {
        var config = new ConfiguracaoNotificacaoDto
        {
            EmailHabilitado = dto.EmailHabilitado ?? true,
            PushHabilitado = dto.PushHabilitado ?? false,
            SmsHabilitado = dto.SmsHabilitado ?? false,
            NotificacaoEleicao = dto.NotificacaoEleicao ?? true,
            NotificacaoDenuncia = dto.NotificacaoDenuncia ?? true,
            NotificacaoImpugnacao = dto.NotificacaoImpugnacao ?? true,
            NotificacaoVotacao = dto.NotificacaoVotacao ?? true,
            NotificacaoResultado = dto.NotificacaoResultado ?? true,
            NotificacaoSistema = dto.NotificacaoSistema ?? true,
            ResumoDigital = dto.ResumoDigital ?? false,
            FrequenciaResumo = dto.FrequenciaResumo ?? "diario"
        };
        return Task.FromResult(config);
    }
}

// ── DocumentoService ──
public class DocumentoApiService : Controllers.IDocumentoService
{
    private readonly AppDbContext _db;
    private readonly IWebHostEnvironment _env;
    public DocumentoApiService(AppDbContext db, IWebHostEnvironment env) { _db = db; _env = env; }

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
            Numero = dto.Numero, DataDocumento = dto.DataDocumento ?? DateTime.UtcNow
        };
        await _db.Documentos.AddAsync(doc, ct);
        await _db.SaveChangesAsync(ct);
        return (await GetByIdAsync(doc.Id, ct))!;
    }

    public async Task<Controllers.DocumentoDto> UploadAsync(IFormFile file, Guid eleicaoId, TipoDocumento tipo, CategoriaDocumento categoria, Guid userId, CancellationToken ct = default)
    {
        var uploadsDir = Path.Combine(_env.ContentRootPath, "uploads", "documentos");
        Directory.CreateDirectory(uploadsDir);

        var fileName = $"{Guid.NewGuid()}{Path.GetExtension(file.FileName)}";
        var filePath = Path.Combine(uploadsDir, fileName);

        using (var stream = new FileStream(filePath, FileMode.Create))
        {
            await file.CopyToAsync(stream, ct);
        }

        var doc = new CAU.Eleitoral.Domain.Entities.Documentos.Documento
        {
            EleicaoId = eleicaoId, Titulo = Path.GetFileNameWithoutExtension(file.FileName),
            Tipo = tipo, Categoria = categoria, Status = StatusDocumento.Rascunho,
            DataDocumento = DateTime.UtcNow, ArquivoUrl = $"/uploads/documentos/{fileName}",
            ArquivoNome = file.FileName, ArquivoTipo = file.ContentType, ArquivoTamanho = file.Length
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

        if (!string.IsNullOrEmpty(d.ArquivoUrl))
        {
            var filePath = Path.Combine(_env.ContentRootPath, d.ArquivoUrl.TrimStart('/'));
            if (File.Exists(filePath))
            {
                var content = await File.ReadAllBytesAsync(filePath, ct);
                return (content, d.ArquivoTipo ?? "application/octet-stream", d.ArquivoNome ?? "documento");
            }
        }

        // Generate a placeholder document with document info
        var sb = new StringBuilder();
        sb.AppendLine($"Documento: {d.Titulo}");
        sb.AppendLine($"Numero: {d.Numero}");
        sb.AppendLine($"Tipo: {d.Tipo}");
        sb.AppendLine($"Categoria: {d.Categoria}");
        sb.AppendLine($"Data: {d.DataDocumento:yyyy-MM-dd}");
        sb.AppendLine($"Status: {d.Status}");
        if (!string.IsNullOrEmpty(d.Ementa)) sb.AppendLine($"\nEmenta:\n{d.Ementa}");
        return (Encoding.UTF8.GetBytes(sb.ToString()), "text/plain", $"{d.Titulo}.txt");
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
    public RelatorioApiService(AppDbContext db) => _db = db;

    public Task<IEnumerable<TipoRelatorioDto>> GetTiposDisponiveisAsync(CancellationToken ct = default)
    {
        var tipos = new List<TipoRelatorioDto>
        {
            new() { Codigo = "participacao", Nome = "Participacao", FormatosDisponiveis = new() { "pdf", "xlsx", "json" }, RequerEleicao = true },
            new() { Codigo = "resultado", Nome = "Resultado", FormatosDisponiveis = new() { "pdf", "xlsx", "json" }, RequerEleicao = true },
            new() { Codigo = "chapas", Nome = "Chapas", FormatosDisponiveis = new() { "pdf", "xlsx", "json" }, RequerEleicao = true },
            new() { Codigo = "eleitores", Nome = "Eleitores", FormatosDisponiveis = new() { "pdf", "xlsx", "json" }, RequerEleicao = true },
            new() { Codigo = "denuncias", Nome = "Denuncias", FormatosDisponiveis = new() { "pdf", "xlsx", "json" }, RequerEleicao = true },
            new() { Codigo = "consolidado", Nome = "Consolidado", FormatosDisponiveis = new() { "pdf", "json" }, RequerEleicao = true },
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

        if (root.TryGetProperty("chapas", out var chapas) && chapas.ValueKind == JsonValueKind.Array)
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

    private async Task<(byte[] Content, string ContentType, string FileName)> GenerateReport(string tipo, Guid eleicaoId, string formato, CancellationToken ct)
    {
        var json = await BuildJsonReport(tipo, eleicaoId, ct);

        if (formato == "json")
            return (Encoding.UTF8.GetBytes(json), "application/json", $"{tipo}_{eleicaoId:N}.json");

        if (formato == "xlsx")
        {
            var csv = BuildCsvReport(json, tipo);
            return (Encoding.UTF8.GetBytes(csv), "text/csv", $"{tipo}_{eleicaoId:N}.csv");
        }

        // PDF format - return formatted text report
        var report = BuildCsvReport(json, tipo);
        var header = $"=== CAU SISTEMA ELEITORAL ===\n=== RELATORIO: {tipo.ToUpper()} ===\n\n";
        return (Encoding.UTF8.GetBytes(header + report), "application/pdf", $"{tipo}_{eleicaoId:N}.pdf");
    }

    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioParticipacaoAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => GenerateReport("participacao", eleicaoId, formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioResultadoAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => GenerateReport("resultado", eleicaoId, formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioChapasAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => GenerateReport("chapas", eleicaoId, formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioEleitoresAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => GenerateReport("eleitores", eleicaoId, formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioDenunciasAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => GenerateReport("denuncias", eleicaoId, formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioImpugnacoesAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => GenerateReport("impugnacoes", eleicaoId, formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioAuditoriaAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => GenerateReport("auditoria", eleicaoId, formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioConsolidadoAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => GenerateReport("consolidado", eleicaoId, formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioComparativoAsync(List<Guid> eleicaoIds, string formato, CancellationToken ct = default) => GenerateReport("comparativo", eleicaoIds.FirstOrDefault(), formato, ct);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioPersonalizadoAsync(RelatorioPersonalizadoDto dto, CancellationToken ct = default) => GenerateReport("personalizado", dto.EleicaoId, dto.Formato, ct);

    public Task<IEnumerable<RelatorioGeradoDto>> GetHistoricoAsync(Guid? eleicaoId, CancellationToken ct = default)
        => Task.FromResult<IEnumerable<RelatorioGeradoDto>>(new List<RelatorioGeradoDto>());
    public Task<(byte[] Content, string ContentType, string FileName)> DownloadAsync(Guid id, CancellationToken ct = default)
        => throw new KeyNotFoundException("Relatorio nao encontrado");
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
