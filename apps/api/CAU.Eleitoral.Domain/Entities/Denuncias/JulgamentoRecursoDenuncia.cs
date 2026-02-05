using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Julgamentos;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class JulgamentoRecursoDenuncia : BaseEntity
{
    public Guid RecursoId { get; set; }
    public virtual RecursoDenuncia Recurso { get; set; } = null!;

    public Guid? SessaoId { get; set; }
    public virtual SessaoJulgamento? Sessao { get; set; }

    public StatusJulgamento Status { get; set; }
    public TipoDecisao? TipoDecisao { get; set; }

    public bool? Provido { get; set; }
    public bool? Desprovido { get; set; }
    public bool? ParcialmenteProvido { get; set; }

    public string? Ementa { get; set; }
    public string? Fundamentacao { get; set; }
    public string? Dispositivo { get; set; }

    public DateTime? DataJulgamento { get; set; }
    public DateTime? DataPublicacao { get; set; }

    public string? AcordaoUrl { get; set; }
}
