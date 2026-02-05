using CAU.Eleitoral.Application.DTOs.Documentos;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IDocumentoService
{
    // CRUD Operations
    Task<DocumentoDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<DocumentoDto?> GetByNumeroAsync(string numero, int? ano = null, CancellationToken cancellationToken = default);
    Task<IEnumerable<DocumentoDto>> GetAllAsync(CancellationToken cancellationToken = default);
    Task<IEnumerable<DocumentoDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<DocumentoDto>> GetByTipoAsync(TipoDocumento tipo, CancellationToken cancellationToken = default);
    Task<IEnumerable<DocumentoDto>> GetByCategoriaAsync(CategoriaDocumento categoria, CancellationToken cancellationToken = default);
    Task<IEnumerable<DocumentoDto>> GetByStatusAsync(StatusDocumento status, CancellationToken cancellationToken = default);
    Task<IEnumerable<DocumentoDto>> SearchAsync(string termo, CancellationToken cancellationToken = default);
    Task<DocumentoDto> CreateAsync(CreateDocumentoDto dto, CancellationToken cancellationToken = default);
    Task<DocumentoDto> UpdateAsync(Guid id, UpdateDocumentoDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);

    // Workflow Operations
    Task<DocumentoDto> EnviarParaRevisaoAsync(Guid id, CancellationToken cancellationToken = default);
    Task<DocumentoDto> AprovarAsync(Guid id, Guid aprovadorId, CancellationToken cancellationToken = default);
    Task<DocumentoDto> PublicarAsync(Guid id, DateTime? dataPublicacao = null, CancellationToken cancellationToken = default);
    Task<DocumentoDto> RevogarAsync(Guid id, string motivo, CancellationToken cancellationToken = default);
    Task<DocumentoDto> ArquivarAsync(Guid id, CancellationToken cancellationToken = default);

    // File Operations
    Task<ArquivoDocumentoDto> UploadArquivoAsync(Guid documentoId, UploadArquivoDto dto, CancellationToken cancellationToken = default);
    Task<ArquivoDocumentoDto> UploadArquivoStreamAsync(Guid documentoId, Stream stream, string nomeArquivo, string tipoArquivo, CancellationToken cancellationToken = default);
    Task<Stream> DownloadArquivoAsync(Guid documentoId, Guid arquivoId, CancellationToken cancellationToken = default);
    Task<byte[]> DownloadArquivoBytesAsync(Guid documentoId, Guid arquivoId, CancellationToken cancellationToken = default);
    Task RemoveArquivoAsync(Guid documentoId, Guid arquivoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<ArquivoDocumentoDto>> GetArquivosAsync(Guid documentoId, CancellationToken cancellationToken = default);
    Task<ArquivoDocumentoDto> DefinirArquivoPrincipalAsync(Guid documentoId, Guid arquivoId, CancellationToken cancellationToken = default);

    // Versioning
    Task<IEnumerable<ArquivoDocumentoDto>> GetVersoesArquivoAsync(Guid documentoId, CancellationToken cancellationToken = default);
    Task<ArquivoDocumentoDto?> GetVersaoAtualAsync(Guid documentoId, CancellationToken cancellationToken = default);

    // Publicacao
    Task<PublicacaoDto> AgendarPublicacaoAsync(Guid documentoId, CreatePublicacaoDto dto, CancellationToken cancellationToken = default);
    Task CancelarPublicacaoAsync(Guid documentoId, Guid publicacaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<PublicacaoDto>> GetPublicacoesAsync(Guid documentoId, CancellationToken cancellationToken = default);

    // Statistics
    Task<int> CountByTipoAsync(TipoDocumento tipo, CancellationToken cancellationToken = default);
    Task<int> CountByStatusAsync(StatusDocumento status, CancellationToken cancellationToken = default);
}
