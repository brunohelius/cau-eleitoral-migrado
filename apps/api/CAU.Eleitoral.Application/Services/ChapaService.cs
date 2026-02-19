using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Application.DTOs.Chapas;
using CAU.Eleitoral.Application.DTOs.Notificacoes;
using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Chapas;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

/// <summary>
/// Servico para gerenciamento de chapas eleitorais
/// </summary>
public class ChapaService : IChapaService
{
    private readonly IRepository<ChapaEleicao> _chapaRepository;
    private readonly IRepository<MembroChapa> _membroRepository;
    private readonly IRepository<DocumentoChapa> _documentoRepository;
    private readonly IRepository<PlataformaEleitoral> _plataformaRepository;
    private readonly IRepository<Eleicao> _eleicaoRepository;
    private readonly ICalendarioService _calendarioService;
    private readonly INotificacaoService? _notificacaoService;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<ChapaService> _logger;

    public ChapaService(
        IRepository<ChapaEleicao> chapaRepository,
        IRepository<MembroChapa> membroRepository,
        IRepository<DocumentoChapa> documentoRepository,
        IRepository<PlataformaEleitoral> plataformaRepository,
        IRepository<Eleicao> eleicaoRepository,
        ICalendarioService calendarioService,
        IUnitOfWork unitOfWork,
        ILogger<ChapaService> logger,
        INotificacaoService? notificacaoService = null)
    {
        _chapaRepository = chapaRepository;
        _membroRepository = membroRepository;
        _documentoRepository = documentoRepository;
        _plataformaRepository = plataformaRepository;
        _eleicaoRepository = eleicaoRepository;
        _calendarioService = calendarioService;
        _notificacaoService = notificacaoService;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    #region Consultas

    public async Task<IEnumerable<ChapaDto>> GetAllAsync(Guid? eleicaoId = null, CancellationToken cancellationToken = default)
    {
        var query = _chapaRepository.Query()
            .Include(c => c.Eleicao)
            .Include(c => c.Membros)
            .AsQueryable();

        if (eleicaoId.HasValue)
        {
            query = query.Where(c => c.EleicaoId == eleicaoId.Value);
        }

        var chapas = await query.OrderBy(c => c.Numero).ToListAsync(cancellationToken);

        return chapas.Select(MapToDto);
    }

    public async Task<PagedResultDto<ChapaDto>> GetPagedAsync(ChapaFilterDto filter, CancellationToken cancellationToken = default)
    {
        var query = _chapaRepository.Query()
            .Include(c => c.Eleicao)
            .Include(c => c.Membros)
            .AsQueryable();

        // Aplicar filtros
        if (filter.EleicaoId.HasValue)
        {
            query = query.Where(c => c.EleicaoId == filter.EleicaoId.Value);
        }

        if (filter.Status.HasValue)
        {
            var status = (StatusChapa)filter.Status.Value;
            query = query.Where(c => c.Status == status);
        }

        if (!string.IsNullOrWhiteSpace(filter.Search))
        {
            var search = filter.Search.ToLower();
            query = query.Where(c =>
                c.Nome.ToLower().Contains(search) ||
                (c.Sigla != null && c.Sigla.ToLower().Contains(search)) ||
                c.Numero.Contains(search));
        }

        if (!string.IsNullOrWhiteSpace(filter.Numero))
        {
            query = query.Where(c => c.Numero == filter.Numero);
        }

        if (filter.DataInscricaoInicio.HasValue)
        {
            query = query.Where(c => c.DataInscricao >= filter.DataInscricaoInicio.Value);
        }

        if (filter.DataInscricaoFim.HasValue)
        {
            query = query.Where(c => c.DataInscricao <= filter.DataInscricaoFim.Value);
        }

        // Contar total
        var totalCount = await query.CountAsync(cancellationToken);

        // Ordenar
        query = filter.OrderBy?.ToLower() switch
        {
            "nome" => filter.OrderDescending ? query.OrderByDescending(c => c.Nome) : query.OrderBy(c => c.Nome),
            "numero" => filter.OrderDescending ? query.OrderByDescending(c => c.Numero) : query.OrderBy(c => c.Numero),
            "status" => filter.OrderDescending ? query.OrderByDescending(c => c.Status) : query.OrderBy(c => c.Status),
            _ => filter.OrderDescending ? query.OrderByDescending(c => c.DataInscricao) : query.OrderBy(c => c.DataInscricao)
        };

        // Paginar
        var chapas = await query
            .Skip((filter.Page - 1) * filter.PageSize)
            .Take(filter.PageSize)
            .ToListAsync(cancellationToken);

        return new PagedResultDto<ChapaDto>
        {
            Items = chapas.Select(MapToDto),
            TotalCount = totalCount,
            Page = filter.Page,
            PageSize = filter.PageSize
        };
    }

    public async Task<ChapaDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.Query()
            .Include(c => c.Eleicao)
            .Include(c => c.Membros)
            .ThenInclude(m => m.Profissional)
            .FirstOrDefaultAsync(c => c.Id == id, cancellationToken);

        if (chapa == null) return null;

        return MapToDto(chapa);
    }

    public async Task<ChapaDetailDto?> GetByIdDetailedAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.Query()
            .Include(c => c.Eleicao)
            .Include(c => c.Membros)
            .ThenInclude(m => m.Profissional)
            .Include(c => c.Documentos)
            .Include(c => c.Plataforma)
            .FirstOrDefaultAsync(c => c.Id == id, cancellationToken);

        if (chapa == null) return null;

        return MapToDetailDto(chapa);
    }

    public async Task<IEnumerable<ChapaDto>> GetByEleicaoAsync(Guid eleicaoId, bool apenasAtivas = false, CancellationToken cancellationToken = default)
    {
        var query = _chapaRepository.Query()
            .Include(c => c.Eleicao)
            .Include(c => c.Membros)
            .Where(c => c.EleicaoId == eleicaoId);

        if (apenasAtivas)
        {
            query = query.Where(c => c.Status == StatusChapa.Deferida || c.Status == StatusChapa.Registrada);
        }

        var chapas = await query.OrderBy(c => c.Numero).ToListAsync(cancellationToken);

        return chapas.Select(MapToDto);
    }

    public async Task<ChapaEstatisticasDto> GetEstatisticasAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId);
        if (eleicao == null)
            throw new KeyNotFoundException("Eleicao nao encontrada");

        var chapas = await _chapaRepository.Query()
            .Include(c => c.Membros)
            .Include(c => c.Documentos)
            .Where(c => c.EleicaoId == eleicaoId)
            .ToListAsync(cancellationToken);

        return new ChapaEstatisticasDto
        {
            EleicaoId = eleicaoId,
            EleicaoNome = eleicao.Nome,
            TotalChapas = chapas.Count,
            ChapasRascunho = chapas.Count(c => c.Status == StatusChapa.Rascunho),
            ChapasPendentes = chapas.Count(c => c.Status == StatusChapa.PendenteDocumentos),
            ChapasAguardandoAnalise = chapas.Count(c => c.Status == StatusChapa.AguardandoAnalise),
            ChapasEmAnalise = chapas.Count(c => c.Status == StatusChapa.EmAnalise),
            ChapasDeferidas = chapas.Count(c => c.Status == StatusChapa.Deferida),
            ChapasIndeferidas = chapas.Count(c => c.Status == StatusChapa.Indeferida),
            ChapasImpugnadas = chapas.Count(c => c.Status == StatusChapa.Impugnada),
            ChapasRegistradas = chapas.Count(c => c.Status == StatusChapa.Registrada),
            ChapasCanceladas = chapas.Count(c => c.Status == StatusChapa.Cancelada),
            TotalMembros = chapas.Sum(c => c.Membros?.Count ?? 0),
            MembrosConfirmados = chapas.Sum(c => c.Membros?.Count(m => m.Status == StatusMembroChapa.Confirmado) ?? 0),
            MembrosPendentes = chapas.Sum(c => c.Membros?.Count(m => m.Status == StatusMembroChapa.Pendente) ?? 0),
            TotalDocumentos = chapas.Sum(c => c.Documentos?.Count ?? 0),
            DocumentosAprovados = chapas.Sum(c => c.Documentos?.Count(d => d.Status == StatusDocumentoChapa.Aprovado) ?? 0),
            DocumentosPendentes = chapas.Sum(c => c.Documentos?.Count(d => d.Status == StatusDocumentoChapa.Pendente || d.Status == StatusDocumentoChapa.Enviado) ?? 0)
        };
    }

    #endregion

    #region CRUD Chapa

    public async Task<ChapaDto> CreateAsync(CreateChapaDto dto, Guid userId, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(dto.EleicaoId);
        if (eleicao == null)
            throw new InvalidOperationException("Eleicao nao encontrada");

        // Validar periodo de inscricao
        var validacao = await _calendarioService.ValidarPeriodoAsync(
            dto.EleicaoId,
            new[] { TipoCalendario.Inscricao },
            "registrar chapa",
            cancellationToken);

        if (!validacao.IsValid)
            throw new InvalidOperationException(validacao.Message);

        // Verificar numero unico
        var numeroExiste = await _chapaRepository.Query()
            .AnyAsync(c => c.EleicaoId == dto.EleicaoId && c.Numero == dto.Numero, cancellationToken);

        if (numeroExiste)
            throw new InvalidOperationException($"Ja existe uma chapa com o numero {dto.Numero} nesta eleicao");

        var chapa = new ChapaEleicao
        {
            EleicaoId = dto.EleicaoId,
            Numero = dto.Numero,
            Nome = dto.Nome,
            Sigla = dto.Sigla,
            Slogan = dto.Slogan,
            CorPrimaria = dto.CorPrimaria,
            CorSecundaria = dto.CorSecundaria,
            Status = StatusChapa.Rascunho,
            DataInscricao = DateTime.UtcNow
        };

        await _chapaRepository.AddAsync(chapa);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Chapa {Nome} criada com sucesso na eleicao {EleicaoId} por {UserId}", dto.Nome, dto.EleicaoId, userId);

        return await GetByIdAsync(chapa.Id, cancellationToken) ?? throw new InvalidOperationException("Erro ao recuperar chapa criada");
    }

    public async Task<ChapaDto> UpdateAsync(Guid id, UpdateChapaDto dto, Guid userId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.GetByIdAsync(id);
        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        if (chapa.Status == StatusChapa.Deferida || chapa.Status == StatusChapa.Indeferida || chapa.Status == StatusChapa.Registrada)
            throw new InvalidOperationException("Chapa ja foi analisada e nao pode ser alterada");

        // Validar periodo de inscricao (chapas so podem ser editadas durante inscricao)
        var validacao = await _calendarioService.ValidarPeriodoAsync(
            chapa.EleicaoId,
            new[] { TipoCalendario.Inscricao },
            "editar chapa",
            cancellationToken);

        if (!validacao.IsValid)
            throw new InvalidOperationException(validacao.Message);

        // Verificar numero unico se mudou
        if (!string.IsNullOrEmpty(dto.Numero) && dto.Numero != chapa.Numero)
        {
            var numeroExiste = await _chapaRepository.Query()
                .AnyAsync(c => c.EleicaoId == chapa.EleicaoId && c.Numero == dto.Numero && c.Id != id, cancellationToken);

            if (numeroExiste)
                throw new InvalidOperationException($"Ja existe uma chapa com o numero {dto.Numero} nesta eleicao");

            chapa.Numero = dto.Numero;
        }

        if (!string.IsNullOrEmpty(dto.Nome))
            chapa.Nome = dto.Nome;

        if (dto.Sigla != null)
            chapa.Sigla = dto.Sigla;

        if (dto.Slogan != null)
            chapa.Slogan = dto.Slogan;

        if (dto.CorPrimaria != null)
            chapa.CorPrimaria = dto.CorPrimaria;

        if (dto.CorSecundaria != null)
            chapa.CorSecundaria = dto.CorSecundaria;

        if (dto.LogoUrl != null)
            chapa.LogoUrl = dto.LogoUrl;

        if (dto.FotoUrl != null)
            chapa.FotoUrl = dto.FotoUrl;

        await _chapaRepository.UpdateAsync(chapa);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Chapa {Id} atualizada com sucesso por {UserId}", id, userId);

        return await GetByIdAsync(id, cancellationToken) ?? throw new InvalidOperationException("Erro ao recuperar chapa atualizada");
    }

    public async Task DeleteAsync(Guid id, Guid userId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.GetByIdAsync(id);
        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        if (chapa.Status != StatusChapa.Rascunho)
            throw new InvalidOperationException("Apenas chapas em rascunho podem ser excluidas");

        await _chapaRepository.DeleteAsync(chapa);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Chapa {Id} excluida com sucesso por {UserId}", id, userId);
    }

    #endregion

    #region Workflow Chapa

    public async Task<ChapaDto> SubmeterParaAnaliseAsync(Guid id, Guid userId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.Query()
            .Include(c => c.Membros)
            .Include(c => c.Documentos)
            .FirstOrDefaultAsync(c => c.Id == id, cancellationToken);

        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        if (chapa.Status != StatusChapa.Rascunho && chapa.Status != StatusChapa.PendenteDocumentos)
            throw new InvalidOperationException("Chapa nao esta em rascunho ou pendente de documentos");

        // Validar periodo de inscricao (submissao so pode ocorrer durante inscricao)
        var validacao = await _calendarioService.ValidarPeriodoAsync(
            chapa.EleicaoId,
            new[] { TipoCalendario.Inscricao },
            "submeter chapa para analise",
            cancellationToken);

        if (!validacao.IsValid)
            throw new InvalidOperationException(validacao.Message);

        // Validar requisitos minimos
        if (chapa.Membros == null || chapa.Membros.Count < 2)
            throw new InvalidOperationException("Chapa precisa ter pelo menos 2 membros");

        // Verificar se tem presidente
        var temPresidente = chapa.Membros.Any(m => m.Tipo == TipoMembroChapa.Presidente);
        if (!temPresidente)
            throw new InvalidOperationException("Chapa precisa ter um presidente");

        chapa.Status = StatusChapa.AguardandoAnalise;

        await _chapaRepository.UpdateAsync(chapa);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Chapa {Id} submetida para analise por {UserId}", id, userId);

        return await GetByIdAsync(id, cancellationToken) ?? throw new InvalidOperationException("Erro ao recuperar chapa");
    }

    public async Task<ChapaDto> IniciarAnaliseAsync(Guid id, Guid userId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.GetByIdAsync(id);
        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        if (chapa.Status != StatusChapa.AguardandoAnalise)
            throw new InvalidOperationException("Chapa nao esta aguardando analise");

        chapa.Status = StatusChapa.EmAnalise;

        await _chapaRepository.UpdateAsync(chapa);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Analise da chapa {Id} iniciada por {UserId}", id, userId);

        return await GetByIdAsync(id, cancellationToken) ?? throw new InvalidOperationException("Erro ao recuperar chapa");
    }

    public async Task<ChapaDto> DeferirAsync(Guid id, string? parecer, Guid analistId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.GetByIdAsync(id);
        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        if (chapa.Status != StatusChapa.AguardandoAnalise && chapa.Status != StatusChapa.EmAnalise)
            throw new InvalidOperationException("Chapa nao esta aguardando analise");

        chapa.Status = StatusChapa.Deferida;
        chapa.DataHomologacao = DateTime.UtcNow;

        await _chapaRepository.UpdateAsync(chapa);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Chapa {Id} deferida por {AnalistId}", id, analistId);

        return await GetByIdAsync(id, cancellationToken) ?? throw new InvalidOperationException("Erro ao recuperar chapa");
    }

    public async Task<ChapaDto> IndeferirAsync(Guid id, string motivo, Guid analistId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.GetByIdAsync(id);
        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        if (chapa.Status != StatusChapa.AguardandoAnalise && chapa.Status != StatusChapa.EmAnalise)
            throw new InvalidOperationException("Chapa nao esta aguardando analise");

        chapa.Status = StatusChapa.Indeferida;
        chapa.DataIndeferimento = DateTime.UtcNow;
        chapa.MotivoIndeferimento = motivo;

        await _chapaRepository.UpdateAsync(chapa);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Chapa {Id} indeferida por {AnalistId}. Motivo: {Motivo}", id, analistId, motivo);

        return await GetByIdAsync(id, cancellationToken) ?? throw new InvalidOperationException("Erro ao recuperar chapa");
    }

    public async Task<ChapaDto> SolicitarDocumentosAsync(Guid id, List<int> documentos, string? observacao, Guid userId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.GetByIdAsync(id);
        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        if (chapa.Status != StatusChapa.EmAnalise && chapa.Status != StatusChapa.AguardandoAnalise)
            throw new InvalidOperationException("Chapa nao esta em analise");

        chapa.Status = StatusChapa.PendenteDocumentos;

        await _chapaRepository.UpdateAsync(chapa);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Documentos solicitados para chapa {Id} por {UserId}: {Documentos}", id, userId, string.Join(", ", documentos));

        return await GetByIdAsync(id, cancellationToken) ?? throw new InvalidOperationException("Erro ao recuperar chapa");
    }

    #endregion

    #region Membros

    public async Task<IEnumerable<MembroChapaDto>> GetMembrosAsync(Guid chapaId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.GetByIdAsync(chapaId);
        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        var membros = await _membroRepository.Query()
            .Include(m => m.Profissional)
            .Include(m => m.SubstituidoPor)
            .Where(m => m.ChapaId == chapaId)
            .OrderBy(m => m.Ordem)
            .ToListAsync(cancellationToken);

        return membros.Select(MapMembroToDto);
    }

    /// <summary>
    /// Valida um membro antes de adicionar a chapa
    /// </summary>
    public async Task<ValidacaoMembroResultDto> ValidarMembroAsync(Guid chapaId, CreateMembroChapaDto dto, CancellationToken cancellationToken = default)
    {
        var erros = new List<string>();
        var avisos = new List<string>();

        var chapa = await _chapaRepository.Query()
            .Include(c => c.Membros)
            .FirstOrDefaultAsync(c => c.Id == chapaId, cancellationToken);

        if (chapa == null)
        {
            erros.Add("Chapa nao encontrada");
            return new ValidacaoMembroResultDto { Valido = false, Erros = erros, Avisos = avisos };
        }

        // 1. Validar unicidade do profissional na eleicao
        if (dto.ProfissionalId.HasValue && dto.ProfissionalId.Value != Guid.Empty)
        {
            var jaParticipa = await _membroRepository.Query()
                .Include(m => m.Chapa)
                .AnyAsync(m => m.ProfissionalId == dto.ProfissionalId.Value &&
                              m.Chapa!.EleicaoId == chapa.EleicaoId &&
                              m.Status != StatusMembroChapa.Recusado &&
                              m.Status != StatusMembroChapa.Substituido &&
                              m.Status != StatusMembroChapa.Inabilitado, cancellationToken);

            if (jaParticipa)
                erros.Add("Profissional ja esta registrado em outra chapa nesta eleicao");
        }

        // 2. Validar unicidade por CPF na eleicao
        if (!string.IsNullOrEmpty(dto.Cpf))
        {
            var cpfExiste = await _membroRepository.Query()
                .Include(m => m.Chapa)
                .AnyAsync(m => m.Cpf == dto.Cpf &&
                              m.Chapa!.EleicaoId == chapa.EleicaoId &&
                              m.Status != StatusMembroChapa.Recusado &&
                              m.Status != StatusMembroChapa.Substituido &&
                              m.Status != StatusMembroChapa.Inabilitado, cancellationToken);

            if (cpfExiste)
                erros.Add("CPF ja esta registrado em outra chapa nesta eleicao");
        }

        // 3. Validar unicidade por RegistroCAU na eleicao
        if (!string.IsNullOrEmpty(dto.RegistroCAU))
        {
            var registroExiste = await _membroRepository.Query()
                .Include(m => m.Chapa)
                .AnyAsync(m => m.RegistroCAU == dto.RegistroCAU &&
                              m.Chapa!.EleicaoId == chapa.EleicaoId &&
                              m.Status != StatusMembroChapa.Recusado &&
                              m.Status != StatusMembroChapa.Substituido &&
                              m.Status != StatusMembroChapa.Inabilitado, cancellationToken);

            if (registroExiste)
                erros.Add("Registro CAU ja esta registrado em outra chapa nesta eleicao");
        }

        // 4. Validar cargo unico: apenas 1 Presidente por chapa
        if (dto.TipoMembro == TipoMembroChapa.Presidente)
        {
            var temPresidente = chapa.Membros?.Any(m =>
                m.Tipo == TipoMembroChapa.Presidente &&
                m.Status != StatusMembroChapa.Recusado &&
                m.Status != StatusMembroChapa.Substituido &&
                m.Status != StatusMembroChapa.Inabilitado) ?? false;

            if (temPresidente)
                erros.Add("Chapa ja possui um Presidente. Apenas 1 Presidente e permitido por chapa");
        }

        // 5. Validar cargo unico: apenas 1 Vice-Presidente por chapa
        if (dto.TipoMembro == TipoMembroChapa.VicePresidente)
        {
            var temVice = chapa.Membros?.Any(m =>
                m.Tipo == TipoMembroChapa.VicePresidente &&
                m.Status != StatusMembroChapa.Recusado &&
                m.Status != StatusMembroChapa.Substituido &&
                m.Status != StatusMembroChapa.Inabilitado) ?? false;

            if (temVice)
                erros.Add("Chapa ja possui um Vice-Presidente. Apenas 1 Vice-Presidente e permitido por chapa");
        }

        // 6. Validar cargo unico: apenas 1 Primeiro Secretario por chapa
        if (dto.TipoMembro == TipoMembroChapa.PrimeiroSecretario)
        {
            var temCargo = chapa.Membros?.Any(m =>
                m.Tipo == TipoMembroChapa.PrimeiroSecretario &&
                m.Status != StatusMembroChapa.Recusado &&
                m.Status != StatusMembroChapa.Substituido &&
                m.Status != StatusMembroChapa.Inabilitado) ?? false;

            if (temCargo)
                erros.Add("Chapa ja possui um Primeiro Secretario");
        }

        // 7. Validar cargo unico: apenas 1 Segundo Secretario por chapa
        if (dto.TipoMembro == TipoMembroChapa.SegundoSecretario)
        {
            var temCargo = chapa.Membros?.Any(m =>
                m.Tipo == TipoMembroChapa.SegundoSecretario &&
                m.Status != StatusMembroChapa.Recusado &&
                m.Status != StatusMembroChapa.Substituido &&
                m.Status != StatusMembroChapa.Inabilitado) ?? false;

            if (temCargo)
                erros.Add("Chapa ja possui um Segundo Secretario");
        }

        // 8. Validar cargo unico: apenas 1 Primeiro Tesoureiro por chapa
        if (dto.TipoMembro == TipoMembroChapa.PrimeiroTesoureiro)
        {
            var temCargo = chapa.Membros?.Any(m =>
                m.Tipo == TipoMembroChapa.PrimeiroTesoureiro &&
                m.Status != StatusMembroChapa.Recusado &&
                m.Status != StatusMembroChapa.Substituido &&
                m.Status != StatusMembroChapa.Inabilitado) ?? false;

            if (temCargo)
                erros.Add("Chapa ja possui um Primeiro Tesoureiro");
        }

        // 9. Validar cargo unico: apenas 1 Segundo Tesoureiro por chapa
        if (dto.TipoMembro == TipoMembroChapa.SegundoTesoureiro)
        {
            var temCargo = chapa.Membros?.Any(m =>
                m.Tipo == TipoMembroChapa.SegundoTesoureiro &&
                m.Status != StatusMembroChapa.Recusado &&
                m.Status != StatusMembroChapa.Substituido &&
                m.Status != StatusMembroChapa.Inabilitado) ?? false;

            if (temCargo)
                erros.Add("Chapa ja possui um Segundo Tesoureiro");
        }

        // 10. Avisos sobre limites de membros (nao bloqueante)
        var membrosAtivos = chapa.Membros?.Count(m =>
            m.Status != StatusMembroChapa.Recusado &&
            m.Status != StatusMembroChapa.Substituido &&
            m.Status != StatusMembroChapa.Inabilitado) ?? 0;

        if (membrosAtivos >= 20)
            avisos.Add($"Chapa ja possui {membrosAtivos} membros. Verifique o limite maximo permitido");

        return new ValidacaoMembroResultDto
        {
            Valido = erros.Count == 0,
            Erros = erros,
            Avisos = avisos
        };
    }

    public async Task<MembroChapaDto> AddMembroAsync(Guid chapaId, CreateMembroChapaDto dto, Guid userId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.Query()
            .Include(c => c.Membros)
            .FirstOrDefaultAsync(c => c.Id == chapaId, cancellationToken);

        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        if (chapa.Status != StatusChapa.Rascunho && chapa.Status != StatusChapa.PendenteDocumentos)
            throw new InvalidOperationException("Chapa nao esta em formacao");

        // Validar periodo de inscricao
        var validacaoPeriodo = await _calendarioService.ValidarPeriodoAsync(
            chapa.EleicaoId,
            new[] { TipoCalendario.Inscricao },
            "adicionar membro a chapa",
            cancellationToken);

        if (!validacaoPeriodo.IsValid)
            throw new InvalidOperationException(validacaoPeriodo.Message);

        // Executar todas as validacoes de membro
        var validacao = await ValidarMembroAsync(chapaId, dto, cancellationToken);
        if (!validacao.Valido)
        {
            var errosMsg = string.Join("; ", validacao.Erros);
            throw new InvalidOperationException(errosMsg);
        }

        // Obter proxima ordem
        var ultimaOrdem = await _membroRepository.Query()
            .Where(m => m.ChapaId == chapaId)
            .MaxAsync(m => (int?)m.Ordem, cancellationToken) ?? 0;

        var membro = new MembroChapa
        {
            ChapaId = chapaId,
            ProfissionalId = dto.ProfissionalId,
            Nome = dto.Nome ?? string.Empty,
            Cpf = dto.Cpf,
            RegistroCAU = dto.RegistroCAU,
            Email = dto.Email,
            Telefone = dto.Telefone,
            Tipo = dto.TipoMembro,
            Cargo = dto.Cargo,
            Titular = dto.Titular,
            CurriculoResumo = dto.CurriculoResumo,
            Status = StatusMembroChapa.Pendente,
            Ordem = ultimaOrdem + 1
        };

        await _membroRepository.AddAsync(membro);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Membro {Nome} ({Tipo}) adicionado a chapa {ChapaId} por {UserId}",
            dto.Nome, dto.TipoMembro, chapaId, userId);

        // Log avisos se houver
        foreach (var aviso in validacao.Avisos)
        {
            _logger.LogWarning("Aviso ao adicionar membro a chapa {ChapaId}: {Aviso}", chapaId, aviso);
        }

        return MapMembroToDto(membro);
    }

    public async Task<MembroChapaDto> UpdateMembroAsync(Guid chapaId, Guid membroId, UpdateMembroChapaDto dto, Guid userId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.Query()
            .Include(c => c.Membros)
            .FirstOrDefaultAsync(c => c.Id == chapaId, cancellationToken);

        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        if (chapa.Status != StatusChapa.Rascunho && chapa.Status != StatusChapa.PendenteDocumentos)
            throw new InvalidOperationException("Chapa nao esta em formacao");

        // Validar periodo de inscricao
        var validacao = await _calendarioService.ValidarPeriodoAsync(
            chapa.EleicaoId,
            new[] { TipoCalendario.Inscricao },
            "atualizar membro da chapa",
            cancellationToken);

        if (!validacao.IsValid)
            throw new InvalidOperationException(validacao.Message);

        var membro = await _membroRepository.GetByIdAsync(membroId);
        if (membro == null || membro.ChapaId != chapaId)
            throw new KeyNotFoundException("Membro nao encontrado na chapa");

        // Se esta alterando o tipo do membro, validar unicidade do cargo
        if (dto.TipoMembro.HasValue && dto.TipoMembro.Value != membro.Tipo)
        {
            var tipoUnico = new[] {
                TipoMembroChapa.Presidente,
                TipoMembroChapa.VicePresidente,
                TipoMembroChapa.PrimeiroSecretario,
                TipoMembroChapa.SegundoSecretario,
                TipoMembroChapa.PrimeiroTesoureiro,
                TipoMembroChapa.SegundoTesoureiro
            };

            if (tipoUnico.Contains(dto.TipoMembro.Value))
            {
                var cargoExiste = chapa.Membros?.Any(m =>
                    m.Id != membroId &&
                    m.Tipo == dto.TipoMembro.Value &&
                    m.Status != StatusMembroChapa.Recusado &&
                    m.Status != StatusMembroChapa.Substituido &&
                    m.Status != StatusMembroChapa.Inabilitado) ?? false;

                if (cargoExiste)
                    throw new InvalidOperationException($"Chapa ja possui um membro com o cargo {dto.TipoMembro.Value}");
            }
        }

        if (!string.IsNullOrEmpty(dto.Nome))
            membro.Nome = dto.Nome;

        if (dto.Email != null)
            membro.Email = dto.Email;

        if (dto.Telefone != null)
            membro.Telefone = dto.Telefone;

        if (dto.TipoMembro.HasValue)
            membro.Tipo = dto.TipoMembro.Value;

        if (dto.Cargo != null)
            membro.Cargo = dto.Cargo;

        if (dto.Titular.HasValue)
            membro.Titular = dto.Titular.Value;

        if (dto.Ordem.HasValue)
            membro.Ordem = dto.Ordem.Value;

        if (dto.FotoUrl != null)
            membro.FotoUrl = dto.FotoUrl;

        if (dto.CurriculoResumo != null)
            membro.CurriculoResumo = dto.CurriculoResumo;

        await _membroRepository.UpdateAsync(membro);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Membro {MembroId} atualizado na chapa {ChapaId} por {UserId}", membroId, chapaId, userId);

        return MapMembroToDto(membro);
    }

    public async Task RemoveMembroAsync(Guid chapaId, Guid membroId, Guid userId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.GetByIdAsync(chapaId);
        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        if (chapa.Status != StatusChapa.Rascunho && chapa.Status != StatusChapa.PendenteDocumentos)
            throw new InvalidOperationException("Chapa nao esta em formacao");

        // Validar periodo de inscricao
        var validacao = await _calendarioService.ValidarPeriodoAsync(
            chapa.EleicaoId,
            new[] { TipoCalendario.Inscricao },
            "remover membro da chapa",
            cancellationToken);

        if (!validacao.IsValid)
            throw new InvalidOperationException(validacao.Message);

        var membro = await _membroRepository.GetByIdAsync(membroId);
        if (membro == null || membro.ChapaId != chapaId)
            throw new KeyNotFoundException("Membro nao encontrado na chapa");

        await _membroRepository.DeleteAsync(membro);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Membro {MembroId} removido da chapa {ChapaId} por {UserId}", membroId, chapaId, userId);
    }

    /// <summary>
    /// Envia convite de confirmacao para o membro (status: Pendente -> AguardandoConfirmacao)
    /// </summary>
    public async Task<MembroChapaDto> EnviarConfirmacaoMembroAsync(Guid chapaId, EnviarConfirmacaoMembroDto dto, Guid userId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.GetByIdAsync(chapaId);
        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        var membro = await _membroRepository.Query()
            .Include(m => m.Confirmacoes)
            .Include(m => m.Profissional)
            .FirstOrDefaultAsync(m => m.Id == dto.MembroId && m.ChapaId == chapaId, cancellationToken);

        if (membro == null)
            throw new KeyNotFoundException("Membro nao encontrado na chapa");

        if (membro.Status != StatusMembroChapa.Pendente && membro.Status != StatusMembroChapa.AguardandoConfirmacao)
            throw new InvalidOperationException($"Membro com status {membro.Status} nao pode receber convite de confirmacao");

        // Gerar token de confirmacao
        var token = Guid.NewGuid().ToString("N");
        var expiracao = DateTime.UtcNow.AddDays(7); // Token valido por 7 dias

        membro.TokenConfirmacao = token;
        membro.TokenConfirmacaoExpiracao = expiracao;
        membro.Email = dto.Email;
        membro.Status = StatusMembroChapa.AguardandoConfirmacao;

        // Registrar tentativa de envio
        var confirmacao = new ConfirmacaoMembroChapa
        {
            MembroId = membro.Id,
            Token = token,
            TokenExpiracao = expiracao,
            TentativasEnvio = 1,
            UltimoEnvio = DateTime.UtcNow
        };
        membro.Confirmacoes.Add(confirmacao);

        await _membroRepository.UpdateAsync(membro);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        if (_notificacaoService != null)
        {
            var assunto = $"Confirmacao de participacao na chapa {chapa.Nome}";
            var linkConfirmacao = $"/candidato/confirmar-membro?token={token}";
            var corpo = $"Voce recebeu um convite para confirmar participacao na chapa {chapa.Nome}. " +
                        $"Utilize o token {token} ou acesse {linkConfirmacao}. " +
                        $"Este convite expira em {expiracao:dd/MM/yyyy HH:mm}.";

            try
            {
                if (!string.IsNullOrWhiteSpace(dto.Email))
                {
                    await _notificacaoService.EnviarEmailAsync(dto.Email, assunto, corpo, cancellationToken);
                }

                if (membro.Profissional?.UsuarioId is Guid usuarioId)
                {
                    await _notificacaoService.EnviarAsync(new EnviarNotificacaoDto
                    {
                        UsuarioId = usuarioId,
                        Tipo = TipoNotificacao.Eleicao,
                        Canal = CanalNotificacao.InApp,
                        Titulo = "Convite de confirmacao de chapa",
                        Mensagem = "Ha um convite pendente para confirmar sua participacao na chapa.",
                        Link = linkConfirmacao
                    }, cancellationToken);
                }
            }
            catch (Exception ex)
            {
                _logger.LogWarning(ex, "Falha ao enviar notificacao de convite do membro {MembroId}", membro.Id);
            }
        }

        _logger.LogInformation("Convite de confirmacao enviado para membro {MembroId} ({Email}) na chapa {ChapaId} por {UserId}",
            dto.MembroId, dto.Email, chapaId, userId);

        return MapMembroToDto(membro);
    }

    /// <summary>
    /// Confirma ou recusa participacao do membro na chapa (status: AguardandoConfirmacao -> Confirmado/Recusado)
    /// </summary>
    public async Task<MembroChapaDto> ConfirmarMembroAsync(ConfirmacaoMembroChapaDto dto, CancellationToken cancellationToken = default)
    {
        var membro = await _membroRepository.Query()
            .Include(m => m.Confirmacoes)
            .FirstOrDefaultAsync(m => m.TokenConfirmacao == dto.Token, cancellationToken);

        if (membro == null)
            throw new KeyNotFoundException("Token de confirmacao invalido");

        if (membro.Status != StatusMembroChapa.AguardandoConfirmacao)
            throw new InvalidOperationException($"Membro com status {membro.Status} nao pode ser confirmado");

        if (membro.TokenConfirmacaoExpiracao < DateTime.UtcNow)
            throw new InvalidOperationException("Token de confirmacao expirado. Solicite um novo convite");

        // Atualizar registro de confirmacao
        var confirmacaoReg = membro.Confirmacoes.FirstOrDefault(c => c.Token == dto.Token);
        if (confirmacaoReg != null)
        {
            confirmacaoReg.Confirmado = dto.Aceito;
            confirmacaoReg.DataConfirmacao = dto.Aceito ? DateTime.UtcNow : null;
            confirmacaoReg.Recusado = !dto.Aceito;
            confirmacaoReg.DataRecusa = dto.Aceito ? null : DateTime.UtcNow;
            confirmacaoReg.MotivoRecusa = dto.MotivoRecusa;
        }

        if (dto.Aceito)
        {
            membro.Status = StatusMembroChapa.Confirmado;
            membro.DataConfirmacao = DateTime.UtcNow;
            _logger.LogInformation("Membro {MembroId} confirmou participacao na chapa {ChapaId}", membro.Id, membro.ChapaId);
        }
        else
        {
            membro.Status = StatusMembroChapa.Recusado;
            membro.MotivoRecusa = dto.MotivoRecusa;
            _logger.LogInformation("Membro {MembroId} recusou participacao na chapa {ChapaId}. Motivo: {Motivo}",
                membro.Id, membro.ChapaId, dto.MotivoRecusa);
        }

        // Limpar token apos uso
        membro.TokenConfirmacao = null;
        membro.TokenConfirmacaoExpiracao = null;

        await _membroRepository.UpdateAsync(membro);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        return MapMembroToDto(membro);
    }

    /// <summary>
    /// Substitui um membro da chapa por outro (status do antigo: Substituido, novo membro criado)
    /// </summary>
    public async Task<MembroChapaDto> SubstituirMembroAsync(Guid chapaId, SubstituicaoMembroChapaDto dto, Guid userId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.Query()
            .Include(c => c.Membros)
            .FirstOrDefaultAsync(c => c.Id == chapaId, cancellationToken);

        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        // Permitir substituicao apenas em estados especificos
        var statusPermitidos = new[] {
            StatusChapa.Rascunho,
            StatusChapa.PendenteDocumentos,
            StatusChapa.Deferida, // Permite substituicao apos deferimento, com restricoes
            StatusChapa.Registrada
        };

        if (!statusPermitidos.Contains(chapa.Status))
            throw new InvalidOperationException($"Chapa com status {chapa.Status} nao permite substituicao de membros");

        var membroAntigo = await _membroRepository.Query()
            .Include(m => m.SubstituidoPor)
            .FirstOrDefaultAsync(m => m.Id == dto.MembroSubstitutoId && m.ChapaId == chapaId, cancellationToken);

        if (membroAntigo == null)
            throw new KeyNotFoundException("Membro a ser substituido nao encontrado na chapa");

        // Nao pode substituir membro ja substituido
        if (membroAntigo.Status == StatusMembroChapa.Substituido)
            throw new InvalidOperationException("Membro ja foi substituido anteriormente");

        // Validar novo profissional se informado
        if (dto.NovoProfissionalId.HasValue && dto.NovoProfissionalId.Value != Guid.Empty)
        {
            var jaParticipa = await _membroRepository.Query()
                .Include(m => m.Chapa)
                .AnyAsync(m => m.ProfissionalId == dto.NovoProfissionalId.Value &&
                              m.Chapa!.EleicaoId == chapa.EleicaoId &&
                              m.Status != StatusMembroChapa.Recusado &&
                              m.Status != StatusMembroChapa.Substituido &&
                              m.Status != StatusMembroChapa.Inabilitado, cancellationToken);

            if (jaParticipa)
                throw new InvalidOperationException("Novo profissional ja esta registrado em outra chapa nesta eleicao");
        }

        // Validar unicidade por CPF do novo membro
        if (!string.IsNullOrEmpty(dto.NovoCpf) && dto.NovoCpf != membroAntigo.Cpf)
        {
            var cpfExiste = await _membroRepository.Query()
                .Include(m => m.Chapa)
                .AnyAsync(m => m.Cpf == dto.NovoCpf &&
                              m.Chapa!.EleicaoId == chapa.EleicaoId &&
                              m.Status != StatusMembroChapa.Recusado &&
                              m.Status != StatusMembroChapa.Substituido &&
                              m.Status != StatusMembroChapa.Inabilitado, cancellationToken);

            if (cpfExiste)
                throw new InvalidOperationException("CPF do novo membro ja esta registrado em outra chapa nesta eleicao");
        }

        // Validar unicidade por RegistroCAU do novo membro
        if (!string.IsNullOrEmpty(dto.NovoRegistroCAU) && dto.NovoRegistroCAU != membroAntigo.RegistroCAU)
        {
            var registroExiste = await _membroRepository.Query()
                .Include(m => m.Chapa)
                .AnyAsync(m => m.RegistroCAU == dto.NovoRegistroCAU &&
                              m.Chapa!.EleicaoId == chapa.EleicaoId &&
                              m.Status != StatusMembroChapa.Recusado &&
                              m.Status != StatusMembroChapa.Substituido &&
                              m.Status != StatusMembroChapa.Inabilitado, cancellationToken);

            if (registroExiste)
                throw new InvalidOperationException("Registro CAU do novo membro ja esta registrado em outra chapa nesta eleicao");
        }

        // Criar novo membro com os mesmos cargo/tipo do antigo
        var novoMembro = new MembroChapa
        {
            ChapaId = chapaId,
            ProfissionalId = dto.NovoProfissionalId,
            Nome = dto.NovoNome ?? string.Empty,
            Cpf = dto.NovoCpf,
            RegistroCAU = dto.NovoRegistroCAU,
            Email = dto.NovoEmail,
            Telefone = dto.NovoTelefone,
            Tipo = membroAntigo.Tipo, // Mantém o mesmo tipo/cargo
            Cargo = membroAntigo.Cargo,
            Titular = membroAntigo.Titular,
            CurriculoResumo = dto.NovoCurriculoResumo,
            Status = StatusMembroChapa.Pendente,
            Ordem = membroAntigo.Ordem // Mantém a mesma ordem
        };

        await _membroRepository.AddAsync(novoMembro);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        // Atualizar membro antigo como substituido
        membroAntigo.Status = StatusMembroChapa.Substituido;
        membroAntigo.SubstituidoPorId = novoMembro.Id;
        membroAntigo.DataSubstituicao = DateTime.UtcNow;
        membroAntigo.MotivoSubstituicao = dto.MotivoSubstituicao;

        await _membroRepository.UpdateAsync(membroAntigo);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Membro {MembroAntigoId} substituido por {NovoMembroId} na chapa {ChapaId} por {UserId}. Motivo: {Motivo}",
            membroAntigo.Id, novoMembro.Id, chapaId, userId, dto.MotivoSubstituicao);

        return MapMembroToDto(novoMembro);
    }

    /// <summary>
    /// Inabilita um membro da chapa (status: -> Inabilitado)
    /// </summary>
    public async Task<MembroChapaDto> InabilitarMembroAsync(Guid chapaId, InabilitarMembroChapaDto dto, Guid userId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.GetByIdAsync(chapaId);
        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        var membro = await _membroRepository.GetByIdAsync(dto.MembroId);
        if (membro == null || membro.ChapaId != chapaId)
            throw new KeyNotFoundException("Membro nao encontrado na chapa");

        // Nao pode inabilitar membro ja inabilitado ou substituido
        if (membro.Status == StatusMembroChapa.Inabilitado)
            throw new InvalidOperationException("Membro ja esta inabilitado");

        if (membro.Status == StatusMembroChapa.Substituido)
            throw new InvalidOperationException("Membro substituido nao pode ser inabilitado");

        membro.Status = StatusMembroChapa.Inabilitado;
        membro.MotivoInabilitacao = dto.Motivo;

        await _membroRepository.UpdateAsync(membro);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Membro {MembroId} inabilitado na chapa {ChapaId} por {UserId}. Motivo: {Motivo}",
            dto.MembroId, chapaId, userId, dto.Motivo);

        return MapMembroToDto(membro);
    }

    #endregion

    #region Documentos

    public async Task<IEnumerable<DocumentoChapaDto>> GetDocumentosAsync(Guid chapaId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.GetByIdAsync(chapaId);
        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        var documentos = await _documentoRepository.Query()
            .Where(d => d.ChapaId == chapaId)
            .OrderBy(d => d.Tipo)
            .ToListAsync(cancellationToken);

        return documentos.Select(MapDocumentoToDto);
    }

    public async Task<DocumentoChapaDto> AddDocumentoAsync(Guid chapaId, CreateDocumentoChapaDto dto, Guid userId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.GetByIdAsync(chapaId);
        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        if (chapa.Status == StatusChapa.Deferida || chapa.Status == StatusChapa.Indeferida || chapa.Status == StatusChapa.Registrada)
            throw new InvalidOperationException("Chapa ja foi analisada");

        // Validar periodo de inscricao (documentos podem ser adicionados durante inscricao)
        var validacao = await _calendarioService.ValidarPeriodoAsync(
            chapa.EleicaoId,
            new[] { TipoCalendario.Inscricao },
            "adicionar documento a chapa",
            cancellationToken);

        if (!validacao.IsValid)
            throw new InvalidOperationException(validacao.Message);

        // Get next order
        var ultimaOrdem = await _documentoRepository.Query()
            .Where(d => d.ChapaId == chapaId)
            .MaxAsync(d => (int?)d.Ordem, cancellationToken) ?? 0;

        var documento = new DocumentoChapa
        {
            ChapaId = chapaId,
            Tipo = dto.Tipo,
            Nome = dto.Nome,
            Descricao = dto.Descricao,
            ArquivoUrl = dto.ArquivoUrl,
            ArquivoNome = dto.ArquivoNome,
            ArquivoTamanho = dto.ArquivoTamanho,
            ArquivoTipo = dto.ArquivoTipo,
            Status = StatusDocumentoChapa.Enviado,
            DataEnvio = DateTime.UtcNow,
            Ordem = ultimaOrdem + 1
        };

        await _documentoRepository.AddAsync(documento);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Documento {Tipo} adicionado a chapa {ChapaId} por {UserId}", dto.Tipo, chapaId, userId);

        return MapDocumentoToDto(documento);
    }

    public async Task<DocumentoChapaDto> AnalisarDocumentoAsync(Guid chapaId, Guid documentoId, AnaliseDocumentoChapaDto dto, Guid analistaId, CancellationToken cancellationToken = default)
    {
        var documento = await _documentoRepository.GetByIdAsync(documentoId);
        if (documento == null || documento.ChapaId != chapaId)
            throw new KeyNotFoundException("Documento nao encontrado");

        documento.Status = dto.Status;
        documento.DataAnalise = DateTime.UtcNow;
        documento.AnalisadoPor = analistaId.ToString();

        if (dto.Status == StatusDocumentoChapa.Rejeitado)
        {
            documento.MotivoRejeicao = dto.Parecer;
        }

        await _documentoRepository.UpdateAsync(documento);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Documento {DocumentoId} analisado na chapa {ChapaId} por {AnalistaId}", documentoId, chapaId, analistaId);

        return MapDocumentoToDto(documento);
    }

    public async Task RemoveDocumentoAsync(Guid chapaId, Guid documentoId, Guid userId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.GetByIdAsync(chapaId);
        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        if (chapa.Status == StatusChapa.Deferida || chapa.Status == StatusChapa.Indeferida || chapa.Status == StatusChapa.Registrada)
            throw new InvalidOperationException("Chapa ja foi analisada");

        // Validar periodo de inscricao
        var validacao = await _calendarioService.ValidarPeriodoAsync(
            chapa.EleicaoId,
            new[] { TipoCalendario.Inscricao },
            "remover documento da chapa",
            cancellationToken);

        if (!validacao.IsValid)
            throw new InvalidOperationException(validacao.Message);

        var documento = await _documentoRepository.GetByIdAsync(documentoId);
        if (documento == null || documento.ChapaId != chapaId)
            throw new KeyNotFoundException("Documento nao encontrado");

        await _documentoRepository.DeleteAsync(documento);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Documento {DocumentoId} removido da chapa {ChapaId} por {UserId}", documentoId, chapaId, userId);
    }

    #endregion

    #region Plataforma

    public async Task<PlataformaEleitoralDto?> GetPlataformaAsync(Guid chapaId, CancellationToken cancellationToken = default)
    {
        var plataforma = await _plataformaRepository.Query()
            .FirstOrDefaultAsync(p => p.ChapaId == chapaId, cancellationToken);

        if (plataforma == null) return null;

        return MapPlataformaToDto(plataforma);
    }

    public async Task<PlataformaEleitoralDto> SavePlataformaAsync(Guid chapaId, CreatePlataformaEleitoralDto dto, Guid userId, CancellationToken cancellationToken = default)
    {
        var chapa = await _chapaRepository.GetByIdAsync(chapaId);
        if (chapa == null)
            throw new KeyNotFoundException("Chapa nao encontrada");

        var plataforma = await _plataformaRepository.Query()
            .FirstOrDefaultAsync(p => p.ChapaId == chapaId, cancellationToken);

        if (plataforma == null)
        {
            plataforma = new PlataformaEleitoral
            {
                ChapaId = chapaId,
                Titulo = dto.Titulo,
                Resumo = dto.Resumo,
                Conteudo = dto.Conteudo,
                Missao = dto.Missao,
                Visao = dto.Visao,
                Valores = dto.Valores,
                PropostasJson = dto.PropostasJson,
                MetasJson = dto.MetasJson,
                EixosJson = dto.EixosJson,
                VideoUrl = dto.VideoUrl,
                ApresentacaoUrl = dto.ApresentacaoUrl,
                DataPublicacao = DateTime.UtcNow,
                Publicada = dto.Publicada
            };

            await _plataformaRepository.AddAsync(plataforma);
        }
        else
        {
            plataforma.Titulo = dto.Titulo;
            plataforma.Resumo = dto.Resumo;
            plataforma.Conteudo = dto.Conteudo;
            plataforma.Missao = dto.Missao;
            plataforma.Visao = dto.Visao;
            plataforma.Valores = dto.Valores;
            plataforma.PropostasJson = dto.PropostasJson;
            plataforma.MetasJson = dto.MetasJson;
            plataforma.EixosJson = dto.EixosJson;
            plataforma.VideoUrl = dto.VideoUrl;
            plataforma.ApresentacaoUrl = dto.ApresentacaoUrl;
            plataforma.Publicada = dto.Publicada;

            await _plataformaRepository.UpdateAsync(plataforma);
        }

        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Plataforma eleitoral da chapa {ChapaId} salva por {UserId}", chapaId, userId);

        return MapPlataformaToDto(plataforma);
    }

    #endregion

    #region Mapping

    private ChapaDto MapToDto(ChapaEleicao chapa)
    {
        return new ChapaDto
        {
            Id = chapa.Id,
            EleicaoId = chapa.EleicaoId,
            EleicaoNome = chapa.Eleicao?.Nome ?? string.Empty,
            Numero = chapa.Numero,
            Nome = chapa.Nome,
            Sigla = chapa.Sigla,
            Slogan = chapa.Slogan,
            Status = chapa.Status,
            StatusNome = chapa.Status.ToString(),
            DataInscricao = chapa.DataInscricao,
            DataHomologacao = chapa.DataHomologacao,
            DataIndeferimento = chapa.DataIndeferimento,
            MotivoIndeferimento = chapa.MotivoIndeferimento,
            LogoUrl = chapa.LogoUrl,
            FotoUrl = chapa.FotoUrl,
            CorPrimaria = chapa.CorPrimaria,
            CorSecundaria = chapa.CorSecundaria,
            OrdemSorteio = chapa.OrdemSorteio,
            TotalMembros = chapa.Membros?.Count ?? 0,
            Membros = chapa.Membros?.Select(MapMembroToDto).OrderBy(m => m.Ordem).ToList() ?? new List<MembroChapaDto>(),
            Documentos = chapa.Documentos?.Select(MapDocumentoToDto).ToList() ?? new List<DocumentoChapaDto>(),
            Plataforma = chapa.Plataforma != null ? MapPlataformaToDto(chapa.Plataforma) : null,
            CreatedAt = chapa.CreatedAt,
            UpdatedAt = chapa.UpdatedAt
        };
    }

    private ChapaDetailDto MapToDetailDto(ChapaEleicao chapa)
    {
        return new ChapaDetailDto
        {
            Id = chapa.Id,
            EleicaoId = chapa.EleicaoId,
            EleicaoNome = chapa.Eleicao?.Nome ?? string.Empty,
            Numero = chapa.Numero,
            Nome = chapa.Nome,
            Sigla = chapa.Sigla,
            Slogan = chapa.Slogan,
            Status = chapa.Status,
            StatusNome = chapa.Status.ToString(),
            DataInscricao = chapa.DataInscricao,
            DataHomologacao = chapa.DataHomologacao,
            DataIndeferimento = chapa.DataIndeferimento,
            MotivoIndeferimento = chapa.MotivoIndeferimento,
            LogoUrl = chapa.LogoUrl,
            FotoUrl = chapa.FotoUrl,
            CorPrimaria = chapa.CorPrimaria,
            CorSecundaria = chapa.CorSecundaria,
            OrdemSorteio = chapa.OrdemSorteio,
            TotalMembros = chapa.Membros?.Count ?? 0,
            Membros = chapa.Membros?.Select(MapMembroToDto).OrderBy(m => m.Ordem).ToList() ?? new List<MembroChapaDto>(),
            Documentos = chapa.Documentos?.Select(MapDocumentoToDto).ToList() ?? new List<DocumentoChapaDto>(),
            Plataforma = chapa.Plataforma != null ? MapPlataformaToDto(chapa.Plataforma) : null,
            CreatedAt = chapa.CreatedAt,
            UpdatedAt = chapa.UpdatedAt
        };
    }

    private MembroChapaDto MapMembroToDto(MembroChapa membro)
    {
        return new MembroChapaDto
        {
            Id = membro.Id,
            ChapaId = membro.ChapaId,
            ProfissionalId = membro.ProfissionalId,
            Nome = membro.Profissional?.Nome ?? membro.Nome,
            Cpf = membro.Cpf,
            RegistroCAU = membro.RegistroCAU ?? membro.Profissional?.RegistroCAU,
            Email = membro.Email ?? membro.Profissional?.Email,
            Telefone = membro.Telefone,
            Tipo = membro.Tipo,
            TipoNome = membro.Tipo.ToString(),
            Status = membro.Status,
            StatusNome = membro.Status.ToString(),
            Ordem = membro.Ordem,
            Cargo = membro.Cargo,
            Titular = membro.Titular,
            DataConfirmacao = membro.DataConfirmacao,
            FotoUrl = membro.FotoUrl,
            CurriculoResumo = membro.CurriculoResumo,
            MotivoRecusa = membro.MotivoRecusa,
            MotivoInabilitacao = membro.MotivoInabilitacao,
            SubstituidoPorId = membro.SubstituidoPorId,
            SubstituidoPorNome = membro.SubstituidoPor?.Nome,
            DataSubstituicao = membro.DataSubstituicao,
            MotivoSubstituicao = membro.MotivoSubstituicao
        };
    }

    private DocumentoChapaDto MapDocumentoToDto(DocumentoChapa documento)
    {
        return new DocumentoChapaDto
        {
            Id = documento.Id,
            ChapaId = documento.ChapaId,
            MembroId = documento.MembroId,
            Tipo = documento.Tipo,
            TipoNome = documento.Tipo.ToString(),
            Status = documento.Status,
            StatusNome = documento.Status.ToString(),
            Nome = documento.Nome,
            Descricao = documento.Descricao,
            ArquivoUrl = documento.ArquivoUrl,
            ArquivoNome = documento.ArquivoNome,
            ArquivoTamanho = documento.ArquivoTamanho,
            ArquivoTipo = documento.ArquivoTipo,
            DataEnvio = documento.DataEnvio,
            DataAnalise = documento.DataAnalise,
            AnalisadoPor = documento.AnalisadoPor,
            MotivoRejeicao = documento.MotivoRejeicao,
            Obrigatorio = documento.Obrigatorio,
            Ordem = documento.Ordem
        };
    }

    private PlataformaEleitoralDto MapPlataformaToDto(PlataformaEleitoral plataforma)
    {
        return new PlataformaEleitoralDto
        {
            Id = plataforma.Id,
            ChapaId = plataforma.ChapaId,
            Titulo = plataforma.Titulo,
            Resumo = plataforma.Resumo,
            Conteudo = plataforma.Conteudo,
            Missao = plataforma.Missao,
            Visao = plataforma.Visao,
            Valores = plataforma.Valores,
            PropostasJson = plataforma.PropostasJson,
            MetasJson = plataforma.MetasJson,
            EixosJson = plataforma.EixosJson,
            VideoUrl = plataforma.VideoUrl,
            ApresentacaoUrl = plataforma.ApresentacaoUrl,
            DataPublicacao = plataforma.DataPublicacao,
            Publicada = plataforma.Publicada
        };
    }

    #endregion
}
