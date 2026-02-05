using System.Security.Cryptography;
using System.Text;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Votacao;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Chapas;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

public class VotacaoService : IVotacaoService
{
    private readonly IRepository<Voto> _votoRepository;
    private readonly IRepository<Eleitor> _eleitorRepository;
    private readonly IRepository<Eleicao> _eleicaoRepository;
    private readonly IRepository<ChapaEleicao> _chapaRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<VotacaoService> _logger;

    public VotacaoService(
        IRepository<Voto> votoRepository,
        IRepository<Eleitor> eleitorRepository,
        IRepository<Eleicao> eleicaoRepository,
        IRepository<ChapaEleicao> chapaRepository,
        IUnitOfWork unitOfWork,
        ILogger<VotacaoService> logger)
    {
        _votoRepository = votoRepository;
        _eleitorRepository = eleitorRepository;
        _eleicaoRepository = eleicaoRepository;
        _chapaRepository = chapaRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    public async Task<ComprovanteVotoDto> VotarAsync(RegistrarVotoDto dto, CancellationToken cancellationToken = default)
    {
        // Validate eleitor
        var validacao = await ValidarEleitorAsync(new ValidarVotoDto { EleicaoId = dto.EleicaoId, EleitorId = dto.EleitorId }, cancellationToken);
        if (!validacao.PodeVotar)
            throw new InvalidOperationException(validacao.MotivoImpedimento ?? "Eleitor nao pode votar");

        // Get eleicao
        var eleicao = await _eleicaoRepository.GetByIdAsync(dto.EleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {dto.EleicaoId} nao encontrada");

        // Get eleitor
        var eleitor = await _eleitorRepository.FirstOrDefaultAsync(
            e => e.EleicaoId == dto.EleicaoId && e.ProfissionalId == dto.EleitorId, cancellationToken)
            ?? throw new KeyNotFoundException("Eleitor nao encontrado");

        // Generate hashes
        var hashEleitor = GerarHashEleitor(dto.EleitorId, dto.EleicaoId);
        var hashVoto = GerarHashVoto(dto.EleicaoId, dto.ChapaId, DateTime.UtcNow);

        // Get chapa name if applicable
        string? chapaNome = null;
        if (dto.ChapaId.HasValue)
        {
            var chapa = await _chapaRepository.GetByIdAsync(dto.ChapaId.Value, cancellationToken);
            chapaNome = chapa?.Nome;
        }

        // Create vote
        var voto = new Voto
        {
            EleicaoId = dto.EleicaoId,
            ChapaId = dto.ChapaId,
            Tipo = dto.Tipo,
            Status = StatusVoto.Confirmado,
            Modo = dto.Modo,
            HashEleitor = hashEleitor,
            HashVoto = hashVoto,
            DataVoto = DateTime.UtcNow,
            IpAddress = dto.IpAddress,
            UserAgent = dto.UserAgent,
            Comprovante = GerarComprovante(hashVoto)
        };

        await _votoRepository.AddAsync(voto, cancellationToken);

        // Update eleitor status
        eleitor.Votou = true;
        eleitor.DataVoto = DateTime.UtcNow;
        eleitor.ComprovanteVotacao = voto.Comprovante;

        await _eleitorRepository.UpdateAsync(eleitor, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Voto registrado na eleicao {EleicaoId}. Hash: {HashVoto}", dto.EleicaoId, hashVoto);

        return new ComprovanteVotoDto
        {
            Comprovante = voto.Comprovante ?? "",
            HashVoto = hashVoto,
            DataVoto = voto.DataVoto,
            EleicaoNome = eleicao.Nome,
            ChapaNome = chapaNome,
            TipoVoto = dto.Tipo,
            Mensagem = "Voto registrado com sucesso"
        };
    }

    public async Task<ValidacaoVotoResultDto> ValidarEleitorAsync(ValidarVotoDto dto, CancellationToken cancellationToken = default)
    {
        // Check if eleicao is open
        var eleicaoAberta = await EleicaoAbertaParaVotacaoAsync(dto.EleicaoId, cancellationToken);

        // Check if eleitor exists and is eligible
        var eleitor = await _eleitorRepository.FirstOrDefaultAsync(
            e => e.EleicaoId == dto.EleicaoId && e.ProfissionalId == dto.EleitorId, cancellationToken);

        if (eleitor == null)
        {
            return new ValidacaoVotoResultDto
            {
                PodeVotar = false,
                JaVotou = false,
                EleitorApto = false,
                EleicaoAberta = eleicaoAberta,
                MotivoImpedimento = "Eleitor nao cadastrado para esta eleicao"
            };
        }

        if (!eleitor.Apto)
        {
            return new ValidacaoVotoResultDto
            {
                PodeVotar = false,
                JaVotou = eleitor.Votou,
                EleitorApto = false,
                EleicaoAberta = eleicaoAberta,
                MotivoImpedimento = eleitor.MotivoInaptidao ?? "Eleitor nao esta apto para votar"
            };
        }

        if (eleitor.Votou)
        {
            return new ValidacaoVotoResultDto
            {
                PodeVotar = false,
                JaVotou = true,
                EleitorApto = true,
                EleicaoAberta = eleicaoAberta,
                MotivoImpedimento = "Eleitor ja votou nesta eleicao"
            };
        }

        if (!eleicaoAberta)
        {
            return new ValidacaoVotoResultDto
            {
                PodeVotar = false,
                JaVotou = false,
                EleitorApto = true,
                EleicaoAberta = false,
                MotivoImpedimento = "Eleicao nao esta aberta para votacao"
            };
        }

        return new ValidacaoVotoResultDto
        {
            PodeVotar = true,
            JaVotou = false,
            EleitorApto = true,
            EleicaoAberta = true,
            MotivoImpedimento = null
        };
    }

    public async Task<CedulaEleitoralDto> ObterCedulaAsync(Guid eleicaoId, Guid eleitorId, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {eleicaoId} nao encontrada");

        var eleitor = await _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .FirstOrDefaultAsync(e => e.EleicaoId == eleicaoId && e.ProfissionalId == eleitorId, cancellationToken)
            ?? throw new KeyNotFoundException("Eleitor nao encontrado");

        var chapas = await _chapaRepository.Query()
            .Include(c => c.Membros)
            .ThenInclude(m => m.Profissional)
            .Where(c => c.EleicaoId == eleicaoId && c.Status == StatusChapa.Deferida)
            .OrderBy(c => c.Numero)
            .ToListAsync(cancellationToken);

        var opcoes = chapas.Select((c, index) => new OpcaoVotoDto
        {
            ChapaId = c.Id,
            Numero = int.TryParse(c.Numero, out var num) ? num : index + 1,
            Nome = c.Nome,
            Sigla = c.Sigla,
            PresidenteNome = c.Membros?.FirstOrDefault(m => m.Tipo == TipoMembroChapa.Presidente)?.Profissional?.Nome,
            Ordem = index + 1
        }).ToList();

        return new CedulaEleitoralDto
        {
            EleicaoId = eleicaoId,
            EleicaoNome = eleicao.Nome,
            EleitorId = eleitor.Id,
            EleitorNome = eleitor.Profissional?.Nome ?? "",
            Opcoes = opcoes,
            PermiteVotoBranco = true,
            ValidoAte = DateTime.UtcNow.AddMinutes(30)
        };
    }

    public async Task<ComprovanteVotoDto?> ObterComprovanteAsync(string hashVoto, CancellationToken cancellationToken = default)
    {
        var voto = await _votoRepository.Query()
            .Include(v => v.Eleicao)
            .Include(v => v.Chapa)
            .FirstOrDefaultAsync(v => v.HashVoto == hashVoto, cancellationToken);

        if (voto == null) return null;

        return new ComprovanteVotoDto
        {
            Comprovante = voto.Comprovante ?? "",
            HashVoto = voto.HashVoto,
            DataVoto = voto.DataVoto,
            EleicaoNome = voto.Eleicao?.Nome ?? "",
            ChapaNome = voto.Chapa?.Nome,
            TipoVoto = voto.Tipo,
            Mensagem = "Comprovante de voto"
        };
    }

    public async Task<bool> VerificarVotoAsync(string hashVoto, CancellationToken cancellationToken = default)
    {
        return await _votoRepository.AnyAsync(v => v.HashVoto == hashVoto, cancellationToken);
    }

    public async Task<EleitorDto?> GetEleitorByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var eleitor = await _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .Include(e => e.Secao)
            .FirstOrDefaultAsync(e => e.Id == id, cancellationToken);

        return eleitor == null ? null : MapEleitorToDto(eleitor);
    }

    public async Task<EleitorDto?> GetEleitorByProfissionalAsync(Guid eleicaoId, Guid profissionalId, CancellationToken cancellationToken = default)
    {
        var eleitor = await _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .Include(e => e.Secao)
            .FirstOrDefaultAsync(e => e.EleicaoId == eleicaoId && e.ProfissionalId == profissionalId, cancellationToken);

        return eleitor == null ? null : MapEleitorToDto(eleitor);
    }

    public async Task<IEnumerable<EleitorDto>> GetEleitoresByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleitores = await _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .Include(e => e.Secao)
            .Where(e => e.EleicaoId == eleicaoId)
            .OrderBy(e => e.Profissional!.Nome)
            .ToListAsync(cancellationToken);

        return eleitores.Select(MapEleitorToDto);
    }

    public async Task<IEnumerable<EleitorDto>> GetEleitoresAptosAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleitores = await _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .Include(e => e.Secao)
            .Where(e => e.EleicaoId == eleicaoId && e.Apto)
            .OrderBy(e => e.Profissional!.Nome)
            .ToListAsync(cancellationToken);

        return eleitores.Select(MapEleitorToDto);
    }

    public async Task<IEnumerable<EleitorDto>> GetEleitoresQueVotaramAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleitores = await _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .Include(e => e.Secao)
            .Where(e => e.EleicaoId == eleicaoId && e.Votou)
            .OrderBy(e => e.Profissional!.Nome)
            .ToListAsync(cancellationToken);

        return eleitores.Select(MapEleitorToDto);
    }

    public async Task<IEnumerable<EleitorDto>> GetEleitoresQueNaoVotaramAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleitores = await _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .Include(e => e.Secao)
            .Where(e => e.EleicaoId == eleicaoId && e.Apto && !e.Votou)
            .OrderBy(e => e.Profissional!.Nome)
            .ToListAsync(cancellationToken);

        return eleitores.Select(MapEleitorToDto);
    }

    public async Task<EleitorDto> RegistrarEleitorAsync(Guid eleicaoId, Guid profissionalId, CancellationToken cancellationToken = default)
    {
        var eleitor = new Eleitor
        {
            EleicaoId = eleicaoId,
            ProfissionalId = profissionalId,
            Apto = true,
            Votou = false
        };

        await _eleitorRepository.AddAsync(eleitor, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Eleitor registrado: {EleitorId} na eleicao {EleicaoId}", eleitor.Id, eleicaoId);

        return await GetEleitorByIdAsync(eleitor.Id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar eleitor");
    }

    public async Task<EleitorDto> AtualizarAptidaoEleitorAsync(Guid eleitorId, bool apto, string? motivo, CancellationToken cancellationToken = default)
    {
        var eleitor = await _eleitorRepository.GetByIdAsync(eleitorId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleitor {eleitorId} nao encontrado");

        eleitor.Apto = apto;
        eleitor.MotivoInaptidao = apto ? null : motivo;

        await _eleitorRepository.UpdateAsync(eleitor, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Aptidao do eleitor {EleitorId} atualizada: {Apto}", eleitorId, apto);

        return await GetEleitorByIdAsync(eleitorId, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar eleitor");
    }

    public async Task<EstatisticasVotacaoDto> GetEstatisticasAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {eleicaoId} nao encontrada");

        var totalEleitoresAptos = await _eleitorRepository.CountAsync(
            e => e.EleicaoId == eleicaoId && e.Apto, cancellationToken);

        var totalVotantes = await _eleitorRepository.CountAsync(
            e => e.EleicaoId == eleicaoId && e.Votou, cancellationToken);

        var votosPresenciais = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Modo == ModoVotacao.Presencial, cancellationToken);

        var votosOnline = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Modo == ModoVotacao.Online, cancellationToken);

        var percentualComparecimento = totalEleitoresAptos > 0
            ? (double)totalVotantes / totalEleitoresAptos * 100
            : 0;

        return new EstatisticasVotacaoDto
        {
            EleicaoId = eleicaoId,
            EleicaoNome = eleicao.Nome,
            TotalEleitoresAptos = totalEleitoresAptos,
            TotalVotantes = totalVotantes,
            TotalAbstencoes = totalEleitoresAptos - totalVotantes,
            PercentualComparecimento = Math.Round(percentualComparecimento, 2),
            PercentualAbstencao = Math.Round(100 - percentualComparecimento, 2),
            VotosPresenciais = votosPresenciais,
            VotosOnline = votosOnline,
            UltimaAtualizacao = DateTime.UtcNow
        };
    }

    public async Task<int> CountVotosAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        return await _votoRepository.CountAsync(v => v.EleicaoId == eleicaoId, cancellationToken);
    }

    public async Task<int> CountVotosByChapaAsync(Guid eleicaoId, Guid chapaId, CancellationToken cancellationToken = default)
    {
        return await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.ChapaId == chapaId, cancellationToken);
    }

    public async Task<int> CountVotosByTipoAsync(Guid eleicaoId, TipoVoto tipo, CancellationToken cancellationToken = default)
    {
        return await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Tipo == tipo, cancellationToken);
    }

    public async Task<int> CountEleitoresAptosAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        return await _eleitorRepository.CountAsync(
            e => e.EleicaoId == eleicaoId && e.Apto, cancellationToken);
    }

    public async Task<int> CountEleitoresQueVotaramAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        return await _eleitorRepository.CountAsync(
            e => e.EleicaoId == eleicaoId && e.Votou, cancellationToken);
    }

    public async Task<double> GetPercentualComparecimentoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var totalAptos = await CountEleitoresAptosAsync(eleicaoId, cancellationToken);
        var totalVotaram = await CountEleitoresQueVotaramAsync(eleicaoId, cancellationToken);

        return totalAptos > 0 ? Math.Round((double)totalVotaram / totalAptos * 100, 2) : 0;
    }

    public async Task<bool> EleicaoAbertaParaVotacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken);
        if (eleicao == null) return false;

        if (eleicao.Status != StatusEleicao.EmAndamento) return false;
        if (eleicao.FaseAtual != FaseEleicao.Votacao) return false;

        var agora = DateTime.UtcNow;
        if (eleicao.DataVotacaoInicio.HasValue && agora < eleicao.DataVotacaoInicio.Value) return false;
        if (eleicao.DataVotacaoFim.HasValue && agora > eleicao.DataVotacaoFim.Value) return false;

        return true;
    }

    public async Task AbrirVotacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {eleicaoId} nao encontrada");

        eleicao.FaseAtual = FaseEleicao.Votacao;
        eleicao.DataVotacaoInicio = DateTime.UtcNow;

        await _eleicaoRepository.UpdateAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Votacao aberta para eleicao {EleicaoId}", eleicaoId);
    }

    public async Task FecharVotacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {eleicaoId} nao encontrada");

        eleicao.FaseAtual = FaseEleicao.Apuracao;
        eleicao.DataVotacaoFim = DateTime.UtcNow;

        await _eleicaoRepository.UpdateAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Votacao fechada para eleicao {EleicaoId}", eleicaoId);
    }

    public async Task<int> ImportarEleitoresAsync(Guid eleicaoId, IEnumerable<Guid> profissionalIds, CancellationToken cancellationToken = default)
    {
        var count = 0;
        foreach (var profissionalId in profissionalIds)
        {
            var existe = await _eleitorRepository.AnyAsync(
                e => e.EleicaoId == eleicaoId && e.ProfissionalId == profissionalId, cancellationToken);

            if (!existe)
            {
                await RegistrarEleitorAsync(eleicaoId, profissionalId, cancellationToken);
                count++;
            }
        }

        _logger.LogInformation("{Count} eleitores importados para eleicao {EleicaoId}", count, eleicaoId);

        return count;
    }

    public async Task<IEnumerable<EleitorDto>> ExportarEleitoresAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        return await GetEleitoresByEleicaoAsync(eleicaoId, cancellationToken);
    }

    private static string GerarHashEleitor(Guid eleitorId, Guid eleicaoId)
    {
        var input = $"{eleitorId}:{eleicaoId}:{DateTime.UtcNow.Ticks}";
        using var sha256 = SHA256.Create();
        var bytes = sha256.ComputeHash(Encoding.UTF8.GetBytes(input));
        return Convert.ToBase64String(bytes);
    }

    private static string GerarHashVoto(Guid eleicaoId, Guid? chapaId, DateTime dataVoto)
    {
        var input = $"{eleicaoId}:{chapaId}:{dataVoto.Ticks}:{Guid.NewGuid()}";
        using var sha256 = SHA256.Create();
        var bytes = sha256.ComputeHash(Encoding.UTF8.GetBytes(input));
        return Convert.ToBase64String(bytes)[..20].ToUpper();
    }

    private static string GerarComprovante(string hashVoto)
    {
        return $"COMP-{DateTime.UtcNow:yyyyMMdd}-{hashVoto[..8]}";
    }

    private static EleitorDto MapEleitorToDto(Eleitor eleitor)
    {
        return new EleitorDto
        {
            Id = eleitor.Id,
            EleicaoId = eleitor.EleicaoId,
            ProfissionalId = eleitor.ProfissionalId,
            ProfissionalNome = eleitor.Profissional?.Nome ?? "",
            NumeroInscricao = eleitor.NumeroInscricao,
            Apto = eleitor.Apto,
            MotivoInaptidao = eleitor.MotivoInaptidao,
            Votou = eleitor.Votou,
            DataVoto = eleitor.DataVoto,
            SecaoId = eleitor.SecaoId,
            SecaoNome = eleitor.Secao?.Local ?? eleitor.Secao?.Numero
        };
    }
}
