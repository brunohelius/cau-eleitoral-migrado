using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Core;

public class AtividadeSecundariaCalendario : BaseEntity
{
    public Guid CalendarioId { get; set; }
    public virtual Calendario Calendario { get; set; } = null!;

    public Guid? AtividadePrincipalId { get; set; }
    public virtual AtividadePrincipalCalendario? AtividadePrincipal { get; set; }

    public string Titulo { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public DateTime DataInicio { get; set; }
    public DateTime DataFim { get; set; }

    public int Ordem { get; set; }
    public bool Concluida { get; set; }
    public DateTime? DataConclusao { get; set; }
}
