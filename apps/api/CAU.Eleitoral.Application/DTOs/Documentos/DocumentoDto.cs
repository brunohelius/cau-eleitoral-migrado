using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.DTOs.Documentos;

public record DocumentoDto
{
    public Guid Id { get; init; }
    public Guid? EleicaoId { get; init; }
    public string? EleicaoNome { get; init; }
    public TipoDocumento Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public CategoriaDocumento Categoria { get; init; }
    public string CategoriaNome { get; init; } = string.Empty;
    public StatusDocumento Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public string Numero { get; init; } = string.Empty;
    public int? Ano { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string? Ementa { get; init; }
    public string? Conteudo { get; init; }
    public DateTime? DataDocumento { get; init; }
    public DateTime? DataPublicacao { get; init; }
    public DateTime? DataVigencia { get; init; }
    public DateTime? DataRevogacao { get; init; }
    public string? ArquivoUrl { get; init; }
    public string? ArquivoNome { get; init; }
    public string? ArquivoTipo { get; init; }
    public long? ArquivoTamanho { get; init; }
    public int TotalArquivos { get; init; }
    public int Versao { get; init; }
    public DateTime CreatedAt { get; init; }
}

public record CreateDocumentoDto
{
    public Guid? EleicaoId { get; init; }
    public TipoDocumento Tipo { get; init; }
    public CategoriaDocumento Categoria { get; init; }
    public string Numero { get; init; } = string.Empty;
    public int? Ano { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string? Ementa { get; init; }
    public string? Conteudo { get; init; }
    public DateTime? DataDocumento { get; init; }
}

public record UpdateDocumentoDto
{
    public string? Numero { get; init; }
    public string? Titulo { get; init; }
    public string? Ementa { get; init; }
    public string? Conteudo { get; init; }
    public DateTime? DataDocumento { get; init; }
    public DateTime? DataVigencia { get; init; }
}

public record ArquivoDocumentoDto
{
    public Guid Id { get; init; }
    public Guid DocumentoId { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public string Url { get; init; } = string.Empty;
    public string Tipo { get; init; } = string.Empty;
    public long Tamanho { get; init; }
    public int Versao { get; init; }
    public bool Principal { get; init; }
    public DateTime DataUpload { get; init; }
}

public record UploadArquivoDto
{
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public string Tipo { get; init; } = string.Empty;
    public long Tamanho { get; init; }
    public string ConteudoBase64 { get; init; } = string.Empty;
    public bool Principal { get; init; }
}

public record PublicacaoDto
{
    public Guid Id { get; init; }
    public Guid DocumentoId { get; init; }
    public TipoPublicacao Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public StatusPublicacao Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public DateTime? DataAgendamento { get; init; }
    public DateTime? DataPublicacao { get; init; }
    public string? Url { get; init; }
    public string? Observacao { get; init; }
}

public record CreatePublicacaoDto
{
    public TipoPublicacao Tipo { get; init; }
    public DateTime? DataAgendamento { get; init; }
    public string? Observacao { get; init; }
}
