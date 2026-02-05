using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

public class Publicacao : BaseEntity
{
    public Guid DocumentoId { get; set; }
    public virtual Documento Documento { get; set; } = null!;

    public TipoPublicacao Tipo { get; set; }
    public StatusPublicacao Status { get; set; }

    public string Titulo { get; set; } = string.Empty;
    public string? Conteudo { get; set; }

    public DateTime? DataAgendada { get; set; }
    public DateTime? DataPublicacao { get; set; }

    public string? Veiculo { get; set; }
    public string? Edicao { get; set; }
    public string? Pagina { get; set; }
    public string? LinkPublicacao { get; set; }

    public string? ArquivoUrl { get; set; }
}
