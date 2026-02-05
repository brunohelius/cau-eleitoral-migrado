using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Julgamentos;

namespace CAU.Eleitoral.Domain.Entities.Impugnacoes;

public class JulgamentoImpugnacao : BaseEntity
{
    public Guid ImpugnacaoId { get; set; }
    public virtual ImpugnacaoResultado Impugnacao { get; set; } = null!;

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
    public string? Efeitos { get; set; }

    public DateTime? DataJulgamento { get; set; }
    public DateTime? DataPublicacao { get; set; }

    public string? AcordaoUrl { get; set; }

    public virtual ICollection<VotacaoJulgamentoImpugnacao> Votacoes { get; set; } = new List<VotacaoJulgamentoImpugnacao>();
}
