using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Relatorios;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Chapas;
using CAU.Eleitoral.Domain.Entities.Documentos;
using CAU.Eleitoral.Domain.Interfaces.Repositories;
using System.Text;
using System.Text.Json;
using StatusChapa = CAU.Eleitoral.Domain.Enums.StatusChapa;
using TipoMembroChapa = CAU.Eleitoral.Domain.Enums.TipoMembroChapa;

namespace CAU.Eleitoral.Application.Services;

public class RelatorioService : IRelatorioService
{
    private readonly IRepository<Eleicao> _eleicaoRepository;
    private readonly IRepository<ChapaEleicao> _chapaRepository;
    private readonly IRepository<Eleitor> _eleitorRepository;
    private readonly IRepository<Voto> _votoRepository;
    private readonly IRepository<ResultadoEleicao> _resultadoRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<RelatorioService> _logger;

    // In-memory storage for report metadata (in production, use a database)
    private static readonly Dictionary<Guid, RelatorioDto> _relatorios = new();

    public RelatorioService(
        IRepository<Eleicao> eleicaoRepository,
        IRepository<ChapaEleicao> chapaRepository,
        IRepository<Eleitor> eleitorRepository,
        IRepository<Voto> votoRepository,
        IRepository<ResultadoEleicao> resultadoRepository,
        IUnitOfWork unitOfWork,
        ILogger<RelatorioService> logger)
    {
        _eleicaoRepository = eleicaoRepository;
        _chapaRepository = chapaRepository;
        _eleitorRepository = eleitorRepository;
        _votoRepository = votoRepository;
        _resultadoRepository = resultadoRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    public async Task<RelatorioDto> GerarRelatorioAsync(GerarRelatorioDto dto, Guid solicitanteId, CancellationToken cancellationToken = default)
    {
        var relatorio = new RelatorioDto
        {
            Id = Guid.NewGuid(),
            Titulo = $"Relatorio {dto.Tipo}",
            Tipo = dto.Tipo,
            TipoNome = dto.Tipo.ToString(),
            Formato = dto.Formato,
            FormatoNome = dto.Formato.ToString(),
            Status = StatusRelatorio.Solicitado,
            StatusNome = StatusRelatorio.Solicitado.ToString(),
            EleicaoId = dto.EleicaoId,
            DataSolicitacao = DateTime.UtcNow,
            SolicitanteId = solicitanteId,
            SolicitanteNome = "",
            Parametros = dto.ParametrosAdicionais != null ? JsonSerializer.Serialize(dto.ParametrosAdicionais) : null
        };

        _relatorios[relatorio.Id] = relatorio;

        _logger.LogInformation("Relatorio solicitado: {RelatorioId} - Tipo: {Tipo}", relatorio.Id, dto.Tipo);

        // Process report asynchronously (in production, use a background job)
        _ = ProcessarRelatorioAsync(relatorio.Id, dto, cancellationToken);

        return relatorio;
    }

    private async Task ProcessarRelatorioAsync(Guid relatorioId, GerarRelatorioDto dto, CancellationToken cancellationToken)
    {
        try
        {
            if (_relatorios.TryGetValue(relatorioId, out var relatorio))
            {
                relatorio = relatorio with { Status = StatusRelatorio.EmProcessamento, StatusNome = StatusRelatorio.EmProcessamento.ToString() };
                _relatorios[relatorioId] = relatorio;

                // Simulate processing
                await Task.Delay(1000, cancellationToken);

                relatorio = relatorio with
                {
                    Status = StatusRelatorio.Concluido,
                    StatusNome = StatusRelatorio.Concluido.ToString(),
                    DataGeracao = DateTime.UtcNow,
                    ArquivoUrl = $"/relatorios/{relatorioId}.{dto.Formato.ToString().ToLower()}",
                    ArquivoNome = $"relatorio-{dto.Tipo.ToString().ToLower()}.{dto.Formato.ToString().ToLower()}",
                    ArquivoTamanho = 1024 * 50 // 50 KB
                };
                _relatorios[relatorioId] = relatorio;

                _logger.LogInformation("Relatorio processado: {RelatorioId}", relatorioId);
            }
        }
        catch (Exception ex)
        {
            if (_relatorios.TryGetValue(relatorioId, out var relatorio))
            {
                relatorio = relatorio with
                {
                    Status = StatusRelatorio.Erro,
                    StatusNome = StatusRelatorio.Erro.ToString(),
                    Erro = ex.Message
                };
                _relatorios[relatorioId] = relatorio;
            }

            _logger.LogError(ex, "Erro ao processar relatorio {RelatorioId}", relatorioId);
        }
    }

    public async Task<RelatorioDto?> GetRelatorioByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        _relatorios.TryGetValue(id, out var relatorio);
        return relatorio;
    }

    public async Task<IEnumerable<RelatorioDto>> GetRelatoriosAsync(FiltroRelatorioDto? filtro = null, CancellationToken cancellationToken = default)
    {
        var query = _relatorios.Values.AsQueryable();

        if (filtro != null)
        {
            if (filtro.Tipo.HasValue)
                query = query.Where(r => r.Tipo == filtro.Tipo.Value);

            if (filtro.Status.HasValue)
                query = query.Where(r => r.Status == filtro.Status.Value);

            if (filtro.EleicaoId.HasValue)
                query = query.Where(r => r.EleicaoId == filtro.EleicaoId.Value);

            if (filtro.SolicitanteId.HasValue)
                query = query.Where(r => r.SolicitanteId == filtro.SolicitanteId.Value);
        }

        return query.OrderByDescending(r => r.DataSolicitacao).ToList();
    }

    public async Task<IEnumerable<RelatorioDto>> GetRelatoriosBySolicitanteAsync(Guid solicitanteId, CancellationToken cancellationToken = default)
    {
        return _relatorios.Values
            .Where(r => r.SolicitanteId == solicitanteId)
            .OrderByDescending(r => r.DataSolicitacao)
            .ToList();
    }

    public async Task DeleteRelatorioAsync(Guid id, CancellationToken cancellationToken = default)
    {
        _relatorios.Remove(id);
        _logger.LogInformation("Relatorio excluido: {RelatorioId}", id);
    }

    public async Task<Stream> DownloadRelatorioAsync(Guid id, CancellationToken cancellationToken = default)
    {
        if (!_relatorios.TryGetValue(id, out var relatorio))
            throw new KeyNotFoundException($"Relatorio {id} nao encontrado");

        if (relatorio.Status != StatusRelatorio.Concluido)
            throw new InvalidOperationException("Relatorio ainda nao foi processado");

        return new MemoryStream(Encoding.UTF8.GetBytes($"Conteudo do relatorio {id}"));
    }

    public async Task<byte[]> DownloadRelatorioBytesAsync(Guid id, CancellationToken cancellationToken = default)
    {
        using var stream = await DownloadRelatorioAsync(id, cancellationToken);
        using var ms = new MemoryStream();
        await stream.CopyToAsync(ms, cancellationToken);
        return ms.ToArray();
    }

    public async Task<RelatorioResultadoEleicaoDto> GerarRelatorioResultadoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {eleicaoId} nao encontrada");

        var resultado = await _resultadoRepository.Query()
            .Include(r => r.VotosChapas)
            .ThenInclude(vc => vc.Chapa)
            .ThenInclude(c => c!.Membros)
            .ThenInclude(m => m.Profissional)
            .FirstOrDefaultAsync(r => r.EleicaoId == eleicaoId && r.Final, cancellationToken);

        var resultadosChapas = resultado?.VotosChapas?
            .OrderBy(vc => vc.Posicao)
            .Select(vc => new ResultadoChapaRelatorioDto
            {
                Numero = int.TryParse(vc.Chapa?.Numero, out var num) ? num : 0,
                Nome = vc.Chapa?.Nome ?? "",
                TotalVotos = vc.TotalVotos,
                Percentual = vc.Percentual,
                Posicao = vc.Posicao
            }).ToList() ?? new List<ResultadoChapaRelatorioDto>();

        var vencedora = resultado?.VotosChapas?.FirstOrDefault(vc => vc.Eleita);
        ChapaVencedoraDto? chapaVencedora = null;

        if (vencedora != null)
        {
            var presidente = vencedora.Chapa?.Membros?.FirstOrDefault(m => m.Tipo == TipoMembroChapa.Presidente);
            chapaVencedora = new ChapaVencedoraDto
            {
                ChapaId = vencedora.ChapaId,
                Numero = int.TryParse(vencedora.Chapa?.Numero, out var num) ? num : 0,
                Nome = vencedora.Chapa?.Nome ?? "",
                TotalVotos = vencedora.TotalVotos,
                Percentual = vencedora.Percentual,
                PresidenteNome = presidente?.Profissional?.Nome ?? ""
            };
        }

        return new RelatorioResultadoEleicaoDto
        {
            EleicaoId = eleicaoId,
            EleicaoNome = eleicao.Nome,
            DataEleicao = eleicao.DataVotacaoInicio ?? eleicao.DataInicio,
            TotalEleitores = resultado?.TotalEleitoresAptos ?? 0,
            TotalVotantes = resultado?.TotalVotantes ?? 0,
            PercentualComparecimento = resultado?.PercentualComparecimento ?? 0,
            VotosValidos = resultado?.TotalVotosValidos ?? 0,
            VotosBrancos = resultado?.TotalVotosBrancos ?? 0,
            VotosNulos = resultado?.TotalVotosNulos ?? 0,
            ResultadosChapas = resultadosChapas,
            ChapaVencedora = chapaVencedora
        };
    }

    public async Task<RelatorioEstatisticasDto> GerarRelatorioEstatisticasAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {eleicaoId} nao encontrada");

        var totalEleitores = await _eleitorRepository.CountAsync(e => e.EleicaoId == eleicaoId && e.Apto, cancellationToken);
        var totalVotantes = await _eleitorRepository.CountAsync(e => e.EleicaoId == eleicaoId && e.Votou, cancellationToken);
        var totalChapas = await _chapaRepository.CountAsync(c => c.EleicaoId == eleicaoId && c.Status == StatusChapa.Deferida, cancellationToken);

        return new RelatorioEstatisticasDto
        {
            EleicaoId = eleicaoId,
            EleicaoNome = eleicao.Nome,
            EstatisticasGerais = new EstatisticasGeraisDto
            {
                TotalEleitores = totalEleitores,
                TotalVotantes = totalVotantes,
                TotalAbstencoes = totalEleitores - totalVotantes,
                TotalChapas = totalChapas,
                TotalDenuncias = 0,
                TotalImpugnacoes = 0
            }
        };
    }

    public async Task<byte[]> GerarRelatorioListaEleitoresAsync(Guid eleicaoId, FormatoRelatorio formato, CancellationToken cancellationToken = default)
    {
        var eleitores = await _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .Where(e => e.EleicaoId == eleicaoId)
            .OrderBy(e => e.Profissional!.Nome)
            .ToListAsync(cancellationToken);

        var content = new StringBuilder();
        content.AppendLine("Nome,Registro CAU,Apto,Votou");

        foreach (var eleitor in eleitores)
        {
            content.AppendLine($"{eleitor.Profissional?.Nome},{eleitor.Profissional?.RegistroCAU},{eleitor.Apto},{eleitor.Votou}");
        }

        _logger.LogInformation("Relatorio lista eleitores gerado para eleicao {EleicaoId}", eleicaoId);

        return Encoding.UTF8.GetBytes(content.ToString());
    }

    public async Task<byte[]> GerarRelatorioListaChapasAsync(Guid eleicaoId, FormatoRelatorio formato, CancellationToken cancellationToken = default)
    {
        var chapas = await _chapaRepository.Query()
            .Include(c => c.Membros)
            .ThenInclude(m => m.Profissional)
            .Where(c => c.EleicaoId == eleicaoId)
            .OrderBy(c => c.Numero)
            .ToListAsync(cancellationToken);

        var content = new StringBuilder();
        content.AppendLine("Numero,Nome,Sigla,Status,Total Membros");

        foreach (var chapa in chapas)
        {
            content.AppendLine($"{chapa.Numero},{chapa.Nome},{chapa.Sigla},{chapa.Status},{chapa.Membros?.Count ?? 0}");
        }

        _logger.LogInformation("Relatorio lista chapas gerado para eleicao {EleicaoId}", eleicaoId);

        return Encoding.UTF8.GetBytes(content.ToString());
    }

    public async Task<byte[]> GerarRelatorioAtaApuracaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var resultado = await GerarRelatorioResultadoAsync(eleicaoId, cancellationToken);

        var content = new StringBuilder();
        content.AppendLine($"ATA DE APURACAO - {resultado.EleicaoNome}");
        content.AppendLine($"Data: {resultado.DataEleicao:dd/MM/yyyy}");
        content.AppendLine();
        content.AppendLine($"Total de Eleitores: {resultado.TotalEleitores}");
        content.AppendLine($"Total de Votantes: {resultado.TotalVotantes}");
        content.AppendLine($"Percentual de Comparecimento: {resultado.PercentualComparecimento}%");
        content.AppendLine();
        content.AppendLine("RESULTADO:");

        foreach (var chapa in resultado.ResultadosChapas)
        {
            content.AppendLine($"{chapa.Posicao}. {chapa.Nome} - {chapa.TotalVotos} votos ({chapa.Percentual}%)");
        }

        if (resultado.ChapaVencedora != null)
        {
            content.AppendLine();
            content.AppendLine($"CHAPA VENCEDORA: {resultado.ChapaVencedora.Nome}");
        }

        return Encoding.UTF8.GetBytes(content.ToString());
    }

    public async Task<byte[]> GerarRelatorioBoletimUrnaAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        return await GerarRelatorioAtaApuracaoAsync(eleicaoId, cancellationToken);
    }

    public async Task<byte[]> GerarRelatorioDenunciasAsync(Guid eleicaoId, FormatoRelatorio formato, CancellationToken cancellationToken = default)
    {
        var content = "Protocolo,Tipo,Status,Data\n";
        return Encoding.UTF8.GetBytes(content);
    }

    public async Task<byte[]> GerarRelatorioImpugnacoesAsync(Guid eleicaoId, FormatoRelatorio formato, CancellationToken cancellationToken = default)
    {
        var content = "Protocolo,Tipo,Status,Data\n";
        return Encoding.UTF8.GetBytes(content);
    }

    public async Task<byte[]> GerarRelatorioAuditoriaAsync(DateTime dataInicio, DateTime dataFim, FormatoRelatorio formato, CancellationToken cancellationToken = default)
    {
        var content = "Data,Usuario,Acao,Entidade,Sucesso\n";
        return Encoding.UTF8.GetBytes(content);
    }

    public async Task<byte[]> GerarRelatorioAcessosAsync(DateTime dataInicio, DateTime dataFim, FormatoRelatorio formato, CancellationToken cancellationToken = default)
    {
        var content = "Data,Usuario,Acao,IP,Sucesso\n";
        return Encoding.UTF8.GetBytes(content);
    }

    public async Task<byte[]> GerarRelatorioCalendarioAsync(Guid eleicaoId, FormatoRelatorio formato, CancellationToken cancellationToken = default)
    {
        var content = "Evento,Data Inicio,Data Fim,Status\n";
        return Encoding.UTF8.GetBytes(content);
    }

    public async Task<byte[]> GerarRelatorioComprovantesAsync(Guid eleicaoId, FormatoRelatorio formato, CancellationToken cancellationToken = default)
    {
        var votos = await _votoRepository.Query()
            .Where(v => v.EleicaoId == eleicaoId)
            .OrderBy(v => v.DataVoto)
            .ToListAsync(cancellationToken);

        var content = new StringBuilder();
        content.AppendLine("Comprovante,Hash,Data");

        foreach (var voto in votos)
        {
            content.AppendLine($"{voto.Comprovante},{voto.HashVoto},{voto.DataVoto:dd/MM/yyyy HH:mm:ss}");
        }

        return Encoding.UTF8.GetBytes(content.ToString());
    }

    public async Task<StatusRelatorio> GetStatusRelatorioAsync(Guid id, CancellationToken cancellationToken = default)
    {
        if (_relatorios.TryGetValue(id, out var relatorio))
            return relatorio.Status;

        throw new KeyNotFoundException($"Relatorio {id} nao encontrado");
    }

    public async Task<IEnumerable<RelatorioDto>> GetRelatoriosEmProcessamentoAsync(CancellationToken cancellationToken = default)
    {
        return _relatorios.Values
            .Where(r => r.Status == StatusRelatorio.EmProcessamento)
            .ToList();
    }

    public async Task CancelarRelatorioAsync(Guid id, CancellationToken cancellationToken = default)
    {
        if (_relatorios.TryGetValue(id, out var relatorio))
        {
            if (relatorio.Status == StatusRelatorio.EmProcessamento)
            {
                _relatorios.Remove(id);
                _logger.LogInformation("Relatorio {RelatorioId} cancelado", id);
            }
        }
    }

    public async Task LimparRelatoriosExpiradosAsync(CancellationToken cancellationToken = default)
    {
        var expirados = _relatorios.Values
            .Where(r => r.DataSolicitacao < DateTime.UtcNow.AddDays(-7))
            .Select(r => r.Id)
            .ToList();

        foreach (var id in expirados)
        {
            _relatorios.Remove(id);
        }

        _logger.LogInformation("{Count} relatorios expirados removidos", expirados.Count);
    }
}
