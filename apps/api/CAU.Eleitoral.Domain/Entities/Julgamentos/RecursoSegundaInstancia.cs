using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class RecursoSegundaInstancia : BaseEntity
{
    public Guid? RecursoJulgamentoFinalOrigemId { get; set; }
    public virtual RecursoJulgamentoFinal? RecursoJulgamentoFinalOrigem { get; set; }

    public string Protocolo { get; set; } = string.Empty;
    public TipoRecurso Tipo { get; set; }
    public StatusRecurso Status { get; set; }

    public string? Recorrente { get; set; }
    public string? Recorrido { get; set; }

    public DateTime DataProtocolo { get; set; }
    public DateTime? DataDistribuicao { get; set; }
    public DateTime? DataAdmissibilidade { get; set; }
    public DateTime? DataJulgamento { get; set; }

    public string? NumeroProcessoOrigem { get; set; }
    public string? OrgaoOrigem { get; set; }

    public string? Fundamentacao { get; set; }
    public string? Pedido { get; set; }
    public string? DecisaoAdmissibilidade { get; set; }

    public Guid? ComissaoJulgadoraId { get; set; }
    public virtual ComissaoJulgadora? ComissaoJulgadora { get; set; }

    public Guid? RelatorId { get; set; }
    public virtual MembroComissaoJulgadora? Relator { get; set; }

    public Guid? RevisorId { get; set; }
    public virtual MembroComissaoJulgadora? Revisor { get; set; }

    public string? ArquivoRecursoUrl { get; set; }

    public virtual ICollection<JulgamentoRecursoSegundaInstancia> Julgamentos { get; set; } = new List<JulgamentoRecursoSegundaInstancia>();
}
