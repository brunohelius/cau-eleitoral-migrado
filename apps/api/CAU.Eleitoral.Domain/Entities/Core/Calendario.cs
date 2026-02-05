using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Core;

public class Calendario : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public TipoCalendario Tipo { get; set; }
    public StatusCalendario Status { get; set; }
    public FaseEleicao Fase { get; set; }

    public DateTime DataInicio { get; set; }
    public DateTime DataFim { get; set; }
    public TimeSpan? HoraInicio { get; set; }
    public TimeSpan? HoraFim { get; set; }

    public int Ordem { get; set; }
    public bool Obrigatorio { get; set; }
    public bool NotificarInicio { get; set; }
    public bool NotificarFim { get; set; }

    public virtual ICollection<AtividadePrincipalCalendario> AtividadesPrincipais { get; set; } = new List<AtividadePrincipalCalendario>();
    public virtual ICollection<AtividadeSecundariaCalendario> AtividadesSecundarias { get; set; } = new List<AtividadeSecundariaCalendario>();
    public virtual ICollection<CalendarioSituacao> HistoricoSituacoes { get; set; } = new List<CalendarioSituacao>();
}
