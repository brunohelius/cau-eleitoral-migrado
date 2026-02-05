using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class DiligenciaJulgamento : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public string Protocolo { get; set; } = string.Empty;
    public TipoDiligencia Tipo { get; set; }
    public StatusDiligencia Status { get; set; }

    public Guid? DeterminadaPorId { get; set; }
    public virtual MembroComissaoJulgadora? DeterminadaPor { get; set; }

    public DateTime DataDeterminacao { get; set; }
    public DateTime DataPrazo { get; set; }
    public int PrazoDias { get; set; }
    public DateTime? DataCumprimento { get; set; }

    public string? Destinatario { get; set; }
    public string? Objeto { get; set; }
    public string? Descricao { get; set; }
    public string? FundamentoLegal { get; set; }
    public string? Instrucoes { get; set; }

    public string? ResultadoCumprimento { get; set; }
    public string? MotivoNaoCumprimento { get; set; }

    public bool Prorrogada { get; set; }
    public int? DiasProrrogacao { get; set; }
    public DateTime? NovoPrazo { get; set; }
    public string? MotivoProrrogacao { get; set; }

    public string? ArquivoDeterminacaoUrl { get; set; }
    public string? ArquivoRespostaUrl { get; set; }
}
