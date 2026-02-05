using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class JulgamentoFinal : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? SessaoId { get; set; }
    public virtual SessaoJulgamento? Sessao { get; set; }

    public string Protocolo { get; set; } = string.Empty;
    public TipoJulgamento Tipo { get; set; }
    public StatusJulgamento Status { get; set; }

    public string? NumeroProcessoOrigem { get; set; }
    public string? Partes { get; set; }
    public string? Assunto { get; set; }

    public string? Ementa { get; set; }
    public string? Relatorio { get; set; }
    public string? Fundamentacao { get; set; }
    public string? Dispositivo { get; set; }

    public TipoDecisao? TipoDecisao { get; set; }
    public DateTime? DataJulgamento { get; set; }
    public DateTime? DataPublicacao { get; set; }

    public string? AcordaoUrl { get; set; }
    public string? CertidaoUrl { get; set; }

    public virtual ICollection<VotoJulgamentoFinal> Votos { get; set; } = new List<VotoJulgamentoFinal>();
}
