using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Application.DTOs;
using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Chapas;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

public class ChapaService : IChapaService
{
    private readonly IRepository<ChapaEleicao> _chapaRepository;
    private readonly IRepository<MembroChapa> _membroRepository;
    private readonly IRepository<DocumentoChapa> _documentoRepository;
    private readonly IRepository<Eleicao> _eleicaoRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<ChapaService> _logger;

    public ChapaService(
        IRepository<ChapaEleicao> chapaRepository,
        IRepository<MembroChapa> membroRepository,
        IRepository<DocumentoChapa> documentoRepository,
        IRepository<Eleicao> eleicaoRepository,
        IUnitOfWork unitOfWork,
        ILogger<ChapaService> logger)
    {
        _chapaRepository = chapaRepository;
        _membroRepository = membroRepository;
        _documentoRepository = documentoRepository;
        _eleicaoRepository = eleicaoRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    public async Task<IEnumerable<ChapaDto>> GetAllAsync(Guid? eleicaoId = null)
    {
        var query = _chapaRepository.Query()
            .Include(c => c.Eleicao)
            .Include(c => c.Membros)
            .AsQueryable();

        if (eleicaoId.HasValue)
        {
            query = query.Where(c => c.EleicaoId == eleicaoId.Value);
        }

        var chapas = await query.OrderBy(c => c.Numero).ToListAsync();

        return chapas.Select(MapToDto);
    }

    public async Task<ChapaDto?> GetByIdAsync(Guid id)
    {
        var chapa = await _chapaRepository.Query()
            .Include(c => c.Eleicao)
            .Include(c => c.Membros)
            .ThenInclude(m => m.Profissional)
            .Include(c => c.Documentos)
            .FirstOrDefaultAsync(c => c.Id == id);

        if (chapa == null) return null;

        return MapToDto(chapa);
    }

    public async Task<ChapaDto> CreateAsync(CreateChapaDto dto)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(dto.EleicaoId);
        if (eleicao == null)
            throw new InvalidOperationException("Eleicao nao encontrada");

        // Verificar numero unico
        var numeroExiste = await _chapaRepository.Query()
            .AnyAsync(c => c.EleicaoId == dto.EleicaoId && c.Numero == dto.Numero.ToString());

        if (numeroExiste)
            throw new InvalidOperationException($"Ja existe uma chapa com o numero {dto.Numero} nesta eleicao");

        var chapa = new ChapaEleicao
        {
            EleicaoId = dto.EleicaoId,
            Numero = dto.Numero.ToString(),
            Nome = dto.Nome,
            Sigla = dto.Sigla,
            Slogan = dto.Lema,
            Status = StatusChapa.Rascunho,
            DataInscricao = DateTime.UtcNow
        };

        await _chapaRepository.AddAsync(chapa);
        await _unitOfWork.SaveChangesAsync();

        _logger.LogInformation("Chapa {Nome} criada com sucesso na eleicao {EleicaoId}", dto.Nome, dto.EleicaoId);

        return await GetByIdAsync(chapa.Id) ?? throw new InvalidOperationException("Erro ao recuperar chapa criada");
    }

    public async Task<ChapaDto> UpdateAsync(Guid id, UpdateChapaDto dto)
    {
        var chapa = await _chapaRepository.GetByIdAsync(id);
        if (chapa == null)
            throw new InvalidOperationException("Chapa nao encontrada");

        if (chapa.Status == StatusChapa.Deferida || chapa.Status == StatusChapa.Indeferida)
            throw new InvalidOperationException("Chapa ja foi analisada e nao pode ser alterada");

        // Verificar numero unico se mudou
        var numeroStr = dto.Numero.ToString();
        if (numeroStr != chapa.Numero)
        {
            var numeroExiste = await _chapaRepository.Query()
                .AnyAsync(c => c.EleicaoId == chapa.EleicaoId && c.Numero == numeroStr && c.Id != id);

            if (numeroExiste)
                throw new InvalidOperationException($"Ja existe uma chapa com o numero {dto.Numero} nesta eleicao");
        }

        chapa.Numero = numeroStr;
        chapa.Nome = dto.Nome;
        chapa.Sigla = dto.Sigla;
        chapa.Slogan = dto.Lema;

        await _chapaRepository.UpdateAsync(chapa);
        await _unitOfWork.SaveChangesAsync();

        _logger.LogInformation("Chapa {Id} atualizada com sucesso", id);

        return await GetByIdAsync(id) ?? throw new InvalidOperationException("Erro ao recuperar chapa atualizada");
    }

    public async Task DeleteAsync(Guid id)
    {
        var chapa = await _chapaRepository.GetByIdAsync(id);
        if (chapa == null)
            throw new InvalidOperationException("Chapa nao encontrada");

        if (chapa.Status != StatusChapa.Rascunho)
            throw new InvalidOperationException("Apenas chapas em rascunho podem ser excluidas");

        await _chapaRepository.DeleteAsync(chapa);
        await _unitOfWork.SaveChangesAsync();

        _logger.LogInformation("Chapa {Id} excluida com sucesso", id);
    }

    public async Task<ChapaDto> SubmeterParaAnaliseAsync(Guid id)
    {
        var chapa = await _chapaRepository.Query()
            .Include(c => c.Membros)
            .Include(c => c.Documentos)
            .FirstOrDefaultAsync(c => c.Id == id);

        if (chapa == null)
            throw new InvalidOperationException("Chapa nao encontrada");

        if (chapa.Status != StatusChapa.Rascunho && chapa.Status != StatusChapa.PendenteDocumentos)
            throw new InvalidOperationException("Chapa nao esta em rascunho ou pendente de documentos");

        // Validar requisitos minimos
        if (chapa.Membros == null || chapa.Membros.Count < 2)
            throw new InvalidOperationException("Chapa precisa ter pelo menos 2 membros");

        // Verificar se tem presidente
        var temPresidente = chapa.Membros.Any(m => m.Tipo == TipoMembroChapa.Presidente);
        if (!temPresidente)
            throw new InvalidOperationException("Chapa precisa ter um presidente");

        chapa.Status = StatusChapa.AguardandoAnalise;

        await _chapaRepository.UpdateAsync(chapa);
        await _unitOfWork.SaveChangesAsync();

        _logger.LogInformation("Chapa {Id} submetida para analise", id);

        return await GetByIdAsync(id) ?? throw new InvalidOperationException("Erro ao recuperar chapa");
    }

    public async Task<ChapaDto> DeferirAsync(Guid id, string parecer, Guid analistId)
    {
        var chapa = await _chapaRepository.GetByIdAsync(id);
        if (chapa == null)
            throw new InvalidOperationException("Chapa nao encontrada");

        if (chapa.Status != StatusChapa.AguardandoAnalise && chapa.Status != StatusChapa.EmAnalise)
            throw new InvalidOperationException("Chapa nao esta aguardando analise");

        chapa.Status = StatusChapa.Deferida;
        chapa.DataHomologacao = DateTime.UtcNow;

        await _chapaRepository.UpdateAsync(chapa);
        await _unitOfWork.SaveChangesAsync();

        _logger.LogInformation("Chapa {Id} deferida por {AnalistId}", id, analistId);

        return await GetByIdAsync(id) ?? throw new InvalidOperationException("Erro ao recuperar chapa");
    }

    public async Task<ChapaDto> IndeferirAsync(Guid id, string motivo, Guid analistId)
    {
        var chapa = await _chapaRepository.GetByIdAsync(id);
        if (chapa == null)
            throw new InvalidOperationException("Chapa nao encontrada");

        if (chapa.Status != StatusChapa.AguardandoAnalise && chapa.Status != StatusChapa.EmAnalise)
            throw new InvalidOperationException("Chapa nao esta aguardando analise");

        chapa.Status = StatusChapa.Indeferida;
        chapa.DataIndeferimento = DateTime.UtcNow;
        chapa.MotivoIndeferimento = motivo;

        await _chapaRepository.UpdateAsync(chapa);
        await _unitOfWork.SaveChangesAsync();

        _logger.LogInformation("Chapa {Id} indeferida por {AnalistId}. Motivo: {Motivo}", id, analistId, motivo);

        return await GetByIdAsync(id) ?? throw new InvalidOperationException("Erro ao recuperar chapa");
    }

    public async Task<MembroChapaDto> AddMembroAsync(Guid chapaId, CreateMembroChapaDto dto)
    {
        var chapa = await _chapaRepository.GetByIdAsync(chapaId);
        if (chapa == null)
            throw new InvalidOperationException("Chapa nao encontrada");

        if (chapa.Status != StatusChapa.Rascunho && chapa.Status != StatusChapa.PendenteDocumentos)
            throw new InvalidOperationException("Chapa nao esta em formacao");

        // Verificar se profissional ja faz parte de outra chapa na mesma eleicao
        if (dto.ProfissionalId != Guid.Empty)
        {
            var jaParticipa = await _membroRepository.Query()
                .Include(m => m.Chapa)
                .AnyAsync(m => m.ProfissionalId == dto.ProfissionalId &&
                              m.Chapa!.EleicaoId == chapa.EleicaoId &&
                              m.Status != StatusMembroChapa.Recusado);

            if (jaParticipa)
                throw new InvalidOperationException("Profissional ja participa de outra chapa nesta eleicao");
        }

        // Obter proxima ordem
        var ultimaOrdem = await _membroRepository.Query()
            .Where(m => m.ChapaId == chapaId)
            .MaxAsync(m => (int?)m.Ordem) ?? 0;

        var membro = new MembroChapa
        {
            ChapaId = chapaId,
            ProfissionalId = dto.ProfissionalId != Guid.Empty ? dto.ProfissionalId : null,
            Nome = "",
            Tipo = (TipoMembroChapa)dto.TipoMembro,
            Cargo = dto.Cargo,
            Status = StatusMembroChapa.Pendente,
            Ordem = ultimaOrdem + 1
        };

        await _membroRepository.AddAsync(membro);
        await _unitOfWork.SaveChangesAsync();

        _logger.LogInformation("Membro {ProfissionalId} adicionado a chapa {ChapaId}", dto.ProfissionalId, chapaId);

        return new MembroChapaDto
        {
            Id = membro.Id,
            ChapaId = membro.ChapaId,
            ProfissionalId = membro.ProfissionalId ?? Guid.Empty,
            TipoMembro = (int)membro.Tipo,
            TipoMembroNome = membro.Tipo.ToString(),
            Cargo = membro.Cargo,
            Status = (int)membro.Status,
            StatusNome = membro.Status.ToString(),
            Ordem = membro.Ordem
        };
    }

    public async Task RemoveMembroAsync(Guid chapaId, Guid membroId)
    {
        var chapa = await _chapaRepository.GetByIdAsync(chapaId);
        if (chapa == null)
            throw new InvalidOperationException("Chapa nao encontrada");

        if (chapa.Status != StatusChapa.Rascunho && chapa.Status != StatusChapa.PendenteDocumentos)
            throw new InvalidOperationException("Chapa nao esta em formacao");

        var membro = await _membroRepository.GetByIdAsync(membroId);
        if (membro == null || membro.ChapaId != chapaId)
            throw new InvalidOperationException("Membro nao encontrado na chapa");

        await _membroRepository.DeleteAsync(membro);
        await _unitOfWork.SaveChangesAsync();

        _logger.LogInformation("Membro {MembroId} removido da chapa {ChapaId}", membroId, chapaId);
    }

    public async Task<IEnumerable<ChapaDto>> GetByEleicaoAsync(Guid eleicaoId)
    {
        return await GetAllAsync(eleicaoId);
    }

    private ChapaDto MapToDto(ChapaEleicao chapa)
    {
        return new ChapaDto
        {
            Id = chapa.Id,
            EleicaoId = chapa.EleicaoId,
            EleicaoNome = chapa.Eleicao?.Nome ?? "",
            Numero = int.TryParse(chapa.Numero, out var num) ? num : 0,
            Nome = chapa.Nome,
            Sigla = chapa.Sigla,
            Lema = chapa.Slogan,
            Status = (int)chapa.Status,
            StatusNome = chapa.Status.ToString(),
            TotalMembros = chapa.Membros?.Count ?? 0,
            Membros = chapa.Membros?.Select(m => new MembroChapaDto
            {
                Id = m.Id,
                ChapaId = m.ChapaId,
                ProfissionalId = m.ProfissionalId ?? Guid.Empty,
                NomeProfissional = m.Profissional?.Nome ?? m.Nome,
                TipoMembro = (int)m.Tipo,
                TipoMembroNome = m.Tipo.ToString(),
                Cargo = m.Cargo,
                Status = (int)m.Status,
                StatusNome = m.Status.ToString(),
                Ordem = m.Ordem
            }).OrderBy(m => m.Ordem).ToList() ?? new List<MembroChapaDto>(),
            DataRegistro = chapa.DataInscricao,
            CriadoEm = chapa.CreatedAt
        };
    }
}
