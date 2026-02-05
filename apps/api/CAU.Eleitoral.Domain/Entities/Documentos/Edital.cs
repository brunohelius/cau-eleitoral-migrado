using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

public class Edital : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public string Numero { get; set; } = string.Empty;
    public int Ano { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string? Ementa { get; set; }
    public string? Conteudo { get; set; }

    public StatusDocumento Status { get; set; }

    public DateTime DataDocumento { get; set; }
    public DateTime? DataPublicacao { get; set; }

    public string? ArquivoUrl { get; set; }
}
