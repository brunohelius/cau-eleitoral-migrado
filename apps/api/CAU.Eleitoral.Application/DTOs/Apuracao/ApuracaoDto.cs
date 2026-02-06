using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.DTOs.Apuracao;

#region Main Result DTOs

/// <summary>
/// Complete vote tallying result
/// </summary>
public record ResultadoApuracaoDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public StatusApuracao StatusApuracao { get; init; }
    public bool Homologado { get; init; }
    public bool Publicado { get; init; }
    public DateTime? DataApuracao { get; init; }
    public DateTime? DataHomologacao { get; init; }
    public DateTime? DataPublicacao { get; init; }

    // Totals
    public int TotalEleitores { get; init; }
    public int TotalVotos { get; init; }
    public int VotosValidos { get; init; }
    public int VotosBrancos { get; init; }
    public int VotosNulos { get; init; }
    public int VotosAnulados { get; init; }
    public int TotalAbstencoes { get; init; }

    // Percentages
    public decimal PercentualParticipacao { get; init; }
    public decimal PercentualAbstencao { get; init; }
    public decimal PercentualVotosValidos { get; init; }
    public decimal PercentualVotosBrancos { get; init; }
    public decimal PercentualVotosNulos { get; init; }

    // Results per chapa
    public List<ResultadoChapaDto> ResultadosChapas { get; init; } = new();

    // Winner info
    public Guid? ChapaVencedoraId { get; init; }
    public string? ChapaVencedoraNome { get; init; }
    public int? VotosChapaVencedora { get; init; }

    // Hash for integrity verification
    public string? HashIntegridade { get; init; }
}

/// <summary>
/// Partial/real-time results during voting
/// </summary>
public record ResultadoParcialDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public bool PermiteVisualizacao { get; init; }
    public int VotosApurados { get; init; }
    public int TotalVotos { get; init; }
    public decimal PercentualApurado { get; init; }
    public DateTime UltimaAtualizacao { get; init; }
    public List<ResultadoChapaDto> ResultadosChapas { get; init; } = new();
}

/// <summary>
/// Final official results
/// </summary>
public record ResultadoFinalDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public TipoEleicao TipoEleicao { get; init; }

    // Status
    public bool Homologado { get; init; }
    public bool Publicado { get; init; }
    public bool Contestado { get; init; }
    public DateTime DataApuracao { get; init; }
    public DateTime? DataHomologacao { get; init; }
    public DateTime? DataPublicacao { get; init; }

    // Totals
    public int TotalEleitoresAptos { get; init; }
    public int TotalVotantes { get; init; }
    public int TotalAbstencoes { get; init; }
    public int TotalVotosValidos { get; init; }
    public int TotalVotosBrancos { get; init; }
    public int TotalVotosNulos { get; init; }
    public int TotalVotosAnulados { get; init; }

    // Percentages
    public decimal PercentualComparecimento { get; init; }
    public decimal PercentualAbstencao { get; init; }

    // Results
    public List<ResultadoChapaDto> ResultadosChapas { get; init; } = new();
    public ChapaVencedoraDto? ChapaVencedora { get; init; }
    public List<EleitoDto> Eleitos { get; init; } = new();

    // Documents
    public string? AtaApuracaoUrl { get; init; }
    public string? BoletimUrnaUrl { get; init; }

    // Integrity
    public string? HashIntegridade { get; init; }
    public string? AssinaturaDigital { get; init; }
}

/// <summary>
/// Result for a single chapa
/// </summary>
public record ResultadoChapaDto
{
    public Guid ChapaId { get; init; }
    public int Numero { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Sigla { get; init; }
    public string? LogoUrl { get; init; }
    public int TotalVotos { get; init; }
    public decimal Percentual { get; init; }
    public decimal PercentualVotosValidos { get; init; }
    public int Posicao { get; init; }
    public bool Vencedora { get; init; }
}

#endregion

#region Winner DTOs

/// <summary>
/// Winning chapa details
/// </summary>
public record ChapaVencedoraDto
{
    public Guid ChapaId { get; init; }
    public int Numero { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Sigla { get; init; }
    public string? LogoUrl { get; init; }
    public int TotalVotos { get; init; }
    public decimal PercentualVotos { get; init; }
    public decimal PercentualVotosValidos { get; init; }
    public int DiferencaParaSegundo { get; init; }
    public decimal PercentualDiferenca { get; init; }
    public bool VenceuPorMaioria { get; init; }
    public bool SegundoTurnoNecessario { get; init; }
    public List<MembroChapaVencedoraDto> Membros { get; init; } = new();
}

/// <summary>
/// Member of winning chapa
/// </summary>
public record MembroChapaVencedoraDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string Cargo { get; init; } = string.Empty;
    public bool Titular { get; init; }
    public int Ordem { get; init; }
}

#endregion

#region Status DTOs

/// <summary>
/// Vote tallying status
/// </summary>
public record StatusApuracaoDto
{
    public Guid EleicaoId { get; init; }
    public StatusApuracao Status { get; init; }
    public DateTime? DataInicio { get; init; }
    public DateTime? DataFim { get; init; }
    public DateTime? DataPausa { get; init; }
    public string? MotivoPausa { get; init; }
    public int VotosApurados { get; init; }
    public int TotalVotos { get; init; }
    public decimal PercentualApurado { get; init; }
    public Guid? ResponsavelId { get; init; }
    public string? ResponsavelNome { get; init; }
    public string? Mensagem { get; init; }
}

#endregion

#region Statistics DTOs

/// <summary>
/// Complete voting statistics
/// </summary>
public record EstatisticasVotacaoDto
{
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public DateTime DataGeracao { get; init; }

    // Participation
    public int TotalEleitoresAptos { get; init; }
    public int TotalVotantes { get; init; }
    public int TotalAbstencoes { get; init; }
    public decimal TaxaParticipacao { get; init; }
    public decimal TaxaAbstencao { get; init; }

    // Vote distribution
    public int TotalVotosValidos { get; init; }
    public int TotalVotosBrancos { get; init; }
    public int TotalVotosNulos { get; init; }
    public int TotalVotosAnulados { get; init; }
    public decimal PercentualVotosValidos { get; init; }
    public decimal PercentualVotosBrancos { get; init; }
    public decimal PercentualVotosNulos { get; init; }

    // By voting mode
    public int VotosOnline { get; init; }
    public int VotosPresenciais { get; init; }
    public decimal PercentualVotosOnline { get; init; }
    public decimal PercentualVotosPresenciais { get; init; }

    // Vote distribution per chapa
    public List<VotosPorChapaDto> VotosPorChapa { get; init; } = new();

    // Regional distribution
    public List<VotosPorRegiaoDto> VotosPorRegiao { get; init; } = new();

    // Timeline
    public List<VotosPorHoraDto> VotosPorHora { get; init; } = new();

    // Comparisons
    public EstatisticasComparativasDto? Comparativo { get; init; }
}

/// <summary>
/// Votes per chapa statistics
/// </summary>
public record VotosPorChapaDto
{
    public Guid ChapaId { get; init; }
    public int Numero { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Sigla { get; init; }
    public int TotalVotos { get; init; }
    public decimal Percentual { get; init; }
    public decimal PercentualVotosValidos { get; init; }
    public int Posicao { get; init; }
    public string? CorGrafico { get; init; }
}

/// <summary>
/// Votes per region/UF statistics
/// </summary>
public record VotosPorRegiaoDto
{
    public string UF { get; init; } = string.Empty;
    public string NomeRegiao { get; init; } = string.Empty;
    public Guid? RegionalId { get; init; }
    public int TotalEleitores { get; init; }
    public int TotalVotantes { get; init; }
    public int TotalAbstencoes { get; init; }
    public decimal TaxaParticipacao { get; init; }
    public int TotalVotosValidos { get; init; }
    public int TotalVotosBrancos { get; init; }
    public int TotalVotosNulos { get; init; }
    public List<VotosPorChapaDto> VotosPorChapa { get; init; } = new();
}

/// <summary>
/// Votes per hour timeline
/// </summary>
public record VotosPorHoraDto
{
    public DateTime Hora { get; init; }
    public int TotalVotos { get; init; }
    public int VotosAcumulados { get; init; }
    public decimal PercentualAcumulado { get; init; }
}

/// <summary>
/// Comparative statistics with previous elections
/// </summary>
public record EstatisticasComparativasDto
{
    public int EleicaoAnteriorAno { get; init; }
    public decimal VariacaoParticipacao { get; init; }
    public decimal VariacaoVotosBrancos { get; init; }
    public decimal VariacaoVotosNulos { get; init; }
}

#endregion

#region Document DTOs

/// <summary>
/// Vote tallying minutes (ata)
/// </summary>
public record AtaApuracaoDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public DateTime DataApuracao { get; init; }
    public string Numero { get; init; } = string.Empty;
    public string Conteudo { get; init; } = string.Empty;
    public string? ArquivoUrl { get; init; }
    public List<MembroComissaoDto> MembrosComissao { get; init; } = new();
    public ResultadoApuracaoDto? Resultado { get; init; }
    public bool Assinada { get; init; }
    public DateTime? DataAssinatura { get; init; }
}

/// <summary>
/// Commission member
/// </summary>
public record MembroComissaoDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string Cargo { get; init; } = string.Empty;
    public bool Assinou { get; init; }
    public DateTime? DataAssinatura { get; init; }
}

/// <summary>
/// Ballot box bulletin
/// </summary>
public record BoletimUrnaDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string EleicaoNome { get; init; } = string.Empty;
    public DateTime DataEmissao { get; init; }
    public string HashIntegridade { get; init; } = string.Empty;
    public Guid? UrnaId { get; init; }
    public string? UrnaIdentificador { get; init; }

    // Totals
    public int TotalVotos { get; init; }
    public int VotosValidos { get; init; }
    public int VotosBrancos { get; init; }
    public int VotosNulos { get; init; }

    // Results
    public List<ResultadoChapaDto> ResultadosChapas { get; init; } = new();

    // Document
    public string? ArquivoUrl { get; init; }
}

#endregion

#region Elected Officials DTOs

/// <summary>
/// Elected official
/// </summary>
public record EleitoDto
{
    public Guid Id { get; init; }
    public Guid ChapaId { get; init; }
    public string ChapaNome { get; init; } = string.Empty;
    public int ChapaNumero { get; init; }
    public Guid ProfissionalId { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? RegistroCAU { get; init; }
    public string Cargo { get; init; } = string.Empty;
    public bool Titular { get; init; }
    public int Ordem { get; init; }
    public DateTime? DataDiplomacao { get; init; }
    public bool Diplomado { get; init; }
}

#endregion

#region Request DTOs

/// <summary>
/// Request to provide a reason/motive
/// </summary>
public record MotivoRequest
{
    public string Motivo { get; init; } = string.Empty;
}

#endregion
