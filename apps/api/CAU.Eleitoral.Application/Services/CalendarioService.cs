using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Calendario;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

public class CalendarioService : ICalendarioService
{
    private readonly IRepository<Calendario> _calendarioRepository;
    private readonly IRepository<AtividadePrincipalCalendario> _atividadePrincipalRepository;
    private readonly IRepository<AtividadeSecundariaCalendario> _atividadeSecundariaRepository;
    private readonly IRepository<Eleicao> _eleicaoRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<CalendarioService> _logger;

    public CalendarioService(
        IRepository<Calendario> calendarioRepository,
        IRepository<AtividadePrincipalCalendario> atividadePrincipalRepository,
        IRepository<AtividadeSecundariaCalendario> atividadeSecundariaRepository,
        IRepository<Eleicao> eleicaoRepository,
        IUnitOfWork unitOfWork,
        ILogger<CalendarioService> logger)
    {
        _calendarioRepository = calendarioRepository;
        _atividadePrincipalRepository = atividadePrincipalRepository;
        _atividadeSecundariaRepository = atividadeSecundariaRepository;
        _eleicaoRepository = eleicaoRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    #region Period Validation Methods

    public async Task<bool> IsWithinPeriodAsync(Guid eleicaoId, TipoCalendario tipo, CancellationToken cancellationToken = default)
    {
        var agora = DateTime.UtcNow;

        var periodo = await _calendarioRepository.Query()
            .Where(c => c.EleicaoId == eleicaoId &&
                       c.Tipo == tipo &&
                       c.Status != StatusCalendario.Cancelado)
            .FirstOrDefaultAsync(cancellationToken);

        if (periodo == null)
        {
            _logger.LogWarning("Periodo {Tipo} nao encontrado para eleicao {EleicaoId}", tipo, eleicaoId);
            return false;
        }

        // Consider time if specified
        var dataInicio = periodo.DataInicio;
        var dataFim = periodo.DataFim;

        if (periodo.HoraInicio.HasValue)
            dataInicio = dataInicio.Date.Add(periodo.HoraInicio.Value);

        if (periodo.HoraFim.HasValue)
            dataFim = dataFim.Date.Add(periodo.HoraFim.Value);

        return agora >= dataInicio && agora <= dataFim;
    }

    public async Task<bool> IsWithinAnyPeriodAsync(Guid eleicaoId, TipoCalendario[] tipos, CancellationToken cancellationToken = default)
    {
        foreach (var tipo in tipos)
        {
            if (await IsWithinPeriodAsync(eleicaoId, tipo, cancellationToken))
                return true;
        }
        return false;
    }

    public async Task<CalendarioDto?> GetPeriodoAtualAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var agora = DateTime.UtcNow;

        var periodos = await _calendarioRepository.Query()
            .Include(c => c.Eleicao)
            .Where(c => c.EleicaoId == eleicaoId &&
                       c.Status != StatusCalendario.Cancelado)
            .OrderBy(c => c.Ordem)
            .ToListAsync(cancellationToken);

        foreach (var periodo in periodos)
        {
            var dataInicio = periodo.DataInicio;
            var dataFim = periodo.DataFim;

            if (periodo.HoraInicio.HasValue)
                dataInicio = dataInicio.Date.Add(periodo.HoraInicio.Value);

            if (periodo.HoraFim.HasValue)
                dataFim = dataFim.Date.Add(periodo.HoraFim.Value);

            if (agora >= dataInicio && agora <= dataFim)
                return MapToDto(periodo);
        }

        return null;
    }

    public async Task<CalendarioDto?> GetProximoPeriodoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var agora = DateTime.UtcNow;

        var proximoPeriodo = await _calendarioRepository.Query()
            .Include(c => c.Eleicao)
            .Where(c => c.EleicaoId == eleicaoId &&
                       c.Status != StatusCalendario.Cancelado &&
                       c.DataInicio > agora)
            .OrderBy(c => c.DataInicio)
            .FirstOrDefaultAsync(cancellationToken);

        return proximoPeriodo == null ? null : MapToDto(proximoPeriodo);
    }

    public async Task<PeriodoValidacaoResult> ValidarPeriodoAsync(Guid eleicaoId, TipoCalendario[] tiposPermitidos, string nomeAcao, CancellationToken cancellationToken = default)
    {
        var agora = DateTime.UtcNow;

        // Get all periods for this election
        var periodos = await _calendarioRepository.Query()
            .Where(c => c.EleicaoId == eleicaoId &&
                       c.Status != StatusCalendario.Cancelado)
            .OrderBy(c => c.Ordem)
            .ToListAsync(cancellationToken);

        if (!periodos.Any())
        {
            return new PeriodoValidacaoResult
            {
                IsValid = false,
                Message = $"Calendario da eleicao nao foi configurado. Nao e possivel {nomeAcao}."
            };
        }

        // Check if current time is within any permitted period
        Calendario? periodoAtualEncontrado = null;

        foreach (var tipo in tiposPermitidos)
        {
            var periodo = periodos.FirstOrDefault(p => p.Tipo == tipo);
            if (periodo == null) continue;

            var dataInicio = periodo.DataInicio;
            var dataFim = periodo.DataFim;

            if (periodo.HoraInicio.HasValue)
                dataInicio = dataInicio.Date.Add(periodo.HoraInicio.Value);

            if (periodo.HoraFim.HasValue)
                dataFim = dataFim.Date.Add(periodo.HoraFim.Value);

            if (agora >= dataInicio && agora <= dataFim)
            {
                periodoAtualEncontrado = periodo;
                break;
            }
        }

        if (periodoAtualEncontrado != null)
        {
            return new PeriodoValidacaoResult
            {
                IsValid = true,
                PeriodoAtual = periodoAtualEncontrado.Tipo,
                DataInicioPeriodo = periodoAtualEncontrado.DataInicio,
                DataFimPeriodo = periodoAtualEncontrado.DataFim
            };
        }

        // If not within permitted period, build informative error message
        var tiposPermitidosStr = string.Join(", ", tiposPermitidos.Select(t => t.ToString()));

        // Find the actual current period (if any)
        var periodoAtualReal = periodos.FirstOrDefault(p =>
        {
            var dataInicio = p.DataInicio;
            var dataFim = p.DataFim;

            if (p.HoraInicio.HasValue)
                dataInicio = dataInicio.Date.Add(p.HoraInicio.Value);

            if (p.HoraFim.HasValue)
                dataFim = dataFim.Date.Add(p.HoraFim.Value);

            return agora >= dataInicio && agora <= dataFim;
        });

        // Find the nearest permitted period
        var proximoPeriodoPermitido = periodos
            .Where(p => tiposPermitidos.Contains(p.Tipo) && p.DataInicio > agora)
            .OrderBy(p => p.DataInicio)
            .FirstOrDefault();

        string mensagem;
        if (periodoAtualReal != null)
        {
            mensagem = $"Acao '{nomeAcao}' nao permitida. Periodo atual: {periodoAtualReal.Tipo}. " +
                      $"Esta acao so e permitida durante: {tiposPermitidosStr}.";
        }
        else if (proximoPeriodoPermitido != null)
        {
            mensagem = $"Acao '{nomeAcao}' nao permitida no momento. " +
                      $"O proximo periodo permitido ({proximoPeriodoPermitido.Tipo}) inicia em {proximoPeriodoPermitido.DataInicio:dd/MM/yyyy}.";
        }
        else
        {
            var ultimoPeriodoPermitido = periodos
                .Where(p => tiposPermitidos.Contains(p.Tipo))
                .OrderByDescending(p => p.DataFim)
                .FirstOrDefault();

            if (ultimoPeriodoPermitido != null && ultimoPeriodoPermitido.DataFim < agora)
            {
                mensagem = $"Acao '{nomeAcao}' nao permitida. O periodo de {ultimoPeriodoPermitido.Tipo} " +
                          $"encerrou em {ultimoPeriodoPermitido.DataFim:dd/MM/yyyy}.";
            }
            else
            {
                mensagem = $"Acao '{nomeAcao}' nao permitida. Periodos permitidos: {tiposPermitidosStr}.";
            }
        }

        _logger.LogWarning("Validacao de periodo falhou para eleicao {EleicaoId}: {Mensagem}", eleicaoId, mensagem);

        return new PeriodoValidacaoResult
        {
            IsValid = false,
            Message = mensagem,
            PeriodoAtual = periodoAtualReal?.Tipo
        };
    }

    public async Task<bool> PeriodoJaPassouAsync(Guid eleicaoId, TipoCalendario tipo, CancellationToken cancellationToken = default)
    {
        var agora = DateTime.UtcNow;

        var periodo = await _calendarioRepository.Query()
            .Where(c => c.EleicaoId == eleicaoId &&
                       c.Tipo == tipo &&
                       c.Status != StatusCalendario.Cancelado)
            .FirstOrDefaultAsync(cancellationToken);

        if (periodo == null)
            return false;

        var dataFim = periodo.DataFim;
        if (periodo.HoraFim.HasValue)
            dataFim = dataFim.Date.Add(periodo.HoraFim.Value);

        return agora > dataFim;
    }

    public async Task<bool> PeriodoAindaNaoIniciouAsync(Guid eleicaoId, TipoCalendario tipo, CancellationToken cancellationToken = default)
    {
        var agora = DateTime.UtcNow;

        var periodo = await _calendarioRepository.Query()
            .Where(c => c.EleicaoId == eleicaoId &&
                       c.Tipo == tipo &&
                       c.Status != StatusCalendario.Cancelado)
            .FirstOrDefaultAsync(cancellationToken);

        if (periodo == null)
            return true;

        var dataInicio = periodo.DataInicio;
        if (periodo.HoraInicio.HasValue)
            dataInicio = dataInicio.Date.Add(periodo.HoraInicio.Value);

        return agora < dataInicio;
    }

    public async Task<IEnumerable<CalendarioDto>> GetPeriodosAtivosAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var agora = DateTime.UtcNow;

        var periodos = await _calendarioRepository.Query()
            .Include(c => c.Eleicao)
            .Where(c => c.EleicaoId == eleicaoId &&
                       c.Status != StatusCalendario.Cancelado)
            .OrderBy(c => c.Ordem)
            .ToListAsync(cancellationToken);

        var periodosAtivos = new List<CalendarioDto>();

        foreach (var periodo in periodos)
        {
            var dataInicio = periodo.DataInicio;
            var dataFim = periodo.DataFim;

            if (periodo.HoraInicio.HasValue)
                dataInicio = dataInicio.Date.Add(periodo.HoraInicio.Value);

            if (periodo.HoraFim.HasValue)
                dataFim = dataFim.Date.Add(periodo.HoraFim.Value);

            if (agora >= dataInicio && agora <= dataFim)
                periodosAtivos.Add(MapToDto(periodo));
        }

        return periodosAtivos;
    }

    #endregion

    #region CRUD Operations

    public async Task<CalendarioDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var calendario = await _calendarioRepository.Query()
            .Include(c => c.Eleicao)
            .Include(c => c.AtividadesPrincipais)
            .Include(c => c.AtividadesSecundarias)
            .FirstOrDefaultAsync(c => c.Id == id, cancellationToken);

        return calendario == null ? null : MapToDto(calendario);
    }

    public async Task<IEnumerable<CalendarioDto>> GetAllAsync(CancellationToken cancellationToken = default)
    {
        var calendarios = await _calendarioRepository.Query()
            .Include(c => c.Eleicao)
            .Include(c => c.AtividadesPrincipais)
            .Include(c => c.AtividadesSecundarias)
            .OrderBy(c => c.Ordem)
            .ToListAsync(cancellationToken);

        return calendarios.Select(MapToDto);
    }

    public async Task<IEnumerable<CalendarioDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var calendarios = await _calendarioRepository.Query()
            .Include(c => c.Eleicao)
            .Include(c => c.AtividadesPrincipais)
            .Include(c => c.AtividadesSecundarias)
            .Where(c => c.EleicaoId == eleicaoId)
            .OrderBy(c => c.Ordem)
            .ToListAsync(cancellationToken);

        return calendarios.Select(MapToDto);
    }

    public async Task<IEnumerable<CalendarioDto>> GetByFaseAsync(FaseEleicao fase, CancellationToken cancellationToken = default)
    {
        var calendarios = await _calendarioRepository.Query()
            .Include(c => c.Eleicao)
            .Where(c => c.Fase == fase)
            .OrderBy(c => c.DataInicio)
            .ToListAsync(cancellationToken);

        return calendarios.Select(MapToDto);
    }

    public async Task<IEnumerable<CalendarioDto>> GetByTipoAsync(TipoCalendario tipo, CancellationToken cancellationToken = default)
    {
        var calendarios = await _calendarioRepository.Query()
            .Include(c => c.Eleicao)
            .Where(c => c.Tipo == tipo)
            .OrderBy(c => c.DataInicio)
            .ToListAsync(cancellationToken);

        return calendarios.Select(MapToDto);
    }

    public async Task<IEnumerable<CalendarioDto>> GetByStatusAsync(StatusCalendario status, CancellationToken cancellationToken = default)
    {
        var calendarios = await _calendarioRepository.Query()
            .Include(c => c.Eleicao)
            .Where(c => c.Status == status)
            .OrderBy(c => c.DataInicio)
            .ToListAsync(cancellationToken);

        return calendarios.Select(MapToDto);
    }

    public async Task<CalendarioDto> CreateAsync(CreateCalendarioDto dto, CancellationToken cancellationToken = default)
    {
        var ultimaOrdem = await _calendarioRepository.Query()
            .Where(c => c.EleicaoId == dto.EleicaoId)
            .MaxAsync(c => (int?)c.Ordem, cancellationToken) ?? 0;

        var calendario = new Calendario
        {
            EleicaoId = dto.EleicaoId,
            Nome = dto.Nome,
            Descricao = dto.Descricao,
            Tipo = dto.Tipo,
            Status = StatusCalendario.Pendente,
            Fase = dto.Fase,
            DataInicio = dto.DataInicio,
            DataFim = dto.DataFim,
            HoraInicio = dto.HoraInicio,
            HoraFim = dto.HoraFim,
            Ordem = dto.Ordem ?? ultimaOrdem + 1,
            Obrigatorio = dto.Obrigatorio,
            NotificarInicio = dto.NotificarInicio,
            NotificarFim = dto.NotificarFim
        };

        await _calendarioRepository.AddAsync(calendario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Evento de calendario criado: {CalendarioId} - {Nome}", calendario.Id, calendario.Nome);

        return await GetByIdAsync(calendario.Id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar calendario criado");
    }

    public async Task<CalendarioDto> UpdateAsync(Guid id, UpdateCalendarioDto dto, CancellationToken cancellationToken = default)
    {
        var calendario = await _calendarioRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Calendario {id} nao encontrado");

        if (dto.Nome != null) calendario.Nome = dto.Nome;
        if (dto.Descricao != null) calendario.Descricao = dto.Descricao;
        if (dto.Tipo.HasValue) calendario.Tipo = dto.Tipo.Value;
        if (dto.DataInicio.HasValue) calendario.DataInicio = dto.DataInicio.Value;
        if (dto.DataFim.HasValue) calendario.DataFim = dto.DataFim.Value;
        if (dto.HoraInicio.HasValue) calendario.HoraInicio = dto.HoraInicio.Value;
        if (dto.HoraFim.HasValue) calendario.HoraFim = dto.HoraFim.Value;
        if (dto.Ordem.HasValue) calendario.Ordem = dto.Ordem.Value;
        if (dto.Obrigatorio.HasValue) calendario.Obrigatorio = dto.Obrigatorio.Value;
        if (dto.NotificarInicio.HasValue) calendario.NotificarInicio = dto.NotificarInicio.Value;
        if (dto.NotificarFim.HasValue) calendario.NotificarFim = dto.NotificarFim.Value;

        await _calendarioRepository.UpdateAsync(calendario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Evento de calendario atualizado: {CalendarioId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar calendario");
    }

    public async Task DeleteAsync(Guid id, CancellationToken cancellationToken = default)
    {
        await _calendarioRepository.SoftDeleteAsync(id, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Evento de calendario excluido: {CalendarioId}", id);
    }

    public async Task<CalendarioDto> IniciarAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var calendario = await _calendarioRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Calendario {id} nao encontrado");

        calendario.Status = StatusCalendario.EmAndamento;

        await _calendarioRepository.UpdateAsync(calendario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Evento de calendario iniciado: {CalendarioId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar calendario");
    }

    public async Task<CalendarioDto> ConcluirAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var calendario = await _calendarioRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Calendario {id} nao encontrado");

        calendario.Status = StatusCalendario.Concluido;

        await _calendarioRepository.UpdateAsync(calendario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Evento de calendario concluido: {CalendarioId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar calendario");
    }

    public async Task<CalendarioDto> CancelarAsync(Guid id, string motivo, CancellationToken cancellationToken = default)
    {
        var calendario = await _calendarioRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Calendario {id} nao encontrado");

        calendario.Status = StatusCalendario.Cancelado;
        calendario.Descricao = $"{calendario.Descricao} - Cancelado: {motivo}";

        await _calendarioRepository.UpdateAsync(calendario, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Evento de calendario cancelado: {CalendarioId} - {Motivo}", id, motivo);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar calendario");
    }

    public async Task<CalendarioDto?> GetEventoAtualAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var agora = DateTime.UtcNow;
        var calendario = await _calendarioRepository.Query()
            .Include(c => c.Eleicao)
            .Where(c => c.EleicaoId == eleicaoId &&
                       c.Status == StatusCalendario.EmAndamento &&
                       c.DataInicio <= agora &&
                       c.DataFim >= agora)
            .OrderBy(c => c.Ordem)
            .FirstOrDefaultAsync(cancellationToken);

        return calendario == null ? null : MapToDto(calendario);
    }

    public async Task<CalendarioDto?> GetProximoEventoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var agora = DateTime.UtcNow;
        var calendario = await _calendarioRepository.Query()
            .Include(c => c.Eleicao)
            .Where(c => c.EleicaoId == eleicaoId &&
                       c.Status == StatusCalendario.Pendente &&
                       c.DataInicio > agora)
            .OrderBy(c => c.DataInicio)
            .FirstOrDefaultAsync(cancellationToken);

        return calendario == null ? null : MapToDto(calendario);
    }

    public async Task<IEnumerable<CalendarioDto>> GetEventosPendentesAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var calendarios = await _calendarioRepository.Query()
            .Include(c => c.Eleicao)
            .Where(c => c.EleicaoId == eleicaoId &&
                       c.Status == StatusCalendario.Pendente)
            .OrderBy(c => c.DataInicio)
            .ToListAsync(cancellationToken);

        return calendarios.Select(MapToDto);
    }

    public async Task<IEnumerable<CalendarioDto>> GetEventosHojeAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var hoje = DateTime.UtcNow.Date;
        var calendarios = await _calendarioRepository.Query()
            .Include(c => c.Eleicao)
            .Where(c => c.EleicaoId == eleicaoId &&
                       c.DataInicio.Date <= hoje &&
                       c.DataFim.Date >= hoje)
            .OrderBy(c => c.Ordem)
            .ToListAsync(cancellationToken);

        return calendarios.Select(MapToDto);
    }

    public async Task<IEnumerable<CalendarioDto>> GetEventosSemanaAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var hoje = DateTime.UtcNow.Date;
        var fimSemana = hoje.AddDays(7);
        var calendarios = await _calendarioRepository.Query()
            .Include(c => c.Eleicao)
            .Where(c => c.EleicaoId == eleicaoId &&
                       c.DataInicio.Date <= fimSemana &&
                       c.DataFim.Date >= hoje)
            .OrderBy(c => c.DataInicio)
            .ToListAsync(cancellationToken);

        return calendarios.Select(MapToDto);
    }

    public async Task<IEnumerable<CalendarioDto>> GetEventosPorPeriodoAsync(Guid eleicaoId, DateTime inicio, DateTime fim, CancellationToken cancellationToken = default)
    {
        var calendarios = await _calendarioRepository.Query()
            .Include(c => c.Eleicao)
            .Where(c => c.EleicaoId == eleicaoId &&
                       c.DataInicio <= fim &&
                       c.DataFim >= inicio)
            .OrderBy(c => c.DataInicio)
            .ToListAsync(cancellationToken);

        return calendarios.Select(MapToDto);
    }

    public async Task<CalendarioResumoDto> GetResumoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {eleicaoId} nao encontrada");

        var eventos = await _calendarioRepository.Query()
            .Where(c => c.EleicaoId == eleicaoId)
            .ToListAsync(cancellationToken);

        var eventoAtual = await GetEventoAtualAsync(eleicaoId, cancellationToken);
        var proximoEvento = await GetProximoEventoAsync(eleicaoId, cancellationToken);
        var eventosPendentes = eventos.Where(e => e.Status == StatusCalendario.Pendente).Select(MapToDto);

        return new CalendarioResumoDto
        {
            EleicaoId = eleicaoId,
            EleicaoNome = eleicao.Nome,
            FaseAtual = eleicao.FaseAtual,
            FaseAtualNome = eleicao.FaseAtual.ToString(),
            EventoAtual = eventoAtual,
            ProximoEvento = proximoEvento,
            EventosPendentes = eventosPendentes,
            TotalEventos = eventos.Count,
            EventosConcluidos = eventos.Count(e => e.Status == StatusCalendario.Concluido),
            EventosPendentesCount = eventos.Count(e => e.Status == StatusCalendario.Pendente)
        };
    }

    public async Task<AtividadeCalendarioDto> AddAtividadePrincipalAsync(Guid calendarioId, CreateAtividadeCalendarioDto dto, CancellationToken cancellationToken = default)
    {
        var atividade = new AtividadePrincipalCalendario
        {
            CalendarioId = calendarioId,
            Titulo = dto.Nome,
            Descricao = dto.Descricao,
            DataInicio = dto.DataInicio,
            DataFim = dto.DataFim,
            Concluida = false
        };

        await _atividadePrincipalRepository.AddAsync(atividade, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Atividade principal adicionada ao calendario {CalendarioId}", calendarioId);

        return new AtividadeCalendarioDto
        {
            Id = atividade.Id,
            CalendarioId = atividade.CalendarioId,
            Nome = atividade.Titulo,
            Descricao = atividade.Descricao,
            DataInicio = atividade.DataInicio,
            DataFim = atividade.DataFim,
            Concluida = atividade.Concluida
        };
    }

    public async Task<AtividadeCalendarioDto> AddAtividadeSecundariaAsync(Guid calendarioId, CreateAtividadeCalendarioDto dto, CancellationToken cancellationToken = default)
    {
        var atividade = new AtividadeSecundariaCalendario
        {
            CalendarioId = calendarioId,
            Titulo = dto.Nome,
            Descricao = dto.Descricao,
            DataInicio = dto.DataInicio,
            DataFim = dto.DataFim,
            Concluida = false
        };

        await _atividadeSecundariaRepository.AddAsync(atividade, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Atividade secundaria adicionada ao calendario {CalendarioId}", calendarioId);

        return new AtividadeCalendarioDto
        {
            Id = atividade.Id,
            CalendarioId = atividade.CalendarioId,
            Nome = atividade.Titulo,
            Descricao = atividade.Descricao,
            DataInicio = atividade.DataInicio,
            DataFim = atividade.DataFim,
            Concluida = atividade.Concluida
        };
    }

    public async Task RemoveAtividadeAsync(Guid calendarioId, Guid atividadeId, bool principal, CancellationToken cancellationToken = default)
    {
        if (principal)
        {
            var atividade = await _atividadePrincipalRepository.GetByIdAsync(atividadeId, cancellationToken)
                ?? throw new KeyNotFoundException($"Atividade {atividadeId} nao encontrada");

            if (atividade.CalendarioId != calendarioId)
                throw new InvalidOperationException("Atividade nao pertence a este calendario");

            await _atividadePrincipalRepository.DeleteAsync(atividade, cancellationToken);
        }
        else
        {
            var atividade = await _atividadeSecundariaRepository.GetByIdAsync(atividadeId, cancellationToken)
                ?? throw new KeyNotFoundException($"Atividade {atividadeId} nao encontrada");

            if (atividade.CalendarioId != calendarioId)
                throw new InvalidOperationException("Atividade nao pertence a este calendario");

            await _atividadeSecundariaRepository.DeleteAsync(atividade, cancellationToken);
        }

        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Atividade {AtividadeId} removida do calendario {CalendarioId}", atividadeId, calendarioId);
    }

    public async Task<AtividadeCalendarioDto> ConcluirAtividadeAsync(Guid calendarioId, Guid atividadeId, bool principal, CancellationToken cancellationToken = default)
    {
        if (principal)
        {
            var atividade = await _atividadePrincipalRepository.GetByIdAsync(atividadeId, cancellationToken)
                ?? throw new KeyNotFoundException($"Atividade {atividadeId} nao encontrada");

            if (atividade.CalendarioId != calendarioId)
                throw new InvalidOperationException("Atividade nao pertence a este calendario");

            atividade.Concluida = true;
            atividade.DataConclusao = DateTime.UtcNow;

            await _atividadePrincipalRepository.UpdateAsync(atividade, cancellationToken);
            await _unitOfWork.SaveChangesAsync(cancellationToken);

            return new AtividadeCalendarioDto
            {
                Id = atividade.Id,
                CalendarioId = atividade.CalendarioId,
                Nome = atividade.Titulo,
                Descricao = atividade.Descricao,
                DataInicio = atividade.DataInicio,
                DataFim = atividade.DataFim,
                Concluida = atividade.Concluida,
                DataConclusao = atividade.DataConclusao
            };
        }
        else
        {
            var atividade = await _atividadeSecundariaRepository.GetByIdAsync(atividadeId, cancellationToken)
                ?? throw new KeyNotFoundException($"Atividade {atividadeId} nao encontrada");

            if (atividade.CalendarioId != calendarioId)
                throw new InvalidOperationException("Atividade nao pertence a este calendario");

            atividade.Concluida = true;
            atividade.DataConclusao = DateTime.UtcNow;

            await _atividadeSecundariaRepository.UpdateAsync(atividade, cancellationToken);
            await _unitOfWork.SaveChangesAsync(cancellationToken);

            return new AtividadeCalendarioDto
            {
                Id = atividade.Id,
                CalendarioId = atividade.CalendarioId,
                Nome = atividade.Titulo,
                Descricao = atividade.Descricao,
                DataInicio = atividade.DataInicio,
                DataFim = atividade.DataFim,
                Concluida = atividade.Concluida,
                DataConclusao = atividade.DataConclusao
            };
        }
    }

    public async Task ReordenarEventosAsync(Guid eleicaoId, IEnumerable<(Guid Id, int Ordem)> novaOrdem, CancellationToken cancellationToken = default)
    {
        foreach (var (id, ordem) in novaOrdem)
        {
            var calendario = await _calendarioRepository.GetByIdAsync(id, cancellationToken);
            if (calendario != null && calendario.EleicaoId == eleicaoId)
            {
                calendario.Ordem = ordem;
                await _calendarioRepository.UpdateAsync(calendario, cancellationToken);
            }
        }

        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Eventos reordenados para eleicao {EleicaoId}", eleicaoId);
    }

    public async Task<IEnumerable<CalendarioDto>> CriarCalendarioPadraoAsync(Guid eleicaoId, DateTime dataInicioEleicao, CancellationToken cancellationToken = default)
    {
        var calendarios = new List<Calendario>();
        var ordem = 1;

        // Fase Preparatoria
        calendarios.Add(new Calendario
        {
            EleicaoId = eleicaoId,
            Nome = "Periodo de Inscricao de Chapas",
            Tipo = TipoCalendario.Inscricao,
            Status = StatusCalendario.Pendente,
            Fase = FaseEleicao.Inscricao,
            DataInicio = dataInicioEleicao,
            DataFim = dataInicioEleicao.AddDays(30),
            Ordem = ordem++,
            Obrigatorio = true,
            NotificarInicio = true,
            NotificarFim = true
        });

        // Fase de Impugnacao
        calendarios.Add(new Calendario
        {
            EleicaoId = eleicaoId,
            Nome = "Periodo de Impugnacao",
            Tipo = TipoCalendario.Impugnacao,
            Status = StatusCalendario.Pendente,
            Fase = FaseEleicao.Impugnacao,
            DataInicio = dataInicioEleicao.AddDays(31),
            DataFim = dataInicioEleicao.AddDays(45),
            Ordem = ordem++,
            Obrigatorio = true,
            NotificarInicio = true,
            NotificarFim = true
        });

        // Fase de Defesa
        calendarios.Add(new Calendario
        {
            EleicaoId = eleicaoId,
            Nome = "Periodo de Defesa",
            Tipo = TipoCalendario.Defesa,
            Status = StatusCalendario.Pendente,
            Fase = FaseEleicao.Impugnacao,
            DataInicio = dataInicioEleicao.AddDays(46),
            DataFim = dataInicioEleicao.AddDays(55),
            Ordem = ordem++,
            Obrigatorio = true,
            NotificarInicio = true,
            NotificarFim = true
        });

        // Fase de Propaganda
        calendarios.Add(new Calendario
        {
            EleicaoId = eleicaoId,
            Nome = "Periodo de Propaganda",
            Tipo = TipoCalendario.Propaganda,
            Status = StatusCalendario.Pendente,
            Fase = FaseEleicao.Propaganda,
            DataInicio = dataInicioEleicao.AddDays(56),
            DataFim = dataInicioEleicao.AddDays(85),
            Ordem = ordem++,
            Obrigatorio = false,
            NotificarInicio = true,
            NotificarFim = true
        });

        // Fase de Votacao
        calendarios.Add(new Calendario
        {
            EleicaoId = eleicaoId,
            Nome = "Periodo de Votacao",
            Tipo = TipoCalendario.Votacao,
            Status = StatusCalendario.Pendente,
            Fase = FaseEleicao.Votacao,
            DataInicio = dataInicioEleicao.AddDays(86),
            DataFim = dataInicioEleicao.AddDays(90),
            Ordem = ordem++,
            Obrigatorio = true,
            NotificarInicio = true,
            NotificarFim = true
        });

        // Fase de Apuracao
        calendarios.Add(new Calendario
        {
            EleicaoId = eleicaoId,
            Nome = "Apuracao dos Votos",
            Tipo = TipoCalendario.Apuracao,
            Status = StatusCalendario.Pendente,
            Fase = FaseEleicao.Apuracao,
            DataInicio = dataInicioEleicao.AddDays(91),
            DataFim = dataInicioEleicao.AddDays(92),
            Ordem = ordem++,
            Obrigatorio = true,
            NotificarInicio = true,
            NotificarFim = true
        });

        // Fase de Resultado
        calendarios.Add(new Calendario
        {
            EleicaoId = eleicaoId,
            Nome = "Publicacao do Resultado",
            Tipo = TipoCalendario.Resultado,
            Status = StatusCalendario.Pendente,
            Fase = FaseEleicao.Resultado,
            DataInicio = dataInicioEleicao.AddDays(93),
            DataFim = dataInicioEleicao.AddDays(93),
            Ordem = ordem++,
            Obrigatorio = true,
            NotificarInicio = true,
            NotificarFim = false
        });

        await _calendarioRepository.AddRangeAsync(calendarios, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Calendario padrao criado para eleicao {EleicaoId}", eleicaoId);

        return calendarios.Select(MapToDto);
    }

    public async Task VerificarNotificacoesAsync(CancellationToken cancellationToken = default)
    {
        var eventosParaNotificar = await GetEventosParaNotificarAsync(cancellationToken);
        // In real implementation, send notifications
        _logger.LogInformation("Verificacao de notificacoes concluida. {Count} eventos para notificar", eventosParaNotificar.Count());
    }

    public async Task<IEnumerable<CalendarioDto>> GetEventosParaNotificarAsync(CancellationToken cancellationToken = default)
    {
        var agora = DateTime.UtcNow;
        var amanha = agora.AddDays(1);

        var calendarios = await _calendarioRepository.Query()
            .Include(c => c.Eleicao)
            .Where(c => (c.NotificarInicio && c.DataInicio.Date == amanha.Date) ||
                       (c.NotificarFim && c.DataFim.Date == amanha.Date))
            .ToListAsync(cancellationToken);

        return calendarios.Select(MapToDto);
    }

    private static CalendarioDto MapToDto(Calendario calendario)
    {
        return new CalendarioDto
        {
            Id = calendario.Id,
            EleicaoId = calendario.EleicaoId,
            EleicaoNome = calendario.Eleicao?.Nome ?? "",
            Nome = calendario.Nome,
            Descricao = calendario.Descricao,
            Tipo = calendario.Tipo,
            TipoNome = calendario.Tipo.ToString(),
            Status = calendario.Status,
            StatusNome = calendario.Status.ToString(),
            Fase = calendario.Fase,
            FaseNome = calendario.Fase.ToString(),
            DataInicio = calendario.DataInicio,
            DataFim = calendario.DataFim,
            HoraInicio = calendario.HoraInicio,
            HoraFim = calendario.HoraFim,
            Ordem = calendario.Ordem,
            Obrigatorio = calendario.Obrigatorio,
            NotificarInicio = calendario.NotificarInicio,
            NotificarFim = calendario.NotificarFim,
            TotalAtividadesPrincipais = calendario.AtividadesPrincipais?.Count ?? 0,
            TotalAtividadesSecundarias = calendario.AtividadesSecundarias?.Count ?? 0,
            CreatedAt = calendario.CreatedAt
        };
    }

    #endregion
}
