namespace CAU.Eleitoral.Application.DTOs;

public record ChapaDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public int Numero { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Sigla { get; init; }
    public string? Lema { get; init; }
    public int Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public int TotalMembros { get; init; }
    public List<MembroChapaDto> Membros { get; init; } = new();
    public DateTime? DataRegistro { get; init; }
    public DateTime CriadoEm { get; init; }
}

public record CreateChapaDto
{
    public Guid EleicaoId { get; init; }
    public int Numero { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Sigla { get; init; }
    public string? Lema { get; init; }
}

public record UpdateChapaDto
{
    public int Numero { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Sigla { get; init; }
    public string? Lema { get; init; }
}

public record MembroChapaDto
{
    public Guid Id { get; init; }
    public Guid ChapaId { get; init; }
    public Guid ProfissionalId { get; init; }
    public string NomeProfissional { get; init; } = string.Empty;
    public int TipoMembro { get; init; }
    public string TipoMembroNome { get; init; } = string.Empty;
    public string? Cargo { get; init; }
    public int Status { get; init; }
    public string StatusNome { get; init; } = string.Empty;
    public int Ordem { get; init; }
}

public record CreateMembroChapaDto
{
    public Guid ProfissionalId { get; init; }
    public int TipoMembro { get; init; }
    public string? Cargo { get; init; }
}
