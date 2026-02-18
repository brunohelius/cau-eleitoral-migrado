using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Core;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Processos;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

public class PedidoService : IPedidoService
{
    private readonly IRepository<Pedido> _pedidoRepository;
    private readonly IRepository<Eleicao> _eleicaoRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<PedidoService> _logger;

    public PedidoService(
        IRepository<Pedido> pedidoRepository,
        IRepository<Eleicao> eleicaoRepository,
        IUnitOfWork unitOfWork,
        ILogger<PedidoService> logger)
    {
        _pedidoRepository = pedidoRepository;
        _eleicaoRepository = eleicaoRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    public async Task<PagedResultDto<PedidoDto>> GetAllAsync(int pageNumber, int pageSize, Guid? eleicaoId, int? status, CancellationToken cancellationToken = default)
    {
        var query = _pedidoRepository.Query()
            .Include(p => p.Eleicao)
            .AsQueryable();

        if (eleicaoId.HasValue)
            query = query.Where(p => p.EleicaoId == eleicaoId.Value);

        if (status.HasValue)
            query = query.Where(p => p.Status == (StatusPedido)status.Value);

        var total = await query.CountAsync(cancellationToken);
        var items = await query
            .OrderByDescending(p => p.DataSolicitacao)
            .Skip((pageNumber - 1) * pageSize)
            .Take(pageSize)
            .ToListAsync(cancellationToken);

        return new PagedResultDto<PedidoDto>
        {
            Items = items.Select(MapToDto).ToList(),
            TotalCount = total,
            PageNumber = pageNumber,
            PageSize = pageSize
        };
    }

    public async Task<PedidoDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var pedido = await _pedidoRepository.Query()
            .Include(p => p.Eleicao)
            .FirstOrDefaultAsync(p => p.Id == id, cancellationToken);

        return pedido == null ? null : MapToDto(pedido);
    }

    public async Task<IEnumerable<PedidoDto>> GetBySolicitanteAsync(CancellationToken cancellationToken = default)
    {
        // Get from claims in real implementation
        var pedidos = await _pedidoRepository.Query()
            .Include(p => p.Eleicao)
            .OrderByDescending(p => p.DataSolicitacao)
            .ToListAsync(cancellationToken);

        return pedidos.Select(MapToDto);
    }

    public async Task<PedidoDto> CreateAsync(CreatePedidoDto dto, CancellationToken cancellationToken = default)
    {
        var pedido = new Pedido
        {
            EleicaoId = dto.EleicaoId,
            SolicitanteId = dto.SolicitanteId,
            SolicitanteNome = dto.SolicitanteNome,
            SolicitanteEmail = dto.SolicitanteEmail,
            Tipo = (TipoPedido)dto.Tipo,
            Assunto = dto.Assunto,
            Descricao = dto.Descricao,
            Status = StatusPedido.Pendente,
            DataSolicitacao = DateTime.UtcNow
        };

        await _pedidoRepository.AddAsync(pedido, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Pedido criado: {PedidoId} - {Assunto}", pedido.Id, pedido.Assunto);

        return await GetByIdAsync(pedido.Id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar pedido criado");
    }

    public async Task<PedidoDto> RespondAsync(Guid id, RespondPedidoDto dto, CancellationToken cancellationToken = default)
    {
        var pedido = await _pedidoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Pedido {id} nao encontrado");

        pedido.Resposta = dto.Resposta;
        pedido.Observacoes = dto.Observacoes;
        pedido.Status = StatusPedido.Respondido;
        pedido.DataResposta = DateTime.UtcNow;

        await _pedidoRepository.UpdateAsync(pedido, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Pedido respondido: {PedidoId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar pedido");
    }

    public async Task<PedidoDto> CancelAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var pedido = await _pedidoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Pedido {id} nao encontrado");

        pedido.Status = StatusPedido.Cancelado;

        await _pedidoRepository.UpdateAsync(pedido, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Pedido cancelado: {PedidoId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar pedido");
    }

    public async Task<PedidoEstatisticasDto> GetEstatisticasAsync(CancellationToken cancellationToken = default)
    {
        var pedidos = await _pedidoRepository.Query().ToListAsync(cancellationToken);

        return new PedidoEstatisticasDto
        {
            Total = pedidos.Count,
            Pendentes = pedidos.Count(p => p.Status == StatusPedido.Pendente),
            EmAnalise = pedidos.Count(p => p.Status == StatusPedido.EmAnalise),
            Respondidos = pedidos.Count(p => p.Status == StatusPedido.Respondido),
            Encerrados = pedidos.Count(p => p.Status == StatusPedido.Encerrado),
            Cancelados = pedidos.Count(p => p.Status == StatusPedido.Cancelado)
        };
    }

    private PedidoDto MapToDto(Pedido pedido)
    {
        return new PedidoDto
        {
            Id = pedido.Id,
            EleicaoId = pedido.EleicaoId,
            EleicaoNome = pedido.Eleicao?.Nome,
            SolicitanteId = pedido.SolicitanteId,
            SolicitanteNome = pedido.SolicitanteNome,
            SolicitanteEmail = pedido.SolicitanteEmail,
            Tipo = (int)pedido.Tipo,
            TipoNome = pedido.Tipo.ToString(),
            Assunto = pedido.Assunto,
            Descricao = pedido.Descricao,
            Status = (int)pedido.Status,
            StatusNome = pedido.Status.ToString(),
            DataSolicitacao = pedido.DataSolicitacao,
            DataResposta = pedido.DataResposta,
            Resposta = pedido.Resposta,
            RespondidoPorId = pedido.RespondidoPorId,
            Observacoes = pedido.Observacoes
        };
    }
}