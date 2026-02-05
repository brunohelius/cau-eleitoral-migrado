using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Impugnacoes;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Impugnacoes;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

public class ImpugnacaoService : IImpugnacaoService
{
    private readonly IRepository<ImpugnacaoResultado> _impugnacaoRepository;
    private readonly IRepository<PedidoImpugnacao> _pedidoRepository;
    private readonly IRepository<AlegacaoImpugnacaoResultado> _alegacaoRepository;
    private readonly IRepository<HistoricoImpugnacao> _historicoRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<ImpugnacaoService> _logger;

    public ImpugnacaoService(
        IRepository<ImpugnacaoResultado> impugnacaoRepository,
        IRepository<PedidoImpugnacao> pedidoRepository,
        IRepository<AlegacaoImpugnacaoResultado> alegacaoRepository,
        IRepository<HistoricoImpugnacao> historicoRepository,
        IUnitOfWork unitOfWork,
        ILogger<ImpugnacaoService> logger)
    {
        _impugnacaoRepository = impugnacaoRepository;
        _pedidoRepository = pedidoRepository;
        _alegacaoRepository = alegacaoRepository;
        _historicoRepository = historicoRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

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
            DataImpugnacao = DateTime.UtcNow
        };

        await _impugnacaoRepository.AddAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(impugnacao.Id, "Impugnacao registrada", cancellationToken);

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

    public async Task<ImpugnacaoDto> ReceberAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        impugnacao.Status = StatusImpugnacao.Recebida;
        impugnacao.DataRecebimento = DateTime.UtcNow;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, "Impugnacao recebida", cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> IniciarAnaliseAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        impugnacao.Status = StatusImpugnacao.EmAnalise;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, "Analise iniciada", cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> AbrirPrazoAlegacoesAsync(Guid id, DateTime prazo, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        impugnacao.Status = StatusImpugnacao.AguardandoAlegacoes;
        impugnacao.PrazoAlegacoes = prazo;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Prazo de alegacoes aberto ate {prazo:dd/MM/yyyy}", cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> RegistrarAlegacaoAsync(Guid id, CreateAlegacaoDto dto, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        var alegacao = new AlegacaoImpugnacaoResultado
        {
            ImpugnacaoId = id,
            Tipo = dto.Tipo,
            Descricao = dto.Descricao,
            Fundamentacao = dto.Fundamentacao
        };

        await _alegacaoRepository.AddAsync(alegacao, cancellationToken);

        impugnacao.Status = StatusImpugnacao.AlegacoesApresentadas;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, "Alegacao apresentada", cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> AbrirPrazoContraAlegacoesAsync(Guid id, DateTime prazo, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        impugnacao.Status = StatusImpugnacao.AguardandoContraAlegacoes;
        impugnacao.PrazoContraAlegacoes = prazo;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Prazo de contra-alegacoes aberto ate {prazo:dd/MM/yyyy}", cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> RegistrarContraAlegacaoAsync(Guid id, string contraAlegacao, Guid autorId, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        impugnacao.Status = StatusImpugnacao.ContraAlegacoesApresentadas;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, "Contra-alegacao apresentada", cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> EncaminharParaJulgamentoAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        impugnacao.Status = StatusImpugnacao.AguardandoJulgamento;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, "Encaminhada para julgamento", cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> RegistrarJulgamentoAsync(Guid id, StatusImpugnacao decisao, string fundamentacao, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        impugnacao.Status = decisao;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Julgamento: {decisao} - {fundamentacao}", cancellationToken);

        _logger.LogInformation("Julgamento registrado para impugnacao {ImpugnacaoId}: {Decisao}", id, decisao);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> ArquivarAsync(Guid id, string motivo, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        impugnacao.Status = StatusImpugnacao.Arquivada;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, $"Arquivada: {motivo}", cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<ImpugnacaoDto> RegistrarRecursoAsync(Guid id, string recurso, Guid autorId, CancellationToken cancellationToken = default)
    {
        var impugnacao = await _impugnacaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Impugnacao {id} nao encontrada");

        impugnacao.Status = StatusImpugnacao.RecursoApresentado;

        await _impugnacaoRepository.UpdateAsync(impugnacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        await RegistrarHistoricoAsync(id, "Recurso apresentado", cancellationToken);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar impugnacao");
    }

    public async Task<PedidoImpugnacaoDto> AddPedidoAsync(Guid impugnacaoId, CreatePedidoImpugnacaoDto dto, CancellationToken cancellationToken = default)
    {
        var pedido = new PedidoImpugnacao
        {
            ImpugnacaoId = impugnacaoId,
            Descricao = dto.Descricao,
            Fundamentacao = dto.Fundamentacao
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
            .OrderBy(p => p.CreatedAt)
            .ToListAsync(cancellationToken);

        return pedidos.Select(p => new PedidoImpugnacaoDto
        {
            Id = p.Id,
            ImpugnacaoId = p.ImpugnacaoId,
            Descricao = p.Descricao,
            Fundamentacao = p.Fundamentacao,
            DataPedido = p.CreatedAt
        });
    }

    public async Task<IEnumerable<AlegacaoDto>> GetAlegacoesAsync(Guid impugnacaoId, CancellationToken cancellationToken = default)
    {
        var alegacoes = await _alegacaoRepository.Query()
            .Where(a => a.ImpugnacaoId == impugnacaoId)
            .OrderBy(a => a.CreatedAt)
            .ToListAsync(cancellationToken);

        return alegacoes.Select(a => new AlegacaoDto
        {
            Id = a.Id,
            ImpugnacaoId = a.ImpugnacaoId,
            Tipo = a.Tipo,
            TipoNome = a.Tipo.ToString(),
            Descricao = a.Descricao,
            Fundamentacao = a.Fundamentacao,
            DataAlegacao = a.CreatedAt
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

    public async Task<int> CountByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        return await _impugnacaoRepository.CountAsync(i => i.EleicaoId == eleicaoId, cancellationToken);
    }

    public async Task<int> CountByStatusAsync(StatusImpugnacao status, CancellationToken cancellationToken = default)
    {
        return await _impugnacaoRepository.CountAsync(i => i.Status == status, cancellationToken);
    }

    private async Task<string> GerarProtocoloAsync(CancellationToken cancellationToken)
    {
        var ano = DateTime.UtcNow.Year;
        var count = await _impugnacaoRepository.CountAsync(
            i => i.DataImpugnacao.Year == ano, cancellationToken);

        return $"IMP-{ano}-{(count + 1):D5}";
    }

    private async Task RegistrarHistoricoAsync(Guid impugnacaoId, string descricao, CancellationToken cancellationToken)
    {
        var historico = new HistoricoImpugnacao
        {
            ImpugnacaoId = impugnacaoId,
            Descricao = descricao,
            DataAlteracao = DateTime.UtcNow
        };

        await _historicoRepository.AddAsync(historico, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
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
            CreatedAt = impugnacao.CreatedAt
        };
    }
}
