using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Chapas;

namespace CAU.Eleitoral.Domain.Entities.Core;

/// <summary>
/// Detalhamento dos votos por chapa em uma apuracao de resultado
/// </summary>
public class ApuracaoResultadoChapa : BaseEntity
{
    public Guid ApuracaoId { get; set; }
    public virtual ApuracaoResultado Apuracao { get; set; } = null!;

    public Guid ChapaId { get; set; }
    public virtual ChapaEleicao Chapa { get; set; } = null!;

    public int TotalVotos { get; set; }
    public decimal PercentualVotos { get; set; }
    public decimal PercentualVotosValidos { get; set; }

    public int Posicao { get; set; }
    public bool Eleita { get; set; }

    public string? Observacao { get; set; }
}
