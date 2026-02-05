using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Chapas;

namespace CAU.Eleitoral.Domain.Entities.Impugnacoes;

public class ContraAlegacaoImpugnacao : BaseEntity
{
    public Guid AlegacaoId { get; set; }
    public virtual AlegacaoImpugnacaoResultado Alegacao { get; set; } = null!;

    public Guid? ChapaId { get; set; }
    public virtual ChapaEleicao? Chapa { get; set; }

    public string Conteudo { get; set; } = string.Empty;
    public string? Fundamentacao { get; set; }

    public DateTime DataApresentacao { get; set; }
    public DateTime PrazoLimite { get; set; }
    public bool Tempestiva { get; set; }

    public string? ArquivoUrl { get; set; }
}
