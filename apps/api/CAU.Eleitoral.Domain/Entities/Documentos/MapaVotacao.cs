using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for voting maps
/// </summary>
public class MapaVotacao : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? RegionalId { get; set; }
    public virtual RegionalCAU? Regional { get; set; }

    public string Titulo { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public DateTime DataGeracao { get; set; }
    public bool Consolidado { get; set; }

    public int TotalZonas { get; set; }
    public int TotalSecoes { get; set; }
    public int TotalUrnas { get; set; }

    public int TotalEleitoresAptos { get; set; }
    public int TotalVotantes { get; set; }
    public int TotalAbstencoes { get; set; }

    public int TotalVotosValidos { get; set; }
    public int TotalVotosBrancos { get; set; }
    public int TotalVotosNulos { get; set; }

    public double PercentualComparecimento { get; set; }
    public double PercentualAbstencao { get; set; }

    public string? DadosJson { get; set; }
    public string? DadosGeoJson { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }

    public string? Observacoes { get; set; }
}
