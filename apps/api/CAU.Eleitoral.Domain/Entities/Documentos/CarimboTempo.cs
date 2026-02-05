using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for timestamp tokens
/// </summary>
public class CarimboTempo : BaseEntity
{
    public StatusCarimboTempo Status { get; set; }

    public string NumeroSerie { get; set; } = string.Empty;
    public string? HashOriginal { get; set; }
    public string? HashComCarimbo { get; set; }

    public DateTime DataCarimbo { get; set; }
    public DateTime? DataValidadeCarimbo { get; set; }

    public string? AutoridadeCarimbo { get; set; }
    public string? PolicyId { get; set; }
    public string? Nonce { get; set; }

    public string? CarimboBase64 { get; set; }
    public string? TokenTSA { get; set; }

    public bool Verificado { get; set; }
    public DateTime? DataVerificacao { get; set; }
    public string? ResultadoVerificacao { get; set; }

    public string? Observacao { get; set; }

    public virtual ICollection<AssinaturaDigital> Assinaturas { get; set; } = new List<AssinaturaDigital>();
}
