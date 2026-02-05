using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Core;

/// <summary>
/// Histórico de mudanças de status do calendário
/// </summary>
public class CalendarioSituacao : BaseEntity
{
    public Guid CalendarioId { get; set; }
    public virtual Calendario Calendario { get; set; } = null!;

    public StatusCalendario StatusAnterior { get; set; }
    public StatusCalendario StatusNovo { get; set; }

    public DateTime DataAlteracao { get; set; }
    public string? Motivo { get; set; }
    public string? Observacao { get; set; }

    public string? AlteradoPor { get; set; }
    public string? Ip { get; set; }
    public string? UserAgent { get; set; }

    public bool Automatico { get; set; }
    public string? ReferenciaAutomatico { get; set; }
}
