using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Denuncias;
using CAU.Eleitoral.Application.DTOs.Auditoria;
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
    private readonly IRepository<AnaliseDenuncia> _analiseRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<DenunciaService> _logger;

    public DenunciaService(
        IRepository<Denuncia> denunciaRepository,
        IRepository<ProvaDenuncia> provaRepository,
        IRepository<DefesaDenuncia> defesaRepository,
        IRepository<HistoricoDenuncia> historicoRepository,
        IRepository<AnaliseDenuncia> analiseRepository,
        IUnitOfWork unitOfWork,
        ILogger<DenunciaService> logger)
    {
        _denunciaRepository = denunciaRepository;
        _provaRepository = provaRepository;
        _defesaRepository = defesaRepository;
        _historicoRepository = historicoRepository;
        _analiseRepository = analiseRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    #region CRUD Operations

    public async Task<DenunciaDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Chapa)
            .Include(d => d.Membro)
            .Include(d => d.Denunciante)
            .Include(d => d.Provas)
            .Include(d => d.Defesas)
            .Include(d => d.Historicos)
            .Include(d => d.Admissibilidade)
            .Include(d => d.Julgamento)
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

    public async Task<PaginatedResultDto<DenunciaListDto>> GetPaginatedAsync(FiltroDenunciaDto filtro, CancellationToken cancellationToken = default)
    {
        var query = _denunciaRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Chapa)
            .Include(d => d.Denunciante)
            .Include(d => d.Provas)
            .Include(d => d.Defesas)
            .AsQueryable();

        // Apply filters
        if (filtro.EleicaoId.HasValue)
            query = query.Where(d => d.EleicaoId == filtro.EleicaoId.Value);

        if (filtro.Status.HasValue)
            query = query.Where(d => d.Status == filtro.Status.Value);

        if (filtro.Tipo.HasValue)
            query = query.Where(d => d.Tipo == filtro.Tipo.Value);

        if (filtro.ChapaId.HasValue)
            query = query.Where(d => d.ChapaId == filtro.ChapaId.Value);

        if (filtro.DenuncianteId.HasValue)
            query = query.Where(d => d.DenuncianteId == filtro.DenuncianteId.Value);

        if (filtro.Anonima.HasValue)
            query = query.Where(d => d.Anonima == filtro.Anonima.Value);

        if (filtro.DataInicio.HasValue)
            query = query.Where(d => d.DataDenuncia >= filtro.DataInicio.Value);

        if (filtro.DataFim.HasValue)
            query = query.Where(d => d.DataDenuncia <= filtro.DataFim.Value);

        if (!string.IsNullOrWhiteSpace(filtro.Termo))
        {
            var termo = filtro.Termo.ToLower();
            query = query.Where(d =>
                d.Titulo.ToLower().Contains(termo) ||
                d.Descricao.ToLower().Contains(termo) ||
                d.Protocolo.ToLower().Contains(termo));
        }

        var totalItems = await query.CountAsync(cancellationToken);

        // Apply ordering
        query = filtro.OrdenarPor?.ToLower() switch
        {
            "protocolo" => filtro.OrdemDescendente
                ? query.OrderByDescending(d => d.Protocolo)
                : query.OrderBy(d => d.Protocolo),
            "status" => filtro.OrdemDescendente
                ? query.OrderByDescending(d => d.Status)
                : query.OrderBy(d => d.Status),
            "tipo" => filtro.OrdemDescendente
                ? query.OrderByDescending(d => d.Tipo)
                : query.OrderBy(d => d.Tipo),
            _ => filtro.OrdemDescendente
                ? query.OrderByDescending(d => d.DataDenuncia)
                : query.OrderBy(d => d.DataDenuncia)
        };

        // Apply pagination
        var items = await query
            .Skip((filtro.Pagina - 1) * filtro.TamanhoPagina)
            .Take(filtro.TamanhoPagina)
            .ToListAsync(cancellationToken);

        var totalPaginas = (int)Math.Ceiling(totalItems / (double)filtro.TamanhoPagina);

        return new PaginatedResultDto<DenunciaListDto>
        {
            Items = items.Select(MapToListDto),
            TotalItems = totalItems,
            Pagina = filtro.Pagina,
            TamanhoPagina = filtro.TamanhoPagina,
            TotalPaginas = totalPaginas,
            TemProximaPagina = filtro.Pagina < totalPaginas,
            TemPaginaAnterior = filtro.Pagina > 1
        };
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

    public async Task<IEnumerable<DenunciaDto>> GetByDenuncianteAsync(Guid denuncianteId, CancellationToken cancellationToken = default)
    {
        var denuncias = await _denunciaRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Chapa)
            .Include(d => d.Provas)
            .Include(d => d.Defesas)
            .Where(d => d.DenuncianteId == denuncianteId)
            .OrderByDescending(d => d.DataDenuncia)
            .ToListAsync(cancellationToken);

        return denuncias.Select(MapToDto);
    }

    public async Task<DenunciaDto> CreateAsync(CreateDenunciaDto dto, Guid? userId = null, CancellationToken cancellationToken = default)
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
            DenuncianteId = dto.Anonima ? null : (dto.DenuncianteId ?? userId),
            Anonima = dto.Anonima,
            Titulo = dto.Titulo,
            Descricao = dto.Descricao,
            Fundamentacao = dto.Fundamentacao,
            DataDenuncia = DateTime.UtcNow,
            DataRecebimento = DateTime.UtcNow
        };

        await _denunciaRepository.AddAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        // Add provas if provided
        if (dto.Provas?.Any() == true)
        {
            foreach (var provaDto in dto.Provas)
            {
                var prova = new ProvaDenuncia
                {
                    DenunciaId = denuncia.Id,
                    Tipo = provaDto.Tipo,
                    Descricao = provaDto.Descricao,
                    ArquivoUrl = provaDto.ArquivoUrl,
                    ArquivoNome = provaDto.ArquivoNome,
                    DataEnvio = DateTime.UtcNow
                };
                await _provaRepository.AddAsync(prova, cancellationToken);
            }
            await _unitOfWork.SaveChangesAsync(cancellationToken);
        }

        await RegistrarHistoricoAsync(denuncia.Id, "Denuncia registrada", userId, cancellationToken);

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

        if (dto.Tipo.HasValue) denuncia.Tipo = dto.Tipo.Value;
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

    #endregion

    #region Workflow Operations

    public async Task<DenunciaDto> ReceberAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        denuncia.Status = StatusDenuncia.Recebida;
        denuncia.DataRecebimento = DateTime.UtcNow;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, "Denuncia recebida", null, cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<DenunciaDto> IniciarAnaliseAsync(Guid id, Guid analistaId, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        if (denuncia.Status != StatusDenuncia.Recebida)
            throw new InvalidOperationException("Apenas denuncias recebidas podem ser analisadas");

        denuncia.Status = StatusDenuncia.EmAnalise;

        // Create analysis record
        var analise = new AnaliseDenuncia
        {
            DenunciaId = id,
            AnalistaId = analistaId,
            Status = StatusAnaliseDenuncia.EmAndamento,
            DataInicio = DateTime.UtcNow
        };

        await _analiseRepository.AddAsync(analise, cancellationToken);
        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, "Analise iniciada", analistaId, cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<AnaliseDenunciaDto> ConcluirAnaliseAsync(Guid id, ConcluirAnaliseDto dto, Guid analistaId, CancellationToken cancellationToken = default)
    {
        var analise = await _analiseRepository.Query()
            .Where(a => a.DenunciaId == id && a.Status == StatusAnaliseDenuncia.EmAndamento)
            .OrderByDescending(a => a.DataInicio)
            .FirstOrDefaultAsync(cancellationToken)
            ?? throw new KeyNotFoundException($"Analise em andamento nao encontrada para denuncia {id}");

        analise.Status = StatusAnaliseDenuncia.Concluida;
        analise.Parecer = dto.Parecer;
        analise.DataConclusao = DateTime.UtcNow;

        await _analiseRepository.UpdateAsync(analise, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Analise concluida: {dto.Parecer}", analistaId, cancellationToken);

        return new AnaliseDenunciaDto
        {
            Id = analise.Id,
            DenunciaId = analise.DenunciaId,
            Status = analise.Status,
            StatusNome = analise.Status.ToString(),
            Parecer = analise.Parecer,
            AnalistaId = analise.AnalistaId.GetValueOrDefault(),
            DataInicio = analise.DataInicio,
            DataConclusao = analise.DataConclusao
        };
    }

    public async Task<DenunciaDto> RegistrarAdmissibilidadeAsync(Guid id, bool admissivel, string parecer, Guid relatorId, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        if (denuncia.Status != StatusDenuncia.EmAnalise)
            throw new InvalidOperationException("Denuncia deve estar em analise para registrar admissibilidade");

        denuncia.Status = admissivel ? StatusDenuncia.AdmissibilidadeAceita : StatusDenuncia.AdmissibilidadeRejeitada;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        var acao = admissivel ? "Admissibilidade aceita" : "Admissibilidade rejeitada";
        await RegistrarHistoricoAsync(id, $"{acao}: {parecer}", relatorId, cancellationToken);

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

        await RegistrarHistoricoAsync(id, $"Prazo de defesa aberto ate {prazoDefesa:dd/MM/yyyy}", null, cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<DenunciaDto> SolicitarDefesaAsync(Guid id, int prazoEmDias, CancellationToken cancellationToken = default)
    {
        var prazoDefesa = DateTime.UtcNow.AddDays(prazoEmDias);
        return await AbrirPrazoDefesaAsync(id, prazoDefesa, cancellationToken);
    }

    public async Task<DenunciaDto> RegistrarDefesaAsync(Guid id, string defesa, Guid autorId, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        if (denuncia.Status != StatusDenuncia.AguardandoDefesa && denuncia.Status != StatusDenuncia.AdmissibilidadeAceita)
            throw new InvalidOperationException("Denuncia nao esta aguardando defesa");

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

        await RegistrarHistoricoAsync(id, "Defesa apresentada", autorId, cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<DenunciaDto> ApresentarDefesaAsync(Guid id, CreateDefesaDto dto, Guid autorId, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        if (denuncia.Status != StatusDenuncia.AguardandoDefesa && denuncia.Status != StatusDenuncia.AdmissibilidadeAceita)
            throw new InvalidOperationException("Denuncia nao esta aguardando defesa");

        // Check if within deadline
        if (denuncia.PrazoDefesa.HasValue && DateTime.UtcNow > denuncia.PrazoDefesa.Value)
        {
            var defesaIntempestiva = new DefesaDenuncia
            {
                DenunciaId = id,
                Conteudo = dto.Conteudo,
                DataApresentacao = DateTime.UtcNow,
                Status = StatusDefesa.Intempestiva
            };
            await _defesaRepository.AddAsync(defesaIntempestiva, cancellationToken);
            await _unitOfWork.SaveChangesAsync(cancellationToken);
            await RegistrarHistoricoAsync(id, "Defesa intempestiva apresentada", autorId, cancellationToken);
        }
        else
        {
            var defesaEntity = new DefesaDenuncia
            {
                DenunciaId = id,
                Conteudo = dto.Conteudo,
                DataApresentacao = DateTime.UtcNow,
                Status = StatusDefesa.Apresentada
            };
            await _defesaRepository.AddAsync(defesaEntity, cancellationToken);

            denuncia.Status = StatusDenuncia.DefesaApresentada;
            await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
            await _unitOfWork.SaveChangesAsync(cancellationToken);
            await RegistrarHistoricoAsync(id, "Defesa apresentada", autorId, cancellationToken);
        }

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<DenunciaDto> EncaminharParaJulgamentoAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        var allowedStatuses = new[]
        {
            StatusDenuncia.DefesaApresentada,
            StatusDenuncia.AguardandoDefesa,
            StatusDenuncia.AdmissibilidadeAceita
        };

        if (!allowedStatuses.Contains(denuncia.Status))
            throw new InvalidOperationException("Denuncia nao pode ser encaminhada para julgamento neste status");

        denuncia.Status = StatusDenuncia.AguardandoJulgamento;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, "Encaminhada para julgamento", null, cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<DenunciaDto> RegistrarJulgamentoAsync(Guid id, StatusDenuncia decisao, string fundamentacao, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        if (denuncia.Status != StatusDenuncia.AguardandoJulgamento)
            throw new InvalidOperationException("Denuncia deve estar aguardando julgamento");

        var validDecisions = new[]
        {
            StatusDenuncia.Procedente,
            StatusDenuncia.Improcedente,
            StatusDenuncia.ParcialmenteProcedente,
            StatusDenuncia.Julgada
        };

        if (!validDecisions.Contains(decisao))
            throw new InvalidOperationException("Decisao invalida");

        denuncia.Status = decisao;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Julgamento: {decisao} - {fundamentacao}", null, cancellationToken);

        _logger.LogInformation("Julgamento registrado para denuncia {DenunciaId}: {Decisao}", id, decisao);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<DenunciaDto> JulgarAsync(Guid id, JulgarDenunciaDto dto, Guid julgadorId, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        if (denuncia.Status != StatusDenuncia.AguardandoJulgamento)
            throw new InvalidOperationException("Denuncia deve estar aguardando julgamento");

        var validDecisions = new[]
        {
            StatusDenuncia.Procedente,
            StatusDenuncia.Improcedente,
            StatusDenuncia.ParcialmenteProcedente,
            StatusDenuncia.Julgada
        };

        if (!validDecisions.Contains(dto.Resultado))
            throw new InvalidOperationException("Decisao invalida");

        denuncia.Status = dto.Resultado;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Julgamento: {dto.Resultado} - {dto.Decisao}", julgadorId, cancellationToken);

        _logger.LogInformation("Julgamento registrado para denuncia {DenunciaId}: {Decisao}", id, dto.Resultado);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    public async Task<DenunciaDto> ArquivarAsync(Guid id, string motivo, Guid? userId = null, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        denuncia.Status = StatusDenuncia.Arquivada;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Arquivada: {motivo}", userId, cancellationToken);

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

        await RegistrarHistoricoAsync(id, $"Prazo de recurso aberto ate {prazoRecurso:dd/MM/yyyy}", null, cancellationToken);

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

        await RegistrarHistoricoAsync(id, "Recurso apresentado", autorId, cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    #endregion

    #region Provas

    public async Task<ProvaDenunciaDto> AddProvaAsync(Guid denunciaId, CreateProvaDenunciaDto dto, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(denunciaId, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {denunciaId} nao encontrada");

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

    #endregion

    #region Defesas

    public async Task<DefesaDenunciaDto> AddDefesaAsync(Guid denunciaId, CreateDefesaDto dto, Guid autorId, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(denunciaId, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {denunciaId} nao encontrada");

        var defesa = new DefesaDenuncia
        {
            DenunciaId = denunciaId,
            Conteudo = dto.Conteudo,
            DataApresentacao = DateTime.UtcNow,
            Status = StatusDefesa.Apresentada
        };

        await _defesaRepository.AddAsync(defesa, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Defesa adicionada a denuncia {DenunciaId}", denunciaId);

        return new DefesaDenunciaDto
        {
            Id = defesa.Id,
            DenunciaId = defesa.DenunciaId,
            Conteudo = defesa.Conteudo,
            Status = defesa.Status,
            StatusNome = defesa.Status.ToString(),
            DataApresentacao = defesa.DataApresentacao
        };
    }

    public async Task<IEnumerable<DefesaDenunciaDto>> GetDefesasAsync(Guid denunciaId, CancellationToken cancellationToken = default)
    {
        var defesas = await _defesaRepository.Query()
            .Where(d => d.DenunciaId == denunciaId)
            .OrderBy(d => d.DataApresentacao)
            .ToListAsync(cancellationToken);

        return defesas.Select(d => new DefesaDenunciaDto
        {
            Id = d.Id,
            DenunciaId = d.DenunciaId,
            Conteudo = d.Conteudo,
            Status = d.Status,
            StatusNome = d.Status.ToString(),
            DataApresentacao = d.DataApresentacao
        });
    }

    #endregion

    #region Historico

    public async Task<IEnumerable<HistoricoDenunciaDto>> GetHistoricoAsync(Guid denunciaId, CancellationToken cancellationToken = default)
    {
        var historicos = await _historicoRepository.Query()
            .Where(h => h.DenunciaId == denunciaId)
            .OrderByDescending(h => h.DataAlteracao)
            .ToListAsync(cancellationToken);

        return historicos.Select(h => new HistoricoDenunciaDto
        {
            Id = h.Id,
            DenunciaId = h.DenunciaId,
            Descricao = h.Descricao,
            DataAlteracao = h.DataAlteracao
        });
    }

    #endregion

    #region Statistics

    public async Task<int> CountByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        return await _denunciaRepository.CountAsync(d => d.EleicaoId == eleicaoId, cancellationToken);
    }

    public async Task<int> CountByStatusAsync(StatusDenuncia status, CancellationToken cancellationToken = default)
    {
        return await _denunciaRepository.CountAsync(d => d.Status == status, cancellationToken);
    }

    public async Task<DenunciaEstatisticasDto> GetEstatisticasAsync(Guid? eleicaoId = null, CancellationToken cancellationToken = default)
    {
        var query = _denunciaRepository.Query();

        if (eleicaoId.HasValue)
            query = query.Where(d => d.EleicaoId == eleicaoId.Value);

        var denuncias = await query.ToListAsync(cancellationToken);

        var porTipo = denuncias
            .GroupBy(d => d.Tipo.ToString())
            .ToDictionary(g => g.Key, g => g.Count());

        var porStatus = denuncias
            .GroupBy(d => d.Status.ToString())
            .ToDictionary(g => g.Key, g => g.Count());

        return new DenunciaEstatisticasDto
        {
            Total = denuncias.Count,
            Recebidas = denuncias.Count(d => d.Status == StatusDenuncia.Recebida),
            EmAnalise = denuncias.Count(d => d.Status == StatusDenuncia.EmAnalise),
            AguardandoDefesa = denuncias.Count(d => d.Status == StatusDenuncia.AguardandoDefesa),
            AguardandoJulgamento = denuncias.Count(d => d.Status == StatusDenuncia.AguardandoJulgamento),
            Julgadas = denuncias.Count(d => d.Status == StatusDenuncia.Julgada),
            Arquivadas = denuncias.Count(d => d.Status == StatusDenuncia.Arquivada),
            Procedentes = denuncias.Count(d => d.Status == StatusDenuncia.Procedente),
            Improcedentes = denuncias.Count(d => d.Status == StatusDenuncia.Improcedente),
            PorTipo = porTipo,
            PorStatus = porStatus
        };
    }

    #endregion

    #region Private Methods

    private async Task<string> GerarProtocoloAsync(CancellationToken cancellationToken)
    {
        var ano = DateTime.UtcNow.Year;
        var count = await _denunciaRepository.CountAsync(
            d => d.DataDenuncia.Year == ano, cancellationToken);

        return $"DEN-{ano}-{(count + 1):D5}";
    }

    private async Task RegistrarHistoricoAsync(Guid denunciaId, string descricao, Guid? usuarioId, CancellationToken cancellationToken)
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
            Provas = denuncia.Provas?.Select(p => new ProvaDenunciaDto
            {
                Id = p.Id,
                DenunciaId = p.DenunciaId,
                Tipo = p.Tipo,
                TipoNome = p.Tipo.ToString(),
                Descricao = p.Descricao,
                ArquivoUrl = p.ArquivoUrl,
                ArquivoNome = p.ArquivoNome,
                DataEnvio = p.DataEnvio
            }).ToList() ?? new(),
            Defesas = denuncia.Defesas?.Select(d => new DefesaDenunciaDto
            {
                Id = d.Id,
                DenunciaId = d.DenunciaId,
                Conteudo = d.Conteudo,
                Status = d.Status,
                StatusNome = d.Status.ToString(),
                DataApresentacao = d.DataApresentacao
            }).ToList() ?? new(),
            Historicos = denuncia.Historicos?.Select(h => new HistoricoDenunciaDto
            {
                Id = h.Id,
                DenunciaId = h.DenunciaId,
                Descricao = h.Descricao,
                DataAlteracao = h.DataAlteracao
            }).ToList() ?? new(),
            CreatedAt = denuncia.CreatedAt,
            UpdatedAt = denuncia.UpdatedAt
        };
    }

    private static DenunciaListDto MapToListDto(Denuncia denuncia)
    {
        return new DenunciaListDto
        {
            Id = denuncia.Id,
            Protocolo = denuncia.Protocolo,
            EleicaoNome = denuncia.Eleicao?.Nome ?? "",
            Tipo = denuncia.Tipo,
            TipoNome = denuncia.Tipo.ToString(),
            Status = denuncia.Status,
            StatusNome = denuncia.Status.ToString(),
            ChapaNome = denuncia.Chapa?.Nome,
            DenuncianteNome = denuncia.Denunciante?.Nome,
            Anonima = denuncia.Anonima,
            Titulo = denuncia.Titulo,
            DataDenuncia = denuncia.DataDenuncia,
            TotalProvas = denuncia.Provas?.Count ?? 0,
            TotalDefesas = denuncia.Defesas?.Count ?? 0
        };
    }

    #endregion
}
