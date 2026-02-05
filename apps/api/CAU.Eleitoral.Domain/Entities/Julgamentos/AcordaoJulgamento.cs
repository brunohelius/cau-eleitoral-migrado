using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class AcordaoJulgamento : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public string Numero { get; set; } = string.Empty;
    public int Ano { get; set; }

    public string? Ementa { get; set; }
    public string? Relatorio { get; set; }
    public string? Voto { get; set; }
    public string? Acordao { get; set; }

    public Guid? RelatorId { get; set; }
    public virtual MembroComissaoJulgadora? Relator { get; set; }

    public DateTime DataJulgamento { get; set; }
    public DateTime? DataPublicacao { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? AssinaturaUrl { get; set; }

    public bool Publicado { get; set; }
}
