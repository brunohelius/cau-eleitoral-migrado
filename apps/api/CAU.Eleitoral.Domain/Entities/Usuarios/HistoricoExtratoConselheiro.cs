using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Usuarios;

public class HistoricoExtratoConselheiro : BaseEntity
{
    public Guid ConselheiroId { get; set; }
    public virtual Conselheiro Conselheiro { get; set; } = null!;

    public string Tipo { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public DateTime DataEvento { get; set; }

    public string? Observacao { get; set; }
    public string? Documento { get; set; }
}
