using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Apuracao;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Chapas;
using CAU.Eleitoral.Domain.Entities.Documentos;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

public class ApuracaoService : IApuracaoService
{
    private readonly IRepository<Voto> _votoRepository;
    private readonly IRepository<Eleitor> _eleitorRepository;
    private readonly IRepository<Eleicao> _eleicaoRepository;
    private readonly IRepository<ChapaEleicao> _chapaRepository;
    private readonly IRepository<ResultadoEleicao> _resultadoRepository;
    private readonly IRepository<VotoChapa> _votoChapaRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<ApuracaoService> _logger;

    public ApuracaoService(
        IRepository<Voto> votoRepository,
        IRepository<Eleitor> eleitorRepository,
        IRepository<Eleicao> eleicaoRepository,
        IRepository<ChapaEleicao> chapaRepository,
        IRepository<ResultadoEleicao> resultadoRepository,
        IRepository<VotoChapa> votoChapaRepository,
        IUnitOfWork unitOfWork,
        ILogger<ApuracaoService> logger)
    {
        _votoRepository = votoRepository;
        _eleitorRepository = eleitorRepository;
        _eleicaoRepository = eleicaoRepository;
        _chapaRepository = chapaRepository;
        _resultadoRepository = resultadoRepository;
        _votoChapaRepository = votoChapaRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    public async Task<ApuracaoResumoDto> IniciarApuracaoAsync(IniciarApuracaoDto dto, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(dto.EleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {dto.EleicaoId} nao encontrada");

        eleicao.Status = StatusEleicao.ApuracaoEmAndamento;
        eleicao.FaseAtual = FaseEleicao.Apuracao;
        eleicao.DataApuracao = DateTime.UtcNow;

        await _eleicaoRepository.UpdateAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Apuracao iniciada para eleicao {EleicaoId}", dto.EleicaoId);

        return await GetResumoApuracaoAsync(dto.EleicaoId, cancellationToken);
    }

    public async Task<ApuracaoResumoDto> GetResumoApuracaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {eleicaoId} nao encontrada");

        var totalVotos = await _votoRepository.CountAsync(v => v.EleicaoId == eleicaoId, cancellationToken);
        var votosContados = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Status == StatusVoto.Confirmado, cancellationToken);

        var chapas = await _chapaRepository.Query()
            .Where(c => c.EleicaoId == eleicaoId && c.Status == StatusChapa.Deferida)
            .ToListAsync(cancellationToken);

        var resultadosParciais = new List<ApuracaoParcialChapaDto>();
        var totalVotosValidos = 0;

        foreach (var chapa in chapas)
        {
            var votosChapa = await _votoRepository.CountAsync(
                v => v.EleicaoId == eleicaoId && v.ChapaId == chapa.Id && v.Tipo == TipoVoto.Chapa, cancellationToken);
            totalVotosValidos += votosChapa;

            resultadosParciais.Add(new ApuracaoParcialChapaDto
            {
                ChapaId = chapa.Id,
                ChapaNome = chapa.Nome,
                ChapaNumero = int.TryParse(chapa.Numero, out var num) ? num : 0,
                TotalVotos = votosChapa,
                Percentual = 0,
                Posicao = 0
            });
        }

        // Calculate percentages and positions
        var ordenados = resultadosParciais.OrderByDescending(r => r.TotalVotos).ToList();
        for (var i = 0; i < ordenados.Count; i++)
        {
            ordenados[i] = ordenados[i] with
            {
                Percentual = totalVotosValidos > 0 ? Math.Round((double)ordenados[i].TotalVotos / totalVotosValidos * 100, 2) : 0,
                Posicao = i + 1
            };
        }

        var progresso = totalVotos > 0 ? Math.Round((double)votosContados / totalVotos * 100, 2) : 0;

        return new ApuracaoResumoDto
        {
            EleicaoId = eleicaoId,
            EleicaoNome = eleicao.Nome,
            StatusEleicao = eleicao.Status,
            ApuracaoIniciada = eleicao.Status == StatusEleicao.ApuracaoEmAndamento || eleicao.Status == StatusEleicao.Finalizada,
            ApuracaoFinalizada = eleicao.Status == StatusEleicao.Finalizada,
            TotalVotosContados = votosContados,
            TotalVotosPendentes = totalVotos - votosContados,
            ProgressoApuracao = progresso,
            DataInicioApuracao = eleicao.DataApuracao,
            DataFimApuracao = eleicao.Status == StatusEleicao.Finalizada ? DateTime.UtcNow : null,
            ResultadosParciais = ordenados
        };
    }

    public async Task<ResultadoEleicaoDto> FinalizarApuracaoAsync(FinalizarApuracaoDto dto, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(dto.EleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {dto.EleicaoId} nao encontrada");

        // Calculate final results
        var totalEleitoresAptos = await _eleitorRepository.CountAsync(
            e => e.EleicaoId == dto.EleicaoId && e.Apto, cancellationToken);
        var totalVotantes = await _eleitorRepository.CountAsync(
            e => e.EleicaoId == dto.EleicaoId && e.Votou, cancellationToken);
        var totalAbstencoes = totalEleitoresAptos - totalVotantes;

        var totalVotosValidos = await _votoRepository.CountAsync(
            v => v.EleicaoId == dto.EleicaoId && v.Tipo == TipoVoto.Chapa, cancellationToken);
        var totalVotosBrancos = await _votoRepository.CountAsync(
            v => v.EleicaoId == dto.EleicaoId && v.Tipo == TipoVoto.Branco, cancellationToken);
        var totalVotosNulos = await _votoRepository.CountAsync(
            v => v.EleicaoId == dto.EleicaoId && v.Tipo == TipoVoto.Nulo, cancellationToken);
        var totalVotosAnulados = await _votoRepository.CountAsync(
            v => v.EleicaoId == dto.EleicaoId && v.Tipo == TipoVoto.Anulado, cancellationToken);

        var percentualComparecimento = totalEleitoresAptos > 0
            ? Math.Round((double)totalVotantes / totalEleitoresAptos * 100, 2)
            : 0;

        // Create result
        var resultado = new ResultadoEleicao
        {
            EleicaoId = dto.EleicaoId,
            Parcial = false,
            Final = true,
            TotalEleitoresAptos = totalEleitoresAptos,
            TotalVotantes = totalVotantes,
            TotalAbstencoes = totalAbstencoes,
            TotalVotosValidos = totalVotosValidos,
            TotalVotosBrancos = totalVotosBrancos,
            TotalVotosNulos = totalVotosNulos,
            TotalVotosAnulados = totalVotosAnulados,
            PercentualComparecimento = percentualComparecimento,
            PercentualAbstencao = 100 - percentualComparecimento,
            DataApuracao = DateTime.UtcNow,
            Publicado = false
        };

        await _resultadoRepository.AddAsync(resultado, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        // Calculate votes per chapa
        var chapas = await _chapaRepository.Query()
            .Where(c => c.EleicaoId == dto.EleicaoId && c.Status == StatusChapa.Deferida)
            .ToListAsync(cancellationToken);

        var votosChapas = new List<VotoChapa>();
        var posicao = 1;

        foreach (var chapa in chapas.OrderByDescending(c => _votoRepository.CountAsync(
            v => v.EleicaoId == dto.EleicaoId && v.ChapaId == c.Id && v.Tipo == TipoVoto.Chapa, cancellationToken).Result))
        {
            var totalVotosChapa = await _votoRepository.CountAsync(
                v => v.EleicaoId == dto.EleicaoId && v.ChapaId == chapa.Id && v.Tipo == TipoVoto.Chapa, cancellationToken);

            var votoChapa = new VotoChapa
            {
                ResultadoId = resultado.Id,
                ChapaId = chapa.Id,
                TotalVotos = totalVotosChapa,
                Percentual = totalVotosValidos > 0 ? Math.Round((double)totalVotosChapa / totalVotosValidos * 100, 2) : 0,
                Posicao = posicao,
                Eleita = posicao == 1
            };

            votosChapas.Add(votoChapa);
            await _votoChapaRepository.AddAsync(votoChapa, cancellationToken);
            posicao++;
        }

        // Update election status
        eleicao.Status = StatusEleicao.Finalizada;
        eleicao.FaseAtual = FaseEleicao.Resultado;

        await _eleicaoRepository.UpdateAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Apuracao finalizada para eleicao {EleicaoId}. Resultado: {ResultadoId}", dto.EleicaoId, resultado.Id);

        return await GetResultadoByIdAsync(resultado.Id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar resultado");
    }

    public async Task<ResultadoEleicaoDto> ReprocessarApuracaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        // Delete existing result
        var resultadoExistente = await _resultadoRepository.FirstOrDefaultAsync(
            r => r.EleicaoId == eleicaoId && r.Final, cancellationToken);

        if (resultadoExistente != null)
        {
            await _resultadoRepository.DeleteAsync(resultadoExistente, cancellationToken);
            await _unitOfWork.SaveChangesAsync(cancellationToken);
        }

        // Reprocess
        return await FinalizarApuracaoAsync(new FinalizarApuracaoDto { EleicaoId = eleicaoId }, cancellationToken);
    }

    public async Task<ResultadoEleicaoDto?> GetResultadoByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var resultado = await _resultadoRepository.Query()
            .Include(r => r.Eleicao)
            .Include(r => r.VotosChapas)
            .ThenInclude(vc => vc.Chapa)
            .FirstOrDefaultAsync(r => r.Id == id, cancellationToken);

        return resultado == null ? null : MapResultadoToDto(resultado);
    }

    public async Task<ResultadoEleicaoDto?> GetResultadoByEleicaoAsync(Guid eleicaoId, bool final = true, CancellationToken cancellationToken = default)
    {
        var resultado = await _resultadoRepository.Query()
            .Include(r => r.Eleicao)
            .Include(r => r.VotosChapas)
            .ThenInclude(vc => vc.Chapa)
            .FirstOrDefaultAsync(r => r.EleicaoId == eleicaoId && r.Final == final, cancellationToken);

        return resultado == null ? null : MapResultadoToDto(resultado);
    }

    public async Task<IEnumerable<ResultadoEleicaoDto>> GetResultadosParciaisAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var resultados = await _resultadoRepository.Query()
            .Include(r => r.Eleicao)
            .Include(r => r.VotosChapas)
            .ThenInclude(vc => vc.Chapa)
            .Where(r => r.EleicaoId == eleicaoId && r.Parcial)
            .OrderByDescending(r => r.DataApuracao)
            .ToListAsync(cancellationToken);

        return resultados.Select(MapResultadoToDto);
    }

    public async Task<ResultadoEleicaoDto> PublicarResultadoAsync(PublicarResultadoDto dto, CancellationToken cancellationToken = default)
    {
        var resultado = await _resultadoRepository.GetByIdAsync(dto.ResultadoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Resultado {dto.ResultadoId} nao encontrado");

        resultado.Publicado = true;
        resultado.DataPublicacao = dto.DataPublicacao ?? DateTime.UtcNow;

        await _resultadoRepository.UpdateAsync(resultado, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Resultado {ResultadoId} publicado", dto.ResultadoId);

        return await GetResultadoByIdAsync(dto.ResultadoId, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar resultado");
    }

    public async Task DespublicarResultadoAsync(Guid resultadoId, CancellationToken cancellationToken = default)
    {
        var resultado = await _resultadoRepository.GetByIdAsync(resultadoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Resultado {resultadoId} nao encontrado");

        resultado.Publicado = false;
        resultado.DataPublicacao = null;

        await _resultadoRepository.UpdateAsync(resultado, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Resultado {ResultadoId} despublicado", resultadoId);
    }

    public async Task<IEnumerable<VotoChapaDto>> GetVotosPorChapaAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var resultado = await _resultadoRepository.Query()
            .Include(r => r.VotosChapas)
            .ThenInclude(vc => vc.Chapa)
            .FirstOrDefaultAsync(r => r.EleicaoId == eleicaoId && r.Final, cancellationToken);

        if (resultado == null)
            return Enumerable.Empty<VotoChapaDto>();

        return resultado.VotosChapas.Select(MapVotoChapaToDto).OrderBy(v => v.Posicao);
    }

    public async Task<VotoChapaDto?> GetChapaVencedoraAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var votosChapas = await GetVotosPorChapaAsync(eleicaoId, cancellationToken);
        return votosChapas.FirstOrDefault(v => v.Eleita);
    }

    public async Task<IEnumerable<VotoChapaDto>> GetClassificacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        return await GetVotosPorChapaAsync(eleicaoId, cancellationToken);
    }

    public async Task<VotoChapaDto> AnularVotoAsync(AnularVotoDto dto, CancellationToken cancellationToken = default)
    {
        var voto = await _votoRepository.GetByIdAsync(dto.VotoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Voto {dto.VotoId} nao encontrado");

        voto.Tipo = TipoVoto.Anulado;
        voto.Status = StatusVoto.Anulado;

        await _votoRepository.UpdateAsync(voto, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Voto {VotoId} anulado por {AutorId}. Motivo: {Motivo}", dto.VotoId, dto.AutorId, dto.Motivo);

        return new VotoChapaDto
        {
            Id = voto.Id,
            ChapaId = voto.ChapaId ?? Guid.Empty,
            TotalVotos = 0,
            Percentual = 0,
            Posicao = 0,
            Eleita = false
        };
    }

    public async Task<VotoChapaDto> ReanalisarVotoAsync(ReanalizarVotoDto dto, CancellationToken cancellationToken = default)
    {
        var voto = await _votoRepository.GetByIdAsync(dto.VotoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Voto {dto.VotoId} nao encontrado");

        voto.Status = StatusVoto.Confirmado;

        await _votoRepository.UpdateAsync(voto, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Voto {VotoId} reanalisado. Motivo: {Motivo}", dto.VotoId, dto.Motivo);

        return new VotoChapaDto
        {
            Id = voto.Id,
            ChapaId = voto.ChapaId ?? Guid.Empty,
            TotalVotos = 0,
            Percentual = 0,
            Posicao = 0,
            Eleita = false
        };
    }

    public async Task<IEnumerable<VotoChapaDto>> GetVotosAnuladosAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var votos = await _votoRepository.Query()
            .Include(v => v.Chapa)
            .Where(v => v.EleicaoId == eleicaoId && v.Tipo == TipoVoto.Anulado)
            .ToListAsync(cancellationToken);

        return votos.Select(v => new VotoChapaDto
        {
            Id = v.Id,
            ChapaId = v.ChapaId ?? Guid.Empty,
            ChapaNome = v.Chapa?.Nome ?? "N/A",
            ChapaNumero = int.TryParse(v.Chapa?.Numero, out var num) ? num : 0,
            TotalVotos = 1,
            Percentual = 0,
            Posicao = 0,
            Eleita = false
        });
    }

    public async Task<AtaApuracaoDto> GerarAtaApuracaoAsync(GerarAtaApuracaoDto dto, CancellationToken cancellationToken = default)
    {
        var resultado = await _resultadoRepository.Query()
            .Include(r => r.Eleicao)
            .Include(r => r.VotosChapas)
            .ThenInclude(vc => vc.Chapa)
            .FirstOrDefaultAsync(r => r.Id == dto.ResultadoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Resultado {dto.ResultadoId} nao encontrado");

        var conteudo = $"ATA DE APURACAO\n\n";
        conteudo += $"Eleicao: {resultado.Eleicao?.Nome}\n";
        conteudo += $"Data da Apuracao: {resultado.DataApuracao:dd/MM/yyyy HH:mm}\n";
        conteudo += $"Local: {dto.Local}\n\n";
        conteudo += $"RESULTADOS:\n";
        conteudo += $"Total de Eleitores Aptos: {resultado.TotalEleitoresAptos}\n";
        conteudo += $"Total de Votantes: {resultado.TotalVotantes}\n";
        conteudo += $"Percentual de Comparecimento: {resultado.PercentualComparecimento}%\n";
        conteudo += $"Votos Validos: {resultado.TotalVotosValidos}\n";
        conteudo += $"Votos em Branco: {resultado.TotalVotosBrancos}\n";
        conteudo += $"Votos Nulos: {resultado.TotalVotosNulos}\n\n";
        conteudo += $"VOTOS POR CHAPA:\n";

        foreach (var votoChapa in resultado.VotosChapas.OrderBy(vc => vc.Posicao))
        {
            conteudo += $"{votoChapa.Posicao}. {votoChapa.Chapa?.Nome} - {votoChapa.TotalVotos} votos ({votoChapa.Percentual}%)\n";
        }

        resultado.AtaApuracaoUrl = $"/documentos/atas/apuracao-{resultado.EleicaoId}.pdf";

        await _resultadoRepository.UpdateAsync(resultado, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Ata de apuracao gerada para resultado {ResultadoId}", dto.ResultadoId);

        return new AtaApuracaoDto
        {
            Id = Guid.NewGuid(),
            ResultadoId = resultado.Id,
            EleicaoId = resultado.EleicaoId,
            EleicaoNome = resultado.Eleicao?.Nome ?? "",
            DataApuracao = resultado.DataApuracao,
            Local = dto.Local,
            Conteudo = conteudo,
            ArquivoUrl = resultado.AtaApuracaoUrl,
            CreatedAt = DateTime.UtcNow
        };
    }

    public async Task<AtaApuracaoDto?> GetAtaApuracaoAsync(Guid resultadoId, CancellationToken cancellationToken = default)
    {
        var resultado = await _resultadoRepository.Query()
            .Include(r => r.Eleicao)
            .FirstOrDefaultAsync(r => r.Id == resultadoId, cancellationToken);

        if (resultado == null || string.IsNullOrEmpty(resultado.AtaApuracaoUrl))
            return null;

        return new AtaApuracaoDto
        {
            ResultadoId = resultado.Id,
            EleicaoId = resultado.EleicaoId,
            EleicaoNome = resultado.Eleicao?.Nome ?? "",
            DataApuracao = resultado.DataApuracao,
            ArquivoUrl = resultado.AtaApuracaoUrl,
            CreatedAt = resultado.CreatedAt
        };
    }

    public async Task<AtaApuracaoDto> AssinarAtaAsync(Guid ataId, Guid membroId, CancellationToken cancellationToken = default)
    {
        _logger.LogInformation("Ata {AtaId} assinada por membro {MembroId}", ataId, membroId);
        return new AtaApuracaoDto { Id = ataId };
    }

    public async Task<string> ExportarAtaAsync(Guid ataId, string formato, CancellationToken cancellationToken = default)
    {
        return $"/exportacao/ata-{ataId}.{formato.ToLower()}";
    }

    public async Task<ApuracaoResumoDto> GetEstatisticasApuracaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        return await GetResumoApuracaoAsync(eleicaoId, cancellationToken);
    }

    public async Task<bool> ApuracaoFinalizadaAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken);
        return eleicao?.Status == StatusEleicao.Finalizada;
    }

    public async Task<double> GetProgressoApuracaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var resumo = await GetResumoApuracaoAsync(eleicaoId, cancellationToken);
        return resumo.ProgressoApuracao;
    }

    private static ResultadoEleicaoDto MapResultadoToDto(ResultadoEleicao resultado)
    {
        return new ResultadoEleicaoDto
        {
            Id = resultado.Id,
            EleicaoId = resultado.EleicaoId,
            EleicaoNome = resultado.Eleicao?.Nome ?? "",
            Parcial = resultado.Parcial,
            Final = resultado.Final,
            TotalEleitoresAptos = resultado.TotalEleitoresAptos,
            TotalVotantes = resultado.TotalVotantes,
            TotalAbstencoes = resultado.TotalAbstencoes,
            TotalVotosValidos = resultado.TotalVotosValidos,
            TotalVotosBrancos = resultado.TotalVotosBrancos,
            TotalVotosNulos = resultado.TotalVotosNulos,
            TotalVotosAnulados = resultado.TotalVotosAnulados,
            PercentualComparecimento = resultado.PercentualComparecimento,
            PercentualAbstencao = resultado.PercentualAbstencao,
            DataApuracao = resultado.DataApuracao,
            DataPublicacao = resultado.DataPublicacao,
            Publicado = resultado.Publicado,
            ArquivoUrl = resultado.ArquivoUrl,
            AtaApuracaoUrl = resultado.AtaApuracaoUrl,
            VotosChapas = resultado.VotosChapas?.Select(MapVotoChapaToDto).OrderBy(v => v.Posicao).ToList() ?? new List<VotoChapaDto>()
        };
    }

    private static VotoChapaDto MapVotoChapaToDto(VotoChapa votoChapa)
    {
        return new VotoChapaDto
        {
            Id = votoChapa.Id,
            ResultadoId = votoChapa.ResultadoId,
            ChapaId = votoChapa.ChapaId,
            ChapaNome = votoChapa.Chapa?.Nome ?? "",
            ChapaNumero = int.TryParse(votoChapa.Chapa?.Numero, out var num) ? num : 0,
            TotalVotos = votoChapa.TotalVotos,
            Percentual = votoChapa.Percentual,
            Posicao = votoChapa.Posicao,
            Eleita = votoChapa.Eleita
        };
    }
}
