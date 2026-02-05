using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class RecursoJulgamentoFinal : BaseEntity
{
    public Guid JulgamentoFinalId { get; set; }
    public virtual JulgamentoFinal JulgamentoFinal { get; set; } = null!;

    public string Protocolo { get; set; } = string.Empty;
    public TipoRecurso Tipo { get; set; }
    public StatusRecurso Status { get; set; }

    public string? Recorrente { get; set; }
    public string? Recorrido { get; set; }

    public DateTime DataProtocolo { get; set; }
    public DateTime? DataAdmissibilidade { get; set; }
    public DateTime? DataJulgamento { get; set; }

    public string? Fundamentacao { get; set; }
    public string? Pedido { get; set; }
    public string? DecisaoAdmissibilidade { get; set; }
    public string? FundamentacaoAdmissibilidade { get; set; }

    public Guid? RelatorId { get; set; }
    public virtual MembroComissaoJulgadora? Relator { get; set; }

    public string? ArquivoRecursoUrl { get; set; }
    public string? ArquivoDecisaoUrl { get; set; }

    public bool TransformadoSegundaInstancia { get; set; }
    public Guid? RecursoSegundaInstanciaId { get; set; }
    public virtual RecursoSegundaInstancia? RecursoSegundaInstancia { get; set; }
}
