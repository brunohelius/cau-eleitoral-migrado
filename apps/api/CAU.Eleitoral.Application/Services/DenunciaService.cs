using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Denuncias;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Denuncias;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

public class DenunciaService : IDenunciaService
{
    private readonly IRepository<Denuncia> _denunciaRepository;
    private readonly IRepository<ProvaDenuncia> _provaRepository;
    private readonly IRepository<DefesaDenuncia> _defesaRepository;
    private readonly IRepository<HistoricoDenuncia> _historicoRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<DenunciaService> _logger;

    public DenunciaService(
        IRepository<Denuncia> denunciaRepository,
        IRepository<ProvaDenuncia> provaRepository,
        IRepository<DefesaDenuncia> defesaRepository,
        IRepository<HistoricoDenuncia> historicoRepository,
        IUnitOfWork unitOfWork,
        ILogger<DenunciaService> logger)
    {
        _denunciaRepository = denunciaRepository;
        _provaRepository = provaRepository;
        _defesaRepository = defesaRepository;
        _historicoRepository = historicoRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    public async Task<DenunciaDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Chapa)
            .Include(d => d.Membro)
            .Include(d => d.Denunciante)
            .Include(d => d.Provas)
            .Include(d => d.Defesas)
            .FirstOrDefaultAsync(d => d.Id == id, cancellationToken);

        return denuncia == null ? null : MapToDto(denuncia);
    }

    public async Task<DenunciaDto?> GetByProtocoloAsync(string protocolo, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Chapa)
            .Include(d => d.Provas)
            .Include(d => d.Defesas)
            .FirstOrDefaultAsync(d => d.Protocolo == protocolo, cancellationToken);

        return denuncia == null ? null : MapToDto(denuncia);
    }

    public async Task<IEnumerable<DenunciaDto>> GetAllAsync(CancellationToken cancellationToken = default)
    {
        var denuncias = await _denunciaRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Chapa)
            .Include(d => d.Provas)
            .Include(d => d.Defesas)
            .OrderByDescending(d => d.DataDenuncia)
            .ToListAsync(cancellationToken);

        return denuncias.Select(MapToDto);
    }

    public async Task<IEnumerable<DenunciaDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var denuncias = await _denunciaRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Chapa)
            .Include(d => d.Provas)
            .Include(d => d.Defesas)
            .Where(d => d.EleicaoId == eleicaoId)
            .OrderByDescending(d => d.DataDenuncia)
            .ToListAsync(cancellationToken);

        return denuncias.Select(MapToDto);
    }

    public async Task<IEnumerable<DenunciaDto>> GetByStatusAsync(StatusDenuncia status, CancellationToken cancellationToken = default)
    {
        var denuncias = await _denunciaRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Chapa)
            .Include(d => d.Provas)
            .Include(d => d.Defesas)
            .Where(d => d.Status == status)
            .OrderByDescending(d => d.DataDenuncia)
            .ToListAsync(cancellationToken);

        return denuncias.Select(MapToDto);
    }

    public async Task<IEnumerable<DenunciaDto>> GetByChapaAsync(Guid chapaId, CancellationToken cancellationToken = default)
    {
        var denuncias = await _denunciaRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Chapa)
            .Include(d => d.Provas)
            .Include(d => d.Defesas)
            .Where(d => d.ChapaId == chapaId)
            .OrderByDescending(d => d.DataDenuncia)
            .ToListAsync(cancellationToken);

        return denuncias.Select(MapToDto);
    }

    public async Task<DenunciaDto> CreateAsync(CreateDenunciaDto dto, CancellationToken cancellationToken = default)
    {
        var protocolo = await GerarProtocoloAsync(cancellationToken);

        var denuncia = new Denuncia
        {
            EleicaoId = dto.EleicaoId,
            Protocolo = protocolo,
            Tipo = dto.Tipo,
            Status = StatusDenuncia.Recebida,
            ChapaId = dto.ChapaId,
            MembroId = dto.MembroId,
            DenuncianteId = dto.Anonima ? null : dto.DenuncianteId,
            Anonima = dto.Anonima,
            Titulo = dto.Titulo,
            Descricao = dto.Descricao,
            Fundamentacao = dto.Fundamentacao,
            DataDenuncia = DateTime.UtcNow
        };

        await _denunciaRepository.AddAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(denuncia.Id, "Denuncia registrada", "Sistema", cancellationToken);

        _logger.LogInformation("Denuncia criada: {DenunciaId} - Protocolo: {Protocolo}", denuncia.Id, protocolo);

        return await GetByIdAsync(denuncia.Id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia criada");
    }

    public async Task<DenunciaDto> UpdateAsync(Guid id, UpdateDenunciaDto dto, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        if (denuncia.Status != StatusDenuncia.Recebida && denuncia.Status != StatusDenuncia.EmAnalise)
            throw new InvalidOperationException("Denuncia nao pode ser alterada neste status");

        if (dto.Titulo != null) denuncia.Titulo = dto.Titulo;
        if (dto.Descricao != null) denuncia.Descricao = dto.Descricao;
        if (dto.Fundamentacao != null) denuncia.Fundamentacao = dto.Fundamentacao;
        if (dto.PrazoDefesa.HasValue) denuncia.PrazoDefesa = dto.PrazoDefesa.Value;
        if (dto.PrazoRecurso.HasValue) denuncia.PrazoRecurso = dto.PrazoRecurso.Value;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Denuncia atualizada: {DenunciaId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task DeleteAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        if (denuncia.Status != StatusDenuncia.Recebida)
            throw new InvalidOperationException("Apenas denuncias recebidas podem ser excluidas");

        await _denunciaRepository.SoftDeleteAsync(id, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Denuncia excluida: {DenunciaId}", id);
    }

    public async Task<DenunciaDto> ReceberAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        denuncia.Status = StatusDenuncia.Recebida;
        denuncia.DataRecebimento = DateTime.UtcNow;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, "Denuncia recebida", "Sistema", cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<DenunciaDto> IniciarAnaliseAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        denuncia.Status = StatusDenuncia.EmAnalise;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, "Analise iniciada", "Sistema", cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<DenunciaDto> RegistrarAdmissibilidadeAsync(Guid id, bool admissivel, string parecer, Guid relatorId, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        denuncia.Status = admissivel ? StatusDenuncia.AdmissibilidadeAceita : StatusDenuncia.AdmissibilidadeRejeitada;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        var acao = admissivel ? "Admissibilidade aceita" : "Admissibilidade rejeitada";
        await RegistrarHistoricoAsync(id, $"{acao}: {parecer}", relatorId.ToString(), cancellationToken);

        _logger.LogInformation("Admissibilidade registrada para denuncia {DenunciaId}: {Admissivel}", id, admissivel);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<DenunciaDto> AbrirPrazoDefesaAsync(Guid id, DateTime prazoDefesa, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        denuncia.Status = StatusDenuncia.AguardandoDefesa;
        denuncia.PrazoDefesa = prazoDefesa;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Prazo de defesa aberto ate {prazoDefesa:dd/MM/yyyy}", "Sistema", cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<DenunciaDto> RegistrarDefesaAsync(Guid id, string defesa, Guid autorId, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        var defesaEntity = new DefesaDenuncia
        {
            DenunciaId = id,
            Conteudo = defesa,
            DataApresentacao = DateTime.UtcNow,
            Status = StatusDefesa.Apresentada
        };

        await _defesaRepository.AddAsync(defesaEntity, cancellationToken);

        denuncia.Status = StatusDenuncia.DefesaApresentada;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, "Defesa apresentada", autorId.ToString(), cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<DenunciaDto> EncaminharParaJulgamentoAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        denuncia.Status = StatusDenuncia.AguardandoJulgamento;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, "Encaminhada para julgamento", "Sistema", cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<DenunciaDto> RegistrarJulgamentoAsync(Guid id, StatusDenuncia decisao, string fundamentacao, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        denuncia.Status = decisao;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Julgamento: {decisao} - {fundamentacao}", "Sistema", cancellationToken);

        _logger.LogInformation("Julgamento registrado para denuncia {DenunciaId}: {Decisao}", id, decisao);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<DenunciaDto> ArquivarAsync(Guid id, string motivo, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        denuncia.Status = StatusDenuncia.Arquivada;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Arquivada: {motivo}", "Sistema", cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<DenunciaDto> AbrirPrazoRecursoAsync(Guid id, DateTime prazoRecurso, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        denuncia.Status = StatusDenuncia.AguardandoRecurso;
        denuncia.PrazoRecurso = prazoRecurso;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Prazo de recurso aberto ate {prazoRecurso:dd/MM/yyyy}", "Sistema", cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<DenunciaDto> RegistrarRecursoAsync(Guid id, string recurso, Guid autorId, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        denuncia.Status = StatusDenuncia.RecursoApresentado;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, "Recurso apresentado", autorId.ToString(), cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<ProvaDenunciaDto> AddProvaAsync(Guid denunciaId, CreateProvaDenunciaDto dto, CancellationToken cancellationToken = default)
    {
        var prova = new ProvaDenuncia
        {
            DenunciaId = denunciaId,
            Tipo = dto.Tipo,
            Descricao = dto.Descricao,
            ArquivoUrl = dto.ArquivoUrl,
            ArquivoNome = dto.ArquivoNome,
            DataEnvio = DateTime.UtcNow
        };

        await _provaRepository.AddAsync(prova, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Prova adicionada a denuncia {DenunciaId}", denunciaId);

        return new ProvaDenunciaDto
        {
            Id = prova.Id,
            DenunciaId = prova.DenunciaId,
            Tipo = prova.Tipo,
            TipoNome = prova.Tipo.ToString(),
            Descricao = prova.Descricao,
            ArquivoUrl = prova.ArquivoUrl,
            ArquivoNome = prova.ArquivoNome,
            DataEnvio = prova.DataEnvio
        };
    }

    public async Task RemoveProvaAsync(Guid denunciaId, Guid provaId, CancellationToken cancellationToken = default)
    {
        var prova = await _provaRepository.GetByIdAsync(provaId, cancellationToken)
            ?? throw new KeyNotFoundException($"Prova {provaId} nao encontrada");

        if (prova.DenunciaId != denunciaId)
            throw new InvalidOperationException("Prova nao pertence a esta denuncia");

        await _provaRepository.DeleteAsync(prova, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Prova {ProvaId} removida da denuncia {DenunciaId}", provaId, denunciaId);
    }

    public async Task<IEnumerable<ProvaDenunciaDto>> GetProvasAsync(Guid denunciaId, CancellationToken cancellationToken = default)
    {
        var provas = await _provaRepository.Query()
            .Where(p => p.DenunciaId == denunciaId)
            .OrderBy(p => p.DataEnvio)
            .ToListAsync(cancellationToken);

        return provas.Select(p => new ProvaDenunciaDto
        {
            Id = p.Id,
            DenunciaId = p.DenunciaId,
            Tipo = p.Tipo,
            TipoNome = p.Tipo.ToString(),
            Descricao = p.Descricao,
            ArquivoUrl = p.ArquivoUrl,
            ArquivoNome = p.ArquivoNome,
            DataEnvio = p.DataEnvio
        });
    }

    public async Task<int> CountByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        return await _denunciaRepository.CountAsync(d => d.EleicaoId == eleicaoId, cancellationToken);
    }

    public async Task<int> CountByStatusAsync(StatusDenuncia status, CancellationToken cancellationToken = default)
    {
        return await _denunciaRepository.CountAsync(d => d.Status == status, cancellationToken);
    }

    private async Task<string> GerarProtocoloAsync(CancellationToken cancellationToken)
    {
        var ano = DateTime.UtcNow.Year;
        var count = await _denunciaRepository.CountAsync(
            d => d.DataDenuncia.Year == ano, cancellationToken);

        return $"DEN-{ano}-{(count + 1):D5}";
    }

    private async Task RegistrarHistoricoAsync(Guid denunciaId, string descricao, string autor, CancellationToken cancellationToken)
    {
        var historico = new HistoricoDenuncia
        {
            DenunciaId = denunciaId,
            Descricao = descricao,
            DataAlteracao = DateTime.UtcNow
        };

        await _historicoRepository.AddAsync(historico, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
    }

    private static DenunciaDto MapToDto(Denuncia denuncia)
    {
        return new DenunciaDto
        {
            Id = denuncia.Id,
            EleicaoId = denuncia.EleicaoId,
            EleicaoNome = denuncia.Eleicao?.Nome ?? "",
            Protocolo = denuncia.Protocolo,
            Tipo = denuncia.Tipo,
            TipoNome = denuncia.Tipo.ToString(),
            Status = denuncia.Status,
            StatusNome = denuncia.Status.ToString(),
            ChapaId = denuncia.ChapaId,
            ChapaNome = denuncia.Chapa?.Nome,
            MembroId = denuncia.MembroId,
            MembroNome = denuncia.Membro?.Nome,
            DenuncianteId = denuncia.DenuncianteId,
            DenuncianteNome = denuncia.Denunciante?.Nome,
            Anonima = denuncia.Anonima,
            Titulo = denuncia.Titulo,
            Descricao = denuncia.Descricao,
            Fundamentacao = denuncia.Fundamentacao,
            DataDenuncia = denuncia.DataDenuncia,
            DataRecebimento = denuncia.DataRecebimento,
            PrazoDefesa = denuncia.PrazoDefesa,
            PrazoRecurso = denuncia.PrazoRecurso,
            TotalProvas = denuncia.Provas?.Count ?? 0,
            TotalDefesas = denuncia.Defesas?.Count ?? 0,
            CreatedAt = denuncia.CreatedAt
        };
    }
}
