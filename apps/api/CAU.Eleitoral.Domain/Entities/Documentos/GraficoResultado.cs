using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for result charts
/// </summary>
public class GraficoResultado : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? ResultadoId { get; set; }
    public virtual ResultadoEleicao? Resultado { get; set; }

    public Guid? RegionalId { get; set; }
    public virtual RegionalCAU? Regional { get; set; }

    public string Titulo { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public TipoGrafico Tipo { get; set; }

    public DateTime DataGeracao { get; set; }

    public string? ConfiguracaoJson { get; set; }
    public string? DadosJson { get; set; }
    public string? LabelsJson { get; set; }
    public string? CoresJson { get; set; }

    public int? Largura { get; set; }
    public int? Altura { get; set; }

    public string? ImagemUrl { get; set; }
    public string? ImagemBase64 { get; set; }

    public bool Publicado { get; set; }

    public string? Observacoes { get; set; }
}
