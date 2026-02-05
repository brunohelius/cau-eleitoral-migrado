using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class PareceristaProcurador : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public Guid? ProcuradorId { get; set; }
    public virtual Conselheiro? Procurador { get; set; }

    public string NomeProcurador { get; set; } = string.Empty;
    public string? RegistroProfissional { get; set; }
    public string? OrgaoOrigem { get; set; }

    public TipoParecer Tipo { get; set; }
    public StatusParecer Status { get; set; }

    public DateTime DataDistribuicao { get; set; }
    public DateTime? DataPrazo { get; set; }
    public DateTime? DataConclusao { get; set; }

    public string? Ementa { get; set; }
    public string? ResumoFatos { get; set; }
    public string? AnaliseLegal { get; set; }
    public string? Fundamentacao { get; set; }
    public string? Conclusao { get; set; }
    public string? Recomendacao { get; set; }

    public string? ArquivoParecerUrl { get; set; }
    public bool Assinado { get; set; }
    public DateTime? DataAssinatura { get; set; }

    public bool Homologado { get; set; }
    public DateTime? DataHomologacao { get; set; }
    public string? HomologadoPor { get; set; }
}
