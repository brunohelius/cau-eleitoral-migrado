using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for notices
/// </summary>
public class Aviso : BaseEntity
{
    public Guid? EleicaoId { get; set; }
    public virtual Eleicao? Eleicao { get; set; }

    public string? Numero { get; set; }
    public int? Ano { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string? Assunto { get; set; }
    public string Conteudo { get; set; } = string.Empty;

    public StatusDocumento Status { get; set; }

    public DateTime DataDocumento { get; set; }
    public DateTime? DataPublicacao { get; set; }
    public DateTime? DataExpiracao { get; set; }

    public bool Destaque { get; set; }
    public bool Urgente { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }

    public string? Observacoes { get; set; }

    public virtual ICollection<PublicacaoOficial> Publicacoes { get; set; } = new List<PublicacaoOficial>();
}
