using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Impugnacoes;

public class AlegacaoImpugnacaoResultado : BaseEntity
{
    public Guid ImpugnacaoId { get; set; }
    public virtual ImpugnacaoResultado Impugnacao { get; set; } = null!;

    public TipoAlegacao Tipo { get; set; }
    public string Descricao { get; set; } = string.Empty;
    public string? Fundamentacao { get; set; }

    public DateTime DataApresentacao { get; set; }
    public DateTime PrazoLimite { get; set; }
    public bool Tempestiva { get; set; }

    public int Ordem { get; set; }

    public virtual ICollection<ContraAlegacaoImpugnacao> ContraAlegacoes { get; set; } = new List<ContraAlegacaoImpugnacao>();
    public virtual ICollection<ProvaImpugnacao> Provas { get; set; } = new List<ProvaImpugnacao>();
}
