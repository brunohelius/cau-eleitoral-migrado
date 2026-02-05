using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class RelatorioJulgamento : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public string Numero { get; set; } = string.Empty;
    public TipoRelatorioJulgamento Tipo { get; set; }

    public Guid? RelatorId { get; set; }
    public virtual MembroComissaoJulgadora? Relator { get; set; }

    public DateTime DataElaboracao { get; set; }
    public DateTime? DataApresentacao { get; set; }
    public DateTime? DataAprovacao { get; set; }

    public string? Ementa { get; set; }
    public string? HistoricoProcessual { get; set; }
    public string? ResumoFatos { get; set; }
    public string? AnalisePreliminar { get; set; }
    public string? AnaliseMerito { get; set; }
    public string? Fundamentacao { get; set; }
    public string? Conclusao { get; set; }
    public string? Proposta { get; set; }

    public bool Aprovado { get; set; }
    public string? ObservacaoAprovacao { get; set; }

    public string? ArquivoUrl { get; set; }
    public bool Assinado { get; set; }
    public DateTime? DataAssinatura { get; set; }
}
