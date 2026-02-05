using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Usuarios;

public class Conselheiro : BaseEntity
{
    public Guid ProfissionalId { get; set; }
    public virtual Profissional Profissional { get; set; } = null!;

    public string? NumeroConselheiro { get; set; }
    public string? Cargo { get; set; }
    public string? Comissao { get; set; }

    public DateTime? InicioMandato { get; set; }
    public DateTime? FimMandato { get; set; }

    public bool MandatoAtivo { get; set; }
    public string? MotivoFinalizacao { get; set; }
    public DateTime? DataFinalizacao { get; set; }

    public virtual ICollection<HistoricoExtratoConselheiro> Historicos { get; set; } = new List<HistoricoExtratoConselheiro>();
}
