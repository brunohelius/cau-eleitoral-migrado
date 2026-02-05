using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class AlegacaoFinal : BaseEntity
{
    public Guid JulgamentoFinalId { get; set; }
    public virtual JulgamentoFinal JulgamentoFinal { get; set; } = null!;

    public string Protocolo { get; set; } = string.Empty;
    public TipoAlegacaoJulgamento Tipo { get; set; }
    public int Ordem { get; set; }

    public string? Parte { get; set; }
    public string? Representante { get; set; }

    public DateTime DataProtocolo { get; set; }
    public DateTime? DataPrazoFinal { get; set; }

    public string? Titulo { get; set; }
    public string? Descricao { get; set; }
    public string? Fundamentacao { get; set; }
    public string? Pedido { get; set; }

    public bool Tempestiva { get; set; }
    public string? ObservacaoTempestividade { get; set; }

    public string? ArquivoUrl { get; set; }

    public virtual ICollection<ContraAlegacaoFinal> ContraAlegacoes { get; set; } = new List<ContraAlegacaoFinal>();
}
