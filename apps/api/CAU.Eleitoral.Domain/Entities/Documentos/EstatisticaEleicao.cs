using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for election statistics
/// </summary>
public class EstatisticaEleicao : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? RegionalId { get; set; }
    public virtual RegionalCAU? Regional { get; set; }

    public string Categoria { get; set; } = string.Empty;
    public string Indicador { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public decimal? ValorNumerico { get; set; }
    public double? ValorPercentual { get; set; }
    public string? ValorTexto { get; set; }

    public DateTime DataCalculo { get; set; }
    public DateTime? PeriodoInicio { get; set; }
    public DateTime? PeriodoFim { get; set; }

    public string? Dimensao { get; set; }
    public string? Subdimensao { get; set; }

    public decimal? ValorAnterior { get; set; }
    public double? VariacaoPercentual { get; set; }

    public string? DadosJson { get; set; }
    public string? MetadadosJson { get; set; }

    public string? Observacoes { get; set; }
}
