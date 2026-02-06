using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Impugnacoes;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Impugnacoes;
using CAU.Eleitoral.Domain.Entities.Chapas;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

public class ImpugnacaoService : IImpugnacaoService
{
    private readonly IRepository<ImpugnacaoResultado> _impugnacaoRepository;
    private readonly IRepository<PedidoImpugnacao> _pedidoRepository;
    private readonly IRepository<AlegacaoImpugnacaoResultado> _alegacaoRepository;
    private readonly IRepository<ContraAlegacaoImpugnacao> _contraAlegacaoRepository;
    private readonly IRepository<DefesaImpugnacao> _defesaRepository;
    private readonly IRepository<JulgamentoImpugnacao> _julgamentoRepository;
    private readonly IRepository<RecursoImpugnacao> _recursoRepository;
    private readonly IRepository<JulgamentoRecursoImpugnacao> _julgamentoRecursoRepository;
    private readonly IRepository<HistoricoImpugnacao> _historicoRepository;
    private readonly IRepository<ChapaEleicao> _chapaRepository;
    private readonly IRepository<MembroChapa> _membroRepository;
    private readonly ICalendarioService _calendarioService;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<ImpugnacaoService> _logger;

    // Default deadlines in days
    private const int PrazoRecursoDias = 5;

    public ImpugnacaoService(
        IRepository<ImpugnacaoResultado> impugnacaoRepository,
        IRepository<PedidoImpugnacao> pedidoRepository,
        IRepository<AlegacaoImpugnacaoResultado> alegacaoRepository,
        IRepository<ContraAlegacaoImpugnacao> contraAlegacaoRepository,
        IRepository<DefesaImpugnacao> defesaRepository,
        IRepository<JulgamentoImpugnacao> julgamentoRepository,
        IRepository<RecursoImpugnacao> recursoRepository,
        IRepository<JulgamentoRecursoImpugnacao> julgamentoRecursoRepository,
        IRepository<HistoricoImpugnacao> historicoRepository,
        IRepository<ChapaEleicao> chapaRepository,
        IRepository<MembroChapa> membroRepository,
        ICalendarioService calendarioService,
        IUnitOfWork unitOfWork,
        ILogger<ImpugnacaoService> logger)
    {
        _impugnacaoRepository = impugnacaoRepository;
        _pedidoRepository = pedidoRepository;
        _alegacaoRepository = alegacaoRepository;
        _contraAlegacaoRepository = contraAlegacaoRepository;
        _defesaRepository = defesaRepository;
        _julgamentoRepository = julgamentoRepository;
        _recursoRepository = recursoRepository;
        _julgamentoRecursoRepository = julgamentoRecursoRepository;
        _historicoRepository = historicoRepository;
        _chapaRepository = chapaRepository;
        _membroRepository = membroRepository;
        _calendarioService = calendarioService;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    #region CRUD Operations

    public async Task<ImpugnacaoDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.Query()
            .Include(i => i.Eleicao)
            .Include(i => i.ChapaImpugnante)
            .Include(i => i.ChapaImpugnada)
            .Include(i => i.MembroImpugnado)
            .Include(i => i.Impugnante)
            .Include(i => i.Pedidos)
            .Include(i => i.Alegacoes)
            .Include(i => i.Defesas)
            .Include(i => i.Julgamento)
            .Include(i => i.Recursos)
            .Include(i => i.Historicos)
            .FirstOrDefaultAsync(i => i.Id == id, cancellationToken);

        return impugnacao == null ? null : MapToDto(impugnacao);
    }

    public async Task<ImpugnacaoDto?> GetByProtocoloAsync(string protocolo, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.Query()
            .Include(i => i.Eleicao)
            .Include(i => i.ChapaImpugnante)
            .Include(i => i.ChapaImpugnada)
            .Include(i => i.Pedidos)
            .Include(i => i.Alegacoes)
            .FirstOrDefaultAsync(i => i.Protocolo == protocolo, cancellationToken);

        return impugnacao == null ? null : MapToDto(impugnacao);
    }

    public async Task<IEnumerable<ImpugnacaoDto>> GetAllAsync(CancellationToken cancellationToken = default)
    {
        var impugnacoes = await _impugnacaoRepository.Query()
            .Include(i => i.Eleicao)
            .Include(i => i.ChapaImpugnante)
            .Include(i => i.ChapaImpugnada)
            .Include(i => i.Pedidos)
            .Include(i => i.Alegacoes)
            .OrderByDescending(i => i.DataImpugnacao)
            .ToListAsync(cancellationToken);

        return impugnacoes.Select(MapToDto);
    }

    public async Task<IEnumerable<ImpugnacaoDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var impugnacoes = await _impugnacaoRepository.Query()
            .Include(i => i.Eleicao)
            .Include(i => i.ChapaImpugnante)
            .Include(i => i.ChapaImpugnada)
            .Include(i => i.Pedidos)
            .Include(i => i.Alegacoes)
            .Where(i => i.EleicaoId == eleicaoId)
            .OrderByDescending(i => i.DataImpugnacao)
            .ToListAsync(cancellationToken);

        return impugnacoes.Select(MapToDto);
    }

    public async Task<IEnumerable<ImpugnacaoDto>> GetByStatusAsync(StatusImpugnacao status, CancellationToken cancellationToken = default)
    {
        var impugnacoes = await _impugnacaoRepository.Query()
            .Include(i => i.Eleicao)
            .Include(i => i.ChapaImpugnante)
            .Include(i => i.ChapaImpugnada)
            .Include(i => i.Pedidos)
            .Include(i => i.Alegacoes)
            .Where(i => i.Status == status)
            .OrderByDescending(i => i.DataImpugnacao)
            .ToListAsync(cancellationToken);

        return impugnacoes.Select(MapToDto);
    }

    public async Task<IEnumerable<ImpugnacaoDto>> GetByChapaImpugnadaAsync(Guid chapaId, CancellationToken cancellationToken = default)
    {
        var impugnacoes = await _impugnacaoRepository.Query()
            .Include(i => i.Eleicao)
            .Include(i => i.ChapaImpugnante)
            .Include(i => i.ChapaImpugnada)
            .Include(i => i.Pedidos)
            .Include(i => i.Alegacoes)
            .Where(i => i.ChapaImpugnadaId == chapaId)
            .OrderByDescending(i => i.DataImpugnacao)
            .ToListAsync(cancellationToken);

        return impugnacoes.Select(MapToDto);
    }

    public async Task<ImpugnacaoDto> CreateAsync(CreateImpugnacaoDto dto, CancellationToken cancellationToken = default)
    {
        // Validate calendar period
        var periodoValido = await ValidarPeriodoImpugnacaoAsync(dto.EleicaoId, cancellationToken);
        if (!periodoValido)
        {
            var validacao = await _calendarioService.ValidarPeriodoAsync(
                dto.EleicaoId,
                new[] { TipoCalendario.Impugnacao },
                "registrar impugnacao",
                cancellationToken);
            throw new InvalidOperationException(validacao.Message ?? "Fora do periodo de impugnacao");
        }

        // Generate protocol
        var protocolo = await GerarProtocoloAsync(cancellationToken);

        var impugnacao = new ImpugnacaoResultado
        {
            EleicaoId = dto.EleicaoId,
            Protocolo = protocolo,
            Tipo = dto.Tipo,
            Status = StatusImpugnacao.Recebida,
            ChapaImpugnanteId = dto.ChapaImpugnanteId,
            ChapaImpugnadaId = dto.ChapaImpugnadaId,
            MembroImpugnadoId = dto.MembroImpugnadoId,
            ImpugnanteId = dto.ImpugnanteId,
            Titulo = dto.Titulo,
            Descricao = dto.Descricao,
            Fundamentacao = dto.Fundamentacao,
            DataImpugnacao = DateTime.UtcNow,
            DataRecebimento = DateTime.UtcNow
        };

        await _impugnacaoRepository.AddAsync(impugnacao, cancellationToken);

        // Update target status (if chapa)
        if (dto.Tipo == TipoImpugnacao.ImpugnacaoChapa && dto.ChapaImpugnadaId.HasValue)
        {
            await AtualizarStatusChapaAsync(dto.ChapaImpugnadaId.Value, StatusChapa.Impugnada, cancellationToken);
        }

        await _unitOfWork.SaveChangesAsync(cancellationToken);

        // Add initial pedidos if any
        if (dto.Pedidos?.Any() == true)
        {
            var ordem = 1;
            foreach (var pedidoDto in dto.Pedidos)
            {
                var pedido = new PedidoImpugnacao
                {
                    ImpugnacaoId = impugnacao.Id,
                    Descricao = pedidoDto.Descricao,
                    Fundamentacao = pedidoDto.Fundamentacao,
                    Ordem = ordem++
                };
                await _pedidoRepository.AddAsync(pedido, cancellationToken);
            }
            await _unitOfWork.SaveChangesAsync(cancellationToken);
        }

        await RegistrarHistoricoAsync(impugnacao.Id, null, StatusImpugnacao.Recebida, "Impugnacao registrada", cancellationToken);

        _logger.LogInformation("Impugnacao criada: {ImpugnacaoId} - Protocolo: {Protocolo}", impugnacao.Id, protocolo);

        return await GetByIdAsync(impugnacao.Id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao criada");
    }

    public async Task<ImpugnacaoDto> UpdateAsync(Guid id, UpdateImpugnacaoDto dto, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        if (impugnacao.Status != StatusImpugnacao.Recebida && impugnacao.Status != StatusImpugnacao.EmAnalise)
            throw new InvalidOperationException("Impugnacao nao pode ser alterada neste status");

        if (dto.Titulo != null) impugnacao.Titulo = dto.Titulo;
        if (dto.Descricao != null) impugnacao.Descricao = dto.Descricao;
        if (dto.Fundamentacao != null) impugnacao.Fundamentacao = dto.Fundamentacao;
        if (dto.PrazoAlegacoes.HasValue) impugnacao.PrazoAlegacoes = dto.PrazoAlegacoes.Value;
        if (dto.PrazoContraAlegacoes.HasValue) impugnacao.PrazoContraAlegacoes = dto.PrazoContraAlegacoes.Value;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Impugnacao atualizada: {ImpugnacaoId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task DeleteAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        if (impugnacao.Status != StatusImpugnacao.Recebida)
            throw new InvalidOperationException("Apenas impugnacoes recebidas podem ser excluidas");

        await _impugnacaoRepository.SoftDeleteAsync(id, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Impugnacao excluida: {ImpugnacaoId}", id);
    }

    #endregion

    #region Workflow Operations

    public async Task<ImpugnacaoDto> ReceberAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        var statusAnterior = impugnacao.Status;
        impugnacao.Status = StatusImpugnacao.Recebida;
        impugnacao.DataRecebimento = DateTime.UtcNow;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, statusAnterior, StatusImpugnacao.Recebida, "Impugnacao recebida", cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> IniciarAnaliseAsync(Guid id, Guid? relatorId = null, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        if (impugnacao.Status != StatusImpugnacao.Recebida)
            throw new InvalidOperationException($"Impugnacao deve estar com status Recebida para iniciar analise. Status atual: {impugnacao.Status}");

        var statusAnterior = impugnacao.Status;
        impugnacao.Status = StatusImpugnacao.EmAnalise;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        var descricao = relatorId.HasValue
            ? $"Analise iniciada. Relator designado: {relatorId.Value}"
            : "Analise iniciada";

        await RegistrarHistoricoAsync(id, statusAnterior, StatusImpugnacao.EmAnalise, descricao, cancellationToken);

        _logger.LogInformation("Analise iniciada para impugnacao {ImpugnacaoId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> SolicitarAlegacoesAsync(Guid id, int prazoEmDias, string? observacoes = null, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        if (impugnacao.Status != StatusImpugnacao.EmAnalise)
            throw new InvalidOperationException($"Impugnacao deve estar Em Analise para solicitar alegacoes. Status atual: {impugnacao.Status}");

        var statusAnterior = impugnacao.Status;
        var prazo = DateTime.UtcNow.AddDays(prazoEmDias);

        impugnacao.Status = StatusImpugnacao.AguardandoAlegacoes;
        impugnacao.PrazoAlegacoes = prazo;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        var descricao = $"Prazo de alegacoes aberto ate {prazo:dd/MM/yyyy}";
        if (!string.IsNullOrEmpty(observacoes))
            descricao += $". Obs: {observacoes}";

        await RegistrarHistoricoAsync(id, statusAnterior, StatusImpugnacao.AguardandoAlegacoes, descricao, cancellationToken);

        _logger.LogInformation("Prazo de alegacoes aberto para impugnacao {ImpugnacaoId} ate {Prazo}", id, prazo);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> ApresentarAlegacoesAsync(Guid id, CreateAlegacaoDto dto, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        if (impugnacao.Status != StatusImpugnacao.AguardandoAlegacoes)
            throw new InvalidOperationException($"Impugnacao deve estar Aguardando Alegacoes. Status atual: {impugnacao.Status}");

        // Validate deadline
        var dentroDosPrazos = await ValidarPrazoAlegacoesAsync(id, cancellationToken);
        var tempestiva = dentroDosPrazos;

        var ultimaOrdem = await _alegacaoRepository.Query()
            .Where(a => a.ImpugnacaoId == id)
            .MaxAsync(a => (int?)a.Ordem, cancellationToken) ?? 0;

        var alegacao = new AlegacaoImpugnacaoResultado
        {
            ImpugnacaoId = id,
            Tipo = dto.Tipo,
            Descricao = dto.Descricao,
            Fundamentacao = dto.Fundamentacao,
            DataApresentacao = DateTime.UtcNow,
            PrazoLimite = impugnacao.PrazoAlegacoes ?? DateTime.UtcNow,
            Tempestiva = tempestiva,
            Ordem = ultimaOrdem + 1
        };

        await _alegacaoRepository.AddAsync(alegacao, cancellationToken);

        var statusAnterior = impugnacao.Status;
        impugnacao.Status = StatusImpugnacao.AlegacoesApresentadas;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        var descricao = tempestiva ? "Alegacao apresentada tempestivamente" : "Alegacao apresentada (intempestiva)";
        await RegistrarHistoricoAsync(id, statusAnterior, StatusImpugnacao.AlegacoesApresentadas, descricao, cancellationToken);

        _logger.LogInformation("Alegacao apresentada para impugnacao {ImpugnacaoId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> SolicitarContraAlegacoesAsync(Guid id, int prazoEmDias, string? observacoes = null, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        if (impugnacao.Status != StatusImpugnacao.AlegacoesApresentadas && impugnacao.Status != StatusImpugnacao.EmAnalise)
            throw new InvalidOperationException($"Impugnacao deve estar com alegacoes apresentadas ou em analise. Status atual: {impugnacao.Status}");

        var statusAnterior = impugnacao.Status;
        var prazo = DateTime.UtcNow.AddDays(prazoEmDias);

        impugnacao.Status = StatusImpugnacao.AguardandoContraAlegacoes;
        impugnacao.PrazoContraAlegacoes = prazo;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        var descricao = $"Prazo de contra-alegacoes aberto ate {prazo:dd/MM/yyyy}";
        if (!string.IsNullOrEmpty(observacoes))
            descricao += $". Obs: {observacoes}";

        await RegistrarHistoricoAsync(id, statusAnterior, StatusImpugnacao.AguardandoContraAlegacoes, descricao, cancellationToken);

        _logger.LogInformation("Prazo de contra-alegacoes aberto para impugnacao {ImpugnacaoId} ate {Prazo}", id, prazo);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> ApresentarContraAlegacoesAsync(Guid id, CreateContraAlegacaoDto dto, Guid autorId, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        if (impugnacao.Status != StatusImpugnacao.AguardandoContraAlegacoes)
            throw new InvalidOperationException($"Impugnacao deve estar Aguardando Contra-Alegacoes. Status atual: {impugnacao.Status}");

        // Validate deadline
        var dentroDosPrazos = await ValidarPrazoContraAlegacoesAsync(id, cancellationToken);
        var tempestiva = dentroDosPrazos;

        // If alegacaoId is provided, link to specific alegacao
        Guid? alegacaoId = dto.AlegacaoId;
        if (!alegacaoId.HasValue)
        {
            // Get the first alegacao
            var primeiraAlegacao = await _alegacaoRepository.Query()
                .Where(a => a.ImpugnacaoId == id)
                .OrderBy(a => a.Ordem)
                .FirstOrDefaultAsync(cancellationToken);

            alegacaoId = primeiraAlegacao?.Id;
        }

        if (!alegacaoId.HasValue)
            throw new InvalidOperationException("Nenhuma alegacao encontrada para esta impugnacao");

        var contraAlegacao = new ContraAlegacaoImpugnacao
        {
            AlegacaoId = alegacaoId.Value,
            ChapaId = impugnacao.ChapaImpugnadaId,
            Conteudo = dto.Descricao,
            Fundamentacao = dto.Fundamentacao,
            DataApresentacao = DateTime.UtcNow,
            PrazoLimite = impugnacao.PrazoContraAlegacoes ?? DateTime.UtcNow,
            Tempestiva = tempestiva
        };

        await _contraAlegacaoRepository.AddAsync(contraAlegacao, cancellationToken);

        var statusAnterior = impugnacao.Status;
        impugnacao.Status = StatusImpugnacao.ContraAlegacoesApresentadas;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        var descricao = tempestiva ? "Contra-alegacao apresentada tempestivamente" : "Contra-alegacao apresentada (intempestiva)";
        await RegistrarHistoricoAsync(id, statusAnterior, StatusImpugnacao.ContraAlegacoesApresentadas, descricao, cancellationToken);

        _logger.LogInformation("Contra-alegacao apresentada para impugnacao {ImpugnacaoId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> EncaminharJulgamentoAsync(Guid id, Guid? comissaoId = null, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        var statusesPermitidos = new[]
        {
            StatusImpugnacao.ContraAlegacoesApresentadas,
            StatusImpugnacao.AlegacoesApresentadas,
            StatusImpugnacao.EmAnalise,
            StatusImpugnacao.AguardandoContraAlegacoes
        };

        if (!statusesPermitidos.Contains(impugnacao.Status))
            throw new InvalidOperationException($"Impugnacao nao pode ser encaminhada para julgamento neste status: {impugnacao.Status}");

        var statusAnterior = impugnacao.Status;
        impugnacao.Status = StatusImpugnacao.AguardandoJulgamento;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        var descricao = comissaoId.HasValue
            ? $"Encaminhada para julgamento. Comissao designada: {comissaoId.Value}"
            : "Encaminhada para julgamento";

        await RegistrarHistoricoAsync(id, statusAnterior, StatusImpugnacao.AguardandoJulgamento, descricao, cancellationToken);

        _logger.LogInformation("Impugnacao {ImpugnacaoId} encaminhada para julgamento", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> JulgarAsync(Guid id, JulgarImpugnacaoDto dto, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.Query()
            .Include(i => i.Julgamento)
            .FirstOrDefaultAsync(i => i.Id == id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        if (impugnacao.Status != StatusImpugnacao.AguardandoJulgamento)
            throw new InvalidOperationException($"Impugnacao deve estar Aguardando Julgamento. Status atual: {impugnacao.Status}");

        // Validate resultado
        var resultadosValidos = new[] { StatusImpugnacao.Procedente, StatusImpugnacao.Improcedente, StatusImpugnacao.ParcialmenteProcedente };
        if (!resultadosValidos.Contains(dto.Resultado))
            throw new InvalidOperationException("Resultado deve ser Procedente, Improcedente ou ParcialmenteProcedente");

        var statusAnterior = impugnacao.Status;

        // Create or update JulgamentoImpugnacao
        var julgamento = impugnacao.Julgamento ?? new JulgamentoImpugnacao
        {
            ImpugnacaoId = id
        };

        julgamento.Status = StatusJulgamento.Concluido;
        julgamento.TipoDecisao = dto.TipoDecisao;
        julgamento.Procedente = dto.Resultado == StatusImpugnacao.Procedente;
        julgamento.Improcedente = dto.Resultado == StatusImpugnacao.Improcedente;
        julgamento.ParcialmenteProcedente = dto.Resultado == StatusImpugnacao.ParcialmenteProcedente;
        julgamento.Ementa = dto.Decisao;
        julgamento.Fundamentacao = dto.Fundamentacao;
        julgamento.DataJulgamento = DateTime.UtcNow;
        julgamento.DataPublicacao = DateTime.UtcNow;

        if (impugnacao.Julgamento == null)
        {
            await _julgamentoRepository.AddAsync(julgamento, cancellationToken);
        }
        else
        {
            await _julgamentoRepository.UpdateAsync(julgamento, cancellationToken);
        }

        // Update impugnacao status
        impugnacao.Status = dto.Resultado;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);

        // Apply effects based on result
        await AplicarEfeitosJulgamentoAsync(impugnacao, dto.Resultado, cancellationToken);

        await _unitOfWork.SaveChangesAsync(cancellationToken);

        var descricao = $"Julgamento: {dto.Resultado}. {dto.Decisao}";
        await RegistrarHistoricoAsync(id, statusAnterior, dto.Resultado, descricao, cancellationToken);

        _logger.LogInformation("Julgamento registrado para impugnacao {ImpugnacaoId}: {Decisao}", id, dto.Resultado);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> InterporRecursoAsync(Guid id, CreateRecursoImpugnacaoDto dto, Guid recorrenteId, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.Query()
            .Include(i => i.Julgamento)
            .FirstOrDefaultAsync(i => i.Id == id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        var statusesPermitidosParaRecurso = new[]
        {
            StatusImpugnacao.Procedente,
            StatusImpugnacao.Improcedente,
            StatusImpugnacao.ParcialmenteProcedente
        };

        if (!statusesPermitidosParaRecurso.Contains(impugnacao.Status))
            throw new InvalidOperationException($"Recurso so pode ser interposto apos julgamento. Status atual: {impugnacao.Status}");

        // Validate deadline
        if (!await ValidarPrazoRecursoAsync(id, cancellationToken))
            throw new InvalidOperationException("Prazo para interposicao de recurso expirado");

        var protocolo = await GerarProtocoloRecursoAsync(cancellationToken);

        var recurso = new RecursoImpugnacao
        {
            ImpugnacaoId = id,
            ChapaId = impugnacao.ChapaImpugnadaId,
            Protocolo = protocolo,
            Status = StatusImpugnacao.RecursoApresentado,
            Fundamentacao = dto.Fundamentacao,
            Pedido = dto.Fundamentacao,
            DataApresentacao = DateTime.UtcNow,
            PrazoLimite = (impugnacao.Julgamento?.DataJulgamento ?? DateTime.UtcNow).AddDays(PrazoRecursoDias),
            Tempestivo = true
        };

        await _recursoRepository.AddAsync(recurso, cancellationToken);

        var statusAnterior = impugnacao.Status;
        impugnacao.Status = StatusImpugnacao.RecursoApresentado;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, statusAnterior, StatusImpugnacao.RecursoApresentado,
            $"Recurso interposto. Protocolo: {protocolo}", cancellationToken);

        _logger.LogInformation("Recurso interposto para impugnacao {ImpugnacaoId}. Protocolo: {Protocolo}", id, protocolo);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> JulgarRecursoAsync(Guid id, Guid recursoId, JulgarRecursoImpugnacaoDto dto, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        if (impugnacao.Status != StatusImpugnacao.RecursoApresentado)
            throw new InvalidOperationException($"Impugnacao deve ter recurso apresentado. Status atual: {impugnacao.Status}");

        var recurso = await _recursoRepository.GetByIdAsync(recursoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Recurso {recursoId} nao encontrado");

        if (recurso.ImpugnacaoId != id)
            throw new InvalidOperationException("Recurso nao pertence a esta impugnacao");

        // Create judgment for the recurso
        var julgamentoRecurso = new JulgamentoRecursoImpugnacao
        {
            RecursoId = recursoId,
            Status = StatusJulgamento.Concluido,
            Provido = dto.Status == StatusRecurso.Provido,
            Desprovido = dto.Status == StatusRecurso.Desprovido,
            ParcialmenteProvido = dto.Status == StatusRecurso.DesprovimidoParcialmente,
            Ementa = dto.Decisao,
            Fundamentacao = dto.Decisao,
            DataJulgamento = DateTime.UtcNow,
            DataPublicacao = DateTime.UtcNow
        };

        await _julgamentoRecursoRepository.AddAsync(julgamentoRecurso, cancellationToken);

        // Update impugnacao status
        var statusAnterior = impugnacao.Status;
        impugnacao.Status = StatusImpugnacao.RecursoJulgado;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        var descricao = $"Recurso julgado: {dto.Status}. {dto.Decisao}";
        await RegistrarHistoricoAsync(id, statusAnterior, StatusImpugnacao.RecursoJulgado, descricao, cancellationToken);

        _logger.LogInformation("Recurso {RecursoId} julgado para impugnacao {ImpugnacaoId}: {Status}", recursoId, id, dto.Status);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> ArquivarAsync(Guid id, string motivo, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        var statusAnterior = impugnacao.Status;
        impugnacao.Status = StatusImpugnacao.Arquivada;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, statusAnterior, StatusImpugnacao.Arquivada, $"Arquivada: {motivo}", cancellationToken);

        _logger.LogInformation("Impugnacao {ImpugnacaoId} arquivada", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    #endregion

    #region Legacy Workflow Methods

    public Task<ImpugnacaoDto> AbrirPrazoAlegacoesAsync(Guid id, DateTime prazo, CancellationToken cancellationToken = default)
    {
        var diasRestantes = (int)(prazo - DateTime.UtcNow).TotalDays;
        return SolicitarAlegacoesAsync(id, diasRestantes > 0 ? diasRestantes : 5, null, cancellationToken);
    }

    public Task<ImpugnacaoDto> RegistrarAlegacaoAsync(Guid id, CreateAlegacaoDto dto, CancellationToken cancellationToken = default)
    {
        return ApresentarAlegacoesAsync(id, dto, cancellationToken);
    }

    public Task<ImpugnacaoDto> AbrirPrazoContraAlegacoesAsync(Guid id, DateTime prazo, CancellationToken cancellationToken = default)
    {
        var diasRestantes = (int)(prazo - DateTime.UtcNow).TotalDays;
        return SolicitarContraAlegacoesAsync(id, diasRestantes > 0 ? diasRestantes : 5, null, cancellationToken);
    }

    public async Task<ImpugnacaoDto> RegistrarContraAlegacaoAsync(Guid id, string contraAlegacao, Guid autorId, CancellationToken cancellationToken = default)
    {
        var dto = new CreateContraAlegacaoDto
        {
            Descricao = contraAlegacao,
            AutorId = autorId
        };
        return await ApresentarContraAlegacoesAsync(id, dto, autorId, cancellationToken);
    }

    public Task<ImpugnacaoDto> EncaminharParaJulgamentoAsync(Guid id, CancellationToken cancellationToken = default)
    {
        return EncaminharJulgamentoAsync(id, null, cancellationToken);
    }

    public async Task<ImpugnacaoDto> RegistrarJulgamentoAsync(Guid id, StatusImpugnacao decisao, string fundamentacao, CancellationToken cancellationToken = default)
    {
        var dto = new JulgarImpugnacaoDto
        {
            Resultado = decisao,
            Decisao = fundamentacao,
            Fundamentacao = fundamentacao
        };
        return await JulgarAsync(id, dto, cancellationToken);
    }

    public async Task<ImpugnacaoDto> RegistrarRecursoAsync(Guid id, string recurso, Guid autorId, CancellationToken cancellationToken = default)
    {
        var dto = new CreateRecursoImpugnacaoDto
        {
            Tipo = TipoRecurso.RecursoOrdinario,
            Fundamentacao = recurso,
            RecorrenteId = autorId
        };
        return await InterporRecursoAsync(id, dto, autorId, cancellationToken);
    }

    #endregion

    #region Pedidos

    public async Task<PedidoImpugnacaoDto> AddPedidoAsync(Guid impugnacaoId, CreatePedidoImpugnacaoDto dto, CancellationToken cancellationToken = default)
    {
        var ultimaOrdem = await _pedidoRepository.Query()
            .Where(p => p.ImpugnacaoId == impugnacaoId)
            .MaxAsync(p => (int?)p.Ordem, cancellationToken) ?? 0;

        var pedido = new PedidoImpugnacao
        {
            ImpugnacaoId = impugnacaoId,
            Descricao = dto.Descricao,
            Fundamentacao = dto.Fundamentacao,
            Ordem = ultimaOrdem + 1
        };

        await _pedidoRepository.AddAsync(pedido, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Pedido adicionado a impugnacao {ImpugnacaoId}", impugnacaoId);

        return new PedidoImpugnacaoDto
        {
            Id = pedido.Id,
            ImpugnacaoId = pedido.ImpugnacaoId,
            Descricao = pedido.Descricao,
            Fundamentacao = pedido.Fundamentacao,
            Ordem = pedido.Ordem,
            DataPedido = pedido.CreatedAt
        };
    }

    public async Task RemovePedidoAsync(Guid impugnacaoId, Guid pedidoId, CancellationToken cancellationToken = default)
    {
        var pedido = await _pedidoRepository.GetByIdAsync(pedidoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Pedido {pedidoId} nao encontrado");

        if (pedido.ImpugnacaoId != impugnacaoId)
            throw new InvalidOperationException("Pedido nao pertence a esta impugnacao");

        await _pedidoRepository.DeleteAsync(pedido, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Pedido {PedidoId} removido da impugnacao {ImpugnacaoId}", pedidoId, impugnacaoId);
    }

    public async Task<IEnumerable<PedidoImpugnacaoDto>> GetPedidosAsync(Guid impugnacaoId, CancellationToken cancellationToken = default)
    {
        var pedidos = await _pedidoRepository.Query()
            .Where(p => p.ImpugnacaoId == impugnacaoId)
            .OrderBy(p => p.Ordem)
            .ToListAsync(cancellationToken);

        return pedidos.Select(p => new PedidoImpugnacaoDto
        {
            Id = p.Id,
            ImpugnacaoId = p.ImpugnacaoId,
            Descricao = p.Descricao,
            Fundamentacao = p.Fundamentacao,
            Ordem = p.Ordem,
            DataPedido = p.CreatedAt,
            Deferido = p.Deferido,
            ParecerDeferimento = p.MotivoIndeferimento
        });
    }

    public async Task<PedidoImpugnacaoDto> AnalisarPedidoAsync(Guid impugnacaoId, Guid pedidoId, AnalisePedidoDto dto, CancellationToken cancellationToken = default)
    {
        var pedido = await _pedidoRepository.GetByIdAsync(pedidoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Pedido {pedidoId} nao encontrado");

        if (pedido.ImpugnacaoId != impugnacaoId)
            throw new InvalidOperationException("Pedido nao pertence a esta impugnacao");

        pedido.Deferido = dto.Deferido;
        pedido.MotivoIndeferimento = dto.Parecer;

        await _pedidoRepository.UpdateAsync(pedido, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Pedido {PedidoId} analisado: {Resultado}", pedidoId, dto.Deferido ? "Deferido" : "Indeferido");

        return new PedidoImpugnacaoDto
        {
            Id = pedido.Id,
            ImpugnacaoId = pedido.ImpugnacaoId,
            Descricao = pedido.Descricao,
            Fundamentacao = pedido.Fundamentacao,
            Ordem = pedido.Ordem,
            DataPedido = pedido.CreatedAt,
            Deferido = pedido.Deferido,
            ParecerDeferimento = pedido.MotivoIndeferimento
        };
    }

    #endregion

    #region Alegacoes

    public async Task<IEnumerable<AlegacaoDto>> GetAlegacoesAsync(Guid impugnacaoId, CancellationToken cancellationToken = default)
    {
        var alegacoes = await _alegacaoRepository.Query()
            .Where(a => a.ImpugnacaoId == impugnacaoId)
            .OrderBy(a => a.Ordem)
            .ToListAsync(cancellationToken);

        return alegacoes.Select(a => new AlegacaoDto
        {
            Id = a.Id,
            ImpugnacaoId = a.ImpugnacaoId,
            Tipo = a.Tipo,
            TipoNome = a.Tipo.ToString(),
            Descricao = a.Descricao,
            Fundamentacao = a.Fundamentacao,
            DataAlegacao = a.DataApresentacao
        });
    }

    public async Task RemoveAlegacaoAsync(Guid impugnacaoId, Guid alegacaoId, CancellationToken cancellationToken = default)
    {
        var alegacao = await _alegacaoRepository.GetByIdAsync(alegacaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Alegacao {alegacaoId} nao encontrada");

        if (alegacao.ImpugnacaoId != impugnacaoId)
            throw new InvalidOperationException("Alegacao nao pertence a esta impugnacao");

        await _alegacaoRepository.DeleteAsync(alegacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Alegacao {AlegacaoId} removida da impugnacao {ImpugnacaoId}", alegacaoId, impugnacaoId);
    }

    #endregion

    #region Contra-Alegacoes

    public async Task<IEnumerable<ContraAlegacaoDto>> GetContraAlegacoesAsync(Guid impugnacaoId, CancellationToken cancellationToken = default)
    {
        var alegacaoIds = await _alegacaoRepository.Query()
            .Where(a => a.ImpugnacaoId == impugnacaoId)
            .Select(a => a.Id)
            .ToListAsync(cancellationToken);

        var contraAlegacoes = await _contraAlegacaoRepository.Query()
            .Include(c => c.Chapa)
            .Where(c => alegacaoIds.Contains(c.AlegacaoId))
            .OrderBy(c => c.CreatedAt)
            .ToListAsync(cancellationToken);

        return contraAlegacoes.Select(c => new ContraAlegacaoDto
        {
            Id = c.Id,
            ImpugnacaoId = impugnacaoId,
            AlegacaoId = c.AlegacaoId,
            Descricao = c.Conteudo,
            Fundamentacao = c.Fundamentacao,
            DataContraAlegacao = c.DataApresentacao,
            AutorNome = c.Chapa?.Nome
        });
    }

    #endregion

    #region Defesas

    public async Task<DefesaImpugnacaoDto> ApresentarDefesaAsync(Guid impugnacaoId, CreateDefesaImpugnacaoDto dto, Guid autorId, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(impugnacaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {impugnacaoId} nao encontrada");

        var statusPermitidos = new[]
        {
            StatusImpugnacao.EmAnalise,
            StatusImpugnacao.AguardandoAlegacoes,
            StatusImpugnacao.AlegacoesApresentadas,
            StatusImpugnacao.AguardandoContraAlegacoes
        };

        if (!statusPermitidos.Contains(impugnacao.Status))
            throw new InvalidOperationException($"Nao e possivel apresentar defesa neste status: {impugnacao.Status}");

        var tempestiva = impugnacao.PrazoContraAlegacoes.HasValue &&
                        DateTime.UtcNow <= impugnacao.PrazoContraAlegacoes.Value;

        var defesa = new DefesaImpugnacao
        {
            ImpugnacaoId = impugnacaoId,
            ChapaId = impugnacao.ChapaImpugnadaId,
            Status = StatusDefesa.Apresentada,
            Conteudo = dto.Conteudo,
            DataApresentacao = DateTime.UtcNow,
            PrazoLimite = impugnacao.PrazoContraAlegacoes ?? DateTime.UtcNow,
            Tempestiva = tempestiva
        };

        await _defesaRepository.AddAsync(defesa, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Defesa apresentada para impugnacao {ImpugnacaoId}", impugnacaoId);

        return new DefesaImpugnacaoDto
        {
            Id = defesa.Id,
            ImpugnacaoId = defesa.ImpugnacaoId,
            Conteudo = defesa.Conteudo,
            DataApresentacao = defesa.DataApresentacao,
            Intempestiva = !defesa.Tempestiva
        };
    }

    public async Task<IEnumerable<DefesaImpugnacaoDto>> GetDefesasAsync(Guid impugnacaoId, CancellationToken cancellationToken = default)
    {
        var defesas = await _defesaRepository.Query()
            .Include(d => d.Chapa)
            .Where(d => d.ImpugnacaoId == impugnacaoId)
            .OrderBy(d => d.CreatedAt)
            .ToListAsync(cancellationToken);

        return defesas.Select(d => new DefesaImpugnacaoDto
        {
            Id = d.Id,
            ImpugnacaoId = d.ImpugnacaoId,
            Conteudo = d.Conteudo,
            DataApresentacao = d.DataApresentacao,
            AutorNome = d.Chapa?.Nome,
            Intempestiva = !d.Tempestiva
        });
    }

    #endregion

    #region Recursos

    public async Task<IEnumerable<RecursoImpugnacaoDto>> GetRecursosAsync(Guid impugnacaoId, CancellationToken cancellationToken = default)
    {
        var recursos = await _recursoRepository.Query()
            .Include(r => r.Chapa)
            .Include(r => r.Julgamento)
            .Where(r => r.ImpugnacaoId == impugnacaoId)
            .OrderBy(r => r.CreatedAt)
            .ToListAsync(cancellationToken);

        return recursos.Select(r => new RecursoImpugnacaoDto
        {
            Id = r.Id,
            ImpugnacaoId = r.ImpugnacaoId,
            Tipo = TipoRecurso.RecursoOrdinario,
            TipoNome = TipoRecurso.RecursoOrdinario.ToString(),
            Status = r.Julgamento != null ? StatusRecurso.Arquivado : StatusRecurso.Protocolado,
            StatusNome = r.Julgamento != null ? "Julgado" : "Em Analise",
            RecorrenteNome = r.Chapa?.Nome,
            Fundamentacao = r.Fundamentacao,
            DataProtocolo = r.DataApresentacao,
            DataJulgamento = r.Julgamento?.DataJulgamento,
            DecisaoRecurso = r.Julgamento?.Ementa
        });
    }

    #endregion

    #region Historico

    public async Task<IEnumerable<HistoricoImpugnacaoDto>> GetHistoricoAsync(Guid impugnacaoId, CancellationToken cancellationToken = default)
    {
        var historicos = await _historicoRepository.Query()
            .Where(h => h.ImpugnacaoId == impugnacaoId)
            .OrderByDescending(h => h.DataAlteracao)
            .ToListAsync(cancellationToken);

        return historicos.Select(h => new HistoricoImpugnacaoDto
        {
            Id = h.Id,
            ImpugnacaoId = h.ImpugnacaoId,
            Descricao = h.Descricao ?? "",
            StatusAnterior = h.StatusAnterior,
            StatusNovo = h.StatusNovo,
            DataAlteracao = h.DataAlteracao,
            UsuarioNome = h.AlteradoPor
        });
    }

    #endregion

    #region Statistics

    public async Task<int> CountByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        return await _impugnacaoRepository.CountAsync(i => i.EleicaoId == eleicaoId, cancellationToken);
    }

    public async Task<int> CountByStatusAsync(StatusImpugnacao status, CancellationToken cancellationToken = default)
    {
        return await _impugnacaoRepository.CountAsync(i => i.Status == status, cancellationToken);
    }

    public async Task<ImpugnacaoEstatisticasDto> GetEstatisticasAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var impugnacoes = await _impugnacaoRepository.Query()
            .Where(i => i.EleicaoId == eleicaoId)
            .ToListAsync(cancellationToken);

        var porTipo = impugnacoes
            .GroupBy(i => i.Tipo)
            .ToDictionary(g => g.Key.ToString(), g => g.Count());

        var porStatus = impugnacoes
            .GroupBy(i => i.Status)
            .ToDictionary(g => g.Key.ToString(), g => g.Count());

        return new ImpugnacaoEstatisticasDto
        {
            Total = impugnacoes.Count,
            Recebidas = impugnacoes.Count(i => i.Status == StatusImpugnacao.Recebida),
            EmAnalise = impugnacoes.Count(i => i.Status == StatusImpugnacao.EmAnalise),
            AguardandoAlegacoes = impugnacoes.Count(i => i.Status == StatusImpugnacao.AguardandoAlegacoes),
            AguardandoContraAlegacoes = impugnacoes.Count(i => i.Status == StatusImpugnacao.AguardandoContraAlegacoes),
            AguardandoJulgamento = impugnacoes.Count(i => i.Status == StatusImpugnacao.AguardandoJulgamento),
            Julgadas = impugnacoes.Count(i => i.Status == StatusImpugnacao.Julgada),
            Arquivadas = impugnacoes.Count(i => i.Status == StatusImpugnacao.Arquivada),
            Procedentes = impugnacoes.Count(i => i.Status == StatusImpugnacao.Procedente),
            Improcedentes = impugnacoes.Count(i => i.Status == StatusImpugnacao.Improcedente),
            ParcialmenteProcedentes = impugnacoes.Count(i => i.Status == StatusImpugnacao.ParcialmenteProcedente),
            PorTipo = porTipo,
            PorStatus = porStatus
        };
    }

    #endregion

    #region Validation

    public async Task<bool> ValidarPeriodoImpugnacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        return await _calendarioService.IsWithinPeriodAsync(eleicaoId, TipoCalendario.Impugnacao, cancellationToken);
    }

    public async Task<bool> ValidarPrazoAlegacoesAsync(Guid impugnacaoId, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(impugnacaoId, cancellationToken);
        if (impugnacao?.PrazoAlegacoes == null) return true;

        return DateTime.UtcNow <= impugnacao.PrazoAlegacoes.Value;
    }

    public async Task<bool> ValidarPrazoContraAlegacoesAsync(Guid impugnacaoId, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(impugnacaoId, cancellationToken);
        if (impugnacao?.PrazoContraAlegacoes == null) return true;

        return DateTime.UtcNow <= impugnacao.PrazoContraAlegacoes.Value;
    }

    public async Task<bool> ValidarPrazoRecursoAsync(Guid impugnacaoId, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.Query()
            .Include(i => i.Julgamento)
            .FirstOrDefaultAsync(i => i.Id == impugnacaoId, cancellationToken);

        if (impugnacao?.Julgamento?.DataJulgamento == null) return false;

        var prazoRecurso = impugnacao.Julgamento.DataJulgamento.Value.AddDays(PrazoRecursoDias);
        return DateTime.UtcNow <= prazoRecurso;
    }

    #endregion

    #region Private Methods

    private async Task<string> GerarProtocoloAsync(CancellationToken cancellationToken)
    {
        var ano = DateTime.UtcNow.Year;
        var count = await _impugnacaoRepository.CountAsync(
            i => i.DataImpugnacao.Year == ano, cancellationToken);

        return $"IMP-{ano}-{(count + 1):D5}";
    }

    private async Task<string> GerarProtocoloRecursoAsync(CancellationToken cancellationToken)
    {
        var ano = DateTime.UtcNow.Year;
        var count = await _recursoRepository.CountAsync(
            r => r.DataApresentacao.Year == ano, cancellationToken);

        return $"REC-{ano}-{(count + 1):D5}";
    }

    private async Task RegistrarHistoricoAsync(Guid impugnacaoId, StatusImpugnacao? statusAnterior, StatusImpugnacao statusNovo, string descricao, CancellationToken cancellationToken)
    {
        var historico = new HistoricoImpugnacao
        {
            ImpugnacaoId = impugnacaoId,
            StatusAnterior = statusAnterior ?? statusNovo,
            StatusNovo = statusNovo,
            Descricao = descricao,
            DataAlteracao = DateTime.UtcNow
        };

        await _historicoRepository.AddAsync(historico, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
    }

    private async Task AtualizarStatusChapaAsync(Guid chapaId, StatusChapa novoStatus, CancellationToken cancellationToken)
    {
        var chapa = await _chapaRepository.GetByIdAsync(chapaId, cancellationToken);
        if (chapa != null)
        {
            chapa.Status = novoStatus;
            await _chapaRepository.UpdateAsync(chapa, cancellationToken);
            _logger.LogInformation("Status da chapa {ChapaId} atualizado para {Status}", chapaId, novoStatus);
        }
    }

    private async Task AplicarEfeitosJulgamentoAsync(ImpugnacaoResultado impugnacao, StatusImpugnacao resultado, CancellationToken cancellationToken)
    {
        // Apply effects based on impugnacao type and result
        if (resultado == StatusImpugnacao.Procedente || resultado == StatusImpugnacao.ParcialmenteProcedente)
        {
            switch (impugnacao.Tipo)
            {
                case TipoImpugnacao.ImpugnacaoChapa:
                    if (impugnacao.ChapaImpugnadaId.HasValue)
                    {
                        var novoStatus = resultado == StatusImpugnacao.Procedente
                            ? StatusChapa.Indeferida
                            : StatusChapa.AguardandoJulgamento;
                        await AtualizarStatusChapaAsync(impugnacao.ChapaImpugnadaId.Value, novoStatus, cancellationToken);
                    }
                    break;

                case TipoImpugnacao.ImpugnacaoMembro:
                    if (impugnacao.MembroImpugnadoId.HasValue && resultado == StatusImpugnacao.Procedente)
                    {
                        var membro = await _membroRepository.GetByIdAsync(impugnacao.MembroImpugnadoId.Value, cancellationToken);
                        if (membro != null)
                        {
                            membro.Status = StatusMembroChapa.Inabilitado;
                            await _membroRepository.UpdateAsync(membro, cancellationToken);
                            _logger.LogInformation("Membro {MembroId} inabilitado por impugnacao procedente", membro.Id);
                        }
                    }
                    break;
            }
        }
        else if (resultado == StatusImpugnacao.Improcedente)
        {
            // If impugnacao is rejected, restore original status
            if (impugnacao.Tipo == TipoImpugnacao.ImpugnacaoChapa && impugnacao.ChapaImpugnadaId.HasValue)
            {
                await AtualizarStatusChapaAsync(impugnacao.ChapaImpugnadaId.Value, StatusChapa.AguardandoAnalise, cancellationToken);
            }
        }
    }

    private static ImpugnacaoDto MapToDto(ImpugnacaoResultado impugnacao)
    {
        return new ImpugnacaoDto
        {
            Id = impugnacao.Id,
            EleicaoId = impugnacao.EleicaoId,
            EleicaoNome = impugnacao.Eleicao?.Nome ?? "",
            Protocolo = impugnacao.Protocolo,
            Tipo = impugnacao.Tipo,
            TipoNome = impugnacao.Tipo.ToString(),
            Status = impugnacao.Status,
            StatusNome = impugnacao.Status.ToString(),
            ChapaImpugnanteId = impugnacao.ChapaImpugnanteId,
            ChapaImpugnanteNome = impugnacao.ChapaImpugnante?.Nome,
            ChapaImpugnadaId = impugnacao.ChapaImpugnadaId,
            ChapaImpugnadaNome = impugnacao.ChapaImpugnada?.Nome,
            MembroImpugnadoId = impugnacao.MembroImpugnadoId,
            MembroImpugnadoNome = impugnacao.MembroImpugnado?.Nome,
            ImpugnanteId = impugnacao.ImpugnanteId,
            ImpugnanteNome = impugnacao.Impugnante?.Nome,
            Titulo = impugnacao.Titulo,
            Descricao = impugnacao.Descricao,
            Fundamentacao = impugnacao.Fundamentacao,
            DataImpugnacao = impugnacao.DataImpugnacao,
            DataRecebimento = impugnacao.DataRecebimento,
            PrazoAlegacoes = impugnacao.PrazoAlegacoes,
            PrazoContraAlegacoes = impugnacao.PrazoContraAlegacoes,
            TotalPedidos = impugnacao.Pedidos?.Count ?? 0,
            TotalAlegacoes = impugnacao.Alegacoes?.Count ?? 0,
            TotalDefesas = impugnacao.Defesas?.Count ?? 0,
            Julgamento = impugnacao.Julgamento != null ? MapJulgamentoToDto(impugnacao.Julgamento) : null,
            Historicos = impugnacao.Historicos?.Select(h => new HistoricoImpugnacaoDto
            {
                Id = h.Id,
                ImpugnacaoId = h.ImpugnacaoId,
                Descricao = h.Descricao ?? "",
                StatusAnterior = h.StatusAnterior,
                StatusNovo = h.StatusNovo,
                DataAlteracao = h.DataAlteracao,
                UsuarioNome = h.AlteradoPor
            }).ToList() ?? new List<HistoricoImpugnacaoDto>(),
            CreatedAt = impugnacao.CreatedAt
        };
    }

    private static JulgamentoImpugnacaoDto MapJulgamentoToDto(JulgamentoImpugnacao julgamento)
    {
        var resultado = julgamento.Procedente == true
            ? StatusImpugnacao.Procedente
            : julgamento.Improcedente == true
                ? StatusImpugnacao.Improcedente
                : julgamento.ParcialmenteProcedente == true
                    ? StatusImpugnacao.ParcialmenteProcedente
                    : StatusImpugnacao.Julgada;

        return new JulgamentoImpugnacaoDto
        {
            Id = julgamento.Id,
            ImpugnacaoId = julgamento.ImpugnacaoId,
            Status = julgamento.Status,
            StatusNome = julgamento.Status.ToString(),
            Resultado = resultado,
            ResultadoNome = resultado.ToString(),
            TipoDecisao = julgamento.TipoDecisao,
            TipoDecisaoNome = julgamento.TipoDecisao?.ToString(),
            Decisao = julgamento.Ementa,
            Fundamentacao = julgamento.Fundamentacao,
            DataJulgamento = julgamento.DataJulgamento
        };
    }

    #endregion
}
