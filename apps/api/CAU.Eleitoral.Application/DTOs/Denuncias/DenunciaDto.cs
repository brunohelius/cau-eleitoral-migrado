using System.ComponentModel.DataAnnotations;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.DTOs.Denuncias;

#region Main DTOs

/// <summary>
/// DTO completo de Denuncia para detalhes
/// </summary>
public record DenunciaDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public string Protocolo { get; init; } = string.Empty;
    public TipoDenuncia Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public StatusDenuncia Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public Guid? ChapaId { get; init; }
    public string? ChapaNome { get; init; }
    public Guid? MembroId { get; init; }
    public string? MembroNome { get; init; }
    public Guid? DenuncianteId { get; init; }
    public string? DenuncianteNome { get; init; }
    public bool Anonima { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
    public DateTime DataDenuncia { get; init; }
    public DateTime? DataRecebimento { get; init; }
    public DateTime? PrazoDefesa { get; init; }
    public DateTime? PrazoRecurso { get; init; }
    public int TotalProvas { get; init; }
    public int TotalDefesas { get; init; }
    public List<ProvaDenunciaDto> Provas { get; init; } = new();
    public List<DefesaDenunciaDto> Defesas { get; init; } = new();
    public List<HistoricoDenunciaDto> Historicos { get; init; } = new();
    public AdmissibilidadeDenunciaDto? Admissibilidade { get; init; }
    public JulgamentoDenunciaDto? Julgamento { get; init; }
    public DateTime CreatedAt { get; init; }
    public DateTime? UpdatedAt { get; init; }
}

/// <summary>
/// DTO resumido para listagens
/// </summary>
public record DenunciaListDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string Protocolo { get; init; } = string.Empty;
    public string EleicaoNome { get; init; } = string.Empty;
    public TipoDenuncia Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public StatusDenuncia Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public string? ChapaNome { get; init; }
    public string? DenuncianteNome { get; init; }
    public bool Anonima { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public DateTime DataDenuncia { get; init; }
    public DateTime? PrazoDefesa { get; init; }
    public int TotalProvas { get; init; }
    public int TotalDefesas { get; init; }
}

#endregion

#region Create/Update DTOs

/// <summary>
/// DTO para criacao de denuncia
/// </summary>
public record CreateDenunciaDto
{
    [Required(ErrorMessage = "EleicaoId eh obrigatorio")]
    public Guid EleicaoId { get; init; }

    [Required(ErrorMessage = "Tipo eh obrigatorio")]
    public TipoDenuncia Tipo { get; init; }

    public Guid? ChapaId { get; init; }
    public Guid? MembroId { get; init; }
    public Guid? DenuncianteId { get; init; }
    public bool Anonima { get; init; }

    [Required(ErrorMessage = "Titulo eh obrigatorio")]
    [StringLength(200, MinimumLength = 5, ErrorMessage = "Titulo deve ter entre 5 e 200 caracteres")]
    public string Titulo { get; init; } = string.Empty;

    [Required(ErrorMessage = "Descricao eh obrigatoria")]
    [StringLength(10000, MinimumLength = 20, ErrorMessage = "Descricao deve ter entre 20 e 10000 caracteres")]
    public string Descricao { get; init; } = string.Empty;

    [StringLength(20000, ErrorMessage = "Fundamentacao deve ter no maximo 20000 caracteres")]
    public string? Fundamentacao { get; init; }

    public List<CreateProvaDenunciaDto>? Provas { get; init; }
}

/// <summary>
/// DTO para atualizacao de denuncia
/// </summary>
public record UpdateDenunciaDto
{
    public TipoDenuncia? Tipo { get; init; }

    [StringLength(200, MinimumLength = 5, ErrorMessage = "Titulo deve ter entre 5 e 200 caracteres")]
    public string? Titulo { get; init; }

    [StringLength(10000, MinimumLength = 20, ErrorMessage = "Descricao deve ter entre 20 e 10000 caracteres")]
    public string? Descricao { get; init; }

    [StringLength(20000, ErrorMessage = "Fundamentacao deve ter no maximo 20000 caracteres")]
    public string? Fundamentacao { get; init; }

    public DateTime? PrazoDefesa { get; init; }
    public DateTime? PrazoRecurso { get; init; }
}

#endregion

#region Prova DTOs

/// <summary>
/// DTO de prova da denuncia
/// </summary>
public record ProvaDenunciaDto
{
    public Guid Id { get; init; }
    public Guid DenunciaId { get; init; }
    public TipoProva Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public string Descricao { get; init; } = string.Empty;
    public string? ArquivoUrl { get; init; }
    public string? ArquivoNome { get; init; }
    public long? ArquivoTamanho { get; init; }
    public string? ArquivoTipo { get; init; }
    public DateTime DataEnvio { get; init; }
    public bool Validada { get; init; }
    public string? ParecerValidacao { get; init; }
}

/// <summary>
/// DTO para criacao de prova
/// </summary>
public record CreateProvaDenunciaDto
{
    [Required(ErrorMessage = "Tipo eh obrigatorio")]
    public TipoProva Tipo { get; init; }

    [Required(ErrorMessage = "Descricao eh obrigatoria")]
    [StringLength(2000, MinimumLength = 10, ErrorMessage = "Descricao deve ter entre 10 e 2000 caracteres")]
    public string Descricao { get; init; } = string.Empty;

    [Url(ErrorMessage = "ArquivoUrl deve ser uma URL valida")]
    public string? ArquivoUrl { get; init; }

    [StringLength(200, ErrorMessage = "ArquivoNome deve ter no maximo 200 caracteres")]
    public string? ArquivoNome { get; init; }

    public long? ArquivoTamanho { get; init; }

    [StringLength(100, ErrorMessage = "ArquivoTipo deve ter no maximo 100 caracteres")]
    public string? ArquivoTipo { get; init; }
}

/// <summary>
/// DTO para validacao de prova
/// </summary>
public record ValidarProvaDto
{
    public bool Validada { get; init; }

    [StringLength(1000, ErrorMessage = "Parecer deve ter no maximo 1000 caracteres")]
    public string? Parecer { get; init; }
}

#endregion

#region Defesa DTOs

/// <summary>
/// DTO de defesa da denuncia
/// </summary>
public record DefesaDenunciaDto
{
    public Guid Id { get; init; }
    public Guid DenunciaId { get; init; }
    public string Conteudo { get; init; } = string.Empty;
    public StatusDefesa Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public DateTime DataApresentacao { get; init; }
    public Guid? AutorId { get; init; }
    public string? AutorNome { get; init; }
    public bool Intempestiva { get; init; }
    public List<ArquivoDefesaDto> Arquivos { get; init; } = new();
}

/// <summary>
/// DTO para criacao de defesa
/// </summary>
public record CreateDefesaDto
{
    [Required(ErrorMessage = "Conteudo eh obrigatorio")]
    [StringLength(50000, MinimumLength = 50, ErrorMessage = "Conteudo deve ter entre 50 e 50000 caracteres")]
    public string Conteudo { get; init; } = string.Empty;

    public Guid? AutorId { get; init; }

    public List<CreateArquivoDefesaDto>? Arquivos { get; init; }
}

/// <summary>
/// DTO de arquivo de defesa
/// </summary>
public record ArquivoDefesaDto
{
    public Guid Id { get; init; }
    public Guid DefesaId { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public string? Url { get; init; }
    public long? Tamanho { get; init; }
    public string? Tipo { get; init; }
    public DateTime DataEnvio { get; init; }
}

/// <summary>
/// DTO para criacao de arquivo de defesa
/// </summary>
public record CreateArquivoDefesaDto
{
    [Required(ErrorMessage = "Nome eh obrigatorio")]
    [StringLength(200, MinimumLength = 3, ErrorMessage = "Nome deve ter entre 3 e 200 caracteres")]
    public string Nome { get; init; } = string.Empty;

    [StringLength(500, ErrorMessage = "Descricao deve ter no maximo 500 caracteres")]
    public string? Descricao { get; init; }

    [Url(ErrorMessage = "Url deve ser uma URL valida")]
    public string? Url { get; init; }

    public long? Tamanho { get; init; }

    [StringLength(100, ErrorMessage = "Tipo deve ter no maximo 100 caracteres")]
    public string? Tipo { get; init; }
}

#endregion

#region Admissibilidade DTOs

/// <summary>
/// DTO de admissibilidade
/// </summary>
public record AdmissibilidadeDenunciaDto
{
    public Guid Id { get; init; }
    public Guid DenunciaId { get; init; }
    public bool Admissivel { get; init; }
    public string Parecer { get; init; } = string.Empty;
    public Guid RelatorId { get; init; }
    public string RelatorNome { get; init; } = string.Empty;
    public DateTime DataAnalise { get; init; }
    public string? Fundamentacao { get; init; }
}

/// <summary>
/// DTO para registro de admissibilidade
/// </summary>
public record RegistrarAdmissibilidadeDto
{
    public bool Admissivel { get; init; }

    [Required(ErrorMessage = "Parecer eh obrigatorio")]
    [StringLength(5000, MinimumLength = 20, ErrorMessage = "Parecer deve ter entre 20 e 5000 caracteres")]
    public string Parecer { get; init; } = string.Empty;

    [StringLength(10000, ErrorMessage = "Fundamentacao deve ter no maximo 10000 caracteres")]
    public string? Fundamentacao { get; init; }
}

#endregion

#region Julgamento DTOs

/// <summary>
/// DTO de julgamento de denuncia
/// </summary>
public record JulgamentoDenunciaDto
{
    public Guid Id { get; init; }
    public Guid DenunciaId { get; init; }
    public StatusDenuncia Resultado { get; init; }
    public string ResultadoNome { get; init; } = string.Empty;
    public string Decisao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
    public Guid JulgadorId { get; init; }
    public string JulgadorNome { get; init; } = string.Empty;
    public DateTime DataJulgamento { get; init; }
    public TipoDecisao? TipoDecisao { get; init; }
    public string? TipoDecisaoNome { get; init; }
    public Guid? ComissaoId { get; init; }
    public string? ComissaoNome { get; init; }
    public List<VotoJulgamentoDenunciaDto> Votos { get; init; } = new();
}

/// <summary>
/// DTO para registro de julgamento
/// </summary>
public record JulgarDenunciaDto
{
    [Required(ErrorMessage = "Resultado eh obrigatorio")]
    public StatusDenuncia Resultado { get; init; }

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
public record VotoJulgamentoDenunciaDto
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
public record RegistrarVotoDenunciaDto
{
    [Required(ErrorMessage = "MembroId eh obrigatorio")]
    public Guid MembroId { get; init; }

    [Required(ErrorMessage = "TipoVoto eh obrigatorio")]
    public TipoVotoJulgamento TipoVoto { get; init; }

    [StringLength(2000, ErrorMessage = "Justificativa deve ter no maximo 2000 caracteres")]
    public string? Justificativa { get; init; }
}

#endregion

#region Historico DTOs

/// <summary>
/// DTO de historico de denuncia
/// </summary>
public record HistoricoDenunciaDto
{
    public Guid Id { get; init; }
    public Guid DenunciaId { get; init; }
    public string Descricao { get; init; } = string.Empty;
    public StatusDenuncia? StatusAnterior { get; init; }
    public StatusDenuncia? StatusNovo { get; init; }
    public Guid? UsuarioId { get; init; }
    public string? UsuarioNome { get; init; }
    public DateTime DataAlteracao { get; init; }
}

#endregion

#region Analise DTOs

/// <summary>
/// DTO de analise de denuncia
/// </summary>
public record AnaliseDenunciaDto
{
    public Guid Id { get; init; }
    public Guid DenunciaId { get; init; }
    public StatusAnaliseDenuncia Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public string? Parecer { get; init; }
    public Guid AnalistaId { get; init; }
    public string AnalistaNome { get; init; } = string.Empty;
    public DateTime DataInicio { get; init; }
    public DateTime? DataConclusao { get; init; }
}

/// <summary>
/// DTO para iniciar analise
/// </summary>
public record IniciarAnaliseDto
{
    [StringLength(1000, ErrorMessage = "Observacoes deve ter no maximo 1000 caracteres")]
    public string? Observacoes { get; init; }
}

/// <summary>
/// DTO para concluir analise
/// </summary>
public record ConcluirAnaliseDto
{
    [Required(ErrorMessage = "Parecer eh obrigatorio")]
    [StringLength(5000, MinimumLength = 20, ErrorMessage = "Parecer deve ter entre 20 e 5000 caracteres")]
    public string Parecer { get; init; } = string.Empty;

    public bool Recomendacao { get; init; }
}

#endregion

#region Acoes DTOs

/// <summary>
/// DTO para arquivar denuncia
/// </summary>
public record ArquivarDenunciaDto
{
    [Required(ErrorMessage = "Motivo eh obrigatorio")]
    [StringLength(2000, MinimumLength = 10, ErrorMessage = "Motivo deve ter entre 10 e 2000 caracteres")]
    public string Motivo { get; init; } = string.Empty;
}

/// <summary>
/// DTO para solicitar defesa
/// </summary>
public record SolicitarDefesaDto
{
    [Range(1, 30, ErrorMessage = "PrazoEmDias deve ser entre 1 e 30")]
    public int PrazoEmDias { get; init; } = 5;

    [StringLength(1000, ErrorMessage = "Observacoes deve ter no maximo 1000 caracteres")]
    public string? Observacoes { get; init; }
}

/// <summary>
/// DTO para reabrir denuncia
/// </summary>
public record ReabrirDenunciaDto
{
    [Required(ErrorMessage = "Motivo eh obrigatorio")]
    [StringLength(2000, MinimumLength = 10, ErrorMessage = "Motivo deve ter entre 10 e 2000 caracteres")]
    public string Motivo { get; init; } = string.Empty;
}

#endregion

#region Recurso DTOs

/// <summary>
/// DTO de recurso de denuncia
/// </summary>
public record RecursoDenunciaDto
{
    public Guid Id { get; init; }
    public Guid DenunciaId { get; init; }
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
public record CreateRecursoDenunciaDto
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
public record JulgarRecursoDenunciaDto
{
    [Required(ErrorMessage = "Status eh obrigatorio")]
    public StatusRecurso Status { get; init; }

    [Required(ErrorMessage = "Decisao eh obrigatoria")]
    [StringLength(10000, MinimumLength = 50, ErrorMessage = "Decisao deve ter entre 50 e 10000 caracteres")]
    public string Decisao { get; init; } = string.Empty;
}

#endregion

#region Filtros e Paginacao

/// <summary>
/// Filtros para busca de denuncias
/// </summary>
public record FiltroDenunciaDto
{
    public Guid? EleicaoId { get; init; }
    public StatusDenuncia? Status { get; init; }
    public TipoDenuncia? Tipo { get; init; }
    public Guid? ChapaId { get; init; }
    public Guid? DenuncianteId { get; init; }
    public bool? Anonima { get; init; }
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
/// Resultado paginado de denuncias
/// </summary>
public record DenunciaPagedResultDto
{
    public IEnumerable<DenunciaListDto> Items { get; init; } = new List<DenunciaListDto>();
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
/// Estatisticas de denuncias
/// </summary>
public record DenunciaEstatisticasDto
{
    public int Total { get; init; }
    public int Recebidas { get; init; }
    public int EmAnalise { get; init; }
    public int AguardandoDefesa { get; init; }
    public int AguardandoJulgamento { get; init; }
    public int Julgadas { get; init; }
    public int Arquivadas { get; init; }
    public int Procedentes { get; init; }
    public int Improcedentes { get; init; }
    public int ParcialmenteProcedentes { get; init; }
    public Dictionary<string, int> PorTipo { get; init; } = new();
    public Dictionary<string, int> PorStatus { get; init; } = new();
    public Dictionary<string, int> PorMes { get; init; } = new();
}

#endregion
