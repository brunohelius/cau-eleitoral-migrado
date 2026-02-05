using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class PublicacaoJulgamento : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public TipoPublicacao Tipo { get; set; }
    public StatusPublicacao Status { get; set; }

    public string Titulo { get; set; } = string.Empty;
    public string? Conteudo { get; set; }

    public DateTime? DataAgendada { get; set; }
    public DateTime? DataPublicacao { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? LinkPublicacao { get; set; }
}
