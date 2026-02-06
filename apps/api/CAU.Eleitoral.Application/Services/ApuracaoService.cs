using System.Security.Cryptography;
using System.Text;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Apuracao;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Chapas;
using CAU.Eleitoral.Domain.Entities.Usuarios;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

/// <summary>
/// Service responsible for vote tallying and election results management
/// </summary>
public class ApuracaoService : IApuracaoService
{
    private readonly IRepository<Voto> _votoRepository;
    private readonly IRepository<Eleitor> _eleitorRepository;
    private readonly IRepository<Eleicao> _eleicaoRepository;
    private readonly IRepository<ChapaEleicao> _chapaRepository;
    private readonly IRepository<MembroChapa> _membroChapaRepository;
    private readonly IRepository<ApuracaoResultado> _apuracaoRepository;
    private readonly IRepository<ApuracaoResultadoChapa> _apuracaoChapaRepository;
    private readonly IRepository<Usuario> _usuarioRepository;
    private readonly IRepository<Profissional> _profissionalRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<ApuracaoService> _logger;

    public ApuracaoService(
        IRepository<Voto> votoRepository,
        IRepository<Eleitor> eleitorRepository,
        IRepository<Eleicao> eleicaoRepository,
        IRepository<ChapaEleicao> chapaRepository,
        IRepository<MembroChapa> membroChapaRepository,
        IRepository<ApuracaoResultado> apuracaoRepository,
        IRepository<ApuracaoResultadoChapa> apuracaoChapaRepository,
        IRepository<Usuario> usuarioRepository,
        IRepository<Profissional> profissionalRepository,
        IUnitOfWork unitOfWork,
        ILogger<ApuracaoService> logger)
    {
        _votoRepository = votoRepository;
        _eleitorRepository = eleitorRepository;
        _eleicaoRepository = eleicaoRepository;
        _chapaRepository = chapaRepository;
        _membroChapaRepository = membroChapaRepository;
        _apuracaoRepository = apuracaoRepository;
        _apuracaoChapaRepository = apuracaoChapaRepository;
        _usuarioRepository = usuarioRepository;
        _profissionalRepository = profissionalRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    #region Main Vote Counting

    public async Task<ResultadoApuracaoDto> ApurarVotosAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        _logger.LogInformation("Iniciando apuracao de votos para eleicao {EleicaoId}", eleicaoId);

        var eleicao = await GetEleicaoAsync(eleicaoId, cancellationToken);

        // Get or create apuracao result
        var apuracao = await _apuracaoRepository.Query()
            .Include(a => a.VotosPorChapa)
            .FirstOrDefaultAsync(a => a.EleicaoId == eleicaoId && !a.Parcial, cancellationToken);

        if (apuracao == null)
        {
            apuracao = new ApuracaoResultado
            {
                EleicaoId = eleicaoId,
                Status = StatusApuracao.EmAndamento,
                DataInicio = DateTime.UtcNow,
                Parcial = false
            };
            await _apuracaoRepository.AddAsync(apuracao, cancellationToken);
            await _unitOfWork.SaveChangesAsync(cancellationToken);
        }

        // Calculate totals
        var totalEleitoresAptos = await _eleitorRepository.CountAsync(
            e => e.EleicaoId == eleicaoId && e.Apto, cancellationToken);
        var totalVotantes = await _eleitorRepository.CountAsync(
            e => e.EleicaoId == eleicaoId && e.Votou, cancellationToken);

        var totalVotosValidos = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Tipo == TipoVoto.Chapa && v.Status == StatusVoto.Confirmado, cancellationToken);
        var totalVotosBrancos = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Tipo == TipoVoto.Branco && v.Status == StatusVoto.Confirmado, cancellationToken);
        var totalVotosNulos = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Tipo == TipoVoto.Nulo && v.Status == StatusVoto.Confirmado, cancellationToken);
        var totalVotosAnulados = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Status == StatusVoto.Anulado, cancellationToken);

        var totalVotos = totalVotosValidos + totalVotosBrancos + totalVotosNulos;
        var totalAbstencoes = totalEleitoresAptos - totalVotantes;

        // Update apuracao
        apuracao.TotalEleitoresAptos = totalEleitoresAptos;
        apuracao.TotalVotantes = totalVotantes;
        apuracao.TotalAbstencoes = totalAbstencoes;
        apuracao.TotalVotosValidos = totalVotosValidos;
        apuracao.TotalVotosBrancos = totalVotosBrancos;
        apuracao.TotalVotosNulos = totalVotosNulos;
        apuracao.TotalVotosAnulados = totalVotosAnulados;
        apuracao.PercentualParticipacao = totalEleitoresAptos > 0
            ? Math.Round((decimal)totalVotantes / totalEleitoresAptos * 100, 2) : 0;
        apuracao.PercentualAbstencao = 100 - apuracao.PercentualParticipacao;

        // Calculate votes per chapa
        var chapas = await _chapaRepository.Query()
            .Where(c => c.EleicaoId == eleicaoId && c.Status == StatusChapa.Deferida)
            .ToListAsync(cancellationToken);

        // Clear existing vote results
        var existingVotosChapa = await _apuracaoChapaRepository.Query()
            .Where(vc => vc.ApuracaoId == apuracao.Id)
            .ToListAsync(cancellationToken);
        foreach (var vc in existingVotosChapa)
        {
            await _apuracaoChapaRepository.DeleteAsync(vc, cancellationToken);
        }
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        var votosChapas = new List<ApuracaoResultadoChapa>();

        foreach (var chapa in chapas)
        {
            var votosChapa = await _votoRepository.CountAsync(
                v => v.EleicaoId == eleicaoId && v.ChapaId == chapa.Id &&
                     v.Tipo == TipoVoto.Chapa && v.Status == StatusVoto.Confirmado, cancellationToken);

            var votoChapa = new ApuracaoResultadoChapa
            {
                ApuracaoId = apuracao.Id,
                ChapaId = chapa.Id,
                TotalVotos = votosChapa,
                PercentualVotos = totalVotos > 0 ? Math.Round((decimal)votosChapa / totalVotos * 100, 2) : 0,
                PercentualVotosValidos = totalVotosValidos > 0 ? Math.Round((decimal)votosChapa / totalVotosValidos * 100, 2) : 0
            };

            votosChapas.Add(votoChapa);
            await _apuracaoChapaRepository.AddAsync(votoChapa, cancellationToken);
        }

        // Sort by votes and assign positions
        var ordenados = votosChapas.OrderByDescending(v => v.TotalVotos).ToList();
        for (var i = 0; i < ordenados.Count; i++)
        {
            ordenados[i].Posicao = i + 1;
            ordenados[i].Eleita = i == 0;
            await _apuracaoChapaRepository.UpdateAsync(ordenados[i], cancellationToken);
        }

        // Set winner
        if (ordenados.Any())
        {
            var vencedor = ordenados.First();
            apuracao.ChapaVencedoraId = vencedor.ChapaId;
            apuracao.VotosChapaVencedora = vencedor.TotalVotos;
        }

        // Generate integrity hash
        apuracao.HashApuracao = GenerateIntegrityHash(apuracao, votosChapas);

        await _apuracaoRepository.UpdateAsync(apuracao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Apuracao de votos concluida para eleicao {EleicaoId}. Total votos: {TotalVotos}",
            eleicaoId, totalVotos);

        return await GetResultadoAsync(eleicaoId, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar resultado da apuracao");
    }

    public async Task<ResultadoApuracaoDto?> GetResultadoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var apuracao = await _apuracaoRepository.Query()
            .Include(a => a.Eleicao)
            .Include(a => a.ChapaVencedora)
            .Include(a => a.VotosPorChapa)
                .ThenInclude(vc => vc.Chapa)
            .FirstOrDefaultAsync(a => a.EleicaoId == eleicaoId && !a.Parcial, cancellationToken);

        if (apuracao == null)
            return null;

        return MapToResultadoApuracaoDto(apuracao);
    }

    public async Task<ResultadoParcialDto> GetResultadoParcialAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await GetEleicaoAsync(eleicaoId, cancellationToken);

        // Check if partial results are allowed (could be a configuration)
        var permiteVisualizacao = eleicao.Status == StatusEleicao.ApuracaoEmAndamento ||
                                   eleicao.Status == StatusEleicao.Finalizada;

        var totalVotos = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Status == StatusVoto.Confirmado, cancellationToken);

        var votosApurados = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Status == StatusVoto.Confirmado, cancellationToken);

        var chapas = await _chapaRepository.Query()
            .Where(c => c.EleicaoId == eleicaoId && c.Status == StatusChapa.Deferida)
            .ToListAsync(cancellationToken);

        var resultadosChapas = new List<ResultadoChapaDto>();
        var totalVotosValidos = 0;

        foreach (var chapa in chapas)
        {
            var votosChapa = await _votoRepository.CountAsync(
                v => v.EleicaoId == eleicaoId && v.ChapaId == chapa.Id &&
                     v.Tipo == TipoVoto.Chapa && v.Status == StatusVoto.Confirmado, cancellationToken);
            totalVotosValidos += votosChapa;

            resultadosChapas.Add(new ResultadoChapaDto
            {
                ChapaId = chapa.Id,
                Numero = int.TryParse(chapa.Numero, out var num) ? num : 0,
                Nome = chapa.Nome,
                Sigla = chapa.Sigla,
                LogoUrl = chapa.LogoUrl,
                TotalVotos = votosChapa,
                Percentual = 0,
                PercentualVotosValidos = 0,
                Posicao = 0,
                Vencedora = false
            });
        }

        // Calculate percentages and positions
        var ordenados = resultadosChapas.OrderByDescending(r => r.TotalVotos).ToList();
        for (var i = 0; i < ordenados.Count; i++)
        {
            var r = ordenados[i];
            ordenados[i] = r with
            {
                Percentual = totalVotos > 0 ? Math.Round((decimal)r.TotalVotos / totalVotos * 100, 2) : 0,
                PercentualVotosValidos = totalVotosValidos > 0 ? Math.Round((decimal)r.TotalVotos / totalVotosValidos * 100, 2) : 0,
                Posicao = i + 1,
                Vencedora = i == 0
            };
        }

        return new ResultadoParcialDto
        {
            EleicaoId = eleicaoId,
            EleicaoNome = eleicao.Nome,
            PermiteVisualizacao = permiteVisualizacao,
            VotosApurados = votosApurados,
            TotalVotos = totalVotos,
            PercentualApurado = totalVotos > 0 ? Math.Round((decimal)votosApurados / totalVotos * 100, 2) : 0,
            UltimaAtualizacao = DateTime.UtcNow,
            ResultadosChapas = ordenados
        };
    }

    public async Task<ResultadoFinalDto> GetResultadoFinalAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var apuracao = await _apuracaoRepository.Query()
            .Include(a => a.Eleicao)
            .Include(a => a.ChapaVencedora)
                .ThenInclude(c => c!.Membros)
                    .ThenInclude(m => m.Profissional)
            .Include(a => a.VotosPorChapa)
                .ThenInclude(vc => vc.Chapa)
            .FirstOrDefaultAsync(a => a.EleicaoId == eleicaoId && !a.Parcial, cancellationToken);

        if (apuracao == null)
            throw new KeyNotFoundException($"Resultado final nao encontrado para eleicao {eleicaoId}");

        var eleitos = await GetEleitosAsync(eleicaoId, cancellationToken);
        var chapaVencedora = apuracao.ChapaVencedora != null
            ? await DeterminarVencedorAsync(eleicaoId, cancellationToken)
            : null;

        return new ResultadoFinalDto
        {
            Id = apuracao.Id,
            EleicaoId = apuracao.EleicaoId,
            EleicaoNome = apuracao.Eleicao?.Nome ?? "",
            TipoEleicao = apuracao.Eleicao?.Tipo ?? TipoEleicao.Ordinaria,
            Homologado = apuracao.Homologada,
            Publicado = apuracao.Status == StatusApuracao.Homologada,
            Contestado = apuracao.Status == StatusApuracao.Contestada,
            DataApuracao = apuracao.DataInicio ?? DateTime.UtcNow,
            DataHomologacao = apuracao.DataHomologacao,
            DataPublicacao = apuracao.Homologada ? apuracao.DataHomologacao : null,
            TotalEleitoresAptos = apuracao.TotalEleitoresAptos,
            TotalVotantes = apuracao.TotalVotantes,
            TotalAbstencoes = apuracao.TotalAbstencoes,
            TotalVotosValidos = apuracao.TotalVotosValidos,
            TotalVotosBrancos = apuracao.TotalVotosBrancos,
            TotalVotosNulos = apuracao.TotalVotosNulos,
            TotalVotosAnulados = apuracao.TotalVotosAnulados,
            PercentualComparecimento = apuracao.PercentualParticipacao,
            PercentualAbstencao = apuracao.PercentualAbstencao,
            ResultadosChapas = apuracao.VotosPorChapa.Select(MapToResultadoChapaDto).OrderBy(r => r.Posicao).ToList(),
            ChapaVencedora = chapaVencedora,
            Eleitos = eleitos.ToList(),
            HashIntegridade = apuracao.HashApuracao,
            AssinaturaDigital = apuracao.AssinaturaDigital
        };
    }

    #endregion

    #region Apuracao Lifecycle

    public async Task<StatusApuracaoDto> IniciarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default)
    {
        var eleicao = await GetEleicaoAsync(eleicaoId, cancellationToken);
        var usuario = await _usuarioRepository.GetByIdAsync(userId, cancellationToken);

        if (eleicao.Status != StatusEleicao.Encerrada && eleicao.Status != StatusEleicao.ApuracaoEmAndamento)
        {
            throw new InvalidOperationException("A eleicao precisa estar encerrada para iniciar a apuracao");
        }

        // Get or create apuracao
        var apuracao = await _apuracaoRepository.Query()
            .FirstOrDefaultAsync(a => a.EleicaoId == eleicaoId && !a.Parcial, cancellationToken);

        if (apuracao == null)
        {
            apuracao = new ApuracaoResultado
            {
                EleicaoId = eleicaoId,
                Status = StatusApuracao.EmAndamento,
                DataInicio = DateTime.UtcNow,
                Parcial = false
            };
            await _apuracaoRepository.AddAsync(apuracao, cancellationToken);
        }
        else if (apuracao.Status == StatusApuracao.Concluida || apuracao.Status == StatusApuracao.Homologada)
        {
            throw new InvalidOperationException("A apuracao ja foi concluida ou homologada");
        }
        else
        {
            apuracao.Status = StatusApuracao.EmAndamento;
            apuracao.DataInicio = DateTime.UtcNow;
            await _apuracaoRepository.UpdateAsync(apuracao, cancellationToken);
        }

        // Update election status
        eleicao.Status = StatusEleicao.ApuracaoEmAndamento;
        eleicao.FaseAtual = FaseEleicao.Apuracao;
        eleicao.DataApuracao = DateTime.UtcNow;
        await _eleicaoRepository.UpdateAsync(eleicao, cancellationToken);

        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Apuracao iniciada para eleicao {EleicaoId} por usuario {UserId}", eleicaoId, userId);

        return await GetStatusAsync(eleicaoId, cancellationToken);
    }

    public async Task<StatusApuracaoDto> PausarAsync(Guid eleicaoId, string motivo, Guid userId, CancellationToken cancellationToken = default)
    {
        var apuracao = await GetApuracaoAsync(eleicaoId, cancellationToken);

        if (apuracao.Status != StatusApuracao.EmAndamento)
        {
            throw new InvalidOperationException("A apuracao precisa estar em andamento para ser pausada");
        }

        apuracao.Status = StatusApuracao.Pausada;
        apuracao.Observacao = motivo;

        await _apuracaoRepository.UpdateAsync(apuracao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Apuracao pausada para eleicao {EleicaoId}. Motivo: {Motivo}", eleicaoId, motivo);

        return await GetStatusAsync(eleicaoId, cancellationToken);
    }

    public async Task<StatusApuracaoDto> RetomarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default)
    {
        var apuracao = await GetApuracaoAsync(eleicaoId, cancellationToken);

        if (apuracao.Status != StatusApuracao.Pausada)
        {
            throw new InvalidOperationException("A apuracao precisa estar pausada para ser retomada");
        }

        apuracao.Status = StatusApuracao.EmAndamento;
        apuracao.Observacao = null;

        await _apuracaoRepository.UpdateAsync(apuracao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Apuracao retomada para eleicao {EleicaoId}", eleicaoId);

        return await GetStatusAsync(eleicaoId, cancellationToken);
    }

    public async Task<ResultadoApuracaoDto> FinalizarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default)
    {
        // First, ensure votes are counted
        await ApurarVotosAsync(eleicaoId, cancellationToken);

        var apuracao = await GetApuracaoAsync(eleicaoId, cancellationToken);
        var eleicao = await GetEleicaoAsync(eleicaoId, cancellationToken);

        apuracao.Status = StatusApuracao.Concluida;
        apuracao.DataFim = DateTime.UtcNow;

        eleicao.Status = StatusEleicao.Finalizada;
        eleicao.FaseAtual = FaseEleicao.Resultado;

        await _apuracaoRepository.UpdateAsync(apuracao, cancellationToken);
        await _eleicaoRepository.UpdateAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Apuracao finalizada para eleicao {EleicaoId}", eleicaoId);

        return await GetResultadoAsync(eleicaoId, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar resultado");
    }

    public async Task<ResultadoApuracaoDto> HomologarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default)
    {
        var apuracao = await GetApuracaoAsync(eleicaoId, cancellationToken);
        var usuario = await _usuarioRepository.GetByIdAsync(userId, cancellationToken);

        if (apuracao.Status != StatusApuracao.Concluida)
        {
            throw new InvalidOperationException("A apuracao precisa estar concluida para ser homologada");
        }

        apuracao.Homologada = true;
        apuracao.DataHomologacao = DateTime.UtcNow;
        apuracao.HomologadoPor = usuario?.Nome ?? "Sistema";
        apuracao.Status = StatusApuracao.Homologada;

        await _apuracaoRepository.UpdateAsync(apuracao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Resultado homologado para eleicao {EleicaoId} por {Usuario}",
            eleicaoId, usuario?.Nome ?? "Sistema");

        return await GetResultadoAsync(eleicaoId, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar resultado");
    }

    public async Task<ResultadoApuracaoDto> PublicarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default)
    {
        var apuracao = await GetApuracaoAsync(eleicaoId, cancellationToken);

        if (!apuracao.Homologada)
        {
            throw new InvalidOperationException("O resultado precisa estar homologado para ser publicado");
        }

        // In this implementation, homologated = published
        // Could add additional publication status if needed

        _logger.LogInformation("Resultado publicado para eleicao {EleicaoId}", eleicaoId);

        return await GetResultadoAsync(eleicaoId, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar resultado");
    }

    #endregion

    #region Status and Monitoring

    public async Task<StatusApuracaoDto> GetStatusAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await GetEleicaoAsync(eleicaoId, cancellationToken);
        var apuracao = await _apuracaoRepository.Query()
            .FirstOrDefaultAsync(a => a.EleicaoId == eleicaoId && !a.Parcial, cancellationToken);

        var totalVotos = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Status == StatusVoto.Confirmado, cancellationToken);

        var votosApurados = apuracao != null
            ? apuracao.TotalVotosValidos + apuracao.TotalVotosBrancos + apuracao.TotalVotosNulos
            : 0;

        return new StatusApuracaoDto
        {
            EleicaoId = eleicaoId,
            Status = apuracao?.Status ?? StatusApuracao.NaoIniciada,
            DataInicio = apuracao?.DataInicio,
            DataFim = apuracao?.DataFim,
            DataPausa = apuracao?.Status == StatusApuracao.Pausada ? DateTime.UtcNow : null,
            MotivoPausa = apuracao?.Observacao,
            VotosApurados = votosApurados,
            TotalVotos = totalVotos,
            PercentualApurado = totalVotos > 0 ? Math.Round((decimal)votosApurados / totalVotos * 100, 2) : 0,
            Mensagem = GetStatusMessage(apuracao?.Status ?? StatusApuracao.NaoIniciada)
        };
    }

    public async Task<StatusApuracaoDto> ReprocessarAsync(Guid eleicaoId, Guid userId, CancellationToken cancellationToken = default)
    {
        var apuracao = await GetApuracaoAsync(eleicaoId, cancellationToken);

        if (apuracao.Homologada)
        {
            throw new InvalidOperationException("Nao e possivel reprocessar uma apuracao homologada");
        }

        // Reset status
        apuracao.Status = StatusApuracao.EmAndamento;
        apuracao.DataInicio = DateTime.UtcNow;
        apuracao.DataFim = null;

        await _apuracaoRepository.UpdateAsync(apuracao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        // Recount votes
        await ApurarVotosAsync(eleicaoId, cancellationToken);

        _logger.LogInformation("Apuracao reprocessada para eleicao {EleicaoId}", eleicaoId);

        return await GetStatusAsync(eleicaoId, cancellationToken);
    }

    #endregion

    #region Winner Determination

    public async Task<ChapaVencedoraDto> DeterminarVencedorAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var apuracao = await _apuracaoRepository.Query()
            .Include(a => a.VotosPorChapa)
                .ThenInclude(vc => vc.Chapa)
                    .ThenInclude(c => c.Membros)
                        .ThenInclude(m => m.Profissional)
            .FirstOrDefaultAsync(a => a.EleicaoId == eleicaoId && !a.Parcial, cancellationToken);

        if (apuracao == null)
            throw new KeyNotFoundException($"Apuracao nao encontrada para eleicao {eleicaoId}");

        var ordenados = apuracao.VotosPorChapa.OrderByDescending(v => v.TotalVotos).ToList();

        if (!ordenados.Any())
            throw new InvalidOperationException("Nenhum voto encontrado para determinar vencedor");

        var primeiro = ordenados.First();
        var segundo = ordenados.Skip(1).FirstOrDefault();
        var chapa = primeiro.Chapa;

        var totalVotos = apuracao.TotalVotosValidos + apuracao.TotalVotosBrancos + apuracao.TotalVotosNulos;
        var diferencaParaSegundo = segundo != null ? primeiro.TotalVotos - segundo.TotalVotos : primeiro.TotalVotos;

        // Check if won by majority (more than 50% of valid votes)
        var venceuPorMaioria = apuracao.TotalVotosValidos > 0 &&
            (decimal)primeiro.TotalVotos / apuracao.TotalVotosValidos > 0.5m;

        return new ChapaVencedoraDto
        {
            ChapaId = chapa.Id,
            Numero = int.TryParse(chapa.Numero, out var num) ? num : 0,
            Nome = chapa.Nome,
            Sigla = chapa.Sigla,
            LogoUrl = chapa.LogoUrl,
            TotalVotos = primeiro.TotalVotos,
            PercentualVotos = primeiro.PercentualVotos,
            PercentualVotosValidos = primeiro.PercentualVotosValidos,
            DiferencaParaSegundo = diferencaParaSegundo,
            PercentualDiferenca = totalVotos > 0 ? Math.Round((decimal)diferencaParaSegundo / totalVotos * 100, 2) : 0,
            VenceuPorMaioria = venceuPorMaioria,
            SegundoTurnoNecessario = false, // Could implement second round logic here
            Membros = chapa.Membros?.Select(m => new MembroChapaVencedoraDto
            {
                Id = m.Id,
                Nome = m.Profissional?.Nome ?? m.Nome,
                Cargo = m.Cargo ?? m.Tipo.ToString(),
                Titular = m.Titular || m.Tipo <= TipoMembroChapa.SegundoTesoureiro,
                Ordem = m.Ordem
            }).OrderBy(m => m.Ordem).ToList() ?? new List<MembroChapaVencedoraDto>()
        };
    }

    #endregion

    #region Statistics

    public async Task<EstatisticasVotacaoDto> GetEstatisticasAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await GetEleicaoAsync(eleicaoId, cancellationToken);

        var totalEleitoresAptos = await _eleitorRepository.CountAsync(
            e => e.EleicaoId == eleicaoId && e.Apto, cancellationToken);
        var totalVotantes = await _eleitorRepository.CountAsync(
            e => e.EleicaoId == eleicaoId && e.Votou, cancellationToken);
        var totalAbstencoes = totalEleitoresAptos - totalVotantes;

        var totalVotosValidos = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Tipo == TipoVoto.Chapa && v.Status == StatusVoto.Confirmado, cancellationToken);
        var totalVotosBrancos = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Tipo == TipoVoto.Branco && v.Status == StatusVoto.Confirmado, cancellationToken);
        var totalVotosNulos = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Tipo == TipoVoto.Nulo && v.Status == StatusVoto.Confirmado, cancellationToken);
        var totalVotosAnulados = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Status == StatusVoto.Anulado, cancellationToken);

        var totalVotos = totalVotosValidos + totalVotosBrancos + totalVotosNulos;

        var votosOnline = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Modo == ModoVotacao.Online && v.Status == StatusVoto.Confirmado, cancellationToken);
        var votosPresenciais = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Modo == ModoVotacao.Presencial && v.Status == StatusVoto.Confirmado, cancellationToken);

        var votosPorChapa = await GetVotosPorChapaAsync(eleicaoId, cancellationToken);
        var votosPorRegiao = await GetVotosPorRegiaoAsync(eleicaoId, cancellationToken);

        return new EstatisticasVotacaoDto
        {
            EleicaoId = eleicaoId,
            EleicaoNome = eleicao.Nome,
            DataGeracao = DateTime.UtcNow,
            TotalEleitoresAptos = totalEleitoresAptos,
            TotalVotantes = totalVotantes,
            TotalAbstencoes = totalAbstencoes,
            TaxaParticipacao = totalEleitoresAptos > 0 ? Math.Round((decimal)totalVotantes / totalEleitoresAptos * 100, 2) : 0,
            TaxaAbstencao = totalEleitoresAptos > 0 ? Math.Round((decimal)totalAbstencoes / totalEleitoresAptos * 100, 2) : 0,
            TotalVotosValidos = totalVotosValidos,
            TotalVotosBrancos = totalVotosBrancos,
            TotalVotosNulos = totalVotosNulos,
            TotalVotosAnulados = totalVotosAnulados,
            PercentualVotosValidos = totalVotos > 0 ? Math.Round((decimal)totalVotosValidos / totalVotos * 100, 2) : 0,
            PercentualVotosBrancos = totalVotos > 0 ? Math.Round((decimal)totalVotosBrancos / totalVotos * 100, 2) : 0,
            PercentualVotosNulos = totalVotos > 0 ? Math.Round((decimal)totalVotosNulos / totalVotos * 100, 2) : 0,
            VotosOnline = votosOnline,
            VotosPresenciais = votosPresenciais,
            PercentualVotosOnline = totalVotos > 0 ? Math.Round((decimal)votosOnline / totalVotos * 100, 2) : 0,
            PercentualVotosPresenciais = totalVotos > 0 ? Math.Round((decimal)votosPresenciais / totalVotos * 100, 2) : 0,
            VotosPorChapa = votosPorChapa.ToList(),
            VotosPorRegiao = votosPorRegiao.ToList()
        };
    }

    public async Task<IEnumerable<VotosPorRegiaoDto>> GetVotosPorRegiaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        // Get unique UFs from eleitores
        var eleitores = await _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .Where(e => e.EleicaoId == eleicaoId)
            .ToListAsync(cancellationToken);

        var ufs = eleitores
            .Where(e => e.Profissional?.UF != null)
            .Select(e => e.Profissional!.UF!)
            .Distinct()
            .ToList();

        var resultado = new List<VotosPorRegiaoDto>();

        foreach (var uf in ufs)
        {
            var eleitoresUf = eleitores.Where(e => e.Profissional?.UF == uf).ToList();
            var profissionalIds = eleitoresUf.Select(e => e.ProfissionalId).ToList();

            // Get votes from this UF (by matching eleitor's profissional)
            var votosUf = await _votoRepository.Query()
                .Include(v => v.Chapa)
                .Where(v => v.EleicaoId == eleicaoId && v.Status == StatusVoto.Confirmado)
                .ToListAsync(cancellationToken);

            // Since votes are anonymous, we'll estimate based on eleitores
            var totalEleitores = eleitoresUf.Count(e => e.Apto);
            var totalVotantes = eleitoresUf.Count(e => e.Votou);

            resultado.Add(new VotosPorRegiaoDto
            {
                UF = uf,
                NomeRegiao = GetNomeRegiao(uf),
                TotalEleitores = totalEleitores,
                TotalVotantes = totalVotantes,
                TotalAbstencoes = totalEleitores - totalVotantes,
                TaxaParticipacao = totalEleitores > 0 ? Math.Round((decimal)totalVotantes / totalEleitores * 100, 2) : 0
            });
        }

        return resultado.OrderByDescending(r => r.TotalVotantes);
    }

    public async Task<IEnumerable<VotosPorChapaDto>> GetVotosPorChapaAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var chapas = await _chapaRepository.Query()
            .Where(c => c.EleicaoId == eleicaoId && c.Status == StatusChapa.Deferida)
            .ToListAsync(cancellationToken);

        var totalVotosValidos = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Tipo == TipoVoto.Chapa && v.Status == StatusVoto.Confirmado, cancellationToken);

        var resultado = new List<VotosPorChapaDto>();

        foreach (var chapa in chapas)
        {
            var votosChapa = await _votoRepository.CountAsync(
                v => v.EleicaoId == eleicaoId && v.ChapaId == chapa.Id &&
                     v.Tipo == TipoVoto.Chapa && v.Status == StatusVoto.Confirmado, cancellationToken);

            resultado.Add(new VotosPorChapaDto
            {
                ChapaId = chapa.Id,
                Numero = int.TryParse(chapa.Numero, out var num) ? num : 0,
                Nome = chapa.Nome,
                Sigla = chapa.Sigla,
                TotalVotos = votosChapa,
                Percentual = totalVotosValidos > 0 ? Math.Round((decimal)votosChapa / totalVotosValidos * 100, 2) : 0,
                PercentualVotosValidos = totalVotosValidos > 0 ? Math.Round((decimal)votosChapa / totalVotosValidos * 100, 2) : 0,
                CorGrafico = chapa.CorPrimaria ?? GetDefaultColor(resultado.Count)
            });
        }

        // Sort and assign positions
        var ordenados = resultado.OrderByDescending(r => r.TotalVotos).ToList();
        for (var i = 0; i < ordenados.Count; i++)
        {
            ordenados[i] = ordenados[i] with { Posicao = i + 1 };
        }

        return ordenados;
    }

    #endregion

    #region Documents

    public async Task<AtaApuracaoDto> GetAtaAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var apuracao = await _apuracaoRepository.Query()
            .Include(a => a.Eleicao)
            .Include(a => a.VotosPorChapa)
                .ThenInclude(vc => vc.Chapa)
            .FirstOrDefaultAsync(a => a.EleicaoId == eleicaoId && !a.Parcial, cancellationToken);

        if (apuracao == null)
            throw new KeyNotFoundException($"Apuracao nao encontrada para eleicao {eleicaoId}");

        var resultado = MapToResultadoApuracaoDto(apuracao);

        var conteudo = GenerateAtaContent(apuracao, resultado);

        return new AtaApuracaoDto
        {
            Id = apuracao.Id,
            EleicaoId = eleicaoId,
            EleicaoNome = apuracao.Eleicao?.Nome ?? "",
            DataApuracao = apuracao.DataInicio ?? DateTime.UtcNow,
            Numero = $"ATA-{eleicaoId.ToString()[..8].ToUpper()}-{DateTime.UtcNow:yyyyMMdd}",
            Conteudo = conteudo,
            Resultado = resultado,
            Assinada = apuracao.Homologada,
            DataAssinatura = apuracao.DataHomologacao
        };
    }

    public async Task<BoletimUrnaDto> GetBoletimUrnaAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var apuracao = await _apuracaoRepository.Query()
            .Include(a => a.Eleicao)
            .Include(a => a.VotosPorChapa)
                .ThenInclude(vc => vc.Chapa)
            .FirstOrDefaultAsync(a => a.EleicaoId == eleicaoId && !a.Parcial, cancellationToken);

        if (apuracao == null)
            throw new KeyNotFoundException($"Apuracao nao encontrada para eleicao {eleicaoId}");

        var totalVotos = apuracao.TotalVotosValidos + apuracao.TotalVotosBrancos + apuracao.TotalVotosNulos;

        return new BoletimUrnaDto
        {
            Id = apuracao.Id,
            EleicaoId = eleicaoId,
            EleicaoNome = apuracao.Eleicao?.Nome ?? "",
            DataEmissao = DateTime.UtcNow,
            HashIntegridade = apuracao.HashApuracao ?? GenerateIntegrityHash(apuracao, apuracao.VotosPorChapa.ToList()),
            TotalVotos = totalVotos,
            VotosValidos = apuracao.TotalVotosValidos,
            VotosBrancos = apuracao.TotalVotosBrancos,
            VotosNulos = apuracao.TotalVotosNulos,
            ResultadosChapas = apuracao.VotosPorChapa.Select(MapToResultadoChapaDto).OrderBy(r => r.Posicao).ToList()
        };
    }

    #endregion

    #region Elected Officials

    public async Task<IEnumerable<EleitoDto>> GetEleitosAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var apuracao = await _apuracaoRepository.Query()
            .Include(a => a.ChapaVencedora)
                .ThenInclude(c => c!.Membros)
                    .ThenInclude(m => m.Profissional)
            .FirstOrDefaultAsync(a => a.EleicaoId == eleicaoId && !a.Parcial && a.ChapaVencedoraId != null, cancellationToken);

        if (apuracao?.ChapaVencedora == null)
            return Enumerable.Empty<EleitoDto>();

        var chapa = apuracao.ChapaVencedora;

        return chapa.Membros
            .Where(m => m.Status == StatusMembroChapa.Confirmado)
            .Select(m => new EleitoDto
            {
                Id = m.Id,
                ChapaId = chapa.Id,
                ChapaNome = chapa.Nome,
                ChapaNumero = int.TryParse(chapa.Numero, out var num) ? num : 0,
                ProfissionalId = m.ProfissionalId.GetValueOrDefault(),
                Nome = m.Profissional?.Nome ?? "",
                RegistroCAU = m.Profissional?.RegistroCAU,
                Cargo = m.Cargo ?? m.Tipo.ToString(),
                Titular = m.Titular || m.Tipo <= TipoMembroChapa.SegundoTesoureiro,
                Ordem = m.Ordem
            })
            .OrderBy(e => e.Ordem);
    }

    #endregion

    #region Private Helper Methods

    private async Task<Eleicao> GetEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken)
    {
        return await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {eleicaoId} nao encontrada");
    }

    private async Task<ApuracaoResultado> GetApuracaoAsync(Guid eleicaoId, CancellationToken cancellationToken)
    {
        return await _apuracaoRepository.Query()
            .FirstOrDefaultAsync(a => a.EleicaoId == eleicaoId && !a.Parcial, cancellationToken)
            ?? throw new KeyNotFoundException($"Apuracao nao encontrada para eleicao {eleicaoId}");
    }

    private ResultadoApuracaoDto MapToResultadoApuracaoDto(ApuracaoResultado apuracao)
    {
        var totalVotos = apuracao.TotalVotosValidos + apuracao.TotalVotosBrancos + apuracao.TotalVotosNulos;

        return new ResultadoApuracaoDto
        {
            Id = apuracao.Id,
            EleicaoId = apuracao.EleicaoId,
            EleicaoNome = apuracao.Eleicao?.Nome ?? "",
            StatusApuracao = apuracao.Status,
            Homologado = apuracao.Homologada,
            Publicado = apuracao.Homologada,
            DataApuracao = apuracao.DataInicio,
            DataHomologacao = apuracao.DataHomologacao,
            DataPublicacao = apuracao.Homologada ? apuracao.DataHomologacao : null,
            TotalEleitores = apuracao.TotalEleitoresAptos,
            TotalVotos = totalVotos,
            VotosValidos = apuracao.TotalVotosValidos,
            VotosBrancos = apuracao.TotalVotosBrancos,
            VotosNulos = apuracao.TotalVotosNulos,
            VotosAnulados = apuracao.TotalVotosAnulados,
            TotalAbstencoes = apuracao.TotalAbstencoes,
            PercentualParticipacao = apuracao.PercentualParticipacao,
            PercentualAbstencao = apuracao.PercentualAbstencao,
            PercentualVotosValidos = totalVotos > 0 ? Math.Round((decimal)apuracao.TotalVotosValidos / totalVotos * 100, 2) : 0,
            PercentualVotosBrancos = totalVotos > 0 ? Math.Round((decimal)apuracao.TotalVotosBrancos / totalVotos * 100, 2) : 0,
            PercentualVotosNulos = totalVotos > 0 ? Math.Round((decimal)apuracao.TotalVotosNulos / totalVotos * 100, 2) : 0,
            ResultadosChapas = apuracao.VotosPorChapa?.Select(MapToResultadoChapaDto).OrderBy(r => r.Posicao).ToList() ?? new List<ResultadoChapaDto>(),
            ChapaVencedoraId = apuracao.ChapaVencedoraId,
            ChapaVencedoraNome = apuracao.ChapaVencedora?.Nome,
            VotosChapaVencedora = apuracao.VotosChapaVencedora,
            HashIntegridade = apuracao.HashApuracao
        };
    }

    private static ResultadoChapaDto MapToResultadoChapaDto(ApuracaoResultadoChapa votoChapa)
    {
        return new ResultadoChapaDto
        {
            ChapaId = votoChapa.ChapaId,
            Numero = int.TryParse(votoChapa.Chapa?.Numero, out var num) ? num : 0,
            Nome = votoChapa.Chapa?.Nome ?? "",
            Sigla = votoChapa.Chapa?.Sigla,
            LogoUrl = votoChapa.Chapa?.LogoUrl,
            TotalVotos = votoChapa.TotalVotos,
            Percentual = votoChapa.PercentualVotos,
            PercentualVotosValidos = votoChapa.PercentualVotosValidos,
            Posicao = votoChapa.Posicao,
            Vencedora = votoChapa.Eleita
        };
    }

    private static string GenerateIntegrityHash(ApuracaoResultado apuracao, List<ApuracaoResultadoChapa> votosChapas)
    {
        var sb = new StringBuilder();
        sb.Append(apuracao.EleicaoId);
        sb.Append(apuracao.TotalVotosValidos);
        sb.Append(apuracao.TotalVotosBrancos);
        sb.Append(apuracao.TotalVotosNulos);

        foreach (var vc in votosChapas.OrderBy(v => v.ChapaId))
        {
            sb.Append(vc.ChapaId);
            sb.Append(vc.TotalVotos);
        }

        using var sha256 = SHA256.Create();
        var hashBytes = sha256.ComputeHash(Encoding.UTF8.GetBytes(sb.ToString()));
        return Convert.ToHexString(hashBytes);
    }

    private static string GetStatusMessage(StatusApuracao status)
    {
        return status switch
        {
            StatusApuracao.NaoIniciada => "Apuracao ainda nao foi iniciada",
            StatusApuracao.EmAndamento => "Apuracao em andamento",
            StatusApuracao.Pausada => "Apuracao pausada",
            StatusApuracao.Concluida => "Apuracao concluida, aguardando homologacao",
            StatusApuracao.Homologada => "Resultado homologado e publicado",
            StatusApuracao.Contestada => "Resultado contestado, em analise",
            StatusApuracao.Anulada => "Apuracao anulada",
            _ => "Status desconhecido"
        };
    }

    private static string GetNomeRegiao(string uf)
    {
        return uf switch
        {
            "AC" => "Acre",
            "AL" => "Alagoas",
            "AP" => "Amapa",
            "AM" => "Amazonas",
            "BA" => "Bahia",
            "CE" => "Ceara",
            "DF" => "Distrito Federal",
            "ES" => "Espirito Santo",
            "GO" => "Goias",
            "MA" => "Maranhao",
            "MT" => "Mato Grosso",
            "MS" => "Mato Grosso do Sul",
            "MG" => "Minas Gerais",
            "PA" => "Para",
            "PB" => "Paraiba",
            "PR" => "Parana",
            "PE" => "Pernambuco",
            "PI" => "Piaui",
            "RJ" => "Rio de Janeiro",
            "RN" => "Rio Grande do Norte",
            "RS" => "Rio Grande do Sul",
            "RO" => "Rondonia",
            "RR" => "Roraima",
            "SC" => "Santa Catarina",
            "SP" => "Sao Paulo",
            "SE" => "Sergipe",
            "TO" => "Tocantins",
            _ => uf
        };
    }

    private static string GetDefaultColor(int index)
    {
        var colors = new[] { "#3B82F6", "#EF4444", "#10B981", "#F59E0B", "#8B5CF6", "#EC4899", "#06B6D4", "#84CC16" };
        return colors[index % colors.Length];
    }

    private static string GenerateAtaContent(ApuracaoResultado apuracao, ResultadoApuracaoDto resultado)
    {
        var sb = new StringBuilder();

        sb.AppendLine("ATA DE APURACAO DE VOTOS");
        sb.AppendLine("=======================");
        sb.AppendLine();
        sb.AppendLine($"Eleicao: {resultado.EleicaoNome}");
        sb.AppendLine($"Data da Apuracao: {resultado.DataApuracao:dd/MM/yyyy HH:mm}");
        sb.AppendLine();
        sb.AppendLine("RESUMO DA VOTACAO");
        sb.AppendLine("-----------------");
        sb.AppendLine($"Total de Eleitores Aptos: {resultado.TotalEleitores:N0}");
        sb.AppendLine($"Total de Votantes: {resultado.TotalVotos:N0}");
        sb.AppendLine($"Percentual de Participacao: {resultado.PercentualParticipacao}%");
        sb.AppendLine($"Total de Abstencoes: {resultado.TotalAbstencoes:N0}");
        sb.AppendLine($"Percentual de Abstencao: {resultado.PercentualAbstencao}%");
        sb.AppendLine();
        sb.AppendLine("VOTOS");
        sb.AppendLine("-----");
        sb.AppendLine($"Votos Validos: {resultado.VotosValidos:N0} ({resultado.PercentualVotosValidos}%)");
        sb.AppendLine($"Votos em Branco: {resultado.VotosBrancos:N0} ({resultado.PercentualVotosBrancos}%)");
        sb.AppendLine($"Votos Nulos: {resultado.VotosNulos:N0} ({resultado.PercentualVotosNulos}%)");
        sb.AppendLine();
        sb.AppendLine("RESULTADO POR CHAPA");
        sb.AppendLine("-------------------");

        foreach (var chapa in resultado.ResultadosChapas.OrderBy(c => c.Posicao))
        {
            sb.AppendLine($"{chapa.Posicao}. {chapa.Nome} (No. {chapa.Numero})");
            sb.AppendLine($"   Votos: {chapa.TotalVotos:N0} ({chapa.PercentualVotosValidos}% dos votos validos)");
            if (chapa.Vencedora)
                sb.AppendLine($"   *** CHAPA VENCEDORA ***");
            sb.AppendLine();
        }

        sb.AppendLine("VERIFICACAO DE INTEGRIDADE");
        sb.AppendLine("--------------------------");
        sb.AppendLine($"Hash: {resultado.HashIntegridade}");
        sb.AppendLine();

        if (resultado.Homologado)
        {
            sb.AppendLine("STATUS: RESULTADO HOMOLOGADO");
            sb.AppendLine($"Data da Homologacao: {resultado.DataHomologacao:dd/MM/yyyy HH:mm}");
        }
        else
        {
            sb.AppendLine("STATUS: AGUARDANDO HOMOLOGACAO");
        }

        return sb.ToString();
    }

    #endregion
}
