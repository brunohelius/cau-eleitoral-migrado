using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Chapas;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

public class EleicaoService : IEleicaoService
{
    private readonly IRepository<Eleicao> _eleicaoRepository;
    private readonly IRepository<Voto> _votoRepository;
    private readonly IRepository<ChapaEleicao> _chapaRepository;
    private readonly IRepository<Calendario> _calendarioRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<EleicaoService> _logger;

    public EleicaoService(
        IRepository<Eleicao> eleicaoRepository,
        IRepository<Voto> votoRepository,
        IRepository<ChapaEleicao> chapaRepository,
        IRepository<Calendario> calendarioRepository,
        IUnitOfWork unitOfWork,
        ILogger<EleicaoService> logger)
    {
        _eleicaoRepository = eleicaoRepository;
        _votoRepository = votoRepository;
        _chapaRepository = chapaRepository;
        _calendarioRepository = calendarioRepository;
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
        // Load election with related data
        var eleicao = await _eleicaoRepository.Query()
            .Include(e => e.Regional)
            .Include(e => e.Chapas)
            .FirstOrDefaultAsync(e => e.Id == id, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {id} nao encontrada");

        // Validate election status - cannot update elections that are finalized or cancelled
        if (eleicao.Status == StatusEleicao.Finalizada)
            throw new InvalidOperationException("Nao e possivel editar uma eleicao finalizada");

        if (eleicao.Status == StatusEleicao.Cancelada)
            throw new InvalidOperationException("Nao e possivel editar uma eleicao cancelada");

        // Check if election has votes - restrict certain updates
        var hasVotes = await _votoRepository.AnyAsync(v => v.EleicaoId == id, cancellationToken);

        if (hasVotes)
        {
            // If election has votes, only allow updating non-critical fields
            if (dto.DataInicio.HasValue || dto.DataVotacaoInicio.HasValue || dto.ModoVotacao.HasValue)
            {
                throw new InvalidOperationException("Nao e possivel alterar datas de inicio ou modo de votacao de uma eleicao com votos registrados");
            }
        }

        // Update fields if provided
        if (!string.IsNullOrWhiteSpace(dto.Nome))
            eleicao.Nome = dto.Nome;

        if (dto.Descricao != null)
            eleicao.Descricao = dto.Descricao;

        if (dto.Tipo.HasValue)
            eleicao.Tipo = dto.Tipo.Value;

        if (dto.Ano.HasValue)
            eleicao.Ano = dto.Ano.Value;

        if (dto.Mandato.HasValue)
            eleicao.Mandato = dto.Mandato;

        if (dto.DataInicio.HasValue)
            eleicao.DataInicio = dto.DataInicio.Value;

        if (dto.DataFim.HasValue)
            eleicao.DataFim = dto.DataFim.Value;

        if (dto.DataVotacaoInicio.HasValue)
            eleicao.DataVotacaoInicio = dto.DataVotacaoInicio;

        if (dto.DataVotacaoFim.HasValue)
            eleicao.DataVotacaoFim = dto.DataVotacaoFim;

        if (dto.DataApuracao.HasValue)
            eleicao.DataApuracao = dto.DataApuracao;

        if (dto.RegionalId.HasValue)
            eleicao.RegionalId = dto.RegionalId;

        if (dto.ModoVotacao.HasValue)
        {
            eleicao.ModoVotacao = dto.ModoVotacao.Value;
            eleicao.PermiteVotoOnline = dto.ModoVotacao.Value == ModoVotacao.Online || dto.ModoVotacao.Value == ModoVotacao.Hibrido;
            eleicao.PermiteVotoPresencial = dto.ModoVotacao.Value == ModoVotacao.Presencial || dto.ModoVotacao.Value == ModoVotacao.Hibrido;
        }

        if (dto.QuantidadeVagas.HasValue)
            eleicao.QuantidadeVagas = dto.QuantidadeVagas;

        if (dto.QuantidadeSuplentes.HasValue)
            eleicao.QuantidadeSuplentes = dto.QuantidadeSuplentes;

        // Validate date consistency
        if (eleicao.DataFim < eleicao.DataInicio)
            throw new ArgumentException("Data de fim nao pode ser anterior a data de inicio");

        if (eleicao.DataVotacaoInicio.HasValue && eleicao.DataVotacaoFim.HasValue &&
            eleicao.DataVotacaoFim < eleicao.DataVotacaoInicio)
            throw new ArgumentException("Data de fim da votacao nao pode ser anterior a data de inicio da votacao");

        if (eleicao.DataVotacaoInicio.HasValue && eleicao.DataVotacaoInicio < eleicao.DataInicio)
            throw new ArgumentException("Data de inicio da votacao nao pode ser anterior a data de inicio da eleicao");

        if (eleicao.DataVotacaoFim.HasValue && eleicao.DataVotacaoFim > eleicao.DataFim)
            throw new ArgumentException("Data de fim da votacao nao pode ser posterior a data de fim da eleicao");

        eleicao.UpdatedAt = DateTime.UtcNow;

        await _eleicaoRepository.UpdateAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Eleicao atualizada: {EleicaoId} - {Nome}", id, eleicao.Nome);

        return MapToDto(eleicao);
    }

    public async Task DeleteAsync(Guid id, CancellationToken cancellationToken = default)
    {
        // Load election to validate
        var eleicao = await _eleicaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {id} nao encontrada");

        // Check if already deleted
        if (eleicao.IsDeleted)
            throw new InvalidOperationException("Eleicao ja foi excluida");

        // Validate election status - cannot delete elections that are in progress
        if (eleicao.Status == StatusEleicao.EmAndamento || eleicao.Status == StatusEleicao.ApuracaoEmAndamento)
            throw new InvalidOperationException("Nao e possivel excluir uma eleicao em andamento. Cancele a eleicao primeiro.");

        // Check if election is in voting phase
        if (eleicao.FaseAtual == FaseEleicao.Votacao)
            throw new InvalidOperationException("Nao e possivel excluir uma eleicao durante o periodo de votacao");

        // Check for votes - elections with votes cannot be deleted, only cancelled
        var hasVotes = await _votoRepository.AnyAsync(v => v.EleicaoId == id, cancellationToken);
        if (hasVotes)
            throw new InvalidOperationException("Nao e possivel excluir uma eleicao com votos registrados. Utilize a opcao de cancelar.");

        // Check for registered chapas
        var hasChapas = await _chapaRepository.AnyAsync(c => c.EleicaoId == id && !c.IsDeleted, cancellationToken);
        if (hasChapas)
        {
            // Soft delete all chapas first
            var chapas = await _chapaRepository.FindAsync(c => c.EleicaoId == id && !c.IsDeleted, cancellationToken);
            foreach (var chapa in chapas)
            {
                await _chapaRepository.SoftDeleteAsync(chapa, cancellationToken);
            }
            _logger.LogInformation("Soft deleted {Count} chapas for election {EleicaoId}", chapas.Count(), id);
        }

        // Perform soft delete
        await _eleicaoRepository.SoftDeleteAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Eleicao excluida (soft delete): {EleicaoId} - {Nome}", id, eleicao.Nome);
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

        // Verificar se todas as fases obrigatorias do calendario foram concluidas
        var calendarios = await _calendarioRepository.Query()
            .Where(c => c.EleicaoId == id && c.Obrigatorio && c.Status != StatusCalendario.Cancelado)
            .ToListAsync(cancellationToken);

        var fasesNaoConcluidas = calendarios
            .Where(c => c.Status != StatusCalendario.Concluido && c.DataFim < DateTime.UtcNow)
            .ToList();

        if (fasesNaoConcluidas.Any())
        {
            var fasesStr = string.Join(", ", fasesNaoConcluidas.Select(f => f.Tipo.ToString()));
            _logger.LogWarning(
                "Eleicao {EleicaoId} sendo encerrada com fases nao concluidas: {Fases}",
                id, fasesStr);
        }

        // Verificar se periodo de resultado ja passou ou esta ativo
        var periodoResultado = calendarios.FirstOrDefault(c => c.Tipo == TipoCalendario.Resultado);
        if (periodoResultado != null && DateTime.UtcNow < periodoResultado.DataFim)
        {
            throw new InvalidOperationException(
                $"A eleicao nao pode ser encerrada antes do fim do periodo de resultado ({periodoResultado.DataFim:dd/MM/yyyy})");
        }

        eleicao.Status = StatusEleicao.Finalizada;
        eleicao.FaseAtual = FaseEleicao.Diplomacao;

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

    public async Task<EleicaoValidationResult> CanDeleteAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(id, cancellationToken);
        if (eleicao == null)
        {
            return new EleicaoValidationResult
            {
                IsValid = false,
                Message = "Eleicao nao encontrada"
            };
        }

        var warnings = new List<string>();
        var voteCount = await _votoRepository.CountAsync(v => v.EleicaoId == id, cancellationToken);
        var chapaCount = await _chapaRepository.CountAsync(c => c.EleicaoId == id && !c.IsDeleted, cancellationToken);

        // Check if already deleted
        if (eleicao.IsDeleted)
        {
            return new EleicaoValidationResult
            {
                IsValid = false,
                Message = "Eleicao ja foi excluida",
                HasVotes = voteCount > 0,
                HasChapas = chapaCount > 0,
                TotalVotes = voteCount,
                TotalChapas = chapaCount
            };
        }

        // Check for votes
        if (voteCount > 0)
        {
            return new EleicaoValidationResult
            {
                IsValid = false,
                Message = "Nao e possivel excluir uma eleicao com votos registrados. Utilize a opcao de cancelar.",
                HasVotes = true,
                HasChapas = chapaCount > 0,
                TotalVotes = voteCount,
                TotalChapas = chapaCount
            };
        }

        // Check status
        if (eleicao.Status == StatusEleicao.EmAndamento || eleicao.Status == StatusEleicao.ApuracaoEmAndamento)
        {
            return new EleicaoValidationResult
            {
                IsValid = false,
                Message = "Nao e possivel excluir uma eleicao em andamento. Cancele a eleicao primeiro.",
                HasVotes = false,
                HasChapas = chapaCount > 0,
                TotalVotes = 0,
                TotalChapas = chapaCount
            };
        }

        // Check if in voting phase
        if (eleicao.FaseAtual == FaseEleicao.Votacao)
        {
            return new EleicaoValidationResult
            {
                IsValid = false,
                Message = "Nao e possivel excluir uma eleicao durante o periodo de votacao",
                HasVotes = false,
                HasChapas = chapaCount > 0,
                TotalVotes = 0,
                TotalChapas = chapaCount
            };
        }

        // Check for chapas - add warning but allow deletion
        if (chapaCount > 0)
        {
            warnings.Add($"A eleicao possui {chapaCount} chapa(s) registrada(s) que serao excluidas junto com a eleicao");
        }

        return new EleicaoValidationResult
        {
            IsValid = true,
            Message = null,
            Warnings = warnings,
            HasVotes = false,
            HasChapas = chapaCount > 0,
            TotalVotes = 0,
            TotalChapas = chapaCount
        };
    }

    public async Task<EleicaoValidationResult> CanEditAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(id, cancellationToken);
        if (eleicao == null)
        {
            return new EleicaoValidationResult
            {
                IsValid = false,
                Message = "Eleicao nao encontrada"
            };
        }

        var warnings = new List<string>();
        var voteCount = await _votoRepository.CountAsync(v => v.EleicaoId == id, cancellationToken);
        var chapaCount = await _chapaRepository.CountAsync(c => c.EleicaoId == id && !c.IsDeleted, cancellationToken);

        // Check if already deleted
        if (eleicao.IsDeleted)
        {
            return new EleicaoValidationResult
            {
                IsValid = false,
                Message = "Eleicao foi excluida e nao pode ser editada",
                HasVotes = voteCount > 0,
                HasChapas = chapaCount > 0,
                TotalVotes = voteCount,
                TotalChapas = chapaCount
            };
        }

        // Check status
        if (eleicao.Status == StatusEleicao.Finalizada)
        {
            return new EleicaoValidationResult
            {
                IsValid = false,
                Message = "Nao e possivel editar uma eleicao finalizada",
                HasVotes = voteCount > 0,
                HasChapas = chapaCount > 0,
                TotalVotes = voteCount,
                TotalChapas = chapaCount
            };
        }

        if (eleicao.Status == StatusEleicao.Cancelada)
        {
            return new EleicaoValidationResult
            {
                IsValid = false,
                Message = "Nao e possivel editar uma eleicao cancelada",
                HasVotes = voteCount > 0,
                HasChapas = chapaCount > 0,
                TotalVotes = voteCount,
                TotalChapas = chapaCount
            };
        }

        // Add warnings for restrictions
        if (voteCount > 0)
        {
            warnings.Add("Eleicao possui votos registrados. Algumas alteracoes serao restritas (datas de inicio, modo de votacao)");
        }

        if (chapaCount > 0)
        {
            warnings.Add($"Eleicao possui {chapaCount} chapa(s) registrada(s). Alteracoes no tipo ou regional nao serao permitidas");
        }

        if (eleicao.Status != StatusEleicao.Rascunho)
        {
            warnings.Add("Eleicao nao esta em rascunho. Ano nao pode ser alterado");
        }

        return new EleicaoValidationResult
        {
            IsValid = true,
            Message = null,
            Warnings = warnings,
            HasVotes = voteCount > 0,
            HasChapas = chapaCount > 0,
            TotalVotes = voteCount,
            TotalChapas = chapaCount
        };
    }

    public async Task<EleicaoDto> AvancarFaseAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {id} nao encontrada");

        if (eleicao.Status != StatusEleicao.EmAndamento && eleicao.Status != StatusEleicao.ApuracaoEmAndamento)
            throw new InvalidOperationException("Apenas eleicoes em andamento podem ter a fase avancada");

        // Verificar se o periodo atual do calendario ja terminou
        var calendarioAtual = await _calendarioRepository.Query()
            .Where(c => c.EleicaoId == id &&
                       c.Status != StatusCalendario.Cancelado &&
                       c.DataFim < DateTime.UtcNow)
            .OrderByDescending(c => c.DataFim)
            .FirstOrDefaultAsync(cancellationToken);

        // Definir a proxima fase baseado na fase atual
        var proximaFase = eleicao.FaseAtual switch
        {
            FaseEleicao.Preparatoria => FaseEleicao.Inscricao,
            FaseEleicao.Inscricao => FaseEleicao.Impugnacao,
            FaseEleicao.Impugnacao => FaseEleicao.Propaganda,
            FaseEleicao.Propaganda => FaseEleicao.Votacao,
            FaseEleicao.Votacao => FaseEleicao.Apuracao,
            FaseEleicao.Apuracao => FaseEleicao.Resultado,
            FaseEleicao.Resultado => FaseEleicao.Diplomacao,
            _ => throw new InvalidOperationException($"Fase {eleicao.FaseAtual} nao pode ser avancada")
        };

        // Validar a transicao
        var validacao = await CanTransitionToPhaseAsync(id, proximaFase, cancellationToken);
        if (!validacao.IsValid)
            throw new InvalidOperationException(validacao.Message);

        eleicao.FaseAtual = proximaFase;

        // Atualizar status se necessario
        if (proximaFase == FaseEleicao.Apuracao)
            eleicao.Status = StatusEleicao.ApuracaoEmAndamento;
        else if (proximaFase == FaseEleicao.Diplomacao)
            eleicao.Status = StatusEleicao.Finalizada;

        await _eleicaoRepository.UpdateAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Eleicao {EleicaoId} avancou para fase {Fase}", id, proximaFase);

        return MapToDto(eleicao);
    }

    public async Task<EleicaoDto> DefinirFaseAsync(Guid id, FaseEleicao fase, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {id} nao encontrada");

        if (eleicao.Status == StatusEleicao.Finalizada || eleicao.Status == StatusEleicao.Cancelada)
            throw new InvalidOperationException("Eleicao finalizada ou cancelada nao pode ter a fase alterada");

        eleicao.FaseAtual = fase;

        // Atualizar status conforme a fase
        if (fase == FaseEleicao.Preparatoria && eleicao.Status != StatusEleicao.Rascunho)
            eleicao.Status = StatusEleicao.Agendada;
        else if (fase == FaseEleicao.Apuracao)
            eleicao.Status = StatusEleicao.ApuracaoEmAndamento;
        else if (fase == FaseEleicao.Diplomacao)
            eleicao.Status = StatusEleicao.Finalizada;
        else if (fase != FaseEleicao.Preparatoria && eleicao.Status == StatusEleicao.Rascunho)
            eleicao.Status = StatusEleicao.EmAndamento;

        await _eleicaoRepository.UpdateAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Eleicao {EleicaoId} definida para fase {Fase}", id, fase);

        return MapToDto(eleicao);
    }

    public async Task<EleicaoValidationResult> CanTransitionToPhaseAsync(Guid id, FaseEleicao fase, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(id, cancellationToken);
        if (eleicao == null)
        {
            return new EleicaoValidationResult
            {
                IsValid = false,
                Message = "Eleicao nao encontrada"
            };
        }

        var warnings = new List<string>();

        // Verificar se o calendario permite a transicao
        var calendarioDaFase = await _calendarioRepository.Query()
            .Where(c => c.EleicaoId == id &&
                       c.Fase == fase &&
                       c.Status != StatusCalendario.Cancelado)
            .FirstOrDefaultAsync(cancellationToken);

        if (calendarioDaFase == null)
        {
            return new EleicaoValidationResult
            {
                IsValid = false,
                Message = $"Nenhum evento do calendario encontrado para a fase {fase}"
            };
        }

        // Verificar se a data de inicio do calendario ja passou
        if (DateTime.UtcNow < calendarioDaFase.DataInicio)
        {
            return new EleicaoValidationResult
            {
                IsValid = false,
                Message = $"O periodo de {fase} ainda nao iniciou. Inicio previsto: {calendarioDaFase.DataInicio:dd/MM/yyyy}",
                Warnings = warnings
            };
        }

        // Verificar se a fase anterior foi concluida
        var faseAnterior = fase switch
        {
            FaseEleicao.Inscricao => FaseEleicao.Preparatoria,
            FaseEleicao.Impugnacao => FaseEleicao.Inscricao,
            FaseEleicao.Propaganda => FaseEleicao.Impugnacao,
            FaseEleicao.Votacao => FaseEleicao.Propaganda,
            FaseEleicao.Apuracao => FaseEleicao.Votacao,
            FaseEleicao.Resultado => FaseEleicao.Apuracao,
            FaseEleicao.Diplomacao => FaseEleicao.Resultado,
            _ => (FaseEleicao?)null
        };

        if (faseAnterior.HasValue)
        {
            var calendarioAnterior = await _calendarioRepository.Query()
                .Where(c => c.EleicaoId == id &&
                           c.Fase == faseAnterior.Value &&
                           c.Status != StatusCalendario.Cancelado)
                .FirstOrDefaultAsync(cancellationToken);

            if (calendarioAnterior != null && calendarioAnterior.Status != StatusCalendario.Concluido)
            {
                if (DateTime.UtcNow < calendarioAnterior.DataFim)
                {
                    warnings.Add($"O periodo de {faseAnterior.Value} ainda nao terminou (termina em {calendarioAnterior.DataFim:dd/MM/yyyy})");
                }
            }
        }

        // Validacoes especificas por fase
        if (fase == FaseEleicao.Votacao)
        {
            var chapasAptas = await _chapaRepository.CountAsync(
                c => c.EleicaoId == id &&
                    !c.IsDeleted &&
                    (c.Status == StatusChapa.Deferida || c.Status == StatusChapa.Registrada),
                cancellationToken);

            if (chapasAptas < 1)
            {
                return new EleicaoValidationResult
                {
                    IsValid = false,
                    Message = "Nenhuma chapa apta para votacao. E necessario pelo menos 1 chapa deferida/registrada.",
                    Warnings = warnings
                };
            }
        }

        return new EleicaoValidationResult
        {
            IsValid = true,
            Message = null,
            Warnings = warnings
        };
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
