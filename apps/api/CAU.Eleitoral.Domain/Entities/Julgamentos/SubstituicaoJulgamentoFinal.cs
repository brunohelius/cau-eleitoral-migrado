using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class SubstituicaoJulgamentoFinal : BaseEntity
{
    public Guid JulgamentoFinalId { get; set; }
    public virtual JulgamentoFinal JulgamentoFinal { get; set; } = null!;

    public string Protocolo { get; set; } = string.Empty;
    public TipoSubstituicao Tipo { get; set; }
    public StatusSubstituicao Status { get; set; }

    public DateTime DataSolicitacao { get; set; }
    public DateTime? DataAnalise { get; set; }
    public DateTime? DataEfetivacao { get; set; }

    public string? Justificativa { get; set; }
    public string? DescricaoAlteracao { get; set; }
    public string? TextoOriginal { get; set; }
    public string? TextoSubstituto { get; set; }

    public Guid? SolicitanteId { get; set; }
    public virtual MembroComissaoJulgadora? Solicitante { get; set; }

    public Guid? AprovadorId { get; set; }
    public virtual MembroComissaoJulgadora? Aprovador { get; set; }

    public string? ParecerAnalise { get; set; }
    public string? MotivoRejeicao { get; set; }

    public string? ArquivoSolicitacaoUrl { get; set; }
    public string? ArquivoDecisaoUrl { get; set; }
}
