using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class VotoRevisor : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public Guid RevisorId { get; set; }
    public virtual MembroComissaoJulgadora Revisor { get; set; } = null!;

    public Guid? SessaoId { get; set; }
    public virtual SessaoJulgamento? Sessao { get; set; }

    public TipoVotoJulgamento Voto { get; set; }
    public DateTime DataVoto { get; set; }

    public bool AcompanhaRelator { get; set; }
    public string? DivergenteEm { get; set; }

    public string? Fundamentacao { get; set; }
    public string? Ressalva { get; set; }
    public string? Complementacao { get; set; }

    public bool VotoVencedor { get; set; }

    public string? ArquivoVotoUrl { get; set; }
    public bool Assinado { get; set; }
    public DateTime? DataAssinatura { get; set; }

    public DateTime? DataLeitura { get; set; }
    public bool LidoEmSessao { get; set; }
}
