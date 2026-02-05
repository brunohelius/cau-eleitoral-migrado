using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Chapas;

public class HistoricoChapaEleicao : BaseEntity
{
    public Guid ChapaId { get; set; }
    public virtual ChapaEleicao Chapa { get; set; } = null!;

    public StatusChapa StatusAnterior { get; set; }
    public StatusChapa StatusNovo { get; set; }

    public string? Descricao { get; set; }
    public string? Observacao { get; set; }
    public DateTime DataAlteracao { get; set; }
    public string? AlteradoPor { get; set; }

    public string? Ip { get; set; }
    public string? UserAgent { get; set; }
}
