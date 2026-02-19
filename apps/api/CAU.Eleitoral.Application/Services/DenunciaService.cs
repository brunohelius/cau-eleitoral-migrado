using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Denuncias;
using CAU.Eleitoral.Application.DTOs.Auditoria;
using CAU.Eleitoral.Application.DTOs.Notificacoes;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Denuncias;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Usuarios;
using CAU.Eleitoral.Domain.Entities.Chapas;
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
    private readonly IRepository<AdmissibilidadeDenuncia> _admissibilidadeRepository;
    private readonly IRepository<JulgamentoDenuncia> _julgamentoRepository;
    private readonly IRepository<ParecerDenuncia> _parecerRepository;
    private readonly IRepository<RecursoDenuncia> _recursoRepository;
    private readonly IRepository<ContrarrazoesRecursoDenuncia> _contraRazoesRepository;
    private readonly IRepository<JulgamentoRecursoDenuncia> _julgamentoRecursoRepository;
    private readonly IRepository<Usuario> _usuarioRepository;
    private readonly IRepository<Profissional> _profissionalRepository;
    private readonly IRepository<MembroChapa> _membroChapaRepository;
    private readonly IRepository<Eleicao> _eleicaoRepository;
    private readonly ICalendarioService _calendarioService;
    private readonly INotificacaoService? _notificacaoService;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<DenunciaService> _logger;

    public DenunciaService(
        IRepository<Denuncia> denunciaRepository,
        IRepository<ProvaDenuncia> provaRepository,
        IRepository<DefesaDenuncia> defesaRepository,
        IRepository<HistoricoDenuncia> historicoRepository,
        IRepository<AnaliseDenuncia> analiseRepository,
        IRepository<AdmissibilidadeDenuncia> admissibilidadeRepository,
        IRepository<JulgamentoDenuncia> julgamentoRepository,
        IRepository<ParecerDenuncia> parecerRepository,
        IRepository<RecursoDenuncia> recursoRepository,
        IRepository<ContrarrazoesRecursoDenuncia> contraRazoesRepository,
        IRepository<JulgamentoRecursoDenuncia> julgamentoRecursoRepository,
        IRepository<Usuario> usuarioRepository,
        IRepository<Profissional> profissionalRepository,
        IRepository<MembroChapa> membroChapaRepository,
        IRepository<Eleicao> eleicaoRepository,
        ICalendarioService calendarioService,
        IUnitOfWork unitOfWork,
        ILogger<DenunciaService> logger,
        INotificacaoService? notificacaoService = null)
    {
        _denunciaRepository = denunciaRepository;
        _provaRepository = provaRepository;
        _defesaRepository = defesaRepository;
        _historicoRepository = historicoRepository;
        _analiseRepository = analiseRepository;
        _admissibilidadeRepository = admissibilidadeRepository;
        _julgamentoRepository = julgamentoRepository;
        _parecerRepository = parecerRepository;
        _recursoRepository = recursoRepository;
        _contraRazoesRepository = contraRazoesRepository;
        _julgamentoRecursoRepository = julgamentoRecursoRepository;
        _usuarioRepository = usuarioRepository;
        _profissionalRepository = profissionalRepository;
        _membroChapaRepository = membroChapaRepository;
        _eleicaoRepository = eleicaoRepository;
        _calendarioService = calendarioService;
        _unitOfWork = unitOfWork;
        _logger = logger;
        _notificacaoService = notificacaoService;
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
        // Validar se a eleicao esta ativa (denuncias podem ser feitas durante qualquer periodo ativo da eleicao)
        var eleicao = await _eleicaoRepository.GetByIdAsync(dto.EleicaoId, cancellationToken);
        if (eleicao == null)
            throw new InvalidOperationException("Eleicao nao encontrada");

        if (eleicao.Status != StatusEleicao.EmAndamento && eleicao.Status != StatusEleicao.ApuracaoEmAndamento)
            throw new InvalidOperationException("Denuncias so podem ser registradas durante uma eleicao em andamento");

        // Validar que estamos em algum periodo ativo (inscricao, impugnacao, defesa, propaganda, votacao, apuracao ou resultado)
        var periodosPermitidos = new[]
        {
            TipoCalendario.Inscricao,
            TipoCalendario.Impugnacao,
            TipoCalendario.Defesa,
            TipoCalendario.Propaganda,
            TipoCalendario.Votacao,
            TipoCalendario.Apuracao,
            TipoCalendario.Resultado
        };

        var estaEmPeriodoAtivo = await _calendarioService.IsWithinAnyPeriodAsync(dto.EleicaoId, periodosPermitidos, cancellationToken);
        if (!estaEmPeriodoAtivo)
        {
            var periodoAtual = await _calendarioService.GetPeriodoAtualAsync(dto.EleicaoId, cancellationToken);
            var proximoPeriodo = await _calendarioService.GetProximoPeriodoAsync(dto.EleicaoId, cancellationToken);

            string mensagem = "Denuncias so podem ser registradas durante o periodo ativo da eleicao.";
            if (proximoPeriodo != null)
                mensagem += $" O proximo periodo ({proximoPeriodo.TipoNome}) inicia em {proximoPeriodo.DataInicio:dd/MM/yyyy}.";

            throw new InvalidOperationException(mensagem);
        }

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

        // Validar periodo de defesa do calendario (se houver)
        var estaEmPeriodoDefesa = await _calendarioService.IsWithinPeriodAsync(
            denuncia.EleicaoId,
            TipoCalendario.Defesa,
            cancellationToken);

        // Se nao esta no periodo de defesa do calendario E nao tem prazo individual definido, bloquear
        if (!estaEmPeriodoDefesa && !denuncia.PrazoDefesa.HasValue)
        {
            var validacao = await _calendarioService.ValidarPeriodoAsync(
                denuncia.EleicaoId,
                new[] { TipoCalendario.Defesa },
                "apresentar defesa",
                cancellationToken);

            if (!validacao.IsValid)
                throw new InvalidOperationException(validacao.Message);
        }

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

        // Verificar se o periodo de defesa ja passou (necessario para encaminhar para julgamento)
        var periodoDefesaPassou = await _calendarioService.PeriodoJaPassouAsync(
            denuncia.EleicaoId,
            TipoCalendario.Defesa,
            cancellationToken);

        // Se o periodo de defesa ainda esta ativo e a denuncia nao teve defesa apresentada, alertar
        if (!periodoDefesaPassou && denuncia.Status == StatusDenuncia.AguardandoDefesa)
        {
            _logger.LogWarning(
                "Denuncia {DenunciaId} sendo encaminhada para julgamento durante periodo de defesa ainda ativo",
                id);
        }

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

        // Validar periodo de julgamento
        var validacao = await _calendarioService.ValidarPeriodoAsync(
            denuncia.EleicaoId,
            new[] { TipoCalendario.Julgamento },
            "julgar denuncia",
            cancellationToken);

        if (!validacao.IsValid)
            throw new InvalidOperationException(validacao.Message);

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

    #region Phase 1: Admissibilidade (Admissibility) - Extended

    /// <summary>
    /// Registra admissibilidade com validacao completa de requisitos formais
    /// </summary>
    public async Task<DenunciaDto> RegistrarAdmissibilidadeAsync(Guid id, RegistrarAdmissibilidadeCompletoDto dto, Guid analistaId, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Provas)
            .FirstOrDefaultAsync(d => d.Id == id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        if (denuncia.Status != StatusDenuncia.EmAnalise)
            throw new InvalidOperationException("Denuncia deve estar em analise para registrar admissibilidade");

        // Validate formal requirements if provided
        if (dto.RequisitosFormais != null && !dto.Admissivel)
        {
            var requisitos = dto.RequisitosFormais;
            if (!requisitos.DentroPrazo)
                _logger.LogWarning("Denuncia {DenunciaId} rejeitada por estar fora do prazo", id);
        }

        // Create admissibilidade record
        var admissibilidade = new AdmissibilidadeDenuncia
        {
            DenunciaId = id,
            AnalistId = analistaId,
            Admitida = dto.Admissivel,
            Fundamentacao = dto.Fundamentacao ?? dto.Parecer,
            Observacao = dto.RequisitosFormais?.Observacoes,
            DataAnalise = DateTime.UtcNow
        };

        await _admissibilidadeRepository.AddAsync(admissibilidade, cancellationToken);

        denuncia.Status = dto.Admissivel ? StatusDenuncia.AdmissibilidadeAceita : StatusDenuncia.AdmissibilidadeRejeitada;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        var acao = dto.Admissivel ? "Admissibilidade aceita" : "Admissibilidade rejeitada";
        await RegistrarHistoricoAsync(id, $"{acao}: {dto.Parecer}", analistaId, cancellationToken);

        _logger.LogInformation("Admissibilidade registrada para denuncia {DenunciaId}: {Admissivel}", id, dto.Admissivel);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    /// <summary>
    /// Valida requisitos formais da denuncia
    /// </summary>
    public async Task<RequisitosFormaisDto> ValidarRequisitosFormaisAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Provas)
            .Include(d => d.Chapa)
            .Include(d => d.Membro)
            .FirstOrDefaultAsync(d => d.Id == id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        var eleicao = denuncia.Eleicao;
        var dentroPrazo = eleicao != null &&
            (eleicao.Status == StatusEleicao.EmAndamento || eleicao.Status == StatusEleicao.ApuracaoEmAndamento);

        var temIdentificacaoDenunciado = denuncia.ChapaId.HasValue || denuncia.MembroId.HasValue;

        return new RequisitosFormaisDto
        {
            TemTitulo = !string.IsNullOrWhiteSpace(denuncia.Titulo),
            TemDescricao = !string.IsNullOrWhiteSpace(denuncia.Descricao) && denuncia.Descricao.Length >= 20,
            TemFundamentacao = !string.IsNullOrWhiteSpace(denuncia.Fundamentacao),
            TemProvas = denuncia.Provas?.Any() == true,
            IdentificacaoDenunciado = temIdentificacaoDenunciado,
            DentroPrazo = dentroPrazo,
            EleicaoValida = eleicao != null && eleicao.Status != StatusEleicao.Cancelada,
            Observacoes = null
        };
    }

    #endregion

    #region Phase 2: Defesa (Defense) - Extended

    /// <summary>
    /// Solicita defesa com configuracoes completas e notificacoes
    /// </summary>
    public async Task<DenunciaDto> SolicitarDefesaAsync(Guid id, SolicitarDefesaCompletoDto dto, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.Query()
            .Include(d => d.Chapa)
            .Include(d => d.Membro)
            .FirstOrDefaultAsync(d => d.Id == id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        if (denuncia.Status != StatusDenuncia.AdmissibilidadeAceita)
            throw new InvalidOperationException("Denuncia deve ter admissibilidade aceita para solicitar defesa");

        var prazoDefesa = DateTime.UtcNow.AddDays(dto.PrazoEmDias);
        denuncia.Status = StatusDenuncia.AguardandoDefesa;
        denuncia.PrazoDefesa = prazoDefesa;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Prazo de defesa aberto ate {prazoDefesa:dd/MM/yyyy}. {dto.Observacoes ?? ""}", null, cancellationToken);

        if (_notificacaoService != null && (dto.NotificarPorEmail || dto.NotificarNoSistema))
        {
            var usuarioIds = new HashSet<Guid>();

            if (denuncia.MembroId.HasValue)
            {
                var membroAcusado = await _membroChapaRepository.Query()
                    .Include(m => m.Profissional)
                    .FirstOrDefaultAsync(m => m.Id == denuncia.MembroId.Value, cancellationToken);

                if (membroAcusado?.Profissional?.UsuarioId is Guid membroUsuarioId)
                {
                    usuarioIds.Add(membroUsuarioId);
                }
            }

            if (denuncia.ChapaId.HasValue)
            {
                var chapaUsuarioIds = await _membroChapaRepository.Query()
                    .Include(m => m.Profissional)
                    .Where(m => m.ChapaId == denuncia.ChapaId.Value)
                    .Where(m => m.Profissional != null && m.Profissional.UsuarioId.HasValue)
                    .Where(m => m.Status != StatusMembroChapa.Substituido && m.Status != StatusMembroChapa.Recusado)
                    .Select(m => m.Profissional!.UsuarioId!.Value)
                    .Distinct()
                    .ToListAsync(cancellationToken);

                foreach (var usuarioId in chapaUsuarioIds)
                {
                    usuarioIds.Add(usuarioId);
                }
            }

            if (usuarioIds.Count > 0)
            {
                var titulo = "Prazo para defesa de denuncia";
                var mensagem = $"Foi aberto prazo de defesa para a denuncia {denuncia.Protocolo} ate {prazoDefesa:dd/MM/yyyy}.";
                var link = $"/denuncias/{denuncia.Id}";

                if (dto.NotificarNoSistema)
                {
                    foreach (var usuarioId in usuarioIds)
                    {
                        try
                        {
                            await _notificacaoService.EnviarAsync(new EnviarNotificacaoDto
                            {
                                UsuarioId = usuarioId,
                                Tipo = TipoNotificacao.Denuncia,
                                Canal = CanalNotificacao.InApp,
                                Titulo = titulo,
                                Mensagem = mensagem,
                                Link = link
                            }, cancellationToken);
                        }
                        catch (Exception ex)
                        {
                            _logger.LogWarning(ex, "Falha ao enviar notificacao in-app para usuario {UsuarioId}", usuarioId);
                        }
                    }
                }

                if (dto.NotificarPorEmail)
                {
                    var emails = await _usuarioRepository.Query()
                        .Where(u => usuarioIds.Contains(u.Id))
                        .Where(u => !string.IsNullOrWhiteSpace(u.Email))
                        .Select(u => u.Email!)
                        .Distinct()
                        .ToListAsync(cancellationToken);

                    foreach (var email in emails)
                    {
                        try
                        {
                            await _notificacaoService.EnviarEmailAsync(email, titulo, mensagem, cancellationToken);
                        }
                        catch (Exception ex)
                        {
                            _logger.LogWarning(ex, "Falha ao enviar email de notificacao para {Email}", email);
                        }
                    }
                }

                _logger.LogInformation("Notificacao de prazo de defesa enviada para denuncia {DenunciaId} ({Destinatarios} destinatarios)",
                    id, usuarioIds.Count);
            }
            else
            {
                _logger.LogWarning("Nenhum usuario vinculado encontrado para notificacao de defesa da denuncia {DenunciaId}", id);
            }
        }

        _logger.LogInformation("Prazo de defesa solicitado para denuncia {DenunciaId}: {PrazoDias} dias", id, dto.PrazoEmDias);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    /// <summary>
    /// Registra defesa com documentos anexos e validacao de prazo
    /// </summary>
    public async Task<DenunciaDto> RegistrarDefesaAsync(Guid id, RegistrarDefesaDto dto, Guid autorId, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        if (denuncia.Status != StatusDenuncia.AguardandoDefesa && denuncia.Status != StatusDenuncia.AdmissibilidadeAceita)
            throw new InvalidOperationException("Denuncia nao esta aguardando defesa");

        // Check if within deadline
        var isIntempestiva = denuncia.PrazoDefesa.HasValue && DateTime.UtcNow > denuncia.PrazoDefesa.Value;
        var prazoLimite = denuncia.PrazoDefesa ?? DateTime.UtcNow.AddDays(5);

        var defesaEntity = new DefesaDenuncia
        {
            DenunciaId = id,
            ChapaId = dto.ChapaId,
            MembroId = dto.MembroId,
            Conteudo = dto.Conteudo,
            Fundamentacao = dto.Fundamentacao,
            DataApresentacao = DateTime.UtcNow,
            PrazoLimite = prazoLimite,
            Tempestiva = !isIntempestiva,
            Status = isIntempestiva ? StatusDefesa.Intempestiva : StatusDefesa.Apresentada
        };

        await _defesaRepository.AddAsync(defesaEntity, cancellationToken);

        if (!isIntempestiva)
        {
            denuncia.Status = StatusDenuncia.DefesaApresentada;
            await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        }

        await _unitOfWork.SaveChangesAsync(cancellationToken);

        var statusDefesa = isIntempestiva ? "Defesa intempestiva apresentada" : "Defesa apresentada";
        await RegistrarHistoricoAsync(id, statusDefesa, autorId, cancellationToken);

        _logger.LogInformation("Defesa registrada para denuncia {DenunciaId}: Tempestiva={Tempestiva}", id, !isIntempestiva);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    #endregion

    #region Phase 3: Julgamento (Judgment) - Extended

    /// <summary>
    /// Atribui relator a denuncia com validacao de impedimento
    /// </summary>
    public async Task<DenunciaDto> AtribuirRelatorAsync(Guid id, Guid relatorId, string? justificativa = null, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.Query()
            .Include(d => d.Chapa)
            .Include(d => d.Membro)
            .Include(d => d.Denunciante)
            .FirstOrDefaultAsync(d => d.Id == id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        // Check for impedimento
        var impedimento = await VerificarImpedimentoRelatorAsync(id, relatorId, cancellationToken);
        if (impedimento.TemImpedimento)
        {
            var motivos = string.Join(", ", impedimento.Motivos);
            throw new InvalidOperationException($"Relator possui impedimento: {motivos}");
        }

        // Create parecer record to track relator assignment
        var parecerExistente = await _parecerRepository.Query()
            .Where(p => p.DenunciaId == id && p.Tipo == TipoParecerDenuncia.Relator && p.Status != StatusParecerDenuncia.Rejeitado)
            .FirstOrDefaultAsync(cancellationToken);

        if (parecerExistente != null)
        {
            throw new InvalidOperationException("Ja existe um relator atribuido para esta denuncia");
        }

        var numero = await GerarNumeroParecerAsync(cancellationToken);
        var parecer = new ParecerDenuncia
        {
            DenunciaId = id,
            Tipo = TipoParecerDenuncia.Relator,
            Status = StatusParecerDenuncia.Elaboracao,
            PareceristaId = relatorId,
            Numero = numero,
            Assunto = $"Relatoria da Denuncia {denuncia.Protocolo}",
            Ementa = "Parecer do Relator - Aguardando elaboracao",
            Conteudo = justificativa ?? "Relator atribuido",
            DataElaboracao = DateTime.UtcNow
        };

        await _parecerRepository.AddAsync(parecer, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Relator atribuido. {justificativa ?? ""}", relatorId, cancellationToken);

        _logger.LogInformation("Relator {RelatorId} atribuido a denuncia {DenunciaId}", relatorId, id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    /// <summary>
    /// Atribui relator usando DTO
    /// </summary>
    public async Task<DenunciaDto> AtribuirRelatorAsync(Guid id, AtribuirRelatorDto dto, CancellationToken cancellationToken = default)
    {
        return await AtribuirRelatorAsync(id, dto.RelatorId, dto.Justificativa, cancellationToken);
    }

    /// <summary>
    /// Verifica se existe impedimento para o relator
    /// </summary>
    public async Task<ImpedimentoResultadoDto> VerificarImpedimentoRelatorAsync(Guid denunciaId, Guid relatorId, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.Query()
            .Include(d => d.Chapa)
                .ThenInclude(c => c!.Membros)
            .Include(d => d.Membro)
            .Include(d => d.Denunciante)
            .FirstOrDefaultAsync(d => d.Id == denunciaId, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {denunciaId} nao encontrada");

        var motivos = new List<string>();

        // Get profissional linked to relator usuario
        var relatorProfissional = await _profissionalRepository.Query()
            .FirstOrDefaultAsync(p => p.UsuarioId == relatorId, cancellationToken);

        // Check if relator is the denunciante
        if (relatorProfissional != null && denuncia.DenuncianteId == relatorProfissional.Id)
        {
            motivos.Add("Relator eh o denunciante");
        }

        // Check if relator is member of the accused chapa
        if (denuncia.Chapa?.Membros != null && relatorProfissional != null)
        {
            var isMembroDaChapa = denuncia.Chapa.Membros.Any(m => m.ProfissionalId == relatorProfissional.Id);
            if (isMembroDaChapa)
            {
                motivos.Add("Relator eh membro da chapa denunciada");
            }
        }

        // Check if relator is the accused member
        if (denuncia.MembroId.HasValue && relatorProfissional != null)
        {
            var membroAcusado = await _membroChapaRepository.GetByIdAsync(denuncia.MembroId.Value, cancellationToken);
            if (membroAcusado?.ProfissionalId == relatorProfissional.Id)
            {
                motivos.Add("Relator eh o membro denunciado");
            }
        }

        return new ImpedimentoResultadoDto
        {
            TemImpedimento = motivos.Any(),
            Motivos = motivos,
            PodeSerRelator = !motivos.Any()
        };
    }

    /// <summary>
    /// Registra parecer do relator com recomendacao
    /// </summary>
    public async Task<ParecerResultadoDto> RegistrarParecerAsync(Guid id, RegistrarParecerDto dto, Guid pareceristaId, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        // Find existing parecer or create new one
        var parecerExistente = await _parecerRepository.Query()
            .Where(p => p.DenunciaId == id && p.PareceristaId == pareceristaId && p.Status == StatusParecerDenuncia.Elaboracao)
            .FirstOrDefaultAsync(cancellationToken);

        ParecerDenuncia parecer;
        if (parecerExistente != null)
        {
            parecer = parecerExistente;
            parecer.Tipo = dto.Tipo;
            parecer.Ementa = dto.Ementa;
            parecer.Conteudo = dto.Conteudo;
            parecer.Fundamentacao = dto.Fundamentacao;
            parecer.Conclusao = dto.Conclusao;
            parecer.Favoravel = dto.Recomendacao == TipoVotoJulgamento.Procedente;
            parecer.Recomendacao = dto.RecomendacaoTexto ?? dto.Recomendacao.ToString();
            parecer.Status = StatusParecerDenuncia.Aprovado;
            parecer.DataAprovacao = DateTime.UtcNow;

            await _parecerRepository.UpdateAsync(parecer, cancellationToken);
        }
        else
        {
            var numero = await GerarNumeroParecerAsync(cancellationToken);
            parecer = new ParecerDenuncia
            {
                DenunciaId = id,
                Tipo = dto.Tipo,
                Status = StatusParecerDenuncia.Aprovado,
                PareceristaId = pareceristaId,
                Numero = numero,
                Assunto = $"Parecer - Denuncia {denuncia.Protocolo}",
                Ementa = dto.Ementa,
                Conteudo = dto.Conteudo,
                Fundamentacao = dto.Fundamentacao,
                Conclusao = dto.Conclusao,
                Favoravel = dto.Recomendacao == TipoVotoJulgamento.Procedente,
                Recomendacao = dto.RecomendacaoTexto ?? dto.Recomendacao.ToString(),
                DataElaboracao = DateTime.UtcNow,
                DataAprovacao = DateTime.UtcNow
            };

            await _parecerRepository.AddAsync(parecer, cancellationToken);
        }

        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Parecer registrado: Recomendacao {dto.Recomendacao}", pareceristaId, cancellationToken);

        _logger.LogInformation("Parecer registrado para denuncia {DenunciaId}: Recomendacao={Recomendacao}", id, dto.Recomendacao);

        return new ParecerResultadoDto
        {
            Id = parecer.Id,
            DenunciaId = parecer.DenunciaId,
            Tipo = parecer.Tipo,
            TipoNome = parecer.Tipo.ToString(),
            Status = parecer.Status,
            StatusNome = parecer.Status.ToString(),
            Numero = parecer.Numero,
            Ementa = parecer.Ementa,
            Conteudo = parecer.Conteudo,
            Fundamentacao = parecer.Fundamentacao,
            Conclusao = parecer.Conclusao,
            Favoravel = parecer.Favoravel,
            Recomendacao = parecer.Recomendacao,
            PareceristaId = parecer.PareceristaId,
            DataElaboracao = parecer.DataElaboracao,
            DataAprovacao = parecer.DataAprovacao
        };
    }

    /// <summary>
    /// Julga denuncia com votos e decisao completa
    /// </summary>
    public async Task<DenunciaDto> JulgarAsync(Guid id, JulgarDenunciaCompletoDto dto, Guid julgadorId, CancellationToken cancellationToken = default)
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

        // Create julgamento record
        var julgamento = new JulgamentoDenuncia
        {
            DenunciaId = id,
            SessaoId = dto.SessaoId,
            Status = StatusJulgamento.Concluido,
            TipoDecisao = dto.TipoDecisao,
            Procedente = dto.Resultado == StatusDenuncia.Procedente,
            Improcedente = dto.Resultado == StatusDenuncia.Improcedente,
            ParcialmenteProcedente = dto.Resultado == StatusDenuncia.ParcialmenteProcedente,
            Ementa = dto.Ementa,
            Fundamentacao = dto.Fundamentacao,
            Dispositivo = dto.Dispositivo,
            Penalidade = dto.Penalidade,
            DataJulgamento = DateTime.UtcNow
        };

        await _julgamentoRepository.AddAsync(julgamento, cancellationToken);

        denuncia.Status = dto.Resultado;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Julgamento: {dto.Resultado} - {dto.Decisao}", julgadorId, cancellationToken);

        // Open appeal deadline if requested
        if (dto.AbrirPrazoRecurso)
        {
            var prazoRecurso = DateTime.UtcNow.AddDays(dto.PrazoDiasRecurso);
            denuncia.Status = StatusDenuncia.AguardandoRecurso;
            denuncia.PrazoRecurso = prazoRecurso;
            await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
            await _unitOfWork.SaveChangesAsync(cancellationToken);

            await RegistrarHistoricoAsync(id, $"Prazo de recurso aberto ate {prazoRecurso:dd/MM/yyyy}", null, cancellationToken);
        }

        _logger.LogInformation("Julgamento completo registrado para denuncia {DenunciaId}: {Decisao}", id, dto.Resultado);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    #endregion

    #region Phase 4: Recurso (Appeal) - Extended

    /// <summary>
    /// Abre prazo de recurso usando dias
    /// </summary>
    public async Task<DenunciaDto> AbrirPrazoRecursoAsync(Guid id, int prazoEmDias, CancellationToken cancellationToken = default)
    {
        var prazoRecurso = DateTime.UtcNow.AddDays(prazoEmDias);
        return await AbrirPrazoRecursoAsync(id, prazoRecurso, cancellationToken);
    }

    /// <summary>
    /// Interpoe recurso contra decisao
    /// </summary>
    public async Task<RecursoResultadoDto> InterporRecursoAsync(Guid id, InterporRecursoDto dto, Guid autorId, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        var statusPermitidos = new[]
        {
            StatusDenuncia.Procedente,
            StatusDenuncia.Improcedente,
            StatusDenuncia.ParcialmenteProcedente,
            StatusDenuncia.Julgada,
            StatusDenuncia.AguardandoRecurso
        };

        if (!statusPermitidos.Contains(denuncia.Status))
            throw new InvalidOperationException("Denuncia nao esta em status que permite recurso");

        // Check if within deadline
        var prazoLimite = denuncia.PrazoRecurso ?? DateTime.UtcNow.AddDays(5);
        var tempestivo = DateTime.UtcNow <= prazoLimite;

        var protocolo = await GerarProtocoloRecursoAsync(cancellationToken);

        var recurso = new RecursoDenuncia
        {
            DenunciaId = id,
            ChapaId = dto.ChapaId,
            Protocolo = protocolo,
            Status = StatusDenuncia.RecursoApresentado,
            Fundamentacao = dto.Fundamentacao,
            Pedido = dto.Pedido,
            DataApresentacao = DateTime.UtcNow,
            PrazoLimite = prazoLimite,
            Tempestivo = tempestivo
        };

        await _recursoRepository.AddAsync(recurso, cancellationToken);

        denuncia.Status = StatusDenuncia.RecursoApresentado;
        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        var statusRecurso = tempestivo ? "Recurso tempestivo apresentado" : "Recurso intempestivo apresentado";
        await RegistrarHistoricoAsync(id, statusRecurso, autorId, cancellationToken);

        _logger.LogInformation("Recurso interposto para denuncia {DenunciaId}: Tempestivo={Tempestivo}", id, tempestivo);

        return new RecursoResultadoDto
        {
            Id = recurso.Id,
            DenunciaId = recurso.DenunciaId,
            Protocolo = recurso.Protocolo,
            Tipo = dto.Tipo,
            TipoNome = dto.Tipo.ToString(),
            Status = StatusRecurso.Protocolado,
            StatusNome = StatusRecurso.Protocolado.ToString(),
            Fundamentacao = recurso.Fundamentacao,
            Pedido = recurso.Pedido,
            DataApresentacao = recurso.DataApresentacao,
            PrazoLimite = recurso.PrazoLimite,
            Tempestivo = recurso.Tempestivo,
            ChapaId = recurso.ChapaId
        };
    }

    /// <summary>
    /// Registra contra-razoes ao recurso
    /// </summary>
    public async Task<ContraRazoesResultadoDto> RegistrarContraRazoesAsync(Guid recursoId, ContraRazoesDto dto, CancellationToken cancellationToken = default)
    {
        var recurso = await _recursoRepository.GetByIdAsync(recursoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Recurso {recursoId} nao encontrado");

        // Prazo para contra-razoes (geralmente mesmo prazo do recurso)
        var prazoLimite = recurso.DataApresentacao.AddDays(5);
        var tempestiva = DateTime.UtcNow <= prazoLimite;

        var contraRazoes = new ContrarrazoesRecursoDenuncia
        {
            RecursoId = recursoId,
            ProfissionalId = dto.ProfissionalId,
            Conteudo = dto.Conteudo,
            Fundamentacao = dto.Fundamentacao,
            DataApresentacao = DateTime.UtcNow,
            PrazoLimite = prazoLimite,
            Tempestiva = tempestiva
        };

        await _contraRazoesRepository.AddAsync(contraRazoes, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(recurso.DenunciaId, "Contra-razoes apresentadas", dto.ProfissionalId, cancellationToken);

        _logger.LogInformation("Contra-razoes registradas para recurso {RecursoId}: Tempestiva={Tempestiva}", recursoId, tempestiva);

        return new ContraRazoesResultadoDto
        {
            Id = contraRazoes.Id,
            RecursoId = contraRazoes.RecursoId,
            Conteudo = contraRazoes.Conteudo,
            Fundamentacao = contraRazoes.Fundamentacao,
            DataApresentacao = contraRazoes.DataApresentacao,
            PrazoLimite = contraRazoes.PrazoLimite,
            Tempestiva = contraRazoes.Tempestiva,
            ProfissionalId = contraRazoes.ProfissionalId
        };
    }

    /// <summary>
    /// Julga recurso (segunda instancia)
    /// </summary>
    public async Task<RecursoResultadoDto> JulgarRecursoAsync(Guid recursoId, JulgarRecursoDto dto, Guid julgadorId, CancellationToken cancellationToken = default)
    {
        var recurso = await _recursoRepository.Query()
            .Include(r => r.Denuncia)
            .Include(r => r.Contrarrazoes)
            .FirstOrDefaultAsync(r => r.Id == recursoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Recurso {recursoId} nao encontrado");

        // Create julgamento recurso record
        var julgamentoRecurso = new JulgamentoRecursoDenuncia
        {
            RecursoId = recursoId,
            SessaoId = dto.SessaoId,
            Status = StatusJulgamento.Concluido,
            TipoDecisao = dto.TipoDecisao,
            Provido = dto.Resultado == StatusRecurso.Provido,
            Desprovido = dto.Resultado == StatusRecurso.Desprovido,
            ParcialmenteProvido = dto.Resultado == StatusRecurso.DesprovimidoParcialmente,
            Ementa = dto.Ementa,
            Fundamentacao = dto.Fundamentacao,
            Dispositivo = dto.Dispositivo,
            DataJulgamento = DateTime.UtcNow
        };

        await _julgamentoRecursoRepository.AddAsync(julgamentoRecurso, cancellationToken);

        // Update recurso status
        recurso.Status = dto.Resultado switch
        {
            StatusRecurso.Provido => StatusDenuncia.RecursoJulgado,
            StatusRecurso.Desprovido => StatusDenuncia.RecursoJulgado,
            StatusRecurso.DesprovimidoParcialmente => StatusDenuncia.RecursoJulgado,
            _ => StatusDenuncia.RecursoJulgado
        };

        await _recursoRepository.UpdateAsync(recurso, cancellationToken);

        // Update denuncia status
        var denuncia = recurso.Denuncia;
        denuncia.Status = StatusDenuncia.RecursoJulgado;
        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);

        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(denuncia.Id, $"Recurso julgado: {dto.Resultado}", julgadorId, cancellationToken);

        _logger.LogInformation("Recurso {RecursoId} julgado: {Resultado}", recursoId, dto.Resultado);

        return new RecursoResultadoDto
        {
            Id = recurso.Id,
            DenunciaId = recurso.DenunciaId,
            Protocolo = recurso.Protocolo,
            Tipo = TipoRecurso.RecursoOrdinario,
            TipoNome = TipoRecurso.RecursoOrdinario.ToString(),
            Status = StatusRecurso.Provido, // Map from dto.Resultado
            StatusNome = dto.Resultado.ToString(),
            Fundamentacao = recurso.Fundamentacao,
            Pedido = recurso.Pedido,
            DataApresentacao = recurso.DataApresentacao,
            PrazoLimite = recurso.PrazoLimite,
            Tempestivo = recurso.Tempestivo,
            ChapaId = recurso.ChapaId,
            Julgamento = new JulgamentoRecursoResultadoDto
            {
                Id = julgamentoRecurso.Id,
                RecursoId = julgamentoRecurso.RecursoId,
                Status = julgamentoRecurso.Status,
                StatusNome = julgamentoRecurso.Status.ToString(),
                TipoDecisao = julgamentoRecurso.TipoDecisao,
                TipoDecisaoNome = julgamentoRecurso.TipoDecisao?.ToString(),
                Provido = julgamentoRecurso.Provido,
                Desprovido = julgamentoRecurso.Desprovido,
                ParcialmenteProvido = julgamentoRecurso.ParcialmenteProvido,
                Ementa = julgamentoRecurso.Ementa,
                Fundamentacao = julgamentoRecurso.Fundamentacao,
                Dispositivo = julgamentoRecurso.Dispositivo,
                DataJulgamento = julgamentoRecurso.DataJulgamento
            }
        };
    }

    /// <summary>
    /// Lista recursos de uma denuncia
    /// </summary>
    public async Task<IEnumerable<RecursoResultadoDto>> GetRecursosAsync(Guid denunciaId, CancellationToken cancellationToken = default)
    {
        var recursos = await _recursoRepository.Query()
            .Include(r => r.Chapa)
            .Include(r => r.Contrarrazoes)
            .Include(r => r.Julgamento)
            .Where(r => r.DenunciaId == denunciaId)
            .OrderByDescending(r => r.DataApresentacao)
            .ToListAsync(cancellationToken);

        return recursos.Select(r => new RecursoResultadoDto
        {
            Id = r.Id,
            DenunciaId = r.DenunciaId,
            Protocolo = r.Protocolo,
            Tipo = TipoRecurso.RecursoOrdinario,
            TipoNome = TipoRecurso.RecursoOrdinario.ToString(),
            Status = StatusRecurso.Protocolado,
            StatusNome = r.Status.ToString(),
            Fundamentacao = r.Fundamentacao,
            Pedido = r.Pedido,
            DataApresentacao = r.DataApresentacao,
            PrazoLimite = r.PrazoLimite,
            Tempestivo = r.Tempestivo,
            ChapaId = r.ChapaId,
            ChapaNome = r.Chapa?.Nome,
            Contrarrazoes = r.Contrarrazoes?.Select(c => new ContraRazoesResultadoDto
            {
                Id = c.Id,
                RecursoId = c.RecursoId,
                Conteudo = c.Conteudo,
                Fundamentacao = c.Fundamentacao,
                DataApresentacao = c.DataApresentacao,
                PrazoLimite = c.PrazoLimite,
                Tempestiva = c.Tempestiva,
                ProfissionalId = c.ProfissionalId
            }).ToList() ?? new(),
            Julgamento = r.Julgamento == null ? null : new JulgamentoRecursoResultadoDto
            {
                Id = r.Julgamento.Id,
                RecursoId = r.Julgamento.RecursoId,
                Status = r.Julgamento.Status,
                StatusNome = r.Julgamento.Status.ToString(),
                TipoDecisao = r.Julgamento.TipoDecisao,
                TipoDecisaoNome = r.Julgamento.TipoDecisao?.ToString(),
                Provido = r.Julgamento.Provido,
                Desprovido = r.Julgamento.Desprovido,
                ParcialmenteProvido = r.Julgamento.ParcialmenteProvido,
                Ementa = r.Julgamento.Ementa,
                Fundamentacao = r.Julgamento.Fundamentacao,
                Dispositivo = r.Julgamento.Dispositivo,
                DataJulgamento = r.Julgamento.DataJulgamento,
                DataPublicacao = r.Julgamento.DataPublicacao
            }
        });
    }

    #endregion

    #region Phase 5: Arquivamento (Archive) - Extended

    /// <summary>
    /// Arquiva denuncia usando DTO
    /// </summary>
    public async Task<DenunciaDto> ArquivarAsync(Guid id, ArquivarDenunciaDto dto, Guid? userId = null, CancellationToken cancellationToken = default)
    {
        return await ArquivarAsync(id, dto.Motivo, userId, cancellationToken);
    }

    /// <summary>
    /// Reabre denuncia arquivada
    /// </summary>
    public async Task<DenunciaDto> ReabrirAsync(Guid id, string motivo, Guid userId, CancellationToken cancellationToken = default)
    {
        var denuncia = await _denunciaRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Denuncia {id} nao encontrada");

        if (denuncia.Status != StatusDenuncia.Arquivada)
            throw new InvalidOperationException("Apenas denuncias arquivadas podem ser reabertas");

        denuncia.Status = StatusDenuncia.EmAnalise;

        await _denunciaRepository.UpdateAsync(denuncia, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Denuncia reaberta: {motivo}", userId, cancellationToken);

        _logger.LogInformation("Denuncia {DenunciaId} reaberta por {UserId}", id, userId);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar denuncia");
    }

    #endregion

    #region Pareceres

    /// <summary>
    /// Lista pareceres de uma denuncia
    /// </summary>
    public async Task<IEnumerable<ParecerResultadoDto>> GetPareceresAsync(Guid denunciaId, CancellationToken cancellationToken = default)
    {
        var pareceres = await _parecerRepository.Query()
            .Include(p => p.Parecerista)
            .Where(p => p.DenunciaId == denunciaId)
            .OrderByDescending(p => p.DataElaboracao)
            .ToListAsync(cancellationToken);

        return pareceres.Select(p => new ParecerResultadoDto
        {
            Id = p.Id,
            DenunciaId = p.DenunciaId,
            Tipo = p.Tipo,
            TipoNome = p.Tipo.ToString(),
            Status = p.Status,
            StatusNome = p.Status.ToString(),
            Numero = p.Numero,
            Ementa = p.Ementa,
            Conteudo = p.Conteudo,
            Fundamentacao = p.Fundamentacao,
            Conclusao = p.Conclusao,
            Favoravel = p.Favoravel,
            Recomendacao = p.Recomendacao,
            PareceristaId = p.PareceristaId,
            PareceristaName = p.Parecerista?.Nome,
            DataElaboracao = p.DataElaboracao,
            DataAprovacao = p.DataAprovacao
        });
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

    private async Task<string> GerarNumeroParecerAsync(CancellationToken cancellationToken)
    {
        var ano = DateTime.UtcNow.Year;
        var count = await _parecerRepository.CountAsync(
            p => p.DataElaboracao.Year == ano, cancellationToken);

        return $"PAR-{ano}-{(count + 1):D5}";
    }

    private async Task<string> GerarProtocoloRecursoAsync(CancellationToken cancellationToken)
    {
        var ano = DateTime.UtcNow.Year;
        var count = await _recursoRepository.CountAsync(
            r => r.DataApresentacao.Year == ano, cancellationToken);

        return $"REC-{ano}-{(count + 1):D5}";
    }

    #endregion
}
