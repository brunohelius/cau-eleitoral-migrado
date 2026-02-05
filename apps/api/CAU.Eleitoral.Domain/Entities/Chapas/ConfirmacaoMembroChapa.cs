using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Chapas;

public class ConfirmacaoMembroChapa : BaseEntity
{
    public Guid MembroId { get; set; }
    public virtual MembroChapa Membro { get; set; } = null!;

    public string Token { get; set; } = string.Empty;
    public DateTime TokenExpiracao { get; set; }

    public bool Confirmado { get; set; }
    public DateTime? DataConfirmacao { get; set; }

    public string? IpConfirmacao { get; set; }
    public string? UserAgentConfirmacao { get; set; }

    public bool Recusado { get; set; }
    public DateTime? DataRecusa { get; set; }
    public string? MotivoRecusa { get; set; }

    public int TentativasEnvio { get; set; }
    public DateTime? UltimoEnvio { get; set; }
}
