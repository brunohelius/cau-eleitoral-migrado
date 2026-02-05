using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class JulgamentoRecursoSegundaInstancia : BaseEntity
{
    public Guid RecursoSegundaInstanciaId { get; set; }
    public virtual RecursoSegundaInstancia RecursoSegundaInstancia { get; set; } = null!;

    public Guid? SessaoId { get; set; }
    public virtual SessaoJulgamento? Sessao { get; set; }

    public StatusJulgamento Status { get; set; }
    public TipoDecisao? TipoDecisao { get; set; }

    public DateTime? DataJulgamento { get; set; }
    public DateTime? DataPublicacao { get; set; }

    public string? Ementa { get; set; }
    public string? Relatorio { get; set; }
    public string? Fundamentacao { get; set; }
    public string? Dispositivo { get; set; }
    public string? Resultado { get; set; }

    public int VotosFavoraveis { get; set; }
    public int VotosContrarios { get; set; }
    public int Abstencoes { get; set; }

    public string? AcordaoUrl { get; set; }
    public string? CertidaoUrl { get; set; }

    public bool Publicado { get; set; }

    public virtual ICollection<VotoPlenario> VotosPlenario { get; set; } = new List<VotoPlenario>();
}
