using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Core;

/// <summary>
/// Histórico de mudanças de status da eleição
/// </summary>
public class EleicaoSituacao : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public StatusEleicao StatusAnterior { get; set; }
    public StatusEleicao StatusNovo { get; set; }

    public FaseEleicao? FaseAnterior { get; set; }
    public FaseEleicao? FaseNova { get; set; }

    public DateTime DataAlteracao { get; set; }
    public string? Motivo { get; set; }
    public string? Observacao { get; set; }

    public string? AlteradoPor { get; set; }
    public string? Ip { get; set; }
    public string? UserAgent { get; set; }

    public bool Automatico { get; set; }
    public string? ReferenciaAutomatico { get; set; }
}
