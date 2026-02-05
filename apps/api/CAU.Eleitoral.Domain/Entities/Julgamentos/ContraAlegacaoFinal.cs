using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class ContraAlegacaoFinal : BaseEntity
{
    public Guid AlegacaoFinalId { get; set; }
    public virtual AlegacaoFinal AlegacaoFinal { get; set; } = null!;

    public string Protocolo { get; set; } = string.Empty;
    public TipoAlegacaoJulgamento Tipo { get; set; }

    public string? Parte { get; set; }
    public string? Representante { get; set; }

    public DateTime DataProtocolo { get; set; }
    public DateTime? DataPrazoFinal { get; set; }

    public string? Titulo { get; set; }
    public string? Descricao { get; set; }
    public string? Fundamentacao { get; set; }
    public string? Conclusao { get; set; }

    public bool Tempestiva { get; set; }
    public string? ObservacaoTempestividade { get; set; }

    public string? ArquivoUrl { get; set; }
}
