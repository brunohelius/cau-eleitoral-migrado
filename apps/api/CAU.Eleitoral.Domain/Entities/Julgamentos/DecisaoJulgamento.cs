using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class DecisaoJulgamento : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public TipoDecisao Tipo { get; set; }
    public string Resultado { get; set; } = string.Empty;
    public string? Fundamentacao { get; set; }
    public string? Dispositivo { get; set; }
    public string? Efeitos { get; set; }

    public DateTime DataDecisao { get; set; }
    public DateTime? DataPublicacao { get; set; }

    public string? ArquivoUrl { get; set; }
    public bool Publicada { get; set; }
}
