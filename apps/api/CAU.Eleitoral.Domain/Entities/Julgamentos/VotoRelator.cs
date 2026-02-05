using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class VotoRelator : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public Guid RelatorId { get; set; }
    public virtual MembroComissaoJulgadora Relator { get; set; } = null!;

    public Guid? SessaoId { get; set; }
    public virtual SessaoJulgamento? Sessao { get; set; }

    public TipoVotoJulgamento Voto { get; set; }
    public DateTime DataVoto { get; set; }

    public string? Ementa { get; set; }
    public string? Relatorio { get; set; }
    public string? Fundamentacao { get; set; }
    public string? Dispositivo { get; set; }
    public string? Proposta { get; set; }

    public bool VotoVencedor { get; set; }
    public bool VotoCondutor { get; set; }

    public string? ArquivoVotoUrl { get; set; }
    public bool Assinado { get; set; }
    public DateTime? DataAssinatura { get; set; }

    public DateTime? DataLeitura { get; set; }
    public bool LidoEmSessao { get; set; }
}
