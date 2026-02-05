using System.ComponentModel.DataAnnotations;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.DTOs.Impugnacoes;

#region Main DTOs

/// <summary>
/// DTO completo de Impugnacao para detalhes
/// </summary>
public record ImpugnacaoDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public string Protocolo { get; init; } = string.Empty;
    public TipoImpugnacao Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public StatusImpugnacao Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public Guid? ChapaImpugnanteId { get; init; }
    public string? ChapaImpugnanteNome { get; init; }
    public Guid? ChapaImpugnadaId { get; init; }
    public string? ChapaImpugnadaNome { get; init; }
    public Guid? MembroImpugnadoId { get; init; }
    public string? MembroImpugnadoNome { get; init; }
    public Guid? ImpugnanteId { get; init; }
    public string? ImpugnanteNome { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
    public DateTime DataImpugnacao { get; init; }
    public DateTime? DataRecebimento { get; init; }
    public DateTime? PrazoAlegacoes { get; init; }
    public DateTime? PrazoContraAlegacoes { get; init; }
    public int TotalPedidos { get; init; }
    public int TotalAlegacoes { get; init; }
    public int TotalDefesas { get; init; }
    public List<PedidoImpugnacaoDto> Pedidos { get; init; } = new();
    public List<AlegacaoDto> Alegacoes { get; init; } = new();
    public List<DefesaImpugnacaoDto> Defesas { get; init; } = new();
    public JulgamentoImpugnacaoDto? Julgamento { get; init; }
    public List<HistoricoImpugnacaoDto> Historicos { get; init; } = new();
    public DateTime CreatedAt { get; init; }
    public DateTime? UpdatedAt { get; init; }
}

/// <summary>
/// DTO resumido para listagens
/// </summary>
public record ImpugnacaoListDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public string Protocolo { get; init; } = string.Empty;
    public TipoImpugnacao Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public StatusImpugnacao Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public string? ChapaImpugnadaNome { get; init; }
    public string? ImpugnanteNome { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public DateTime DataImpugnacao { get; init; }
    public DateTime? PrazoAlegacoes { get; init; }
    public int TotalPedidos { get; init; }
    public int TotalAlegacoes { get; init; }
}

#endregion

#region Create/Update DTOs

/// <summary>
/// DTO para criacao de impugnacao
/// </summary>
public record CreateImpugnacaoDto
{
    [Required(ErrorMessage = "EleicaoId eh obrigatorio")]
    public Guid EleicaoId { get; init; }

    [Required(ErrorMessage = "Tipo eh obrigatorio")]
    public TipoImpugnacao Tipo { get; init; }

    public Guid? ChapaImpugnanteId { get; init; }
    public Guid? ChapaImpugnadaId { get; init; }
    public Guid? MembroImpugnadoId { get; init; }
    public Guid? ImpugnanteId { get; init; }

    [Required(ErrorMessage = "Titulo eh obrigatorio")]
    [StringLength(200, MinimumLength = 5, ErrorMessage = "Titulo deve ter entre 5 e 200 caracteres")]
    public string Titulo { get; init; } = string.Empty;

    [Required(ErrorMessage = "Descricao eh obrigatoria")]
    [StringLength(10000, MinimumLength = 20, ErrorMessage = "Descricao deve ter entre 20 e 10000 caracteres")]
    public string Descricao { get; init; } = string.Empty;

    [StringLength(20000, ErrorMessage = "Fundamentacao deve ter no maximo 20000 caracteres")]
    public string? Fundamentacao { get; init; }

    /// <summary>
    /// Pedidos iniciais da impugnacao
    /// </summary>
    public List<CreatePedidoImpugnacaoDto>? Pedidos { get; init; }
}

/// <summary>
/// DTO para atualizacao de impugnacao
/// </summary>
public record UpdateImpugnacaoDto
{
    [StringLength(200, MinimumLength = 5, ErrorMessage = "Titulo deve ter entre 5 e 200 caracteres")]
    public string? Titulo { get; init; }

    [StringLength(10000, MinimumLength = 20, ErrorMessage = "Descricao deve ter entre 20 e 10000 caracteres")]
    public string? Descricao { get; init; }

    [StringLength(20000, ErrorMessage = "Fundamentacao deve ter no maximo 20000 caracteres")]
    public string? Fundamentacao { get; init; }

    public DateTime? PrazoAlegacoes { get; init; }
    public DateTime? PrazoContraAlegacoes { get; init; }
}

#endregion

#region Pedido DTOs

/// <summary>
/// DTO de pedido de impugnacao
/// </summary>
public record PedidoImpugnacaoDto
{
    public Guid Id { get; init; }
    public Guid ImpugnacaoId { get; init; }
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
    public int Ordem { get; init; }
    public DateTime DataPedido { get; init; }
    public bool Deferido { get; init; }
    public string? ParecerDeferimento { get; init; }
}

/// <summary>
/// DTO para criacao de pedido
/// </summary>
public record CreatePedidoImpugnacaoDto
{
    [Required(ErrorMessage = "Descricao eh obrigatoria")]
    [StringLength(5000, MinimumLength = 20, ErrorMessage = "Descricao deve ter entre 20 e 5000 caracteres")]
    public string Descricao { get; init; } = string.Empty;

    [StringLength(10000, ErrorMessage = "Fundamentacao deve ter no maximo 10000 caracteres")]
    public string? Fundamentacao { get; init; }
}

/// <summary>
/// DTO para deferir/indeferir pedido
/// </summary>
public record AnalisePedidoDto
{
    public bool Deferido { get; init; }

    [Required(ErrorMessage = "Parecer eh obrigatorio")]
    [StringLength(5000, MinimumLength = 20, ErrorMessage = "Parecer deve ter entre 20 e 5000 caracteres")]
    public string Parecer { get; init; } = string.Empty;
}

#endregion

#region Alegacao DTOs

/// <summary>
/// DTO de alegacao de impugnacao
/// </summary>
public record AlegacaoDto
{
    public Guid Id { get; init; }
    public Guid ImpugnacaoId { get; init; }
    public TipoAlegacao Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
    public DateTime DataAlegacao { get; init; }
    public Guid? AutorId { get; init; }
    public string? AutorNome { get; init; }
    public List<ArquivoAlegacaoDto> Arquivos { get; init; } = new();
}

/// <summary>
/// DTO para criacao de alegacao
/// </summary>
public record CreateAlegacaoDto
{
    [Required(ErrorMessage = "Tipo eh obrigatorio")]
    public TipoAlegacao Tipo { get; init; }

    [Required(ErrorMessage = "Descricao eh obrigatoria")]
    [StringLength(10000, MinimumLength = 20, ErrorMessage = "Descricao deve ter entre 20 e 10000 caracteres")]
    public string Descricao { get; init; } = string.Empty;

    [StringLength(20000, ErrorMessage = "Fundamentacao deve ter no maximo 20000 caracteres")]
    public string? Fundamentacao { get; init; }

    public Guid? AutorId { get; init; }

    public List<CreateArquivoAlegacaoDto>? Arquivos { get; init; }
}

/// <summary>
/// DTO de arquivo de alegacao
/// </summary>
public record ArquivoAlegacaoDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public string? Url { get; init; }
    public long? Tamanho { get; init; }
    public DateTime DataEnvio { get; init; }
}

/// <summary>
/// DTO para criacao de arquivo de alegacao
/// </summary>
public record CreateArquivoAlegacaoDto
{
    [Required(ErrorMessage = "Nome eh obrigatorio")]
    [StringLength(200, MinimumLength = 3, ErrorMessage = "Nome deve ter entre 3 e 200 caracteres")]
    public string Nome { get; init; } = string.Empty;

    [StringLength(500, ErrorMessage = "Descricao deve ter no maximo 500 caracteres")]
    public string? Descricao { get; init; }

    [Url(ErrorMessage = "Url deve ser uma URL valida")]
    public string? Url { get; init; }

    public long? Tamanho { get; init; }
}

#endregion

#region Contra-Alegacao DTOs

/// <summary>
/// DTO de contra-alegacao
/// </summary>
public record ContraAlegacaoDto
{
    public Guid Id { get; init; }
    public Guid ImpugnacaoId { get; init; }
    public Guid? AlegacaoId { get; init; }
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
    public DateTime DataContraAlegacao { get; init; }
    public Guid? AutorId { get; init; }
    public string? AutorNome { get; init; }
}

/// <summary>
/// DTO para criacao de contra-alegacao
/// </summary>
public record CreateContraAlegacaoDto
{
    public Guid? AlegacaoId { get; init; }

    [Required(ErrorMessage = "Descricao eh obrigatoria")]
    [StringLength(10000, MinimumLength = 20, ErrorMessage = "Descricao deve ter entre 20 e 10000 caracteres")]
    public string Descricao { get; init; } = string.Empty;

    [StringLength(20000, ErrorMessage = "Fundamentacao deve ter no maximo 20000 caracteres")]
    public string? Fundamentacao { get; init; }

    public Guid? AutorId { get; init; }
}

#endregion

#region Defesa DTOs

/// <summary>
/// DTO de defesa de impugnacao
/// </summary>
public record DefesaImpugnacaoDto
{
    public Guid Id { get; init; }
    public Guid ImpugnacaoId { get; init; }
    public string Conteudo { get; init; } = string.Empty;
    public DateTime DataApresentacao { get; init; }
    public Guid? AutorId { get; init; }
    public string? AutorNome { get; init; }
    public bool Intempestiva { get; init; }
    public List<ArquivoDefesaImpugnacaoDto> Arquivos { get; init; } = new();
}

/// <summary>
/// DTO para criacao de defesa
/// </summary>
public record CreateDefesaImpugnacaoDto
{
    [Required(ErrorMessage = "Conteudo eh obrigatorio")]
    [StringLength(50000, MinimumLength = 50, ErrorMessage = "Conteudo deve ter entre 50 e 50000 caracteres")]
    public string Conteudo { get; init; } = string.Empty;

    public Guid? AutorId { get; init; }

    public List<CreateArquivoDefesaImpugnacaoDto>? Arquivos { get; init; }
}

/// <summary>
/// DTO de arquivo de defesa
/// </summary>
public record ArquivoDefesaImpugnacaoDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public string? Url { get; init; }
    public long? Tamanho { get; init; }
    public DateTime DataEnvio { get; init; }
}

/// <summary>
/// DTO para criacao de arquivo de defesa
/// </summary>
public record CreateArquivoDefesaImpugnacaoDto
{
    [Required(ErrorMessage = "Nome eh obrigatorio")]
    [StringLength(200, MinimumLength = 3, ErrorMessage = "Nome deve ter entre 3 e 200 caracteres")]
    public string Nome { get; init; } = string.Empty;

    [StringLength(500, ErrorMessage = "Descricao deve ter no maximo 500 caracteres")]
    public string? Descricao { get; init; }

    [Url(ErrorMessage = "Url deve ser uma URL valida")]
    public string? Url { get; init; }

    public long? Tamanho { get; init; }
}

#endregion

#region Julgamento DTOs

/// <summary>
/// DTO de julgamento de impugnacao
/// </summary>
public record JulgamentoImpugnacaoDto
{
    public Guid Id { get; init; }
    public Guid ImpugnacaoId { get; init; }
    public StatusJulgamento Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public StatusImpugnacao Resultado { get; init; }
    public string ResultadoNome { get; init; } = string.Empty;
    public TipoDecisao? TipoDecisao { get; init; }
    public string? TipoDecisaoNome { get; init; }
    public string? Decisao { get; init; }
    public string? Fundamentacao { get; init; }
    public DateTime? DataJulgamento { get; init; }
    public Guid? ComissaoId { get; init; }
    public string? ComissaoNome { get; init; }
    public Guid? JulgadorId { get; init; }
    public string? JulgadorNome { get; init; }
    public List<VotoJulgamentoImpugnacaoDto> Votos { get; init; } = new();
}

/// <summary>
/// DTO para registro de julgamento
/// </summary>
public record JulgarImpugnacaoDto
{
    [Required(ErrorMessage = "Resultado eh obrigatorio")]
    public StatusImpugnacao Resultado { get; init; }

    [Required(ErrorMessage = "Decisao eh obrigatoria")]
    [StringLength(5000, MinimumLength = 20, ErrorMessage = "Decisao deve ter entre 20 e 5000 caracteres")]
    public string Decisao { get; init; } = string.Empty;

    [StringLength(20000, ErrorMessage = "Fundamentacao deve ter no maximo 20000 caracteres")]
    public string? Fundamentacao { get; init; }

    public TipoDecisao? TipoDecisao { get; init; }
    public Guid? ComissaoId { get; init; }
}

/// <summary>
/// DTO de voto em julgamento
/// </summary>
public record VotoJulgamentoImpugnacaoDto
{
    public Guid Id { get; init; }
    public Guid JulgamentoId { get; init; }
    public Guid MembroId { get; init; }
    public string MembroNome { get; init; } = string.Empty;
    public TipoVotoJulgamento TipoVoto { get; init; }
    public string TipoVotoNome { get; init; } = string.Empty;
    public string? Justificativa { get; init; }
    public DateTime DataVoto { get; init; }
}

/// <summary>
/// DTO para registro de voto
/// </summary>
public record RegistrarVotoImpugnacaoDto
{
    [Required(ErrorMessage = "MembroId eh obrigatorio")]
    public Guid MembroId { get; init; }

    [Required(ErrorMessage = "TipoVoto eh obrigatorio")]
    public TipoVotoJulgamento TipoVoto { get; init; }

    [StringLength(2000, ErrorMessage = "Justificativa deve ter no maximo 2000 caracteres")]
    public string? Justificativa { get; init; }
}

#endregion

#region Recurso DTOs

/// <summary>
/// DTO de recurso de impugnacao
/// </summary>
public record RecursoImpugnacaoDto
{
    public Guid Id { get; init; }
    public Guid ImpugnacaoId { get; init; }
    public TipoRecurso Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public StatusRecurso Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public Guid? RecorrenteId { get; init; }
    public string? RecorrenteNome { get; init; }
    public string Fundamentacao { get; init; } = string.Empty;
    public DateTime DataProtocolo { get; init; }
    public DateTime? DataJulgamento { get; init; }
    public string? DecisaoRecurso { get; init; }
}

/// <summary>
/// DTO para criacao de recurso
/// </summary>
public record CreateRecursoImpugnacaoDto
{
    [Required(ErrorMessage = "Tipo eh obrigatorio")]
    public TipoRecurso Tipo { get; init; }

    public Guid? RecorrenteId { get; init; }

    [Required(ErrorMessage = "Fundamentacao eh obrigatoria")]
    [StringLength(20000, MinimumLength = 50, ErrorMessage = "Fundamentacao deve ter entre 50 e 20000 caracteres")]
    public string Fundamentacao { get; init; } = string.Empty;
}

/// <summary>
/// DTO para julgar recurso
/// </summary>
public record JulgarRecursoImpugnacaoDto
{
    [Required(ErrorMessage = "Status eh obrigatorio")]
    public StatusRecurso Status { get; init; }

    [Required(ErrorMessage = "Decisao eh obrigatoria")]
    [StringLength(10000, MinimumLength = 50, ErrorMessage = "Decisao deve ter entre 50 e 10000 caracteres")]
    public string Decisao { get; init; } = string.Empty;
}

#endregion

#region Historico DTOs

/// <summary>
/// DTO de historico de impugnacao
/// </summary>
public record HistoricoImpugnacaoDto
{
    public Guid Id { get; init; }
    public Guid ImpugnacaoId { get; init; }
    public string Descricao { get; init; } = string.Empty;
    public StatusImpugnacao? StatusAnterior { get; init; }
    public StatusImpugnacao? StatusNovo { get; init; }
    public Guid? UsuarioId { get; init; }
    public string? UsuarioNome { get; init; }
    public DateTime DataAlteracao { get; init; }
}

#endregion

#region Acoes DTOs

/// <summary>
/// DTO para arquivar impugnacao
/// </summary>
public record ArquivarImpugnacaoDto
{
    [Required(ErrorMessage = "Motivo eh obrigatorio")]
    [StringLength(2000, MinimumLength = 10, ErrorMessage = "Motivo deve ter entre 10 e 2000 caracteres")]
    public string Motivo { get; init; } = string.Empty;
}

/// <summary>
/// DTO para abrir prazo de alegacoes
/// </summary>
public record AbrirPrazoAlegacoesDto
{
    [Range(1, 30, ErrorMessage = "PrazoEmDias deve ser entre 1 e 30")]
    public int PrazoEmDias { get; init; } = 5;

    [StringLength(1000, ErrorMessage = "Observacoes deve ter no maximo 1000 caracteres")]
    public string? Observacoes { get; init; }
}

/// <summary>
/// DTO para abrir prazo de contra-alegacoes
/// </summary>
public record AbrirPrazoContraAlegacoesDto
{
    [Range(1, 30, ErrorMessage = "PrazoEmDias deve ser entre 1 e 30")]
    public int PrazoEmDias { get; init; } = 5;

    [StringLength(1000, ErrorMessage = "Observacoes deve ter no maximo 1000 caracteres")]
    public string? Observacoes { get; init; }
}

#endregion

#region Filtros e Paginacao

/// <summary>
/// Filtros para busca de impugnacoes
/// </summary>
public record FiltroImpugnacaoDto
{
    public Guid? EleicaoId { get; init; }
    public StatusImpugnacao? Status { get; init; }
    public TipoImpugnacao? Tipo { get; init; }
    public Guid? ChapaImpugnadaId { get; init; }
    public Guid? ImpugnanteId { get; init; }
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }

    [StringLength(200, ErrorMessage = "Termo deve ter no maximo 200 caracteres")]
    public string? Termo { get; init; }

    [Range(1, int.MaxValue, ErrorMessage = "Pagina deve ser maior que 0")]
    public int Pagina { get; init; } = 1;

    [Range(1, 100, ErrorMessage = "TamanhoPagina deve ser entre 1 e 100")]
    public int TamanhoPagina { get; init; } = 20;

    public string? OrdenarPor { get; init; }
    public bool OrdemDescendente { get; init; } = true;
}

/// <summary>
/// Resultado paginado de impugnacoes
/// </summary>
public record ImpugnacaoPagedResultDto
{
    public IEnumerable<ImpugnacaoListDto> Items { get; init; } = new List<ImpugnacaoListDto>();
    public int TotalItems { get; init; }
    public int TotalPages { get; init; }
    public int CurrentPage { get; init; }
    public int PageSize { get; init; }
    public bool HasPreviousPage => CurrentPage > 1;
    public bool HasNextPage => CurrentPage < TotalPages;
}

#endregion

#region Estatisticas DTOs

/// <summary>
/// Estatisticas de impugnacoes
/// </summary>
public record ImpugnacaoEstatisticasDto
{
    public int Total { get; init; }
    public int Recebidas { get; init; }
    public int EmAnalise { get; init; }
    public int AguardandoAlegacoes { get; init; }
    public int AguardandoContraAlegacoes { get; init; }
    public int AguardandoJulgamento { get; init; }
    public int Julgadas { get; init; }
    public int Arquivadas { get; init; }
    public int Procedentes { get; init; }
    public int Improcedentes { get; init; }
    public int ParcialmenteProcedentes { get; init; }
    public Dictionary<string, int> PorTipo { get; init; } = new();
    public Dictionary<string, int> PorStatus { get; init; } = new();
}

#endregion
