using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for voting reports
/// </summary>
public class RelatorioVotacao : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? RegionalId { get; set; }
    public virtual RegionalCAU? Regional { get; set; }

    public string Titulo { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public TipoRelatorio Tipo { get; set; }
    public StatusRelatorio Status { get; set; }

    public DateTime DataGeracao { get; set; }
    public DateTime? PeriodoInicio { get; set; }
    public DateTime? PeriodoFim { get; set; }

    public Guid? GeradoPorId { get; set; }
    public virtual Usuario? GeradoPor { get; set; }

    public int TotalEleitoresAptos { get; set; }
    public int TotalVotantes { get; set; }
    public int TotalAbstencoes { get; set; }

    public int TotalVotosValidos { get; set; }
    public int TotalVotosBrancos { get; set; }
    public int TotalVotosNulos { get; set; }

    public double PercentualComparecimento { get; set; }
    public double PercentualAbstencao { get; set; }

    public string? DadosJson { get; set; }
    public string? Filtros { get; set; }

    public FormatoExportacao? Formato { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }
    public long? ArquivoTamanho { get; set; }

    public string? Observacoes { get; set; }
}
