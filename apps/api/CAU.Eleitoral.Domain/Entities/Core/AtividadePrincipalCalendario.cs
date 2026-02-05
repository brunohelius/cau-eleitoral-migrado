using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Core;

public class AtividadePrincipalCalendario : BaseEntity
{
    public Guid CalendarioId { get; set; }
    public virtual Calendario Calendario { get; set; } = null!;

    public string Titulo { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public DateTime DataInicio { get; set; }
    public DateTime DataFim { get; set; }

    public int Ordem { get; set; }
    public bool Concluida { get; set; }
    public DateTime? DataConclusao { get; set; }

    public virtual ICollection<AtividadeSecundariaCalendario> AtividadesSecundarias { get; set; } = new List<AtividadeSecundariaCalendario>();
}
