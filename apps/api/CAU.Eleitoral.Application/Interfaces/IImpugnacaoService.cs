using CAU.Eleitoral.Application.DTOs.Impugnacoes;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IImpugnacaoService
{
    #region CRUD Operations

    Task<ImpugnacaoDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto?> GetByProtocoloAsync(string protocolo, CancellationToken cancellationToken = default);
    Task<IEnumerable<ImpugnacaoDto>> GetAllAsync(CancellationToken cancellationToken = default);
    Task<IEnumerable<ImpugnacaoDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<ImpugnacaoDto>> GetByStatusAsync(StatusImpugnacao status, CancellationToken cancellationToken = default);
    Task<IEnumerable<ImpugnacaoDto>> GetByChapaImpugnadaAsync(Guid chapaId, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> CreateAsync(CreateImpugnacaoDto dto, CancellationToken cancellationToken = default);
    Task<ImpugnacaoDto> UpdateAsync(Guid id, UpdateImpugnacaoDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);

    #endregion

    #region Workflow Operations - Status Transitions

    /// <summary>
    /// Receives the impugnation for processing
    /// Status: -> Recebida
    /// </summary>
    Task<ImpugnacaoDto> ReceberAsync(Guid id, CancellationToken cancellationToken = default);

    /// <summary>
    /// Starts analysis of the impugnation
    /// Status: Recebida -> EmAnalise
    /// Assigns analyst/relator
    /// </summary>
    Task<ImpugnacaoDto> IniciarAnaliseAsync(Guid id, Guid? relatorId = null, CancellationToken cancellationToken = default);

    /// <summary>
    /// Opens deadline for impugnante to present allegations
    /// Status: EmAnalise -> AguardandoAlegacoes
    /// </summary>
    Task<ImpugnacaoDto> SolicitarAlegacoesAsync(Guid id, int prazoEmDias, string? observacoes = null, CancellationToken cancellationToken = default);

    /// <summary>
    /// Registers allegations from impugnante
    /// Status: AguardandoAlegacoes -> AlegacoesApresentadas
    /// Validates within deadline
    /// </summary>
    Task<ImpugnacaoDto> ApresentarAlegacoesAsync(Guid id, CreateAlegacaoDto dto, CancellationToken cancellationToken = default);

    /// <summary>
    /// Opens deadline for impugnado to present counter-allegations
    /// Status: AlegacoesApresentadas -> AguardandoContraAlegacoes
    /// </summary>
    Task<ImpugnacaoDto> SolicitarContraAlegacoesAsync(Guid id, int prazoEmDias, string? observacoes = null, CancellationToken cancellationToken = default);

    /// <summary>
    /// Registers counter-allegations from impugnado
    /// Status: AguardandoContraAlegacoes -> ContraAlegacoesApresentadas
    /// Validates within deadline
    /// </summary>
    Task<ImpugnacaoDto> ApresentarContraAlegacoesAsync(Guid id, CreateContraAlegacaoDto dto, Guid autorId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Forwards impugnation to judgment
    /// Status: ContraAlegacoesApresentadas (or AlegacoesApresentadas) -> AguardandoJulgamento
    /// Assigns judge/commission
    /// </summary>
    Task<ImpugnacaoDto> EncaminharJulgamentoAsync(Guid id, Guid? comissaoId = null, CancellationToken cancellationToken = default);

    /// <summary>
    /// Renders judgment decision
    /// Status: AguardandoJulgamento -> Procedente/Improcedente/ParcialmenteProcedente
    /// Applies effects to target (chapa status, member ineligibility, etc.)
    /// </summary>
    Task<ImpugnacaoDto> JulgarAsync(Guid id, JulgarImpugnacaoDto dto, CancellationToken cancellationToken = default);

    /// <summary>
    /// Files an appeal against the decision
    /// Status: Procedente/Improcedente/ParcialmenteProcedente -> AguardandoRecurso (via RecursoApresentado)
    /// Validates within appeal deadline
    /// </summary>
    Task<ImpugnacaoDto> InterporRecursoAsync(Guid id, CreateRecursoImpugnacaoDto dto, Guid recorrenteId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Judges the appeal - final decision
    /// Status: RecursoApresentado -> RecursoJulgado
    /// </summary>
    Task<ImpugnacaoDto> JulgarRecursoAsync(Guid id, Guid recursoId, JulgarRecursoImpugnacaoDto dto, CancellationToken cancellationToken = default);

    /// <summary>
    /// Archives the impugnation
    /// Status: Any -> Arquivada
    /// </summary>
    Task<ImpugnacaoDto> ArquivarAsync(Guid id, string motivo, CancellationToken cancellationToken = default);

    #endregion

    #region Legacy Workflow Methods (kept for compatibility)

    /// <summary>
    /// [Deprecated] Use SolicitarAlegacoesAsync instead
    /// </summary>
    Task<ImpugnacaoDto> AbrirPrazoAlegacoesAsync(Guid id, DateTime prazo, CancellationToken cancellationToken = default);

    /// <summary>
    /// [Deprecated] Use ApresentarAlegacoesAsync instead
    /// </summary>
    Task<ImpugnacaoDto> RegistrarAlegacaoAsync(Guid id, CreateAlegacaoDto dto, CancellationToken cancellationToken = default);

    /// <summary>
    /// [Deprecated] Use SolicitarContraAlegacoesAsync instead
    /// </summary>
    Task<ImpugnacaoDto> AbrirPrazoContraAlegacoesAsync(Guid id, DateTime prazo, CancellationToken cancellationToken = default);

    /// <summary>
    /// [Deprecated] Use ApresentarContraAlegacoesAsync instead
    /// </summary>
    Task<ImpugnacaoDto> RegistrarContraAlegacaoAsync(Guid id, string contraAlegacao, Guid autorId, CancellationToken cancellationToken = default);

    /// <summary>
    /// [Deprecated] Use EncaminharJulgamentoAsync instead
    /// </summary>
    Task<ImpugnacaoDto> EncaminharParaJulgamentoAsync(Guid id, CancellationToken cancellationToken = default);

    /// <summary>
    /// [Deprecated] Use JulgarAsync instead
    /// </summary>
    Task<ImpugnacaoDto> RegistrarJulgamentoAsync(Guid id, StatusImpugnacao decisao, string fundamentacao, CancellationToken cancellationToken = default);

    /// <summary>
    /// [Deprecated] Use InterporRecursoAsync instead
    /// </summary>
    Task<ImpugnacaoDto> RegistrarRecursoAsync(Guid id, string recurso, Guid autorId, CancellationToken cancellationToken = default);

    #endregion

    #region Pedidos

    Task<PedidoImpugnacaoDto> AddPedidoAsync(Guid impugnacaoId, CreatePedidoImpugnacaoDto dto, CancellationToken cancellationToken = default);
    Task RemovePedidoAsync(Guid impugnacaoId, Guid pedidoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<PedidoImpugnacaoDto>> GetPedidosAsync(Guid impugnacaoId, CancellationToken cancellationToken = default);
    Task<PedidoImpugnacaoDto> AnalisarPedidoAsync(Guid impugnacaoId, Guid pedidoId, AnalisePedidoDto dto, CancellationToken cancellationToken = default);

    #endregion

    #region Alegacoes

    Task<IEnumerable<AlegacaoDto>> GetAlegacoesAsync(Guid impugnacaoId, CancellationToken cancellationToken = default);
    Task RemoveAlegacaoAsync(Guid impugnacaoId, Guid alegacaoId, CancellationToken cancellationToken = default);

    #endregion

    #region Contra-Alegacoes

    Task<IEnumerable<ContraAlegacaoDto>> GetContraAlegacoesAsync(Guid impugnacaoId, CancellationToken cancellationToken = default);

    #endregion

    #region Defesas

    Task<DefesaImpugnacaoDto> ApresentarDefesaAsync(Guid impugnacaoId, CreateDefesaImpugnacaoDto dto, Guid autorId, CancellationToken cancellationToken = default);
    Task<IEnumerable<DefesaImpugnacaoDto>> GetDefesasAsync(Guid impugnacaoId, CancellationToken cancellationToken = default);

    #endregion

    #region Recursos

    Task<IEnumerable<RecursoImpugnacaoDto>> GetRecursosAsync(Guid impugnacaoId, CancellationToken cancellationToken = default);

    #endregion

    #region Historico

    Task<IEnumerable<HistoricoImpugnacaoDto>> GetHistoricoAsync(Guid impugnacaoId, CancellationToken cancellationToken = default);

    #endregion

    #region Statistics

    Task<int> CountByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<int> CountByStatusAsync(StatusImpugnacao status, CancellationToken cancellationToken = default);
    Task<ImpugnacaoEstatisticasDto> GetEstatisticasAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    #endregion

    #region Validation

    /// <summary>
    /// Validates if the impugnation can be created based on calendar period
    /// </summary>
    Task<bool> ValidarPeriodoImpugnacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Validates if alegacoes deadline has not passed
    /// </summary>
    Task<bool> ValidarPrazoAlegacoesAsync(Guid impugnacaoId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Validates if contra-alegacoes deadline has not passed
    /// </summary>
    Task<bool> ValidarPrazoContraAlegacoesAsync(Guid impugnacaoId, CancellationToken cancellationToken = default);

    /// <summary>
    /// Validates if recurso deadline has not passed (typically X days after judgment)
    /// </summary>
    Task<bool> ValidarPrazoRecursoAsync(Guid impugnacaoId, CancellationToken cancellationToken = default);

    #endregion
}
