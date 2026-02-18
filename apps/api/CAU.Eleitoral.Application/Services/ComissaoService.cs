using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Comissoes;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Comissoes;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Usuarios;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

public class ComissaoService : IComissaoService
{
    private readonly IRepository<ComissaoEleitoral> _comissaoRepository;
    private readonly IRepository<MembroComissao> _membroRepository;
    private readonly IRepository<MembroComissaoSituacao> _situacaoRepository;
    private readonly IRepository<ComissaoDocumento> _comissaoDocRepository;
    private readonly IRepository<MembroComissaoDocumento> _membroDocRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<ComissaoService> _logger;

    public ComissaoService(
        IRepository<ComissaoEleitoral> comissaoRepository,
        IRepository<MembroComissao> membroRepository,
        IRepository<MembroComissaoSituacao> situacaoRepository,
        IRepository<ComissaoDocumento> comissaoDocRepository,
        IRepository<MembroComissaoDocumento> membroDocRepository,
        IUnitOfWork unitOfWork,
        ILogger<ComissaoService> logger)
    {
        _comissaoRepository = comissaoRepository;
        _membroRepository = membroRepository;
        _situacaoRepository = situacaoRepository;
        _comissaoDocRepository = comissaoDocRepository;
        _membroDocRepository = membroDocRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    public async Task<ComissaoEleitoralDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var comissao = await _comissaoRepository.Query()
            .Include(c => c.Calendario)
            .Include(c => c.Filial)
            .Include(c => c.Membros).ThenInclude(m => m.Profissional)
            .Include(c => c.Membros).ThenInclude(m => m.Conselheiro)
            .FirstOrDefaultAsync(c => c.Id == id, cancellationToken);
        return comissao == null ? null : MapToDto(comissao);
    }

    public async Task<IEnumerable<ComissaoEleitoralDto>> GetByCalendarioAsync(Guid calendarioId, CancellationToken cancellationToken = default)
    {
        var comissoes = await _comissaoRepository.Query()
            .Include(c => c.Calendario)
            .Include(c => c.Filial)
            .Include(c => c.Membros)
            .Where(c => c.CalendarioId == calendarioId)
            .OrderBy(c => c.Nome)
            .ToListAsync(cancellationToken);
        return comissoes.Select(MapToDto);
    }

    public async Task<IEnumerable<ComissaoEleitoralDto>> GetAtivasAsync(CancellationToken cancellationToken = default)
    {
        var comissoes = await _comissaoRepository.Query()
            .Include(c => c.Calendario)
            .Include(c => c.Filial)
            .Include(c => c.Membros)
            .Where(c => c.Ativa)
            .OrderBy(c => c.Nome)
            .ToListAsync(cancellationToken);
        return comissoes.Select(MapToDto);
    }

    public async Task<PagedResult<ComissaoEleitoralDto>> GetAllAsync(ComissaoEleitoralFilter filter, CancellationToken cancellationToken = default)
    {
        var query = _comissaoRepository.Query()
            .Include(c => c.Calendario)
            .Include(c => c.Filial)
            .Include(c => c.Membros)
            .AsQueryable();

        if (filter.CalendarioId.HasValue)
            query = query.Where(c => c.CalendarioId == filter.CalendarioId.Value);
        if (filter.FilialId.HasValue)
            query = query.Where(c => c.FilialId == filter.FilialId.Value);
        if (filter.Ativa.HasValue)
            query = query.Where(c => c.Ativa == filter.Ativa.Value);
        if (!string.IsNullOrEmpty(filter.Nome))
            query = query.Where(c => c.Nome.Contains(filter.Nome));

        var total = await query.CountAsync(cancellationToken);
        var items = await query
            .OrderBy(c => c.Nome)
            .Skip((filter.PageNumber - 1) * filter.PageSize)
            .Take(filter.PageSize)
            .ToListAsync(cancellationToken);

        return new PagedResult<ComissaoEleitoralDto>
        {
            Items = items.Select(MapToDto).ToList(),
            TotalCount = total,
            PageNumber = filter.PageNumber,
            PageSize = filter.PageSize
        };
    }

    public async Task<ComissaoEleitoralDto> CreateAsync(CreateComissaoEleitoralDto dto, CancellationToken cancellationToken = default)
    {
        var comissao = new ComissaoEleitoral
        {
            CalendarioId = dto.CalendarioId,
            FilialId = dto.FilialId,
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
        _logger.LogInformation("Comissao eleitoral criada: {ComissaoId} - {Nome}", comissao.Id, comissao.Nome);
        return await GetByIdAsync(comissao.Id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao criar comissao");
    }

    public async Task<ComissaoEleitoralDto> UpdateAsync(Guid id, UpdateComissaoEleitoralDto dto, CancellationToken cancellationToken = default)
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
        if (dto.MotivoEncerramento != null) comissao.MotivoEncerramento = dto.MotivoEncerramento;

        await _comissaoRepository.UpdateAsync(comissao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
        _logger.LogInformation("Comissao eleitoral atualizada: {ComissaoId}", id);
        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar comissao");
    }

    public async Task DeleteAsync(Guid id, CancellationToken cancellationToken = default)
    {
        await _comissaoRepository.SoftDeleteAsync(id, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
        _logger.LogInformation("Comissao eleitoral excluida: {ComissaoId}", id);
    }

    public async Task<ComissaoEleitoralDto> AtivarAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var comissao = await _comissaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Comissao {id} nao encontrada");
        comissao.Ativa = true;
        comissao.MotivoEncerramento = null;
        await _comissaoRepository.UpdateAsync(comissao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar comissao");
    }

    public async Task<ComissaoEleitoralDto> EncerrarAsync(Guid id, string motivo, CancellationToken cancellationToken = default)
    {
        var comissao = await _comissaoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Comissao {id} nao encontrada");
        comissao.Ativa = false;
        comissao.MotivoEncerramento = motivo;
        await _comissaoRepository.UpdateAsync(comissao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar comissao");
    }

    public async Task<MembroComissaoDto?> GetMembroByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var membro = await _membroRepository.Query()
            .Include(m => m.Profissional)
            .Include(m => m.Conselheiro)
            .Include(m => m.MembroSubstituto)
            .Include(m => m.HistoricoSituacoes)
            .FirstOrDefaultAsync(m => m.Id == id, cancellationToken);
        return membro == null ? null : MapMembroToDto(membro);
    }

    public async Task<IEnumerable<MembroComissaoDto>> GetMembrosByComissaoAsync(Guid comissaoId, CancellationToken cancellationToken = default)
    {
        var membros = await _membroRepository.Query()
            .Include(m => m.Profissional)
            .Include(m => m.Conselheiro)
            .Include(m => m.MembroSubstituto)
            .Where(m => m.ComissaoEleitoralId == comissaoId)
            .OrderBy(m => m.TipoParticipacao)
            .ThenBy(m => m.DataInicio)
            .ToListAsync(cancellationToken);
        return membros.Select(MapMembroToDto);
    }

    public async Task<PagedResult<MembroComissaoDto>> GetAllMembrosAsync(MembroComissaoFilter filter, CancellationToken cancellationToken = default)
    {
        var query = _membroRepository.Query()
            .Include(m => m.Profissional)
            .Include(m => m.Conselheiro)
            .Include(m => m.ComissaoEleitoral)
            .AsQueryable();

        if (filter.ComissaoEleitoralId.HasValue)
            query = query.Where(m => m.ComissaoEleitoralId == filter.ComissaoEleitoralId.Value);
        if (filter.ProfissionalId.HasValue)
            query = query.Where(m => m.ProfissionalId == filter.ProfissionalId.Value);
        if (filter.Ativo.HasValue)
            query = query.Where(m => m.Ativo == filter.Ativo.Value);
        if (filter.TipoParticipacao.HasValue)
            query = query.Where(m => m.TipoParticipacao == filter.TipoParticipacao.Value);

        var total = await query.CountAsync(cancellationToken);
        var items = await query
            .OrderBy(m => m.ComissaoEleitoral.Nome)
            .ThenBy(m => m.DataInicio)
            .Skip((filter.PageNumber - 1) * filter.PageSize)
            .Take(filter.PageSize)
            .ToListAsync(cancellationToken);

        return new PagedResult<MembroComissaoDto>
        {
            Items = items.Select(MapMembroToDto).ToList(),
            TotalCount = total,
            PageNumber = filter.PageNumber,
            PageSize = filter.PageSize
        };
    }

    public async Task<MembroComissaoDto> CreateMembroAsync(CreateMembroComissaoDto dto, CancellationToken cancellationToken = default)
    {
        var membro = new MembroComissao
        {
            ComissaoEleitoralId = dto.ComissaoEleitoralId,
            ProfissionalId = dto.ProfissionalId,
            ConselheiroId = dto.ConselheiroId,
            TipoParticipacao = dto.TipoParticipacao,
            DataInicio = dto.DataInicio,
            DataFim = dto.DataFim,
            MembroSubstitutoId = dto.MembroSubstitutoId,
            Ativo = true
        };

        await _membroRepository.AddAsync(membro, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        var situacao = new MembroComissaoSituacao
        {
            MembroComissaoId = membro.Id,
            Situacao = SituacaoMembroComissao.Ativo,
            Data = DateTime.UtcNow
        };
        await _situacaoRepository.AddAsync(situacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Membro adicionado a comissao {ComissaoId}: {ProfissionalId}", dto.ComissaoEleitoralId, dto.ProfissionalId);
        return await GetMembroByIdAsync(membro.Id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao criar membro");
    }

    public async Task<MembroComissaoDto> UpdateMembroAsync(Guid id, UpdateMembroComissaoDto dto, CancellationToken cancellationToken = default)
    {
        var membro = await _membroRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Membro {id} nao encontrado");

        if (dto.TipoParticipacao.HasValue) membro.TipoParticipacao = dto.TipoParticipacao.Value;
        if (dto.DataInicio.HasValue) membro.DataInicio = dto.DataInicio.Value;
        if (dto.DataFim.HasValue) membro.DataFim = dto.DataFim.Value;
        if (dto.Ativo.HasValue) membro.Ativo = dto.Ativo.Value;
        if (dto.MembroSubstitutoId.HasValue) membro.MembroSubstitutoId = dto.MembroSubstitutoId.Value;

        await _membroRepository.UpdateAsync(membro, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
        return await GetMembroByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao atualizar membro");
    }

    public async Task DeleteMembroAsync(Guid id, CancellationToken cancellationToken = default)
    {
        await _membroRepository.SoftDeleteAsync(id, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
        _logger.LogInformation("Membro comissao excluido: {MembroId}", id);
    }

    public async Task<MembroComissaoDto> AtivarMembroAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var membro = await _membroRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Membro {id} nao encontrado");
        membro.Ativo = true;
        await _membroRepository.UpdateAsync(membro, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
        return await GetMembroByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao ativar membro");
    }

    public async Task<MembroComissaoDto> InativarMembroAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var membro = await _membroRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Membro {id} nao encontrado");
        membro.Ativo = false;
        await _membroRepository.UpdateAsync(membro, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
        return await GetMembroByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao inativar membro");
    }

    public async Task<MembroComissaoDto> ResponderDeclaracaoAsync(Guid id, bool resposta, CancellationToken cancellationToken = default)
    {
        var membro = await _membroRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Membro {id} nao encontrado");
        membro.RespostaDeclaracao = resposta;
        membro.DataRespostaDeclaracao = DateTime.UtcNow;
        await _membroRepository.UpdateAsync(membro, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
        return await GetMembroByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao responder declaracao");
    }

    public async Task<MembroComissaoSituacaoDto> AddSituacaoAsync(CreateMembroComissaoSituacaoDto dto, CancellationToken cancellationToken = default)
    {
        var situacao = new MembroComissaoSituacao
        {
            MembroComissaoId = dto.MembroComissaoId,
            Situacao = dto.Situacao,
            Data = DateTime.UtcNow,
            Motivo = dto.Motivo
        };
        await _situacaoRepository.AddAsync(situacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
        _logger.LogInformation("Situacao adicionada ao membro {MembroId}: {Situacao}", dto.MembroComissaoId, dto.Situacao);
        return new MembroComissaoSituacaoDto
        {
            Id = situacao.Id,
            MembroComissaoId = situacao.MembroComissaoId,
            Situacao = situacao.Situacao,
            Data = situacao.Data,
            Motivo = situacao.Motivo
        };
    }

    public async Task<IEnumerable<MembroComissaoSituacaoDto>> GetHistoricoSituacoesAsync(Guid membroId, CancellationToken cancellationToken = default)
    {
        var situacoes = await _situacaoRepository.Query()
            .Where(s => s.MembroComissaoId == membroId)
            .OrderByDescending(s => s.Data)
            .ToListAsync(cancellationToken);
        return situacoes.Select(s => new MembroComissaoSituacaoDto
        {
            Id = s.Id,
            MembroComissaoId = s.MembroComissaoId,
            Situacao = s.Situacao,
            Data = s.Data,
            Motivo = s.Motivo
        });
    }

    public async Task<ComissaoDocumentoDto> UploadDocumentoAsync(CreateComissaoDocumentoDto dto, CancellationToken cancellationToken = default)
    {
        var documento = new ComissaoDocumento
        {
            ComissaoEleitoralId = dto.ComissaoEleitoralId,
            Tipo = dto.Tipo,
            Descricao = dto.Descricao,
            NomeArquivo = dto.Arquivo?.FileName ?? "documento.pdf",
            CaminhoArquivo = $"/comissoes/{dto.ComissaoEleitoralId}/documentos/",
            TamanhoArquivo = dto.Arquivo?.Length ?? 0
        };
        await _comissaoDocRepository.AddAsync(documento, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
        return new ComissaoDocumentoDto
        {
            Id = documento.Id,
            ComissaoEleitoralId = documento.ComissaoEleitoralId,
            Tipo = documento.Tipo,
            Descricao = documento.Descricao,
            NomeArquivo = documento.NomeArquivo,
            CaminhoArquivo = documento.CaminhoArquivo,
            TamanhoArquivo = documento.TamanhoArquivo,
            ContentType = documento.ContentType,
            CreatedAt = documento.CreatedAt
        };
    }

    public async Task DeleteDocumentoAsync(Guid id, CancellationToken cancellationToken = default)
    {
        await _comissaoDocRepository.SoftDeleteAsync(id, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
    }

    public async Task<MembroComissaoDocumentoDto> UploadMembroDocumentoAsync(CreateMembroComissaoDocumentoDto dto, CancellationToken cancellationToken = default)
    {
        var documento = new MembroComissaoDocumento
        {
            MembroComissaoId = dto.MembroComissaoId,
            Tipo = dto.Tipo,
            Descricao = dto.Descricao,
            NomeArquivo = dto.Arquivo?.FileName ?? "documento.pdf",
            CaminhoArquivo = $"/membros/{dto.MembroComissaoId}/documentos/",
            TamanhoArquivo = dto.Arquivo?.Length ?? 0
        };
        await _membroDocRepository.AddAsync(documento, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
        return new MembroComissaoDocumentoDto
        {
            Id = documento.Id,
            MembroComissaoId = documento.MembroComissaoId,
            Tipo = documento.Tipo,
            Descricao = documento.Descricao,
            NomeArquivo = documento.NomeArquivo,
            CaminhoArquivo = documento.CaminhoArquivo,
            TamanhoArquivo = documento.TamanhoArquivo,
            ContentType = documento.ContentType,
            CreatedAt = documento.CreatedAt
        };
    }

    public async Task DeleteMembroDocumentoAsync(Guid id, CancellationToken cancellationToken = default)
    {
        await _membroDocRepository.SoftDeleteAsync(id, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);
    }

    public async Task<bool> ValidarMembroComissaoEleicaoVigenteAsync(CancellationToken cancellationToken = default)
    {
        var hoje = DateTime.UtcNow.Date;
        var comissaoAtiva = await _comissaoRepository.Query()
            .Where(c => c.Ativa && c.DataInicio <= hoje && (c.DataFim == null || c.DataFim >= hoje))
            .FirstOrDefaultAsync(cancellationToken);
        return comissaoAtiva != null;
    }

    public async Task<IEnumerable<MembroComissaoDto>> GetMembrosComissaoLogadoAsync(CancellationToken cancellationToken = default)
    {
        return new List<MembroComissaoDto>();
    }

    private ComissaoEleitoralDto MapToDto(ComissaoEleitoral comissao)
    {
        return new ComissaoEleitoralDto
        {
            Id = comissao.Id,
            CalendarioId = comissao.CalendarioId,
            CalendarioNome = comissao.Calendario?.Nome,
            FilialId = comissao.FilialId,
            FilialNome = comissao.Filial?.Nome,
            Nome = comissao.Nome,
            Descricao = comissao.Descricao,
            Sigla = comissao.Sigla,
            Portaria = comissao.Portaria,
            DataPortaria = comissao.DataPortaria,
            DataInicio = comissao.DataInicio,
            DataFim = comissao.DataFim,
            Ativa = comissao.Ativa,
            MotivoEncerramento = comissao.MotivoEncerramento,
            CreatedAt = comissao.CreatedAt,
            Membros = comissao.Membros?.Select(MapMembroToDto).ToList() ?? new List<MembroComissaoDto>()
        };
    }

    private MembroComissaoDto MapMembroToDto(MembroComissao membro)
    {
        return new MembroComissaoDto
        {
            Id = membro.Id,
            ComissaoEleitoralId = membro.ComissaoEleitoralId,
            ProfissionalId = membro.ProfissionalId,
            ProfissionalNome = membro.Profissional?.Nome,
            ProfissionalRegistroCAU = membro.Profissional?.RegistroCAU,
            ProfissionalCPF = membro.Profissional?.CPF,
            ConselheiroId = membro.ConselheiroId,
            ConselheiroNome = membro.Conselheiro?.Nome,
            TipoParticipacao = membro.TipoParticipacao,
            DataInicio = membro.DataInicio,
            DataFim = membro.DataFim,
            Ativo = membro.Ativo,
            RespostaDeclaracao = membro.RespostaDeclaracao,
            DataRespostaDeclaracao = membro.DataRespostaDeclaracao,
            MembroSubstitutoId = membro.MembroSubstitutoId,
            MembroSubstitutoNome = membro.MembroSubstituto?.Profissional?.Nome,
            SituacaoAtual = membro.HistoricoSituacoes?.OrderByDescending(s => s.Data).FirstOrDefault()?.Situacao,
            CreatedAt = membro.CreatedAt
        };
    }
}