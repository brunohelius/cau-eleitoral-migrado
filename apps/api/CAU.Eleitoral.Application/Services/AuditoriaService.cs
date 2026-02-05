using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using System.Text;
using System.Text.Json;
using CAU.Eleitoral.Application.DTOs.Auditoria;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Usuarios;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

public class AuditoriaService : IAuditoriaService
{
    private readonly IRepository<LogAcesso> _logAcessoRepository;
    private readonly IRepository<Usuario> _usuarioRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<AuditoriaService> _logger;

    // In-memory storage for audit logs (in production, use a dedicated audit table)
    private static readonly List<LogAuditoriaDto> _auditLogs = new();

    public AuditoriaService(
        IRepository<LogAcesso> logAcessoRepository,
        IRepository<Usuario> usuarioRepository,
        IUnitOfWork unitOfWork,
        ILogger<AuditoriaService> logger)
    {
        _logAcessoRepository = logAcessoRepository;
        _usuarioRepository = usuarioRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    public async Task RegistrarAsync(RegistrarAuditoriaDto dto, CancellationToken cancellationToken = default)
    {
        var usuario = dto.UsuarioId.HasValue
            ? await _usuarioRepository.GetByIdAsync(dto.UsuarioId.Value, cancellationToken)
            : null;

        var log = new LogAuditoriaDto
        {
            Id = Guid.NewGuid(),
            UsuarioId = dto.UsuarioId,
            UsuarioNome = usuario?.Nome,
            UsuarioEmail = usuario?.Email,
            Acao = dto.Acao,
            AcaoNome = dto.Acao.ToString(),
            Entidade = dto.Entidade,
            EntidadeId = dto.EntidadeId,
            EntidadeNome = dto.EntidadeNome,
            ValorAnterior = dto.ValorAnterior != null ? JsonSerializer.Serialize(dto.ValorAnterior) : null,
            ValorNovo = dto.ValorNovo != null ? JsonSerializer.Serialize(dto.ValorNovo) : null,
            IpAddress = dto.IpAddress,
            UserAgent = dto.UserAgent,
            Recurso = dto.Recurso,
            Metodo = dto.Metodo,
            StatusCode = dto.StatusCode,
            Sucesso = dto.Sucesso,
            Mensagem = dto.Mensagem,
            DataAcao = DateTime.UtcNow
        };

        _auditLogs.Add(log);

        _logger.LogInformation("Auditoria registrada: {Acao} - {Entidade} por {UsuarioId}",
            dto.Acao, dto.Entidade, dto.UsuarioId);
    }

    public async Task RegistrarCriacaoAsync<T>(T entidade, Guid? usuarioId, string? ipAddress = null, CancellationToken cancellationToken = default)
    {
        await RegistrarAsync(new RegistrarAuditoriaDto
        {
            UsuarioId = usuarioId,
            Acao = TipoAcaoAuditoria.Criar,
            Entidade = typeof(T).Name,
            ValorNovo = entidade,
            IpAddress = ipAddress,
            Sucesso = true
        }, cancellationToken);
    }

    public async Task RegistrarAtualizacaoAsync<T>(T entidadeAnterior, T entidadeNova, Guid? usuarioId, string? ipAddress = null, CancellationToken cancellationToken = default)
    {
        await RegistrarAsync(new RegistrarAuditoriaDto
        {
            UsuarioId = usuarioId,
            Acao = TipoAcaoAuditoria.Atualizar,
            Entidade = typeof(T).Name,
            ValorAnterior = entidadeAnterior,
            ValorNovo = entidadeNova,
            IpAddress = ipAddress,
            Sucesso = true
        }, cancellationToken);
    }

    public async Task RegistrarExclusaoAsync<T>(T entidade, Guid? usuarioId, string? ipAddress = null, CancellationToken cancellationToken = default)
    {
        await RegistrarAsync(new RegistrarAuditoriaDto
        {
            UsuarioId = usuarioId,
            Acao = TipoAcaoAuditoria.Excluir,
            Entidade = typeof(T).Name,
            ValorAnterior = entidade,
            IpAddress = ipAddress,
            Sucesso = true
        }, cancellationToken);
    }

    public async Task RegistrarAcessoAsync(Guid usuarioId, string acao, string? recurso = null, bool sucesso = true, string? mensagem = null, string? ipAddress = null, CancellationToken cancellationToken = default)
    {
        var logAcesso = new LogAcesso
        {
            UsuarioId = usuarioId,
            Acao = acao,
            Recurso = recurso,
            IpAddress = ipAddress,
            DataAcesso = DateTime.UtcNow,
            Sucesso = sucesso,
            Mensagem = mensagem
        };

        await _logAcessoRepository.AddAsync(logAcesso, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Acesso registrado: {Acao} por usuario {UsuarioId}", acao, usuarioId);
    }

    public async Task RegistrarLoginAsync(Guid usuarioId, bool sucesso, string? ipAddress = null, string? userAgent = null, string? mensagem = null, CancellationToken cancellationToken = default)
    {
        var acao = sucesso ? TipoAcaoAuditoria.Login : TipoAcaoAuditoria.FalhaLogin;

        await RegistrarAsync(new RegistrarAuditoriaDto
        {
            UsuarioId = usuarioId,
            Acao = acao,
            Entidade = "Usuario",
            EntidadeId = usuarioId,
            IpAddress = ipAddress,
            UserAgent = userAgent,
            Sucesso = sucesso,
            Mensagem = mensagem
        }, cancellationToken);

        await RegistrarAcessoAsync(usuarioId, sucesso ? "Login" : "Login Falhou", null, sucesso, mensagem, ipAddress, cancellationToken);
    }

    public async Task RegistrarLogoutAsync(Guid usuarioId, string? ipAddress = null, CancellationToken cancellationToken = default)
    {
        await RegistrarAsync(new RegistrarAuditoriaDto
        {
            UsuarioId = usuarioId,
            Acao = TipoAcaoAuditoria.Logout,
            Entidade = "Usuario",
            EntidadeId = usuarioId,
            IpAddress = ipAddress,
            Sucesso = true
        }, cancellationToken);

        await RegistrarAcessoAsync(usuarioId, "Logout", null, true, null, ipAddress, cancellationToken);
    }

    public async Task RegistrarVotoAsync(Guid eleicaoId, string hashVoto, string? ipAddress = null, CancellationToken cancellationToken = default)
    {
        await RegistrarAsync(new RegistrarAuditoriaDto
        {
            Acao = TipoAcaoAuditoria.Votar,
            Entidade = "Voto",
            EntidadeNome = hashVoto,
            IpAddress = ipAddress,
            Sucesso = true,
            Mensagem = $"Voto registrado na eleicao {eleicaoId}"
        }, cancellationToken);
    }

    public async Task<LogAuditoriaDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        return _auditLogs.FirstOrDefault(l => l.Id == id);
    }

    public async Task<PaginatedResultDto<LogAuditoriaDto>> GetLogsAsync(FiltroAuditoriaDto filtro, CancellationToken cancellationToken = default)
    {
        var query = _auditLogs.AsQueryable();

        if (filtro.UsuarioId.HasValue)
            query = query.Where(l => l.UsuarioId == filtro.UsuarioId.Value);

        if (filtro.Acao.HasValue)
            query = query.Where(l => l.Acao == filtro.Acao.Value);

        if (!string.IsNullOrEmpty(filtro.Entidade))
            query = query.Where(l => l.Entidade == filtro.Entidade);

        if (filtro.EntidadeId.HasValue)
            query = query.Where(l => l.EntidadeId == filtro.EntidadeId.Value);

        if (filtro.DataInicio.HasValue)
            query = query.Where(l => l.DataAcao >= filtro.DataInicio.Value);

        if (filtro.DataFim.HasValue)
            query = query.Where(l => l.DataAcao <= filtro.DataFim.Value);

        if (filtro.ApenasSucesso == true)
            query = query.Where(l => l.Sucesso);

        if (filtro.ApenasFalhas == true)
            query = query.Where(l => !l.Sucesso);

        if (!string.IsNullOrEmpty(filtro.IpAddress))
            query = query.Where(l => l.IpAddress == filtro.IpAddress);

        var total = query.Count();
        var items = query
            .OrderByDescending(l => l.DataAcao)
            .Skip((filtro.Pagina - 1) * filtro.TamanhoPagina)
            .Take(filtro.TamanhoPagina)
            .ToList();

        return new PaginatedResultDto<LogAuditoriaDto>
        {
            Items = items,
            TotalItems = total,
            Pagina = filtro.Pagina,
            TamanhoPagina = filtro.TamanhoPagina,
            TotalPaginas = (int)Math.Ceiling((double)total / filtro.TamanhoPagina),
            TemProximaPagina = filtro.Pagina * filtro.TamanhoPagina < total,
            TemPaginaAnterior = filtro.Pagina > 1
        };
    }

    public async Task<IEnumerable<LogAuditoriaDto>> GetByUsuarioAsync(Guid usuarioId, int quantidade = 100, CancellationToken cancellationToken = default)
    {
        return _auditLogs
            .Where(l => l.UsuarioId == usuarioId)
            .OrderByDescending(l => l.DataAcao)
            .Take(quantidade)
            .ToList();
    }

    public async Task<IEnumerable<LogAuditoriaDto>> GetByEntidadeAsync(string entidade, Guid? entidadeId = null, int quantidade = 100, CancellationToken cancellationToken = default)
    {
        var query = _auditLogs.Where(l => l.Entidade == entidade);

        if (entidadeId.HasValue)
            query = query.Where(l => l.EntidadeId == entidadeId.Value);

        return query
            .OrderByDescending(l => l.DataAcao)
            .Take(quantidade)
            .ToList();
    }

    public async Task<IEnumerable<LogAuditoriaDto>> GetByPeriodoAsync(DateTime inicio, DateTime fim, CancellationToken cancellationToken = default)
    {
        return _auditLogs
            .Where(l => l.DataAcao >= inicio && l.DataAcao <= fim)
            .OrderByDescending(l => l.DataAcao)
            .ToList();
    }

    public async Task<AuditoriaResumoDto> GetResumoAsync(CancellationToken cancellationToken = default)
    {
        var hoje = DateTime.UtcNow.Date;
        var inicioSemana = hoje.AddDays(-(int)hoje.DayOfWeek);
        var inicioMes = new DateTime(hoje.Year, hoje.Month, 1);

        return new AuditoriaResumoDto
        {
            TotalAcoes = _auditLogs.Count,
            TotalHoje = _auditLogs.Count(l => l.DataAcao.Date == hoje),
            TotalSemana = _auditLogs.Count(l => l.DataAcao.Date >= inicioSemana),
            TotalMes = _auditLogs.Count(l => l.DataAcao.Date >= inicioMes),
            TotalFalhas = _auditLogs.Count(l => !l.Sucesso),
            AcoesPorTipo = _auditLogs
                .GroupBy(l => l.AcaoNome)
                .ToDictionary(g => g.Key, g => g.Count()),
            AcoesPorEntidade = _auditLogs
                .GroupBy(l => l.Entidade)
                .ToDictionary(g => g.Key, g => g.Count()),
            UltimasAcoes = _auditLogs
                .OrderByDescending(l => l.DataAcao)
                .Take(10)
                .ToList()
        };
    }

    public async Task<AuditoriaPorUsuarioDto> GetResumoUsuarioAsync(Guid usuarioId, CancellationToken cancellationToken = default)
    {
        var usuario = await _usuarioRepository.GetByIdAsync(usuarioId, cancellationToken);
        var logs = _auditLogs.Where(l => l.UsuarioId == usuarioId).ToList();

        return new AuditoriaPorUsuarioDto
        {
            UsuarioId = usuarioId,
            UsuarioNome = usuario?.Nome ?? "",
            UsuarioEmail = usuario?.Email ?? "",
            TotalAcoes = logs.Count,
            UltimaAcao = logs.OrderByDescending(l => l.DataAcao).FirstOrDefault()?.DataAcao,
            AcoesPorTipo = logs
                .GroupBy(l => l.AcaoNome)
                .ToDictionary(g => g.Key, g => g.Count()),
            UltimasAcoes = logs
                .OrderByDescending(l => l.DataAcao)
                .Take(10)
                .ToList()
        };
    }

    public async Task<AuditoriaPorEntidadeDto> GetResumoEntidadeAsync(string entidade, Guid? entidadeId = null, CancellationToken cancellationToken = default)
    {
        var logs = _auditLogs.Where(l => l.Entidade == entidade);

        if (entidadeId.HasValue)
            logs = logs.Where(l => l.EntidadeId == entidadeId.Value);

        var logsList = logs.ToList();

        return new AuditoriaPorEntidadeDto
        {
            Entidade = entidade,
            EntidadeId = entidadeId,
            EntidadeNome = logsList.FirstOrDefault()?.EntidadeNome,
            TotalAlteracoes = logsList.Count,
            UltimaAlteracao = logsList.OrderByDescending(l => l.DataAcao).FirstOrDefault()?.DataAcao,
            Historico = logsList
                .OrderByDescending(l => l.DataAcao)
                .Take(50)
                .ToList()
        };
    }

    public async Task<PaginatedResultDto<LogAcessoDto>> GetLogsAcessoAsync(FiltroLogAcessoDto filtro, CancellationToken cancellationToken = default)
    {
        var query = _logAcessoRepository.Query()
            .Include(l => l.Usuario)
            .AsQueryable();

        if (filtro.UsuarioId.HasValue)
            query = query.Where(l => l.UsuarioId == filtro.UsuarioId.Value);

        if (!string.IsNullOrEmpty(filtro.Acao))
            query = query.Where(l => l.Acao.Contains(filtro.Acao));

        if (!string.IsNullOrEmpty(filtro.Recurso))
            query = query.Where(l => l.Recurso != null && l.Recurso.Contains(filtro.Recurso));

        if (filtro.DataInicio.HasValue)
            query = query.Where(l => l.DataAcesso >= filtro.DataInicio.Value);

        if (filtro.DataFim.HasValue)
            query = query.Where(l => l.DataAcesso <= filtro.DataFim.Value);

        if (filtro.ApenasSucesso == true)
            query = query.Where(l => l.Sucesso);

        if (!string.IsNullOrEmpty(filtro.IpAddress))
            query = query.Where(l => l.IpAddress == filtro.IpAddress);

        var total = await query.CountAsync(cancellationToken);
        var items = await query
            .OrderByDescending(l => l.DataAcesso)
            .Skip((filtro.Pagina - 1) * filtro.TamanhoPagina)
            .Take(filtro.TamanhoPagina)
            .Select(l => new LogAcessoDto
            {
                Id = l.Id,
                UsuarioId = l.UsuarioId,
                UsuarioNome = l.Usuario.Nome,
                UsuarioEmail = l.Usuario.Email,
                Acao = l.Acao,
                Recurso = l.Recurso,
                Metodo = l.Metodo,
                Url = l.Url,
                IpAddress = l.IpAddress,
                UserAgent = l.UserAgent,
                DataAcesso = l.DataAcesso,
                Sucesso = l.Sucesso,
                Mensagem = l.Mensagem
            })
            .ToListAsync(cancellationToken);

        return new PaginatedResultDto<LogAcessoDto>
        {
            Items = items,
            TotalItems = total,
            Pagina = filtro.Pagina,
            TamanhoPagina = filtro.TamanhoPagina,
            TotalPaginas = (int)Math.Ceiling((double)total / filtro.TamanhoPagina),
            TemProximaPagina = filtro.Pagina * filtro.TamanhoPagina < total,
            TemPaginaAnterior = filtro.Pagina > 1
        };
    }

    public async Task<IEnumerable<LogAcessoDto>> GetUltimosAcessosUsuarioAsync(Guid usuarioId, int quantidade = 10, CancellationToken cancellationToken = default)
    {
        return await _logAcessoRepository.Query()
            .Include(l => l.Usuario)
            .Where(l => l.UsuarioId == usuarioId)
            .OrderByDescending(l => l.DataAcesso)
            .Take(quantidade)
            .Select(l => new LogAcessoDto
            {
                Id = l.Id,
                UsuarioId = l.UsuarioId,
                UsuarioNome = l.Usuario.Nome,
                UsuarioEmail = l.Usuario.Email,
                Acao = l.Acao,
                Recurso = l.Recurso,
                Metodo = l.Metodo,
                Url = l.Url,
                IpAddress = l.IpAddress,
                UserAgent = l.UserAgent,
                DataAcesso = l.DataAcesso,
                Sucesso = l.Sucesso,
                Mensagem = l.Mensagem
            })
            .ToListAsync(cancellationToken);
    }

    public async Task<byte[]> ExportarLogsAsync(ExportarAuditoriaDto dto, CancellationToken cancellationToken = default)
    {
        var result = await GetLogsAsync(dto.Filtro, cancellationToken);
        var content = new StringBuilder();

        switch (dto.Formato)
        {
            case FormatoExportacao.CSV:
                content.AppendLine("Data,Usuario,Acao,Entidade,Sucesso,IP");
                foreach (var log in result.Items)
                {
                    content.AppendLine($"{log.DataAcao:yyyy-MM-dd HH:mm:ss},{log.UsuarioNome},{log.AcaoNome},{log.Entidade},{log.Sucesso},{log.IpAddress}");
                }
                break;

            case FormatoExportacao.JSON:
                content.Append(JsonSerializer.Serialize(result.Items, new JsonSerializerOptions { WriteIndented = true }));
                break;

            default:
                content.AppendLine("Data,Usuario,Acao,Entidade,Sucesso,IP");
                foreach (var log in result.Items)
                {
                    content.AppendLine($"{log.DataAcao:yyyy-MM-dd HH:mm:ss},{log.UsuarioNome},{log.AcaoNome},{log.Entidade},{log.Sucesso},{log.IpAddress}");
                }
                break;
        }

        return Encoding.UTF8.GetBytes(content.ToString());
    }

    public async Task<Stream> ExportarLogsStreamAsync(ExportarAuditoriaDto dto, CancellationToken cancellationToken = default)
    {
        var bytes = await ExportarLogsAsync(dto, cancellationToken);
        return new MemoryStream(bytes);
    }

    public async Task LimparLogsAntigosAsync(int diasRetencao = 365, CancellationToken cancellationToken = default)
    {
        var dataLimite = DateTime.UtcNow.AddDays(-diasRetencao);

        // Clean in-memory logs
        _auditLogs.RemoveAll(l => l.DataAcao < dataLimite);

        // Clean database logs
        var logsAntigos = await _logAcessoRepository.Query()
            .Where(l => l.DataAcesso < dataLimite)
            .ToListAsync(cancellationToken);

        foreach (var log in logsAntigos)
        {
            await _logAcessoRepository.DeleteAsync(log, cancellationToken);
        }

        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Logs antigos limpos. Retencao: {DiasRetencao} dias", diasRetencao);
    }

    public async Task ArquivarLogsAsync(DateTime dataLimite, CancellationToken cancellationToken = default)
    {
        // In real implementation, move logs to archive storage
        _logger.LogInformation("Logs anteriores a {DataLimite} arquivados", dataLimite);
    }
}
