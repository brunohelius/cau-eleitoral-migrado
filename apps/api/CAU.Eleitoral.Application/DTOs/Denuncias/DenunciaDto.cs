using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.DTOs.Denuncias;

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
    public DateTime CreatedAt { get; init; }
}

public record CreateDenunciaDto
{
    public Guid EleicaoId { get; init; }
    public TipoDenuncia Tipo { get; init; }
    public Guid? ChapaId { get; init; }
    public Guid? MembroId { get; init; }
    public Guid? DenuncianteId { get; init; }
    public bool Anonima { get; init; }
    public string Titulo { get; init; } = string.Empty;
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
}

public record UpdateDenunciaDto
{
    public string? Titulo { get; init; }
    public string? Descricao { get; init; }
    public string? Fundamentacao { get; init; }
    public DateTime? PrazoDefesa { get; init; }
    public DateTime? PrazoRecurso { get; init; }
}

public record ProvaDenunciaDto
{
    public Guid Id { get; init; }
    public Guid DenunciaId { get; init; }
    public TipoProva Tipo { get; init; }
    public string TipoNome { get; init; } = string.Empty;
    public string Descricao { get; init; } = string.Empty;
    public string? ArquivoUrl { get; init; }
    public string? ArquivoNome { get; init; }
    public DateTime DataEnvio { get; init; }
}

public record CreateProvaDenunciaDto
{
    public TipoProva Tipo { get; init; }
    public string Descricao { get; init; } = string.Empty;
    public string? ArquivoUrl { get; init; }
    public string? ArquivoNome { get; init; }
}

public record AdmissibilidadeDto
{
    public Guid Id { get; init; }
    public Guid DenunciaId { get; init; }
    public bool Admissivel { get; init; }
    public string Parecer { get; init; } = string.Empty;
    public Guid RelatorId { get; init; }
    public string RelatorNome { get; init; } = string.Empty;
    public DateTime DataAnalise { get; init; }
}

public record JulgamentoDenunciaDto
{
    public Guid Id { get; init; }
    public Guid DenunciaId { get; init; }
    public StatusJulgamento Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public TipoDecisao? TipoDecisao { get; init; }
    public string? Decisao { get; init; }
    public string? Fundamentacao { get; init; }
    public DateTime? DataJulgamento { get; init; }
}
