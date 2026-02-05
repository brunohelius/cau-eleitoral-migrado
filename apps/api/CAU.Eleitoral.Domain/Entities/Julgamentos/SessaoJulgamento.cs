using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class SessaoJulgamento : BaseEntity
{
    public Guid ComissaoId { get; set; }
    public virtual ComissaoJulgadora Comissao { get; set; } = null!;

    public string Numero { get; set; } = string.Empty;
    public TipoSessao Tipo { get; set; }
    public StatusSessao Status { get; set; }

    public DateTime DataSessao { get; set; }
    public TimeSpan? HoraInicio { get; set; }
    public TimeSpan? HoraFim { get; set; }

    public string? Local { get; set; }
    public string? Observacao { get; set; }

    public string? ConvocacaoUrl { get; set; }
    public DateTime? DataConvocacao { get; set; }

    public virtual ICollection<PautaSessao> Pautas { get; set; } = new List<PautaSessao>();
    public virtual AtaSessao? Ata { get; set; }
}
