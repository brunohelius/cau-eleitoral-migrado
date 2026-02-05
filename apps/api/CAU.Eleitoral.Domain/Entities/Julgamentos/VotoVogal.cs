using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class VotoVogal : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public Guid VogalId { get; set; }
    public virtual MembroComissaoJulgadora Vogal { get; set; } = null!;

    public Guid? SessaoId { get; set; }
    public virtual SessaoJulgamento? Sessao { get; set; }

    public TipoVotoJulgamento Voto { get; set; }
    public DateTime DataVoto { get; set; }
    public int Ordem { get; set; }

    public bool AcompanhaRelator { get; set; }
    public bool AcompanhaRevisor { get; set; }
    public string? VotoAcompanha { get; set; }

    public string? Fundamentacao { get; set; }
    public string? Ressalva { get; set; }
    public string? DeclaracaoVoto { get; set; }

    public bool VotoVencedor { get; set; }
    public bool Impedido { get; set; }
    public string? MotivoImpedimento { get; set; }

    public string? ArquivoVotoUrl { get; set; }
    public bool Assinado { get; set; }
    public DateTime? DataAssinatura { get; set; }
}
