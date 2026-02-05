using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Documentos;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Documentos;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

public class DocumentoService : IDocumentoService
{
    private readonly IRepository<Documento> _documentoRepository;
    private readonly IRepository<ArquivoDocumento> _arquivoRepository;
    private readonly IRepository<Publicacao> _publicacaoRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<DocumentoService> _logger;

    public DocumentoService(
        IRepository<Documento> documentoRepository,
        IRepository<ArquivoDocumento> arquivoRepository,
        IRepository<Publicacao> publicacaoRepository,
        IUnitOfWork unitOfWork,
        ILogger<DocumentoService> logger)
    {
        _documentoRepository = documentoRepository;
        _arquivoRepository = arquivoRepository;
        _publicacaoRepository = publicacaoRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    public async Task<DocumentoDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var documento = await _documentoRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Arquivos)
            .FirstOrDefaultAsync(d => d.Id == id, cancellationToken);

        return documento == null ? null : MapToDto(documento);
    }

    public async Task<DocumentoDto?> GetByNumeroAsync(string numero, int? ano = null, CancellationToken cancellationToken = default)
    {
        var query = _documentoRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Arquivos)
            .Where(d => d.Numero == numero);

        if (ano.HasValue)
            query = query.Where(d => d.Ano == ano.Value);

        var documento = await query.FirstOrDefaultAsync(cancellationToken);

        return documento == null ? null : MapToDto(documento);
    }

    public async Task<IEnumerable<DocumentoDto>> GetAllAsync(CancellationToken cancellationToken = default)
    {
        var documentos = await _documentoRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Arquivos)
            .OrderByDescending(d => d.DataDocumento)
            .ToListAsync(cancellationToken);

        return documentos.Select(MapToDto);
    }

    public async Task<IEnumerable<DocumentoDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var documentos = await _documentoRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Arquivos)
            .Where(d => d.EleicaoId == eleicaoId)
            .OrderByDescending(d => d.DataDocumento)
            .ToListAsync(cancellationToken);

        return documentos.Select(MapToDto);
    }

    public async Task<IEnumerable<DocumentoDto>> GetByTipoAsync(TipoDocumento tipo, CancellationToken cancellationToken = default)
    {
        var documentos = await _documentoRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Arquivos)
            .Where(d => d.Tipo == tipo)
            .OrderByDescending(d => d.DataDocumento)
            .ToListAsync(cancellationToken);

        return documentos.Select(MapToDto);
    }

    public async Task<IEnumerable<DocumentoDto>> GetByCategoriaAsync(CategoriaDocumento categoria, CancellationToken cancellationToken = default)
    {
        var documentos = await _documentoRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Arquivos)
            .Where(d => d.Categoria == categoria)
            .OrderByDescending(d => d.DataDocumento)
            .ToListAsync(cancellationToken);

        return documentos.Select(MapToDto);
    }

    public async Task<IEnumerable<DocumentoDto>> GetByStatusAsync(StatusDocumento status, CancellationToken cancellationToken = default)
    {
        var documentos = await _documentoRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Arquivos)
            .Where(d => d.Status == status)
            .OrderByDescending(d => d.DataDocumento)
            .ToListAsync(cancellationToken);

        return documentos.Select(MapToDto);
    }

    public async Task<IEnumerable<DocumentoDto>> SearchAsync(string termo, CancellationToken cancellationToken = default)
    {
        var termoLower = termo.ToLower();
        var documentos = await _documentoRepository.Query()
            .Include(d => d.Eleicao)
            .Include(d => d.Arquivos)
            .Where(d => d.Titulo.ToLower().Contains(termoLower) ||
                        (d.Ementa != null && d.Ementa.ToLower().Contains(termoLower)) ||
                        d.Numero.ToLower().Contains(termoLower))
            .OrderByDescending(d => d.DataDocumento)
            .ToListAsync(cancellationToken);

        return documentos.Select(MapToDto);
    }

    public async Task<DocumentoDto> CreateAsync(CreateDocumentoDto dto, CancellationToken cancellationToken = default)
    {
        var documento = new Documento
        {
            EleicaoId = dto.EleicaoId,
            Tipo = dto.Tipo,
            Categoria = dto.Categoria,
            Status = StatusDocumento.Rascunho,
            Numero = dto.Numero,
            Ano = dto.Ano ?? DateTime.UtcNow.Year,
            Titulo = dto.Titulo,
            Ementa = dto.Ementa,
            Conteudo = dto.Conteudo,
            DataDocumento = dto.DataDocumento ?? DateTime.UtcNow
        };

        await _documentoRepository.AddAsync(documento, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Documento criado: {DocumentoId} - {Numero}", documento.Id, documento.Numero);

        return await GetByIdAsync(documento.Id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar documento criado");
    }

    public async Task<DocumentoDto> UpdateAsync(Guid id, UpdateDocumentoDto dto, CancellationToken cancellationToken = default)
    {
        var documento = await _documentoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Documento {id} nao encontrado");

        if (documento.Status == StatusDocumento.Publicado)
            throw new InvalidOperationException("Documentos publicados nao podem ser alterados");

        if (dto.Numero != null) documento.Numero = dto.Numero;
        if (dto.Titulo != null) documento.Titulo = dto.Titulo;
        if (dto.Ementa != null) documento.Ementa = dto.Ementa;
        if (dto.Conteudo != null) documento.Conteudo = dto.Conteudo;
        if (dto.DataDocumento.HasValue) documento.DataDocumento = dto.DataDocumento.Value;
        if (dto.DataVigencia.HasValue) documento.DataVigencia = dto.DataVigencia.Value;

        await _documentoRepository.UpdateAsync(documento, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Documento atualizado: {DocumentoId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar documento");
    }

    public async Task DeleteAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var documento = await _documentoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Documento {id} nao encontrado");

        if (documento.Status == StatusDocumento.Publicado)
            throw new InvalidOperationException("Documentos publicados nao podem ser excluidos");

        await _documentoRepository.SoftDeleteAsync(id, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Documento excluido: {DocumentoId}", id);
    }

    public async Task<DocumentoDto> EnviarParaRevisaoAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var documento = await _documentoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Documento {id} nao encontrado");

        documento.Status = StatusDocumento.EmRevisao;

        await _documentoRepository.UpdateAsync(documento, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Documento enviado para revisao: {DocumentoId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar documento");
    }

    public async Task<DocumentoDto> AprovarAsync(Guid id, Guid aprovadorId, CancellationToken cancellationToken = default)
    {
        var documento = await _documentoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Documento {id} nao encontrado");

        documento.Status = StatusDocumento.Aprovado;

        await _documentoRepository.UpdateAsync(documento, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Documento aprovado: {DocumentoId} por {AprovadorId}", id, aprovadorId);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar documento");
    }

    public async Task<DocumentoDto> PublicarAsync(Guid id, DateTime? dataPublicacao = null, CancellationToken cancellationToken = default)
    {
        var documento = await _documentoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Documento {id} nao encontrado");

        documento.Status = StatusDocumento.Publicado;
        documento.DataPublicacao = dataPublicacao ?? DateTime.UtcNow;

        await _documentoRepository.UpdateAsync(documento, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Documento publicado: {DocumentoId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar documento");
    }

    public async Task<DocumentoDto> RevogarAsync(Guid id, string motivo, CancellationToken cancellationToken = default)
    {
        var documento = await _documentoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Documento {id} nao encontrado");

        documento.Status = StatusDocumento.Revogado;
        documento.DataRevogacao = DateTime.UtcNow;

        await _documentoRepository.UpdateAsync(documento, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Documento revogado: {DocumentoId} - {Motivo}", id, motivo);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar documento");
    }

    public async Task<DocumentoDto> ArquivarAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var documento = await _documentoRepository.GetByIdAsync(id, cancellationToken)
            ?? throw new KeyNotFoundException($"Documento {id} nao encontrado");

        documento.Status = StatusDocumento.Arquivado;

        await _documentoRepository.UpdateAsync(documento, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Documento arquivado: {DocumentoId}", id);

        return await GetByIdAsync(id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar documento");
    }

    public async Task<ArquivoDocumentoDto> UploadArquivoAsync(Guid documentoId, UploadArquivoDto dto, CancellationToken cancellationToken = default)
    {
        var documento = await _documentoRepository.GetByIdAsync(documentoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Documento {documentoId} nao encontrado");

        // Get next order
        var ultimaOrdem = await _arquivoRepository.Query()
            .Where(a => a.DocumentoId == documentoId)
            .MaxAsync(a => (int?)a.Ordem, cancellationToken) ?? 0;

        var arquivo = new ArquivoDocumento
        {
            DocumentoId = documentoId,
            Nome = dto.Nome,
            Descricao = dto.Descricao,
            ArquivoTipo = dto.Tipo,
            ArquivoTamanho = dto.Tamanho,
            Ordem = ultimaOrdem + 1,
            DataUpload = DateTime.UtcNow,
            // In real implementation, save file to storage and get URL
            ArquivoUrl = $"/arquivos/documentos/{documentoId}/{dto.Nome}",
            ArquivoNome = dto.Nome
        };

        await _arquivoRepository.AddAsync(arquivo, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Arquivo adicionado ao documento {DocumentoId}: {ArquivoNome}", documentoId, dto.Nome);

        return MapArquivoToDto(arquivo);
    }

    public async Task<ArquivoDocumentoDto> UploadArquivoStreamAsync(Guid documentoId, Stream stream, string nomeArquivo, string tipoArquivo, CancellationToken cancellationToken = default)
    {
        var documento = await _documentoRepository.GetByIdAsync(documentoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Documento {documentoId} nao encontrado");

        var ultimaOrdem = await _arquivoRepository.Query()
            .Where(a => a.DocumentoId == documentoId)
            .MaxAsync(a => (int?)a.Ordem, cancellationToken) ?? 0;

        var arquivo = new ArquivoDocumento
        {
            DocumentoId = documentoId,
            Nome = nomeArquivo,
            ArquivoNome = nomeArquivo,
            ArquivoTipo = tipoArquivo,
            ArquivoTamanho = stream.Length,
            Ordem = ultimaOrdem + 1,
            DataUpload = DateTime.UtcNow,
            ArquivoUrl = $"/arquivos/documentos/{documentoId}/{nomeArquivo}"
        };

        await _arquivoRepository.AddAsync(arquivo, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Arquivo stream adicionado ao documento {DocumentoId}: {ArquivoNome}", documentoId, nomeArquivo);

        return MapArquivoToDto(arquivo);
    }

    public async Task<Stream> DownloadArquivoAsync(Guid documentoId, Guid arquivoId, CancellationToken cancellationToken = default)
    {
        var arquivo = await _arquivoRepository.GetByIdAsync(arquivoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Arquivo {arquivoId} nao encontrado");

        if (arquivo.DocumentoId != documentoId)
            throw new InvalidOperationException("Arquivo nao pertence a este documento");

        // In real implementation, retrieve file from storage
        return new MemoryStream();
    }

    public async Task<byte[]> DownloadArquivoBytesAsync(Guid documentoId, Guid arquivoId, CancellationToken cancellationToken = default)
    {
        using var stream = await DownloadArquivoAsync(documentoId, arquivoId, cancellationToken);
        using var ms = new MemoryStream();
        await stream.CopyToAsync(ms, cancellationToken);
        return ms.ToArray();
    }

    public async Task RemoveArquivoAsync(Guid documentoId, Guid arquivoId, CancellationToken cancellationToken = default)
    {
        var arquivo = await _arquivoRepository.GetByIdAsync(arquivoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Arquivo {arquivoId} nao encontrado");

        if (arquivo.DocumentoId != documentoId)
            throw new InvalidOperationException("Arquivo nao pertence a este documento");

        await _arquivoRepository.DeleteAsync(arquivo, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Arquivo {ArquivoId} removido do documento {DocumentoId}", arquivoId, documentoId);
    }

    public async Task<IEnumerable<ArquivoDocumentoDto>> GetArquivosAsync(Guid documentoId, CancellationToken cancellationToken = default)
    {
        var arquivos = await _arquivoRepository.Query()
            .Where(a => a.DocumentoId == documentoId)
            .OrderBy(a => a.Ordem)
            .ToListAsync(cancellationToken);

        return arquivos.Select(MapArquivoToDto);
    }

    public async Task<ArquivoDocumentoDto> DefinirArquivoPrincipalAsync(Guid documentoId, Guid arquivoId, CancellationToken cancellationToken = default)
    {
        // Move the specified arquivo to the first position (Ordem = 1)
        var arquivos = await _arquivoRepository.Query()
            .Where(a => a.DocumentoId == documentoId)
            .OrderBy(a => a.Ordem)
            .ToListAsync(cancellationToken);

        int ordem = 1;
        var arquivoPrincipal = arquivos.First(a => a.Id == arquivoId);
        arquivoPrincipal.Ordem = ordem++;
        await _arquivoRepository.UpdateAsync(arquivoPrincipal, cancellationToken);

        foreach (var arq in arquivos.Where(a => a.Id != arquivoId))
        {
            arq.Ordem = ordem++;
            await _arquivoRepository.UpdateAsync(arq, cancellationToken);
        }

        await _unitOfWork.SaveChangesAsync(cancellationToken);

        return MapArquivoToDto(arquivoPrincipal);
    }

    public async Task<IEnumerable<ArquivoDocumentoDto>> GetVersoesArquivoAsync(Guid documentoId, CancellationToken cancellationToken = default)
    {
        return await GetArquivosAsync(documentoId, cancellationToken);
    }

    public async Task<ArquivoDocumentoDto?> GetVersaoAtualAsync(Guid documentoId, CancellationToken cancellationToken = default)
    {
        var arquivo = await _arquivoRepository.Query()
            .Where(a => a.DocumentoId == documentoId)
            .OrderBy(a => a.Ordem)
            .FirstOrDefaultAsync(cancellationToken);

        return arquivo == null ? null : MapArquivoToDto(arquivo);
    }

    public async Task<PublicacaoDto> AgendarPublicacaoAsync(Guid documentoId, CreatePublicacaoDto dto, CancellationToken cancellationToken = default)
    {
        var publicacao = new Publicacao
        {
            DocumentoId = documentoId,
            Tipo = dto.Tipo,
            Status = StatusPublicacao.Agendada,
            Titulo = "",
            DataAgendada = dto.DataAgendamento,
            Conteudo = dto.Observacao
        };

        await _publicacaoRepository.AddAsync(publicacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Publicacao agendada para documento {DocumentoId}", documentoId);

        return MapPublicacaoToDto(publicacao);
    }

    public async Task CancelarPublicacaoAsync(Guid documentoId, Guid publicacaoId, CancellationToken cancellationToken = default)
    {
        var publicacao = await _publicacaoRepository.GetByIdAsync(publicacaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Publicacao {publicacaoId} nao encontrada");

        if (publicacao.DocumentoId != documentoId)
            throw new InvalidOperationException("Publicacao nao pertence a este documento");

        publicacao.Status = StatusPublicacao.Cancelada;

        await _publicacaoRepository.UpdateAsync(publicacao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Publicacao {PublicacaoId} cancelada", publicacaoId);
    }

    public async Task<IEnumerable<PublicacaoDto>> GetPublicacoesAsync(Guid documentoId, CancellationToken cancellationToken = default)
    {
        var publicacoes = await _publicacaoRepository.Query()
            .Where(p => p.DocumentoId == documentoId)
            .OrderByDescending(p => p.CreatedAt)
            .ToListAsync(cancellationToken);

        return publicacoes.Select(MapPublicacaoToDto);
    }

    private static PublicacaoDto MapPublicacaoToDto(Publicacao p)
    {
        return new PublicacaoDto
        {
            Id = p.Id,
            DocumentoId = p.DocumentoId,
            Tipo = p.Tipo,
            TipoNome = p.Tipo.ToString(),
            Status = p.Status,
            StatusNome = p.Status.ToString(),
            DataAgendamento = p.DataAgendada,
            DataPublicacao = p.DataPublicacao,
            Url = p.LinkPublicacao,
            Observacao = p.Conteudo
        };
    }

    public async Task<int> CountByTipoAsync(TipoDocumento tipo, CancellationToken cancellationToken = default)
    {
        return await _documentoRepository.CountAsync(d => d.Tipo == tipo, cancellationToken);
    }

    public async Task<int> CountByStatusAsync(StatusDocumento status, CancellationToken cancellationToken = default)
    {
        return await _documentoRepository.CountAsync(d => d.Status == status, cancellationToken);
    }

    private static DocumentoDto MapToDto(Documento documento)
    {
        return new DocumentoDto
        {
            Id = documento.Id,
            EleicaoId = documento.EleicaoId,
            EleicaoNome = documento.Eleicao?.Nome,
            Tipo = documento.Tipo,
            TipoNome = documento.Tipo.ToString(),
            Categoria = documento.Categoria,
            CategoriaNome = documento.Categoria.ToString(),
            Status = documento.Status,
            StatusNome = documento.Status.ToString(),
            Numero = documento.Numero,
            Ano = documento.Ano,
            Titulo = documento.Titulo,
            Ementa = documento.Ementa,
            Conteudo = documento.Conteudo,
            DataDocumento = documento.DataDocumento,
            DataPublicacao = documento.DataPublicacao,
            DataVigencia = documento.DataVigencia,
            DataRevogacao = documento.DataRevogacao,
            ArquivoUrl = documento.ArquivoUrl,
            ArquivoNome = documento.ArquivoNome,
            ArquivoTipo = documento.ArquivoTipo,
            ArquivoTamanho = documento.ArquivoTamanho,
            TotalArquivos = documento.Arquivos?.Count ?? 0,
            Versao = documento.Arquivos?.Max(a => a.Ordem) ?? 1,
            CreatedAt = documento.CreatedAt
        };
    }

    private static ArquivoDocumentoDto MapArquivoToDto(ArquivoDocumento arquivo)
    {
        return new ArquivoDocumentoDto
        {
            Id = arquivo.Id,
            DocumentoId = arquivo.DocumentoId,
            Nome = arquivo.Nome,
            Descricao = arquivo.Descricao,
            Url = arquivo.ArquivoUrl,
            Tipo = arquivo.ArquivoTipo,
            Tamanho = arquivo.ArquivoTamanho ?? 0,
            Versao = arquivo.Ordem,
            Principal = arquivo.Ordem == 1,
            DataUpload = arquivo.DataUpload
        };
    }
}
