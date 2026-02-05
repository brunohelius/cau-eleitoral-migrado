using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

public class EleicaoService : IEleicaoService
{
    private readonly IRepository<Eleicao> _eleicaoRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<EleicaoService> _logger;

    public EleicaoService(
        IRepository<Eleicao> eleicaoRepository,
        IUnitOfWork unitOfWork,
        ILogger<EleicaoService> logger)
    {
        _eleicaoRepository = eleicaoRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    public async Task<EleicaoDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.Query()
            .Include(e => e.Regional)
            .Include(e => e.Chapas)
            .FirstOrDefaultAsync(e => e.Id == id, cancellationToken);

        return eleicao == null ? null : MapToDto(eleicao);
    }

    public async Task<IEnumerable<EleicaoDto>> GetAllAsync(CancellationToken cancellationToken = default)
    {
        var eleicoes = await _eleicaoRepository.Query()
            .Include(e => e.Regional)
            .Include(e => e.Chapas)
            .OrderByDescending(e => e.Ano)
            .ThenByDescending(e => e.DataInicio)
            .ToListAsync(cancellationToken);

        return eleicoes.Select(MapToDto);
    }

    public async Task<IEnumerable<EleicaoDto>> GetByStatusAsync(int status, CancellationToken cancellationToken = default)
    {
        var statusEnum = (StatusEleicao)status;
        var eleicoes = await _eleicaoRepository.Query()
            .Include(e => e.Regional)
            .Include(e => e.Chapas)
            .Where(e => e.Status == statusEnum)
            .OrderByDescending(e => e.DataInicio)
            .ToListAsync(cancellationToken);

        return eleicoes.Select(MapToDto);
    }

    public async Task<IEnumerable<EleicaoDto>> GetAtivasAsync(CancellationToken cancellationToken = default)
    {
        var eleicoes = await _eleicaoRepository.Query()
            .Include(e => e.Regional)
            .Include(e => e.Chapas)
            .Where(e => e.Status == StatusEleicao.Agendada ||
                        e.Status == StatusEleicao.EmAndamento ||
                        e.Status == StatusEleicao.ApuracaoEmAndamento)
            .OrderByDescending(e => e.DataInicio)
            .ToListAsync(cancellationToken);

        return eleicoes.Select(MapToDto);
    }

    public async Task<EleicaoDto> CreateAsync(CreateEleicaoDto dto, CancellationToken cancellationToken = default)
    {
        var eleicao = new Eleicao
        {
            Nome = dto.Nome,
            Descricao = dto.Descricao,
            Tipo = dto.Tipo,
            Status = StatusEleicao.Rascunho,
            FaseAtual = FaseEleicao.Preparatoria,
            Ano = dto.Ano,
            Mandato = dto.Mandato,
            DataInicio = dto.DataInicio,
            DataFim = dto.DataFim,
            DataVotacaoInicio = dto.DataVotacaoInicio,
            DataVotacaoFim = dto.DataVotacaoFim,
            RegionalId = dto.RegionalId,
            ModoVotacao = dto.ModoVotacao,
            PermiteVotoOnline = dto.ModoVotacao == ModoVotacao.Online || dto.ModoVotacao == ModoVotacao.Hibrido,
            PermiteVotoPresencial = dto.ModoVotacao == ModoVotacao.Presencial || dto.ModoVotacao == ModoVotacao.Hibrido,
            QuantidadeVagas = dto.QuantidadeVagas,
            QuantidadeSuplentes = dto.QuantidadeSuplentes
        };

        await _eleicaoRepository.AddAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Eleição criada: {EleicaoId} - {Nome}", eleicao.Id, eleicao.Nome);

        return MapToDto(eleicao);
    }

    public async Task<EleicaoDto> UpdateAsync(Guid id, UpdateEleicaoDto dto, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleição {id} não encontrada");

        if (dto.Nome != null) eleicao.Nome = dto.Nome;
        if (dto.Descricao != null) eleicao.Descricao = dto.Descricao;
        if (dto.DataInicio.HasValue) eleicao.DataInicio = dto.DataInicio.Value;
        if (dto.DataFim.HasValue) eleicao.DataFim = dto.DataFim.Value;
        if (dto.DataVotacaoInicio.HasValue) eleicao.DataVotacaoInicio = dto.DataVotacaoInicio;
        if (dto.DataVotacaoFim.HasValue) eleicao.DataVotacaoFim = dto.DataVotacaoFim;
        if (dto.DataApuracao.HasValue) eleicao.DataApuracao = dto.DataApuracao;
        if (dto.ModoVotacao.HasValue) eleicao.ModoVotacao = dto.ModoVotacao.Value;
        if (dto.QuantidadeVagas.HasValue) eleicao.QuantidadeVagas = dto.QuantidadeVagas;
        if (dto.QuantidadeSuplentes.HasValue) eleicao.QuantidadeSuplentes = dto.QuantidadeSuplentes;

        await _eleicaoRepository.UpdateAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Eleição atualizada: {EleicaoId}", id);

        return MapToDto(eleicao);
    }

    public async Task DeleteAsync(Guid id, CancellationToken cancellationToken = default)
    {
        await _eleicaoRepository.SoftDeleteAsync(id, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Eleição excluída: {EleicaoId}", id);
    }

    public async Task<EleicaoDto> IniciarAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleição {id} não encontrada");

        if (eleicao.Status != StatusEleicao.Agendada)
            throw new InvalidOperationException("Apenas eleições agendadas podem ser iniciadas");

        eleicao.Status = StatusEleicao.EmAndamento;
        eleicao.FaseAtual = FaseEleicao.Inscricao;

        await _eleicaoRepository.UpdateAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Eleição iniciada: {EleicaoId}", id);

        return MapToDto(eleicao);
    }

    public async Task<EleicaoDto> EncerrarAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleição {id} não encontrada");

        eleicao.Status = StatusEleicao.Finalizada;

        await _eleicaoRepository.UpdateAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Eleição encerrada: {EleicaoId}", id);

        return MapToDto(eleicao);
    }

    public async Task<EleicaoDto> SuspenderAsync(Guid id, string motivo, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleição {id} não encontrada");

        eleicao.Status = StatusEleicao.Suspensa;

        await _eleicaoRepository.UpdateAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Eleição suspensa: {EleicaoId} - Motivo: {Motivo}", id, motivo);

        return MapToDto(eleicao);
    }

    public async Task<EleicaoDto> CancelarAsync(Guid id, string motivo, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleição {id} não encontrada");

        eleicao.Status = StatusEleicao.Cancelada;

        await _eleicaoRepository.UpdateAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Eleição cancelada: {EleicaoId} - Motivo: {Motivo}", id, motivo);

        return MapToDto(eleicao);
    }

    private static EleicaoDto MapToDto(Eleicao eleicao)
    {
        return new EleicaoDto
        {
            Id = eleicao.Id,
            Nome = eleicao.Nome,
            Descricao = eleicao.Descricao,
            Tipo = eleicao.Tipo,
            Status = eleicao.Status,
            FaseAtual = eleicao.FaseAtual,
            Ano = eleicao.Ano,
            Mandato = eleicao.Mandato,
            DataInicio = eleicao.DataInicio,
            DataFim = eleicao.DataFim,
            DataVotacaoInicio = eleicao.DataVotacaoInicio,
            DataVotacaoFim = eleicao.DataVotacaoFim,
            DataApuracao = eleicao.DataApuracao,
            RegionalId = eleicao.RegionalId,
            RegionalNome = eleicao.Regional?.Nome,
            ModoVotacao = eleicao.ModoVotacao,
            QuantidadeVagas = eleicao.QuantidadeVagas,
            QuantidadeSuplentes = eleicao.QuantidadeSuplentes,
            TotalChapas = eleicao.Chapas?.Count ?? 0,
            TotalEleitores = 0,
            CreatedAt = eleicao.CreatedAt,
            UpdatedAt = eleicao.UpdatedAt
        };
    }
}
