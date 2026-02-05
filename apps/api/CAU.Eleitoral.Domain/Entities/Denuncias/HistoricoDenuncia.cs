using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class HistoricoDenuncia : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Denuncia Denuncia { get; set; } = null!;

    public StatusDenuncia StatusAnterior { get; set; }
    public StatusDenuncia StatusNovo { get; set; }

    public string? Descricao { get; set; }
    public string? Observacao { get; set; }
    public DateTime DataAlteracao { get; set; }
    public string? AlteradoPor { get; set; }
}
