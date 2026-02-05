using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Julgamentos;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class JulgamentoDenuncia : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Denuncia Denuncia { get; set; } = null!;

    public Guid? SessaoId { get; set; }
    public virtual SessaoJulgamento? Sessao { get; set; }

    public StatusJulgamento Status { get; set; }
    public TipoDecisao? TipoDecisao { get; set; }

    public bool? Procedente { get; set; }
    public bool? Improcedente { get; set; }
    public bool? ParcialmenteProcedente { get; set; }

    public string? Ementa { get; set; }
    public string? Fundamentacao { get; set; }
    public string? Dispositivo { get; set; }
    public string? Penalidade { get; set; }

    public DateTime? DataJulgamento { get; set; }
    public DateTime? DataPublicacao { get; set; }

    public string? AcordaoUrl { get; set; }

    public virtual ICollection<VotacaoJulgamentoDenuncia> Votacoes { get; set; } = new List<VotacaoJulgamentoDenuncia>();
}
