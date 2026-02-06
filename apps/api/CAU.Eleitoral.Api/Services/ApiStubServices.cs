using CAU.Eleitoral.Api.Controllers;
using CAU.Eleitoral.Application.DTOs;
using CAU.Eleitoral.Domain.Entities.Chapas;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Infrastructure.Data;
using Microsoft.AspNetCore.Http;
using Microsoft.EntityFrameworkCore;

namespace CAU.Eleitoral.Api.Services;

// ── AuditoriaService ──
public class AuditoriaApiService : Controllers.IAuditoriaService
{
    public Task<PagedResult<AuditoriaLogDto>> GetAllAsync(FiltroAuditoriaDto filtro, int page, int pageSize, CancellationToken ct = default)
        => Task.FromResult(PagedResult<AuditoriaLogDto>.Empty);
    public Task<AuditoriaLogDetalheDto?> GetByIdAsync(Guid id, CancellationToken ct = default)
        => Task.FromResult<AuditoriaLogDetalheDto?>(null);
    public Task<PagedResult<AuditoriaLogDto>> GetByUsuarioAsync(Guid usuarioId, int page, int pageSize, CancellationToken ct = default)
        => Task.FromResult(PagedResult<AuditoriaLogDto>.Empty);
    public Task<PagedResult<AuditoriaLogDto>> GetByEntidadeAsync(string entidadeTipo, Guid entidadeId, int page, int pageSize, CancellationToken ct = default)
        => Task.FromResult(PagedResult<AuditoriaLogDto>.Empty);
    public Task<PagedResult<AuditoriaLogDto>> GetByAcaoAsync(string acao, int page, int pageSize, CancellationToken ct = default)
        => Task.FromResult(PagedResult<AuditoriaLogDto>.Empty);
    public Task<PagedResult<AuditoriaLogDto>> GetByPeriodoAsync(DateTime dataInicio, DateTime dataFim, int page, int pageSize, CancellationToken ct = default)
        => Task.FromResult(PagedResult<AuditoriaLogDto>.Empty);
    public Task<EstatisticasAuditoriaDto> GetEstatisticasAsync(DateTime? dataInicio, DateTime? dataFim, CancellationToken ct = default)
        => Task.FromResult(new EstatisticasAuditoriaDto());
    public Task<IEnumerable<string>> GetAcoesAsync(CancellationToken ct = default)
        => Task.FromResult<IEnumerable<string>>(new List<string> { "Login", "Logout", "Criacao", "Atualizacao", "Exclusao", "Votacao" });
    public Task<IEnumerable<string>> GetTiposEntidadeAsync(CancellationToken ct = default)
        => Task.FromResult<IEnumerable<string>>(new List<string> { "Eleicao", "Chapa", "Denuncia", "Impugnacao", "Usuario", "Voto" });
    public Task<(byte[] Content, string ContentType, string FileName)> ExportarAsync(FiltroAuditoriaDto filtro, string formato, CancellationToken ct = default)
        => Task.FromResult((Array.Empty<byte>(), "application/octet-stream", "auditoria.csv"));
    public Task<ResultadoLimpezaDto> LimparLogsAntigosAsync(int diasRetencao, CancellationToken ct = default)
        => Task.FromResult(new ResultadoLimpezaDto { LogsRemovidos = 0, DataCorte = DateTime.UtcNow.AddDays(-diasRetencao), DataExecucao = DateTime.UtcNow });
    public Task<IEnumerable<HistoricoAlteracaoDto>> GetHistoricoAlteracoesAsync(string entidadeTipo, Guid entidadeId, CancellationToken ct = default)
        => Task.FromResult<IEnumerable<HistoricoAlteracaoDto>>(new List<HistoricoAlteracaoDto>());
}

// ── FilialService ──
public class FilialApiService : Controllers.IFilialService
{
    public Task<IEnumerable<FilialDto>> GetAllAsync(bool? ativa, string? uf, CancellationToken ct = default)
        => Task.FromResult<IEnumerable<FilialDto>>(new List<FilialDto>());
    public Task<FilialDetalheDto?> GetByIdAsync(Guid id, CancellationToken ct = default)
        => Task.FromResult<FilialDetalheDto?>(null);
    public Task<FilialDto?> GetByCodigoAsync(string codigo, CancellationToken ct = default)
        => Task.FromResult<FilialDto?>(null);
    public Task<FilialDto?> GetByUFAsync(string uf, CancellationToken ct = default)
        => Task.FromResult<FilialDto?>(null);
    public Task<FilialDetalheDto> CreateAsync(CreateFilialDto dto, Guid userId, CancellationToken ct = default)
        => throw new NotImplementedException("Criar filial nao implementado");
    public Task<FilialDetalheDto> UpdateAsync(Guid id, UpdateFilialDto dto, CancellationToken ct = default)
        => throw new NotImplementedException("Atualizar filial nao implementado");
    public Task DeleteAsync(Guid id, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<FilialDto> AtivarAsync(Guid id, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<FilialDto> DesativarAsync(Guid id, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<EstatisticasFilialDto> GetEstatisticasAsync(Guid id, CancellationToken ct = default)
        => Task.FromResult(new EstatisticasFilialDto { FilialId = id });
    public Task<PagedResult<ProfissionalFilialDto>> GetProfissionaisAsync(Guid id, int page, int pageSize, CancellationToken ct = default)
        => Task.FromResult(PagedResult<ProfissionalFilialDto>.Empty);
    public Task<IEnumerable<EleicaoFilialDto>> GetEleicoesAsync(Guid id, CancellationToken ct = default)
        => Task.FromResult<IEnumerable<EleicaoFilialDto>>(new List<EleicaoFilialDto>());
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
    public Task<PagedResult<NotificacaoDto>> GetByUsuarioAsync(Guid userId, bool apenasNaoLidas, int page, int pageSize, CancellationToken ct = default)
        => Task.FromResult(PagedResult<NotificacaoDto>.Empty);
    public Task<NotificacaoDto?> GetByIdAsync(Guid id, Guid userId, CancellationToken ct = default)
        => Task.FromResult<NotificacaoDto?>(null);
    public Task<ContagemNotificacoesDto> GetContagemNaoLidasAsync(Guid userId, CancellationToken ct = default)
        => Task.FromResult(new ContagemNotificacoesDto());
    public Task<NotificacaoDto> MarcarComoLidaAsync(Guid id, Guid userId, CancellationToken ct = default)
        => throw new KeyNotFoundException("Notificacao nao encontrada");
    public Task<int> MarcarTodasComoLidasAsync(Guid userId, CancellationToken ct = default) => Task.FromResult(0);
    public Task DeleteAsync(Guid id, Guid userId, CancellationToken ct = default) => Task.CompletedTask;
    public Task<int> DeleteLidasAsync(Guid userId, CancellationToken ct = default) => Task.FromResult(0);
    public Task<NotificacaoDto> EnviarAsync(CreateNotificacaoDto dto, CancellationToken ct = default)
        => Task.FromResult(new NotificacaoDto { Id = Guid.NewGuid(), Titulo = dto.Titulo, Mensagem = dto.Mensagem, CreatedAt = DateTime.UtcNow });
    public Task<ResultadoEnvioMassaDto> EnviarEmMassaAsync(CreateNotificacaoMassaDto dto, CancellationToken ct = default)
        => Task.FromResult(new ResultadoEnvioMassaDto());
    public Task<ConfiguracaoNotificacaoDto> GetConfiguracoesAsync(Guid userId, CancellationToken ct = default)
        => Task.FromResult(new ConfiguracaoNotificacaoDto { EmailHabilitado = true, NotificacaoEleicao = true, NotificacaoVotacao = true, NotificacaoSistema = true });
    public Task<ConfiguracaoNotificacaoDto> UpdateConfiguracoesAsync(Guid userId, UpdateConfiguracaoNotificacaoDto dto, CancellationToken ct = default)
        => Task.FromResult(new ConfiguracaoNotificacaoDto());
}

// ── DocumentoService ──
public class DocumentoApiService : Controllers.IDocumentoService
{
    private readonly AppDbContext _db;
    public DocumentoApiService(AppDbContext db) => _db = db;

    private static Controllers.DocumentoDto MapToDto(CAU.Eleitoral.Domain.Entities.Documentos.Documento d) => new()
    {
        Id = d.Id,
        EleicaoId = d.EleicaoId,
        EleicaoNome = d.Eleicao?.Nome ?? string.Empty,
        Titulo = d.Titulo,
        Descricao = d.Ementa,
        Tipo = d.Tipo,
        Categoria = d.Categoria,
        Status = d.Status,
        Numero = d.Numero,
        DataDocumento = d.DataDocumento,
        DataPublicacao = d.DataPublicacao,
        DataRevogacao = d.DataRevogacao,
        Url = d.ArquivoUrl,
        NomeArquivo = d.ArquivoNome,
        TipoArquivo = d.ArquivoTipo,
        Tamanho = d.ArquivoTamanho,
        CreatedAt = d.CreatedAt,
        UpdatedAt = d.UpdatedAt
    };

    public async Task<IEnumerable<Controllers.DocumentoDto>> GetAllAsync(Guid? eleicaoId, TipoDocumento? tipo, CategoriaDocumento? categoria, CancellationToken ct = default)
    {
        var query = _db.Documentos.Include(d => d.Eleicao).AsQueryable();
        if (eleicaoId.HasValue) query = query.Where(d => d.EleicaoId == eleicaoId.Value);
        if (tipo.HasValue) query = query.Where(d => d.Tipo == tipo.Value);
        if (categoria.HasValue) query = query.Where(d => d.Categoria == categoria.Value);
        var items = await query.OrderByDescending(d => d.DataPublicacao ?? d.CreatedAt).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<Controllers.DocumentoDto?> GetByIdAsync(Guid id, CancellationToken ct = default)
    {
        var d = await _db.Documentos.Include(x => x.Eleicao).FirstOrDefaultAsync(x => x.Id == id, ct);
        return d == null ? null : MapToDto(d);
    }

    public async Task<IEnumerable<Controllers.DocumentoDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken ct = default)
    {
        var items = await _db.Documentos.Include(d => d.Eleicao)
            .Where(d => d.EleicaoId == eleicaoId).OrderByDescending(d => d.DataPublicacao ?? d.CreatedAt).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<IEnumerable<Controllers.DocumentoDto>> GetPublicadosAsync(Guid? eleicaoId, CancellationToken ct = default)
    {
        var query = _db.Documentos.Include(d => d.Eleicao).Where(d => d.Status == StatusDocumento.Publicado);
        if (eleicaoId.HasValue) query = query.Where(d => d.EleicaoId == eleicaoId.Value);
        var items = await query.OrderByDescending(d => d.DataPublicacao ?? d.CreatedAt).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public Task<Controllers.DocumentoDto> CreateAsync(Controllers.CreateDocumentoDto dto, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<Controllers.DocumentoDto> UploadAsync(IFormFile file, Guid eleicaoId, TipoDocumento tipo, CategoriaDocumento categoria, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<Controllers.DocumentoDto> UpdateAsync(Guid id, Controllers.UpdateDocumentoDto dto, CancellationToken ct = default) => throw new NotImplementedException();
    public Task DeleteAsync(Guid id, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<(byte[] Content, string ContentType, string FileName)> DownloadAsync(Guid id, CancellationToken ct = default) => throw new KeyNotFoundException("Documento nao encontrado");
    public Task<Controllers.DocumentoDto> EnviarParaRevisaoAsync(Guid id, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<Controllers.DocumentoDto> AprovarAsync(Guid id, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<Controllers.DocumentoDto> PublicarAsync(Guid id, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<Controllers.DocumentoDto> RevogarAsync(Guid id, string motivo, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<Controllers.DocumentoDto> ArquivarAsync(Guid id, CancellationToken ct = default) => throw new NotImplementedException();
}

// ── JulgamentoService ──
public class JulgamentoApiService : Controllers.IJulgamentoService
{
    public Task<IEnumerable<JulgamentoDto>> GetAllAsync(Guid? eleicaoId, StatusJulgamento? status, CancellationToken ct = default)
        => Task.FromResult<IEnumerable<JulgamentoDto>>(new List<JulgamentoDto>());
    public Task<JulgamentoDto?> GetByIdAsync(Guid id, CancellationToken ct = default) => Task.FromResult<JulgamentoDto?>(null);
    public Task<IEnumerable<JulgamentoDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken ct = default)
        => Task.FromResult<IEnumerable<JulgamentoDto>>(new List<JulgamentoDto>());
    public Task<IEnumerable<JulgamentoDto>> GetAgendadosAsync(CancellationToken ct = default)
        => Task.FromResult<IEnumerable<JulgamentoDto>>(new List<JulgamentoDto>());
    public Task<JulgamentoDto> CreateAsync(CreateJulgamentoDto dto, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<JulgamentoDto> UpdateAsync(Guid id, UpdateJulgamentoDto dto, CancellationToken ct = default) => throw new NotImplementedException();
    public Task DeleteAsync(Guid id, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<JulgamentoDto> IniciarAsync(Guid id, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<JulgamentoDto> SuspenderAsync(Guid id, string motivo, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<JulgamentoDto> RetomarAsync(Guid id, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<JulgamentoDto> RegistrarVotoAsync(Guid id, VotoJulgamentoDto dto, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<JulgamentoDto> ConcluirAsync(Guid id, ConcluirJulgamentoDto dto, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<JulgamentoDto> CancelarAsync(Guid id, string motivo, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<IEnumerable<SessaoJulgamentoDto>> GetSessoesAsync(Guid? eleicaoId, CancellationToken ct = default)
        => Task.FromResult<IEnumerable<SessaoJulgamentoDto>>(new List<SessaoJulgamentoDto>());
    public Task<SessaoJulgamentoDto> CreateSessaoAsync(CreateSessaoJulgamentoDto dto, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
}

// ── RelatorioService ──
public class RelatorioApiService : Controllers.IRelatorioService
{
    public Task<IEnumerable<TipoRelatorioDto>> GetTiposDisponiveisAsync(CancellationToken ct = default)
    {
        var tipos = new List<TipoRelatorioDto>
        {
            new() { Codigo = "participacao", Nome = "Participacao", FormatosDisponiveis = new() { "pdf", "xlsx" }, RequerEleicao = true },
            new() { Codigo = "resultado", Nome = "Resultado", FormatosDisponiveis = new() { "pdf", "xlsx" }, RequerEleicao = true },
            new() { Codigo = "chapas", Nome = "Chapas", FormatosDisponiveis = new() { "pdf", "xlsx" }, RequerEleicao = true },
            new() { Codigo = "eleitores", Nome = "Eleitores", FormatosDisponiveis = new() { "pdf", "xlsx" }, RequerEleicao = true },
            new() { Codigo = "denuncias", Nome = "Denuncias", FormatosDisponiveis = new() { "pdf", "xlsx" }, RequerEleicao = true },
            new() { Codigo = "consolidado", Nome = "Consolidado", FormatosDisponiveis = new() { "pdf" }, RequerEleicao = true },
        };
        return Task.FromResult<IEnumerable<TipoRelatorioDto>>(tipos);
    }
    private static Task<(byte[] Content, string ContentType, string FileName)> StubReport(string nome, string formato)
        => Task.FromResult((Array.Empty<byte>(), formato == "xlsx" ? "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" : "application/pdf", $"{nome}.{formato}"));
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioParticipacaoAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => StubReport("participacao", formato);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioResultadoAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => StubReport("resultado", formato);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioChapasAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => StubReport("chapas", formato);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioEleitoresAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => StubReport("eleitores", formato);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioDenunciasAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => StubReport("denuncias", formato);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioImpugnacoesAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => StubReport("impugnacoes", formato);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioAuditoriaAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => StubReport("auditoria", formato);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioConsolidadoAsync(Guid eleicaoId, string formato, CancellationToken ct = default) => StubReport("consolidado", formato);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioComparativoAsync(List<Guid> eleicaoIds, string formato, CancellationToken ct = default) => StubReport("comparativo", formato);
    public Task<(byte[] Content, string ContentType, string FileName)> GerarRelatorioPersonalizadoAsync(RelatorioPersonalizadoDto dto, CancellationToken ct = default) => StubReport("personalizado", dto.Formato);
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
        Id = m.Id,
        ChapaId = m.ChapaId,
        ChapaNome = m.Chapa?.Nome ?? string.Empty,
        ProfissionalId = m.ProfissionalId ?? Guid.Empty,
        ProfissionalNome = m.Profissional?.NomeCompleto ?? m.Nome,
        ProfissionalRegistroCAU = m.RegistroCAU ?? m.Profissional?.RegistroCAU,
        ProfissionalCpf = m.Cpf ?? m.Profissional?.Cpf,
        ProfissionalEmail = m.Email ?? m.Profissional?.Email,
        TipoMembro = (int)m.Tipo,
        TipoMembroNome = m.Tipo.ToString(),
        Cargo = m.Cargo,
        Status = (int)m.Status,
        StatusNome = m.Status.ToString(),
        Ordem = m.Ordem,
        CreatedAt = m.CreatedAt,
        UpdatedAt = m.UpdatedAt
    };

    public async Task<IEnumerable<MembroChapaDetalheDto>> GetByChapaAsync(Guid chapaId, CancellationToken ct = default)
    {
        var items = await _db.MembrosChapa.Include(m => m.Chapa).Include(m => m.Profissional)
            .Where(m => m.ChapaId == chapaId).OrderBy(m => m.Ordem).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<MembroChapaDetalheDto?> GetByIdAsync(Guid id, CancellationToken ct = default)
    {
        var m = await _db.MembrosChapa.Include(x => x.Chapa).Include(x => x.Profissional).FirstOrDefaultAsync(x => x.Id == id, ct);
        return m == null ? null : MapToDto(m);
    }

    public async Task<IEnumerable<MembroChapaDetalheDto>> GetByProfissionalAsync(Guid profissionalId, CancellationToken ct = default)
    {
        var items = await _db.MembrosChapa.Include(m => m.Chapa).Include(m => m.Profissional)
            .Where(m => m.ProfissionalId == profissionalId).OrderBy(m => m.Ordem).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public Task<MembroChapaDetalheDto> CreateAsync(CreateMembroChapaDetalheDto dto, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<MembroChapaDetalheDto> UpdateAsync(Guid id, UpdateMembroChapaRequestDto dto, CancellationToken ct = default) => throw new NotImplementedException();
    public Task DeleteAsync(Guid id, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<IEnumerable<MembroChapaDetalheDto>> ReordenarAsync(Guid chapaId, List<Guid> ordemIds, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<ValidacaoElegibilidadeDto> ValidarElegibilidadeAsync(Guid id, CancellationToken ct = default)
        => Task.FromResult(new ValidacaoElegibilidadeDto { MembroId = id, Elegivel = true, RegistroAtivo = true, AdimplenteAnuidade = true, SemDebitos = true, SemPenalidadesAtivas = true });
    public Task<MembroChapaDetalheDto> AprovarAsync(Guid id, string? parecer, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<MembroChapaDetalheDto> RejeitarAsync(Guid id, string motivo, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
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
    public CalendarioApiService(AppDbContext db, ILogger<CalendarioApiService> logger)
    {
        _db = db;
        _logger = logger;
    }

    private static CalendarioEventoDto MapToDto(Calendario c) => new()
    {
        Id = c.Id,
        EleicaoId = c.EleicaoId,
        EleicaoNome = c.Eleicao?.Nome ?? string.Empty,
        Titulo = c.Nome,
        Descricao = c.Descricao,
        Tipo = c.Tipo,
        Status = c.Status,
        DataInicio = c.DataInicio,
        DataFim = c.DataFim,
        DiaInteiro = !c.HoraInicio.HasValue,
        Obrigatorio = c.Obrigatorio,
        CreatedAt = c.CreatedAt,
        UpdatedAt = c.UpdatedAt
    };

    public async Task<IEnumerable<CalendarioEventoDto>> GetAllAsync(Guid? eleicaoId, TipoCalendario? tipo, CancellationToken ct = default)
    {
        var query = _db.Calendarios.IgnoreQueryFilters().Include(c => c.Eleicao).Where(c => !c.IsDeleted).AsQueryable();
        if (eleicaoId.HasValue) query = query.Where(c => c.EleicaoId == eleicaoId.Value);
        if (tipo.HasValue) query = query.Where(c => c.Tipo == tipo.Value);
        var items = await query.OrderBy(c => c.Ordem).ToListAsync(ct);
        _logger.LogInformation("CalendarioApiService.GetAllAsync: found {Count} items (eleicaoId={EleicaoId}, tipo={Tipo})", items.Count, eleicaoId, tipo);

        if (items.Count == 0)
        {
            var totalAll = await _db.Calendarios.IgnoreQueryFilters().CountAsync(ct);
            var totalDeleted = await _db.Calendarios.IgnoreQueryFilters().Where(c => c.IsDeleted).CountAsync(ct);
            _logger.LogWarning("CalendarioApiService: 0 results. Total (incl deleted): {Total}, Deleted: {Deleted}", totalAll, totalDeleted);

            if (totalDeleted > 0 && totalAll == totalDeleted)
            {
                _logger.LogWarning("All {Count} calendario records are soft-deleted! Restoring...", totalDeleted);
                var deletedItems = await _db.Calendarios.IgnoreQueryFilters().Where(c => c.IsDeleted).ToListAsync(ct);
                foreach (var item in deletedItems) item.IsDeleted = false;
                await _db.SaveChangesAsync(ct);
                items = await _db.Calendarios.Include(c => c.Eleicao).OrderBy(c => c.Ordem).ToListAsync(ct);
                _logger.LogInformation("Restored {Count} calendario records", items.Count);
            }
        }

        return items.Select(MapToDto);
    }

    public async Task<CalendarioEventoDto?> GetByIdAsync(Guid id, CancellationToken ct = default)
    {
        var c = await _db.Calendarios.Include(x => x.Eleicao).FirstOrDefaultAsync(x => x.Id == id, ct);
        return c == null ? null : MapToDto(c);
    }

    public async Task<IEnumerable<CalendarioEventoDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken ct = default)
    {
        var items = await _db.Calendarios.Include(c => c.Eleicao)
            .Where(c => c.EleicaoId == eleicaoId).OrderBy(c => c.Ordem).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<IEnumerable<CalendarioEventoDto>> GetProximosAsync(int dias, Guid? eleicaoId, CancellationToken ct = default)
    {
        var hoje = DateTime.UtcNow;
        var limite = hoje.AddDays(dias);
        var query = _db.Calendarios.Include(c => c.Eleicao)
            .Where(c => c.DataInicio >= hoje && c.DataInicio <= limite);
        if (eleicaoId.HasValue) query = query.Where(c => c.EleicaoId == eleicaoId.Value);
        var items = await query.OrderBy(c => c.DataInicio).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<IEnumerable<CalendarioEventoDto>> GetEmAndamentoAsync(Guid? eleicaoId, CancellationToken ct = default)
    {
        var hoje = DateTime.UtcNow;
        var query = _db.Calendarios.Include(c => c.Eleicao)
            .Where(c => c.DataInicio <= hoje && c.DataFim >= hoje);
        if (eleicaoId.HasValue) query = query.Where(c => c.EleicaoId == eleicaoId.Value);
        var items = await query.OrderBy(c => c.Ordem).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public async Task<IEnumerable<CalendarioEventoDto>> GetByPeriodoAsync(DateTime dataInicio, DateTime dataFim, Guid? eleicaoId, CancellationToken ct = default)
    {
        var query = _db.Calendarios.Include(c => c.Eleicao)
            .Where(c => c.DataInicio >= dataInicio && c.DataFim <= dataFim);
        if (eleicaoId.HasValue) query = query.Where(c => c.EleicaoId == eleicaoId.Value);
        var items = await query.OrderBy(c => c.DataInicio).ToListAsync(ct);
        return items.Select(MapToDto);
    }

    public Task<CalendarioEventoDto> CreateAsync(CreateCalendarioEventoDto dto, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<CalendarioEventoDto> UpdateAsync(Guid id, UpdateCalendarioEventoDto dto, CancellationToken ct = default) => throw new NotImplementedException();
    public Task DeleteAsync(Guid id, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<CalendarioEventoDto> IniciarAsync(Guid id, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<CalendarioEventoDto> ConcluirAsync(Guid id, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<CalendarioEventoDto> CancelarAsync(Guid id, string motivo, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<IEnumerable<CalendarioEventoDto>> GerarCalendarioPadraoAsync(Guid eleicaoId, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
}

// ── ConselheiroService ──
public class ConselheiroApiService : Controllers.IConselheiroService
{
    public Task<IEnumerable<ConselheiroDto>> GetAllAsync(StatusConselheiro? status, TipoConselheiro? tipo, Guid? regionalId, CancellationToken ct = default)
        => Task.FromResult<IEnumerable<ConselheiroDto>>(new List<ConselheiroDto>());
    public Task<ConselheiroDetalheDto?> GetByIdAsync(Guid id, CancellationToken ct = default) => Task.FromResult<ConselheiroDetalheDto?>(null);
    public Task<IEnumerable<ConselheiroDto>> GetByRegionalAsync(Guid regionalId, CancellationToken ct = default)
        => Task.FromResult<IEnumerable<ConselheiroDto>>(new List<ConselheiroDto>());
    public Task<IEnumerable<ConselheiroDto>> GetByMandatoAsync(int mandato, CancellationToken ct = default)
        => Task.FromResult<IEnumerable<ConselheiroDto>>(new List<ConselheiroDto>());
    public Task<ConselheiroDetalheDto> CreateAsync(CreateConselheiroDto dto, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<ConselheiroDetalheDto> UpdateAsync(Guid id, UpdateConselheiroDto dto, CancellationToken ct = default) => throw new NotImplementedException();
    public Task DeleteAsync(Guid id, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<IEnumerable<ConselheiroDto>> EmpossarAsync(Guid eleicaoId, EmpossarConselheirosDto dto, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<ConselheiroDetalheDto> AfastarAsync(Guid id, AfastarConselheiroDto dto, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<ConselheiroDetalheDto> ReintegrarAsync(Guid id, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<ConselheiroDetalheDto> RenovarMandatoAsync(Guid id, RenovarMandatoDto dto, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<ConselheiroDetalheDto> EncerrarMandatoAsync(Guid id, EncerrarMandatoDto dto, Guid userId, CancellationToken ct = default) => throw new NotImplementedException();
    public Task<ComposicaoConselhoDto> GetComposicaoAsync(Guid? regionalId, CancellationToken ct = default)
        => Task.FromResult(new ComposicaoConselhoDto());
}
