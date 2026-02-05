using System.ComponentModel.DataAnnotations;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.DTOs.Chapas;

#region Main DTOs

/// <summary>
/// DTO completo de Chapa para detalhes
/// </summary>
public record ChapaDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public string Numero { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string? Sigla { get; init; }
    public string? Slogan { get; init; }
    public StatusChapa Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public DateTime DataInscricao { get; init; }
    public DateTime? DataHomologacao { get; init; }
    public DateTime? DataIndeferimento { get; init; }
    public string? MotivoIndeferimento { get; init; }
    public string? LogoUrl { get; init; }
    public string? FotoUrl { get; init; }
    public string? CorPrimaria { get; init; }
    public string? CorSecundaria { get; init; }
    public int OrdemSorteio { get; init; }
    public int TotalMembros { get; init; }
    public List<MembroChapaDto> Membros { get; init; } = new();
    public List<DocumentoChapaDto> Documentos { get; init; } = new();
    public PlataformaEleitoralDto? Plataforma { get; init; }
    public DateTime CreatedAt { get; init; }
    public DateTime? UpdatedAt { get; init; }
}

/// <summary>
/// DTO resumido para listagens
/// </summary>
public record ChapaListDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public string Numero { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string? Sigla { get; init; }
    public StatusChapa Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public int TotalMembros { get; init; }
    public DateTime DataInscricao { get; init; }
    public string? LogoUrl { get; init; }
}

/// <summary>
/// DTO para criacao de chapa
/// </summary>
public record CreateChapaDto
{
    [Required(ErrorMessage = "EleicaoId eh obrigatorio")]
    public Guid EleicaoId { get; init; }

    [Required(ErrorMessage = "Numero eh obrigatorio")]
    [StringLength(10, MinimumLength = 1, ErrorMessage = "Numero deve ter entre 1 e 10 caracteres")]
    public string Numero { get; init; } = string.Empty;

    [Required(ErrorMessage = "Nome eh obrigatorio")]
    [StringLength(200, MinimumLength = 3, ErrorMessage = "Nome deve ter entre 3 e 200 caracteres")]
    public string Nome { get; init; } = string.Empty;

    [StringLength(20, ErrorMessage = "Sigla deve ter no maximo 20 caracteres")]
    public string? Sigla { get; init; }

    [StringLength(500, ErrorMessage = "Slogan deve ter no maximo 500 caracteres")]
    public string? Slogan { get; init; }

    [StringLength(7, ErrorMessage = "CorPrimaria deve ter no maximo 7 caracteres")]
    [RegularExpression(@"^#[0-9A-Fa-f]{6}$", ErrorMessage = "CorPrimaria deve ser um codigo hexadecimal valido")]
    public string? CorPrimaria { get; init; }

    [StringLength(7, ErrorMessage = "CorSecundaria deve ter no maximo 7 caracteres")]
    [RegularExpression(@"^#[0-9A-Fa-f]{6}$", ErrorMessage = "CorSecundaria deve ser um codigo hexadecimal valido")]
    public string? CorSecundaria { get; init; }
}

/// <summary>
/// DTO para atualizacao de chapa
/// </summary>
public record UpdateChapaDto
{
    [StringLength(10, MinimumLength = 1, ErrorMessage = "Numero deve ter entre 1 e 10 caracteres")]
    public string? Numero { get; init; }

    [StringLength(200, MinimumLength = 3, ErrorMessage = "Nome deve ter entre 3 e 200 caracteres")]
    public string? Nome { get; init; }

    [StringLength(20, ErrorMessage = "Sigla deve ter no maximo 20 caracteres")]
    public string? Sigla { get; init; }

    [StringLength(500, ErrorMessage = "Slogan deve ter no maximo 500 caracteres")]
    public string? Slogan { get; init; }

    [StringLength(7, ErrorMessage = "CorPrimaria deve ter no maximo 7 caracteres")]
    [RegularExpression(@"^#[0-9A-Fa-f]{6}$", ErrorMessage = "CorPrimaria deve ser um codigo hexadecimal valido")]
    public string? CorPrimaria { get; init; }

    [StringLength(7, ErrorMessage = "CorSecundaria deve ter no maximo 7 caracteres")]
    [RegularExpression(@"^#[0-9A-Fa-f]{6}$", ErrorMessage = "CorSecundaria deve ser um codigo hexadecimal valido")]
    public string? CorSecundaria { get; init; }

    [Url(ErrorMessage = "LogoUrl deve ser uma URL valida")]
    public string? LogoUrl { get; init; }

    [Url(ErrorMessage = "FotoUrl deve ser uma URL valida")]
    public string? FotoUrl { get; init; }
}

/// <summary>
/// DTO para analise (deferimento/indeferimento) de chapa
/// </summary>
public record AnaliseChapaDto
{
    [Required(ErrorMessage = "Decisao eh obrigatoria")]
    public bool Deferido { get; init; }

    [Required(ErrorMessage = "Parecer eh obrigatorio")]
    [StringLength(5000, MinimumLength = 10, ErrorMessage = "Parecer deve ter entre 10 e 5000 caracteres")]
    public string Parecer { get; init; } = string.Empty;

    [StringLength(2000, ErrorMessage = "Motivo deve ter no maximo 2000 caracteres")]
    public string? MotivoIndeferimento { get; init; }
}

#endregion

#region Membro Chapa DTOs

/// <summary>
/// DTO completo de membro da chapa
/// </summary>
public record MembroChapaDto
{
    public Guid Id { get; init; }
    public Guid ChapaId { get; init; }
    public Guid? ProfissionalId { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Cpf { get; init; }
    public string? RegistroCAU { get; init; }
    public string? Email { get; init; }
    public string? Telefone { get; init; }
    public TipoMembroChapa Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public StatusMembroChapa Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public int Ordem { get; init; }
    public string? Cargo { get; init; }
    public bool Titular { get; init; }
    public DateTime? DataConfirmacao { get; init; }
    public string? FotoUrl { get; init; }
    public string? CurriculoResumo { get; init; }
    public string? MotivoRecusa { get; init; }
    public string? MotivoInabilitacao { get; init; }
}

/// <summary>
/// DTO resumido de membro para listagens
/// </summary>
public record MembroChapaListDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? RegistroCAU { get; init; }
    public TipoMembroChapa Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public StatusMembroChapa Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public int Ordem { get; init; }
    public bool Titular { get; init; }
}

/// <summary>
/// DTO para adicao de membro a chapa
/// </summary>
public record CreateMembroChapaDto
{
    public Guid? ProfissionalId { get; init; }

    [StringLength(200, MinimumLength = 3, ErrorMessage = "Nome deve ter entre 3 e 200 caracteres")]
    public string? Nome { get; init; }

    [StringLength(11, MinimumLength = 11, ErrorMessage = "CPF deve ter 11 digitos")]
    [RegularExpression(@"^\d{11}$", ErrorMessage = "CPF deve conter apenas numeros")]
    public string? Cpf { get; init; }

    [StringLength(20, ErrorMessage = "RegistroCAU deve ter no maximo 20 caracteres")]
    public string? RegistroCAU { get; init; }

    [EmailAddress(ErrorMessage = "Email invalido")]
    public string? Email { get; init; }

    [Phone(ErrorMessage = "Telefone invalido")]
    public string? Telefone { get; init; }

    [Required(ErrorMessage = "TipoMembro eh obrigatorio")]
    public TipoMembroChapa TipoMembro { get; init; }

    [StringLength(100, ErrorMessage = "Cargo deve ter no maximo 100 caracteres")]
    public string? Cargo { get; init; }

    public bool Titular { get; init; } = true;

    [StringLength(2000, ErrorMessage = "CurriculoResumo deve ter no maximo 2000 caracteres")]
    public string? CurriculoResumo { get; init; }
}

/// <summary>
/// DTO para atualizacao de membro
/// </summary>
public record UpdateMembroChapaDto
{
    [StringLength(200, MinimumLength = 3, ErrorMessage = "Nome deve ter entre 3 e 200 caracteres")]
    public string? Nome { get; init; }

    [EmailAddress(ErrorMessage = "Email invalido")]
    public string? Email { get; init; }

    [Phone(ErrorMessage = "Telefone invalido")]
    public string? Telefone { get; init; }

    public TipoMembroChapa? TipoMembro { get; init; }

    [StringLength(100, ErrorMessage = "Cargo deve ter no maximo 100 caracteres")]
    public string? Cargo { get; init; }

    public bool? Titular { get; init; }

    public int? Ordem { get; init; }

    [Url(ErrorMessage = "FotoUrl deve ser uma URL valida")]
    public string? FotoUrl { get; init; }

    [StringLength(2000, ErrorMessage = "CurriculoResumo deve ter no maximo 2000 caracteres")]
    public string? CurriculoResumo { get; init; }
}

/// <summary>
/// DTO para confirmacao de membro
/// </summary>
public record ConfirmacaoMembroChapaDto
{
    [Required(ErrorMessage = "Token eh obrigatorio")]
    public string Token { get; init; } = string.Empty;

    public bool Aceito { get; init; }

    [StringLength(500, ErrorMessage = "MotivoRecusa deve ter no maximo 500 caracteres")]
    public string? MotivoRecusa { get; init; }
}

#endregion

#region Documento Chapa DTOs

/// <summary>
/// DTO de documento da chapa
/// </summary>
public record DocumentoChapaDto
{
    public Guid Id { get; init; }
    public Guid ChapaId { get; init; }
    public Guid? MembroId { get; init; }
    public TipoDocumentoChapa Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public StatusDocumentoChapa Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public string ArquivoUrl { get; init; } = string.Empty;
    public string? ArquivoNome { get; init; }
    public long? ArquivoTamanho { get; init; }
    public string? ArquivoTipo { get; init; }
    public DateTime DataEnvio { get; init; }
    public DateTime? DataAnalise { get; init; }
    public string? AnalisadoPor { get; init; }
    public string? MotivoRejeicao { get; init; }
    public bool Obrigatorio { get; init; }
    public int Ordem { get; init; }
}

/// <summary>
/// DTO para upload de documento
/// </summary>
public record CreateDocumentoChapaDto
{
    [Required(ErrorMessage = "Tipo eh obrigatorio")]
    public TipoDocumentoChapa Tipo { get; init; }

    [Required(ErrorMessage = "Nome eh obrigatorio")]
    [StringLength(200, MinimumLength = 3, ErrorMessage = "Nome deve ter entre 3 e 200 caracteres")]
    public string Nome { get; init; } = string.Empty;

    [StringLength(500, ErrorMessage = "Descricao deve ter no maximo 500 caracteres")]
    public string? Descricao { get; init; }

    [Required(ErrorMessage = "ArquivoUrl eh obrigatorio")]
    [Url(ErrorMessage = "ArquivoUrl deve ser uma URL valida")]
    public string ArquivoUrl { get; init; } = string.Empty;

    [Required(ErrorMessage = "ArquivoNome eh obrigatorio")]
    [StringLength(200, ErrorMessage = "ArquivoNome deve ter no maximo 200 caracteres")]
    public string ArquivoNome { get; init; } = string.Empty;

    public long? ArquivoTamanho { get; init; }

    [StringLength(100, ErrorMessage = "ArquivoTipo deve ter no maximo 100 caracteres")]
    public string? ArquivoTipo { get; init; }
}

/// <summary>
/// DTO para analise de documento
/// </summary>
public record AnaliseDocumentoChapaDto
{
    [Required(ErrorMessage = "Status eh obrigatorio")]
    public StatusDocumentoChapa Status { get; init; }

    [StringLength(2000, ErrorMessage = "Parecer deve ter no maximo 2000 caracteres")]
    public string? Parecer { get; init; }
}

#endregion

#region Plataforma Eleitoral DTOs

/// <summary>
/// DTO de plataforma eleitoral
/// </summary>
public record PlataformaEleitoralDto
{
    public Guid Id { get; init; }
    public Guid ChapaId { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string? Resumo { get; init; }
    public string? Conteudo { get; init; }
    public string? Missao { get; init; }
    public string? Visao { get; init; }
    public string? Valores { get; init; }
    public string? PropostasJson { get; init; }
    public string? MetasJson { get; init; }
    public string? EixosJson { get; init; }
    public string? VideoUrl { get; init; }
    public string? ApresentacaoUrl { get; init; }
    public DateTime DataPublicacao { get; init; }
    public bool Publicada { get; init; }
}

/// <summary>
/// DTO para criar/atualizar plataforma
/// </summary>
public record CreatePlataformaEleitoralDto
{
    [Required(ErrorMessage = "Titulo eh obrigatorio")]
    [StringLength(200, MinimumLength = 5, ErrorMessage = "Titulo deve ter entre 5 e 200 caracteres")]
    public string Titulo { get; init; } = string.Empty;

    [StringLength(2000, ErrorMessage = "Resumo deve ter no maximo 2000 caracteres")]
    public string? Resumo { get; init; }

    [StringLength(50000, ErrorMessage = "Conteudo deve ter no maximo 50000 caracteres")]
    public string? Conteudo { get; init; }

    [StringLength(2000, ErrorMessage = "Missao deve ter no maximo 2000 caracteres")]
    public string? Missao { get; init; }

    [StringLength(2000, ErrorMessage = "Visao deve ter no maximo 2000 caracteres")]
    public string? Visao { get; init; }

    [StringLength(2000, ErrorMessage = "Valores deve ter no maximo 2000 caracteres")]
    public string? Valores { get; init; }

    public string? PropostasJson { get; init; }
    public string? MetasJson { get; init; }
    public string? EixosJson { get; init; }

    [Url(ErrorMessage = "VideoUrl deve ser uma URL valida")]
    public string? VideoUrl { get; init; }

    [Url(ErrorMessage = "ApresentacaoUrl deve ser uma URL valida")]
    public string? ApresentacaoUrl { get; init; }

    [Url(ErrorMessage = "ArquivoUrl deve ser uma URL valida")]
    public string? ArquivoUrl { get; init; }

    [StringLength(50000, ErrorMessage = "ConteudoCompleto deve ter no maximo 50000 caracteres")]
    public string? ConteudoCompleto { get; init; }

    public bool Publicada { get; init; }
}

#endregion

#region Filtros e Paginacao

/// <summary>
/// Filtros para busca de chapas
/// </summary>
public record ChapaFilterDto
{
    public Guid? EleicaoId { get; init; }
    public int? Status { get; init; }
    public string? Search { get; init; }
    public string? Numero { get; init; }
    public DateTime? DataInscricaoInicio { get; init; }
    public DateTime? DataInscricaoFim { get; init; }
    public int Page { get; init; } = 1;
    public int PageSize { get; init; } = 10;
    public string? OrderBy { get; init; } = "DataInscricao";
    public bool OrderDescending { get; init; } = true;
}

/// <summary>
/// Resultado paginado generico
/// </summary>
public record PagedResultDto<T>
{
    public IEnumerable<T> Items { get; init; } = new List<T>();
    public int TotalCount { get; init; }
    public int Page { get; init; }
    public int PageSize { get; init; }
    public int TotalPages => PageSize > 0 ? (int)Math.Ceiling(TotalCount / (double)PageSize) : 0;
    public bool HasNextPage => Page < TotalPages;
    public bool HasPreviousPage => Page > 1;
}

/// <summary>
/// Resultado paginado de chapas
/// </summary>
public record ChapaPagedResultDto
{
    public IEnumerable<ChapaListDto> Items { get; init; } = new List<ChapaListDto>();
    public int TotalItems { get; init; }
    public int TotalPages { get; init; }
    public int CurrentPage { get; init; }
    public int PageSize { get; init; }
    public bool HasPreviousPage => CurrentPage > 1;
    public bool HasNextPage => CurrentPage < TotalPages;
}

#endregion

#region Detail e Estatisticas DTOs

/// <summary>
/// DTO detalhado de chapa para visualizacao completa
/// </summary>
public record ChapaDetailDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public string Numero { get; init; } = string.Empty;
    public string Nome { get; init; } = string.Empty;
    public string? Sigla { get; init; }
    public string? Slogan { get; init; }
    public StatusChapa Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public DateTime DataInscricao { get; init; }
    public DateTime? DataHomologacao { get; init; }
    public DateTime? DataIndeferimento { get; init; }
    public string? MotivoIndeferimento { get; init; }
    public string? LogoUrl { get; init; }
    public string? FotoUrl { get; init; }
    public string? CorPrimaria { get; init; }
    public string? CorSecundaria { get; init; }
    public int OrdemSorteio { get; init; }
    public int TotalMembros { get; init; }
    public List<MembroChapaDto> Membros { get; init; } = new();
    public List<DocumentoChapaDto> Documentos { get; init; } = new();
    public PlataformaEleitoralDto? Plataforma { get; init; }
    public DateTime CreatedAt { get; init; }
    public DateTime? UpdatedAt { get; init; }
    public Guid? CriadoPorId { get; init; }
    public string? CriadoPorNome { get; init; }
    public Guid? AnalisadoPorId { get; init; }
    public string? AnalisadoPorNome { get; init; }
    public string? ParecerAnalise { get; init; }
}

/// <summary>
/// DTO de estatisticas de chapas por eleicao
/// </summary>
public record ChapaEstatisticasDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public int TotalChapas { get; init; }
    public int ChapasRascunho { get; init; }
    public int ChapasPendentes { get; init; }
    public int ChapasAguardandoAnalise { get; init; }
    public int ChapasEmAnalise { get; init; }
    public int ChapasDeferidas { get; init; }
    public int ChapasIndeferidas { get; init; }
    public int ChapasImpugnadas { get; init; }
    public int ChapasRegistradas { get; init; }
    public int ChapasCanceladas { get; init; }
    public int TotalMembros { get; init; }
    public int MembrosConfirmados { get; init; }
    public int MembrosPendentes { get; init; }
    public int TotalDocumentos { get; init; }
    public int DocumentosAprovados { get; init; }
    public int DocumentosPendentes { get; init; }
}

#endregion
