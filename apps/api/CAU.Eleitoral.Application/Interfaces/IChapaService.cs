using CAU.Eleitoral.Application.DTOs;
using CAU.Eleitoral.Application.DTOs.Chapas;

namespace CAU.Eleitoral.Application.Interfaces;

/// <summary>
/// Interface para servico de gerenciamento de chapas eleitorais
/// </summary>
public interface IChapaService
{
    #region Consultas

    /// <summary>
    /// Obtem todas as chapas com filtro opcional por eleicao
    /// </summary>
    Task<IEnumerable<ChapaDto>> GetAllAsync(Guid? eleicaoId = null, CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem chapas com paginacao e filtros
    /// </summary>
    Task<PagedResultDto<ChapaDto>> GetPagedAsync(ChapaFilterDto filter, CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem uma chapa pelo ID
    /// </summary>
    Task<ChapaDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem uma chapa pelo ID com todos os detalhes
    /// </summary>
    Task<ChapaDetailDto?> GetByIdDetailedAsync(Guid id, CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem chapas por eleicao
    /// </summary>
    Task<IEnumerable<ChapaDto>> GetByEleicaoAsync(Guid eleicaoId, bool apenasAtivas = false, CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem estatisticas de chapas por eleicao
    /// </summary>
    Task<ChapaEstatisticasDto> GetEstatisticasAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    #endregion

    #region CRUD Chapa

    /// <summary>
    /// Cria uma nova chapa
    /// </summary>
    Task<ChapaDto> CreateAsync(CreateChapaDto dto, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Atualiza uma chapa existente
    /// </summary>
    Task<ChapaDto> UpdateAsync(Guid id, UpdateChapaDto dto, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Remove uma chapa (apenas rascunhos)
    /// </summary>
    Task DeleteAsync(Guid id, Guid userId, CancellationToken cancellationToken = default);

    #endregion

    #region Workflow Chapa

    /// <summary>
    /// Submete chapa para analise
    /// </summary>
    Task<ChapaDto> SubmeterParaAnaliseAsync(Guid id, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Inicia analise de uma chapa
    /// </summary>
    Task<ChapaDto> IniciarAnaliseAsync(Guid id, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Defere (aprova) uma chapa
    /// </summary>
    Task<ChapaDto> DeferirAsync(Guid id, string? parecer, Guid analistId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Indefere (reprova) uma chapa
    /// </summary>
    Task<ChapaDto> IndeferirAsync(Guid id, string motivo, Guid analistId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Solicita documentos pendentes
    /// </summary>
    Task<ChapaDto> SolicitarDocumentosAsync(Guid id, List<int> documentos, string? observacao, Guid userId, CancellationToken cancellationToken = default);

    #endregion

    #region Membros

    /// <summary>
    /// Obtem membros de uma chapa
    /// </summary>
    Task<IEnumerable<MembroChapaDto>> GetMembrosAsync(Guid chapaId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Adiciona um membro a chapa
    /// </summary>
    Task<MembroChapaDto> AddMembroAsync(Guid chapaId, CreateMembroChapaDto dto, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Atualiza um membro da chapa
    /// </summary>
    Task<MembroChapaDto> UpdateMembroAsync(Guid chapaId, Guid membroId, UpdateMembroChapaDto dto, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Remove um membro da chapa
    /// </summary>
    Task RemoveMembroAsync(Guid chapaId, Guid membroId, Guid userId, CancellationToken cancellationToken = default);

    #endregion

    #region Documentos

    /// <summary>
    /// Obtem documentos de uma chapa
    /// </summary>
    Task<IEnumerable<DocumentoChapaDto>> GetDocumentosAsync(Guid chapaId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Adiciona um documento a chapa
    /// </summary>
    Task<DocumentoChapaDto> AddDocumentoAsync(Guid chapaId, CreateDocumentoChapaDto dto, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Analisa um documento da chapa
    /// </summary>
    Task<DocumentoChapaDto> AnalisarDocumentoAsync(Guid chapaId, Guid documentoId, AnaliseDocumentoChapaDto dto, Guid analistaId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Remove um documento da chapa
    /// </summary>
    Task RemoveDocumentoAsync(Guid chapaId, Guid documentoId, Guid userId, CancellationToken cancellationToken = default);

    #endregion

    #region Plataforma

    /// <summary>
    /// Obtem ou cria a plataforma eleitoral da chapa
    /// </summary>
    Task<PlataformaEleitoralDto?> GetPlataformaAsync(Guid chapaId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Cria ou atualiza a plataforma eleitoral
    /// </summary>
    Task<PlataformaEleitoralDto> SavePlataformaAsync(Guid chapaId, CreatePlataformaEleitoralDto dto, Guid userId, CancellationToken cancellationToken = default);

    #endregion
}
