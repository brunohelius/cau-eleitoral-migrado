using CAU.Eleitoral.Application.DTOs.Configuracoes;

namespace CAU.Eleitoral.Application.Interfaces;

/// <summary>
/// Interface do servico de configuracoes do sistema
/// </summary>
public interface IConfiguracaoService
{
    /// <summary>
    /// Obtem todas as configuracoes do sistema
    /// </summary>
    Task<ConfiguracaoSistemaDto> GetAllAsync(CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem uma configuracao especifica por chave
    /// </summary>
    Task<ConfiguracaoItemDto?> GetByChaveAsync(string chave, CancellationToken cancellationToken = default);

    /// <summary>
    /// Atualiza uma configuracao especifica
    /// </summary>
    Task<ConfiguracaoItemDto> UpdateAsync(string chave, string valor, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Atualiza multiplas configuracoes
    /// </summary>
    Task<ConfiguracaoSistemaDto> UpdateMultipleAsync(Dictionary<string, string> configuracoes, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem configuracoes de email
    /// </summary>
    Task<ConfiguracaoEmailDto> GetEmailConfigAsync(CancellationToken cancellationToken = default);

    /// <summary>
    /// Atualiza configuracoes de email
    /// </summary>
    Task<ConfiguracaoEmailDto> UpdateEmailConfigAsync(ConfiguracaoEmailDto dto, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Testa configuracoes de email enviando um email de teste
    /// </summary>
    Task<bool> TestarEmailAsync(string emailDestino, CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem configuracoes de seguranca
    /// </summary>
    Task<ConfiguracaoSegurancaDto> GetSegurancaConfigAsync(CancellationToken cancellationToken = default);

    /// <summary>
    /// Atualiza configuracoes de seguranca
    /// </summary>
    Task<ConfiguracaoSegurancaDto> UpdateSegurancaConfigAsync(ConfiguracaoSegurancaDto dto, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem configuracoes de votacao
    /// </summary>
    Task<ConfiguracaoVotacaoDto> GetVotacaoConfigAsync(CancellationToken cancellationToken = default);

    /// <summary>
    /// Atualiza configuracoes de votacao
    /// </summary>
    Task<ConfiguracaoVotacaoDto> UpdateVotacaoConfigAsync(ConfiguracaoVotacaoDto dto, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Restaura todas as configuracoes para os valores padrao
    /// </summary>
    Task<ConfiguracaoSistemaDto> RestaurarPadraoAsync(Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Exporta configuracoes para arquivo JSON
    /// </summary>
    Task<(byte[] Content, string ContentType, string FileName)> ExportarAsync(CancellationToken cancellationToken = default);

    /// <summary>
    /// Importa configuracoes de arquivo JSON
    /// </summary>
    Task<ConfiguracaoSistemaDto> ImportarAsync(Stream stream, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem todas as roles do sistema
    /// </summary>
    Task<IEnumerable<RoleDto>> GetRolesAsync(CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem informacoes gerais do sistema
    /// </summary>
    Task<InfoSistemaDto> GetInfoSistemaAsync(CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem configuracoes padrao de eleicao
    /// </summary>
    Task<ConfiguracaoEleicaoDefaultDto> GetEleicaoConfigAsync(CancellationToken cancellationToken = default);

    /// <summary>
    /// Atualiza configuracoes padrao de eleicao
    /// </summary>
    Task<ConfiguracaoEleicaoDefaultDto> UpdateEleicaoConfigAsync(ConfiguracaoEleicaoDefaultDto dto, Guid userId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Obtem configuracoes especificas de uma eleicao
    /// </summary>
    Task<IEnumerable<ConfiguracaoItemDto>> GetConfiguracoesEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Atualiza configuracoes especificas de uma eleicao
    /// </summary>
    Task<IEnumerable<ConfiguracaoItemDto>> UpdateConfiguracoesEleicaoAsync(Guid eleicaoId, Dictionary<string, string> configuracoes, Guid userId, CancellationToken cancellationToken = default);
}
