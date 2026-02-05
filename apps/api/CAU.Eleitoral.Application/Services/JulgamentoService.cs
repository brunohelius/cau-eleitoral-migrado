using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Julgamentos;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Julgamentos;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

public class JulgamentoService : IJulgamentoService
{
    private readonly IRepository<ComissaoJulgadora> _comissaoRepository;
    private readonly IRepository<MembroComissaoJulgadora> _membroComissaoRepository;
    private readonly IRepository<SessaoJulgamento> _sessaoRepository;
    private readonly IRepository<PautaSessao> _pautaRepository;
    private readonly IRepository<AtaSessao> _ataRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<JulgamentoService> _logger;

    public JulgamentoService(
        IRepository<ComissaoJulgadora> comissaoRepository,
        IRepository<MembroComissaoJulgadora> membroComissaoRepository,
        IRepository<SessaoJulgamento> sessaoRepository,
        IRepository<PautaSessao> pautaRepository,
        IRepository<AtaSessao> ataRepository,
        IUnitOfWork unitOfWork,
        ILogger<JulgamentoService> logger)
    {
        _comissaoRepository = comissaoRepository;
        _membroComissaoRepository = membroComissaoRepository;
        _sessaoRepository = sessaoRepository;
        _pautaRepository = pautaRepository;
        _ataRepository = ataRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    // Comissao Julgadora
    public async Task<ComissaoJulgadoraDto?> GetComissaoByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var comissao = await _comissaoRepository.Query()
            .Include(c => c.Eleicao)
            .Include(c => c.Membros)
            .ThenInclude(m => m.Conselheiro)
            .Include(c => c.Sessoes)
            .FirstOrDefaultAsync(c => c.Id == id, cancellationToken);

        return comissao == null ? null : MapComissaoToDto(comissao);
    }

    public async Task<IEnumerable<ComissaoJulgadoraDto>> GetComissoesByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var comissoes = await _comissaoRepository.Query()
            .Include(c => c.Eleicao)
            .Include(c => c.Membros)
            .Include(c => c.Sessoes)
            .Where(c => c.EleicaoId == eleicaoId)
            .OrderByDescending(c => c.CreatedAt)
            .ToListAsync(cancellationToken);

        return comissoes.Select(MapComissaoToDto);
    }

    public async Task<IEnumerable<ComissaoJulgadoraDto>> GetComissoesAtivasAsync(CancellationToken cancellationToken = default)
    {
        var comissoes = await _comissaoRepository.Query()
            .Include(c => c.Eleicao)
            .Include(c => c.Membros)
            .Include(c => c.Sessoes)
            .Where(c => c.Ativa)
            .OrderByDescending(c => c.CreatedAt)
            .ToListAsync(cancellationToken);

        return comissoes.Select(MapComissaoToDto);
    }

    public async Task<ComissaoJulgadoraDto> CreateComissaoAsync(CreateComissaoJulgadoraDto dto, CancellationToken cancellationToken = default)
    {
        var comissao = new ComissaoJulgadora
        {
            EleicaoId = dto.EleicaoId,
            Nome = dto.Nome,
            Descricao = dto.Descricao,
            Sigla = dto.Sigla,
            Portaria = dto.Portaria,
            DataPortaria = dto.DataPortaria,
            DataInicio = dto.DataInicio,
            DataFim = dto.DataFim,
            Ativa = true
        };

        await _comissaoRepository.AddAsync(comissao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Comissao julgadora criada: {ComissaoId} - {Nome}", comissao.Id, comissao.Nome);

        return await GetComissaoByIdAsync(comissao.Id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar comissao criada");
    }

    public async Task<ComissaoJulgadoraDto> UpdateComissaoAsync(Guid id, UpdateComissaoJulgadoraDto dto, CancellationToken cancellationToken = default)
    {
        var comissao = await _comissaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Comissao {id} nao encontrada");

        if (dto.Nome != null) comissao.Nome = dto.Nome;
        if (dto.Descricao != null) comissao.Descricao = dto.Descricao;
        if (dto.Sigla != null) comissao.Sigla = dto.Sigla;
        if (dto.Portaria != null) comissao.Portaria = dto.Portaria;
        if (dto.DataPortaria.HasValue) comissao.DataPortaria = dto.DataPortaria.Value;
        if (dto.DataInicio.HasValue) comissao.DataInicio = dto.DataInicio.Value;
        if (dto.DataFim.HasValue) comissao.DataFim = dto.DataFim.Value;
        if (dto.Ativa.HasValue) comissao.Ativa = dto.Ativa.Value;

        await _comissaoRepository.UpdateAsync(comissao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Comissao julgadora atualizada: {ComissaoId}", id);

        return await GetComissaoByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar comissao");
    }

    public async Task DeleteComissaoAsync(Guid id, CancellationToken cancellationToken = default)
    {
        await _comissaoRepository.SoftDeleteAsync(id, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Comissao julgadora excluida: {ComissaoId}", id);
    }

    public async Task<ComissaoJulgadoraDto> AtivarComissaoAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var comissao = await _comissaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Comissao {id} nao encontrada");

        comissao.Ativa = true;

        await _comissaoRepository.UpdateAsync(comissao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Comissao julgadora ativada: {ComissaoId}", id);

        return await GetComissaoByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar comissao");
    }

    public async Task<ComissaoJulgadoraDto> DesativarComissaoAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var comissao = await _comissaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Comissao {id} nao encontrada");

        comissao.Ativa = false;

        await _comissaoRepository.UpdateAsync(comissao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Comissao julgadora desativada: {ComissaoId}", id);

        return await GetComissaoByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar comissao");
    }

    // Membros da Comissao
    public async Task<MembroComissaoDto> AddMembroComissaoAsync(Guid comissaoId, CreateMembroComissaoDto dto, CancellationToken cancellationToken = default)
    {
        var membro = new MembroComissaoJulgadora
        {
            ComissaoId = comissaoId,
            ConselheiroId = dto.ConselheiroId,
            Tipo = dto.Tipo,
            DataInicio = dto.DataInicio,
            DataFim = dto.DataFim,
            Ativo = true
        };

        await _membroComissaoRepository.AddAsync(membro, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Membro adicionado a comissao {ComissaoId}: {ConselheiroId}", comissaoId, dto.ConselheiroId);

        return new MembroComissaoDto
        {
            Id = membro.Id,
            ComissaoId = membro.ComissaoId,
            ConselheiroId = membro.ConselheiroId,
            ConselheiroNome = "",
            Tipo = membro.Tipo,
            TipoNome = membro.Tipo.ToString(),
            DataInicio = membro.DataInicio,
            DataFim = membro.DataFim,
            Ativo = membro.Ativo
        };
    }

    public async Task RemoveMembroComissaoAsync(Guid comissaoId, Guid membroId, CancellationToken cancellationToken = default)
    {
        var membro = await _membroComissaoRepository.GetByIdAsync(membroId, cancellationToken)
            ?? throw new KeyNotFoundException($"Membro {membroId} nao encontrado");

        if (membro.ComissaoId != comissaoId)
            throw new InvalidOperationException("Membro nao pertence a esta comissao");

        await _membroComissaoRepository.DeleteAsync(membro, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Membro {MembroId} removido da comissao {ComissaoId}", membroId, comissaoId);
    }

    public async Task<IEnumerable<MembroComissaoDto>> GetMembrosComissaoAsync(Guid comissaoId, CancellationToken cancellationToken = default)
    {
        var membros = await _membroComissaoRepository.Query()
            .Include(m => m.Conselheiro)
            .Where(m => m.ComissaoId == comissaoId)
            .OrderBy(m => m.Tipo)
            .ToListAsync(cancellationToken);

        return membros.Select(m => new MembroComissaoDto
        {
            Id = m.Id,
            ComissaoId = m.ComissaoId,
            ConselheiroId = m.ConselheiroId,
            ConselheiroNome = m.Conselheiro?.Profissional?.Nome ?? "",
            Tipo = m.Tipo,
            TipoNome = m.Tipo.ToString(),
            DataInicio = m.DataInicio,
            DataFim = m.DataFim,
            Ativo = m.Ativo
        });
    }

    public async Task<MembroComissaoDto> AtualizarMembroComissaoAsync(Guid comissaoId, Guid membroId, TipoMembroComissao novoTipo, CancellationToken cancellationToken = default)
    {
        var membro = await _membroComissaoRepository.Query()
            .Include(m => m.Conselheiro)
            .FirstOrDefaultAsync(m => m.Id == membroId && m.ComissaoId == comissaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Membro {membroId} nao encontrado");

        membro.Tipo = novoTipo;

        await _membroComissaoRepository.UpdateAsync(membro, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Membro {MembroId} atualizado na comissao {ComissaoId}", membroId, comissaoId);

        return new MembroComissaoDto
        {
            Id = membro.Id,
            ComissaoId = membro.ComissaoId,
            ConselheiroId = membro.ConselheiroId,
            ConselheiroNome = membro.Conselheiro?.Profissional?.Nome ?? "",
            Tipo = membro.Tipo,
            TipoNome = membro.Tipo.ToString(),
            DataInicio = membro.DataInicio,
            DataFim = membro.DataFim,
            Ativo = membro.Ativo
        };
    }

    // Sessoes de Julgamento
    public async Task<SessaoJulgamentoDto?> GetSessaoByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var sessao = await _sessaoRepository.Query()
            .Include(s => s.Comissao)
            .Include(s => s.Pautas)
            .FirstOrDefaultAsync(s => s.Id == id, cancellationToken);

        return sessao == null ? null : MapSessaoToDto(sessao);
    }

    public async Task<IEnumerable<SessaoJulgamentoDto>> GetSessoesByComissaoAsync(Guid comissaoId, CancellationToken cancellationToken = default)
    {
        var sessoes = await _sessaoRepository.Query()
            .Include(s => s.Comissao)
            .Include(s => s.Pautas)
            .Where(s => s.ComissaoId == comissaoId)
            .OrderByDescending(s => s.DataSessao)
            .ToListAsync(cancellationToken);

        return sessoes.Select(MapSessaoToDto);
    }

    public async Task<IEnumerable<SessaoJulgamentoDto>> GetSessoesAgendadasAsync(CancellationToken cancellationToken = default)
    {
        var sessoes = await _sessaoRepository.Query()
            .Include(s => s.Comissao)
            .Include(s => s.Pautas)
            .Where(s => s.Status == StatusSessao.Agendada && s.DataSessao >= DateTime.UtcNow.Date)
            .OrderBy(s => s.DataSessao)
            .ToListAsync(cancellationToken);

        return sessoes.Select(MapSessaoToDto);
    }

    public async Task<SessaoJulgamentoDto> CreateSessaoAsync(CreateSessaoJulgamentoDto dto, CancellationToken cancellationToken = default)
    {
        var sessao = new SessaoJulgamento
        {
            ComissaoId = dto.ComissaoId,
            Numero = dto.Numero,
            Tipo = dto.Tipo,
            Status = StatusSessao.Agendada,
            DataSessao = dto.DataSessao,
            HoraInicio = dto.HoraInicio,
            Local = dto.Local,
            Observacao = dto.Observacao
        };

        await _sessaoRepository.AddAsync(sessao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Sessao de julgamento criada: {SessaoId} - {Numero}", sessao.Id, sessao.Numero);

        return await GetSessaoByIdAsync(sessao.Id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar sessao criada");
    }

    public async Task<SessaoJulgamentoDto> UpdateSessaoAsync(Guid id, UpdateSessaoJulgamentoDto dto, CancellationToken cancellationToken = default)
    {
        var sessao = await _sessaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Sessao {id} nao encontrada");

        if (sessao.Status != StatusSessao.Agendada)
            throw new InvalidOperationException("Apenas sessoes agendadas podem ser alteradas");

        if (dto.Numero != null) sessao.Numero = dto.Numero;
        if (dto.Tipo.HasValue) sessao.Tipo = dto.Tipo.Value;
        if (dto.DataSessao.HasValue) sessao.DataSessao = dto.DataSessao.Value;
        if (dto.HoraInicio.HasValue) sessao.HoraInicio = dto.HoraInicio.Value;
        if (dto.HoraFim.HasValue) sessao.HoraFim = dto.HoraFim.Value;
        if (dto.Local != null) sessao.Local = dto.Local;
        if (dto.Observacao != null) sessao.Observacao = dto.Observacao;

        await _sessaoRepository.UpdateAsync(sessao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Sessao de julgamento atualizada: {SessaoId}", id);

        return await GetSessaoByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar sessao");
    }

    public async Task DeleteSessaoAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var sessao = await _sessaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Sessao {id} nao encontrada");

        if (sessao.Status != StatusSessao.Agendada)
            throw new InvalidOperationException("Apenas sessoes agendadas podem ser excluidas");

        await _sessaoRepository.SoftDeleteAsync(id, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Sessao de julgamento excluida: {SessaoId}", id);
    }

    public async Task<SessaoJulgamentoDto> IniciarSessaoAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var sessao = await _sessaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Sessao {id} nao encontrada");

        sessao.Status = StatusSessao.EmAndamento;
        sessao.HoraInicio = TimeSpan.FromTicks(DateTime.UtcNow.TimeOfDay.Ticks);

        await _sessaoRepository.UpdateAsync(sessao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Sessao de julgamento iniciada: {SessaoId}", id);

        return await GetSessaoByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar sessao");
    }

    public async Task<SessaoJulgamentoDto> EncerrarSessaoAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var sessao = await _sessaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Sessao {id} nao encontrada");

        sessao.Status = StatusSessao.Encerrada;
        sessao.HoraFim = TimeSpan.FromTicks(DateTime.UtcNow.TimeOfDay.Ticks);

        await _sessaoRepository.UpdateAsync(sessao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Sessao de julgamento encerrada: {SessaoId}", id);

        return await GetSessaoByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar sessao");
    }

    public async Task<SessaoJulgamentoDto> CancelarSessaoAsync(Guid id, string motivo, CancellationToken cancellationToken = default)
    {
        var sessao = await _sessaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Sessao {id} nao encontrada");

        sessao.Status = StatusSessao.Cancelada;
        sessao.Observacao = $"{sessao.Observacao} - Cancelada: {motivo}";

        await _sessaoRepository.UpdateAsync(sessao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Sessao de julgamento cancelada: {SessaoId} - {Motivo}", id, motivo);

        return await GetSessaoByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar sessao");
    }

    public async Task<SessaoJulgamentoDto> AdiarSessaoAsync(Guid id, DateTime novaData, string motivo, CancellationToken cancellationToken = default)
    {
        var sessao = await _sessaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Sessao {id} nao encontrada");

        sessao.Status = StatusSessao.Adiada;
        sessao.Observacao = $"{sessao.Observacao} - Adiada para {novaData:dd/MM/yyyy}: {motivo}";
        sessao.DataSessao = novaData;

        await _sessaoRepository.UpdateAsync(sessao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Sessao de julgamento adiada: {SessaoId} para {NovaData}", id, novaData);

        return await GetSessaoByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar sessao");
    }

    // Pauta
    public async Task<PautaSessaoDto> AddPautaAsync(Guid sessaoId, CreatePautaSessaoDto dto, CancellationToken cancellationToken = default)
    {
        var pauta = new PautaSessao
        {
            SessaoId = sessaoId,
            Ordem = dto.Ordem,
            TipoProcesso = dto.TipoProcesso,
            NumeroProcesso = dto.NumeroProcesso,
            Partes = dto.Partes,
            Assunto = dto.Assunto,
            RelatorId = dto.RelatorId,
            Observacao = dto.Observacao,
            Julgado = false,
            Adiado = false
        };

        await _pautaRepository.AddAsync(pauta, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Pauta adicionada a sessao {SessaoId}: {PautaId}", sessaoId, pauta.Id);

        return MapPautaToDto(pauta);
    }

    public async Task RemovePautaAsync(Guid sessaoId, Guid pautaId, CancellationToken cancellationToken = default)
    {
        var pauta = await _pautaRepository.GetByIdAsync(pautaId, cancellationToken)
            ?? throw new KeyNotFoundException($"Pauta {pautaId} nao encontrada");

        if (pauta.SessaoId != sessaoId)
            throw new InvalidOperationException("Pauta nao pertence a esta sessao");

        await _pautaRepository.DeleteAsync(pauta, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Pauta {PautaId} removida da sessao {SessaoId}", pautaId, sessaoId);
    }

    public async Task<IEnumerable<PautaSessaoDto>> GetPautasSessaoAsync(Guid sessaoId, CancellationToken cancellationToken = default)
    {
        var pautas = await _pautaRepository.Query()
            .Include(p => p.Relator)
            .ThenInclude(r => r!.Conselheiro)
            .ThenInclude(c => c.Profissional)
            .Where(p => p.SessaoId == sessaoId)
            .OrderBy(p => p.Ordem)
            .ToListAsync(cancellationToken);

        return pautas.Select(MapPautaToDto);
    }

    public async Task<PautaSessaoDto> MarcarPautaComoJulgadaAsync(Guid sessaoId, Guid pautaId, CancellationToken cancellationToken = default)
    {
        var pauta = await _pautaRepository.GetByIdAsync(pautaId, cancellationToken)
            ?? throw new KeyNotFoundException($"Pauta {pautaId} nao encontrada");

        if (pauta.SessaoId != sessaoId)
            throw new InvalidOperationException("Pauta nao pertence a esta sessao");

        pauta.Julgado = true;

        await _pautaRepository.UpdateAsync(pauta, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Pauta {PautaId} marcada como julgada", pautaId);

        return MapPautaToDto(pauta);
    }

    public async Task<PautaSessaoDto> ReordenarPautaAsync(Guid sessaoId, Guid pautaId, int novaOrdem, CancellationToken cancellationToken = default)
    {
        var pauta = await _pautaRepository.GetByIdAsync(pautaId, cancellationToken)
            ?? throw new KeyNotFoundException($"Pauta {pautaId} nao encontrada");

        if (pauta.SessaoId != sessaoId)
            throw new InvalidOperationException("Pauta nao pertence a esta sessao");

        pauta.Ordem = novaOrdem;

        await _pautaRepository.UpdateAsync(pauta, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        return MapPautaToDto(pauta);
    }

    // Convocacao e Ata
    public async Task<string> GerarConvocacaoAsync(Guid sessaoId, CancellationToken cancellationToken = default)
    {
        var sessao = await _sessaoRepository.Query()
            .Include(s => s.Comissao)
            .ThenInclude(c => c.Membros)
            .ThenInclude(m => m.Conselheiro)
            .Include(s => s.Pautas)
            .FirstOrDefaultAsync(s => s.Id == sessaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Sessao {sessaoId} nao encontrada");

        // Generate convocation document
        var convocacao = $"CONVOCACAO - SESSAO {sessao.Numero}\n\n";
        convocacao += $"Data: {sessao.DataSessao:dd/MM/yyyy}\n";
        convocacao += $"Hora: {sessao.HoraInicio}\n";
        convocacao += $"Local: {sessao.Local ?? "A definir"}\n\n";
        convocacao += "PAUTA:\n";

        foreach (var pauta in sessao.Pautas.OrderBy(p => p.Ordem))
        {
            convocacao += $"{pauta.Ordem}. {pauta.NumeroProcesso} - {pauta.Assunto}\n";
        }

        sessao.ConvocacaoUrl = $"/documentos/convocacoes/sessao-{sessao.Numero}.pdf";
        sessao.DataConvocacao = DateTime.UtcNow;

        await _sessaoRepository.UpdateAsync(sessao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Convocacao gerada para sessao: {SessaoId}", sessaoId);

        return convocacao;
    }

    public async Task EnviarConvocacaoAsync(Guid sessaoId, CancellationToken cancellationToken = default)
    {
        var sessao = await _sessaoRepository.Query()
            .Include(s => s.Comissao)
            .ThenInclude(c => c.Membros)
            .ThenInclude(m => m.Conselheiro)
            .FirstOrDefaultAsync(s => s.Id == sessaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Sessao {sessaoId} nao encontrada");

        // In a real implementation, this would send emails to members
        _logger.LogInformation("Convocacao enviada para membros da sessao: {SessaoId}", sessaoId);
    }

    public async Task<string> GerarAtaSessaoAsync(Guid sessaoId, CancellationToken cancellationToken = default)
    {
        var sessao = await _sessaoRepository.Query()
            .Include(s => s.Comissao)
            .ThenInclude(c => c.Membros)
            .ThenInclude(m => m.Conselheiro)
            .Include(s => s.Pautas)
            .FirstOrDefaultAsync(s => s.Id == sessaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Sessao {sessaoId} nao encontrada");

        // Generate minutes document
        var ata = $"ATA DA SESSAO {sessao.Numero}\n\n";
        ata += $"Data: {sessao.DataSessao:dd/MM/yyyy}\n";
        ata += $"Inicio: {sessao.HoraInicio}\n";
        ata += $"Termino: {sessao.HoraFim}\n";
        ata += $"Local: {sessao.Local ?? "Nao informado"}\n\n";
        ata += "MEMBROS PRESENTES:\n";

        foreach (var membro in sessao.Comissao.Membros.Where(m => m.Ativo))
        {
            ata += $"- {membro.Conselheiro?.Profissional?.Nome ?? "N/A"} ({membro.Tipo})\n";
        }

        ata += "\nPAUTA E DELIBERACOES:\n";

        foreach (var pauta in sessao.Pautas.OrderBy(p => p.Ordem))
        {
            ata += $"\n{pauta.Ordem}. {pauta.NumeroProcesso} - {pauta.Assunto}\n";
            ata += $"   Status: {(pauta.Julgado ? "Julgado" : (pauta.Adiado ? "Adiado" : "Nao julgado"))}\n";
        }

        _logger.LogInformation("Ata gerada para sessao: {SessaoId}", sessaoId);

        return ata;
    }

    private static ComissaoJulgadoraDto MapComissaoToDto(ComissaoJulgadora comissao)
    {
        return new ComissaoJulgadoraDto
        {
            Id = comissao.Id,
            EleicaoId = comissao.EleicaoId,
            EleicaoNome = comissao.Eleicao?.Nome ?? "",
            Nome = comissao.Nome,
            Descricao = comissao.Descricao,
            Sigla = comissao.Sigla,
            Portaria = comissao.Portaria,
            DataPortaria = comissao.DataPortaria,
            DataInicio = comissao.DataInicio,
            DataFim = comissao.DataFim,
            Ativa = comissao.Ativa,
            TotalMembros = comissao.Membros?.Count ?? 0,
            TotalSessoes = comissao.Sessoes?.Count ?? 0,
            CreatedAt = comissao.CreatedAt
        };
    }

    private static SessaoJulgamentoDto MapSessaoToDto(SessaoJulgamento sessao)
    {
        return new SessaoJulgamentoDto
        {
            Id = sessao.Id,
            ComissaoId = sessao.ComissaoId,
            ComissaoNome = sessao.Comissao?.Nome ?? "",
            Numero = sessao.Numero,
            Tipo = sessao.Tipo,
            TipoNome = sessao.Tipo.ToString(),
            Status = sessao.Status,
            StatusNome = sessao.Status.ToString(),
            DataSessao = sessao.DataSessao,
            HoraInicio = sessao.HoraInicio,
            HoraFim = sessao.HoraFim,
            Local = sessao.Local,
            Observacao = sessao.Observacao,
            ConvocacaoUrl = sessao.ConvocacaoUrl,
            DataConvocacao = sessao.DataConvocacao,
            TotalPautas = sessao.Pautas?.Count ?? 0,
            CreatedAt = sessao.CreatedAt
        };
    }

    private static PautaSessaoDto MapPautaToDto(PautaSessao pauta)
    {
        return new PautaSessaoDto
        {
            Id = pauta.Id,
            SessaoId = pauta.SessaoId,
            Ordem = pauta.Ordem,
            TipoProcesso = pauta.TipoProcesso,
            TipoProcessoNome = pauta.TipoProcesso.ToString(),
            NumeroProcesso = pauta.NumeroProcesso,
            Partes = pauta.Partes,
            Assunto = pauta.Assunto,
            RelatorId = pauta.RelatorId,
            RelatorNome = pauta.Relator?.Conselheiro?.Profissional?.Nome,
            Julgado = pauta.Julgado,
            Adiado = pauta.Adiado,
            MotivoAdiamento = pauta.MotivoAdiamento,
            Observacao = pauta.Observacao
        };
    }
}
