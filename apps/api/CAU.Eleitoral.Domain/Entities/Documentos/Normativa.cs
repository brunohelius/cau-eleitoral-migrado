using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for regulations
/// </summary>
public class Normativa : BaseEntity
{
    public Guid? EleicaoId { get; set; }
    public virtual Eleicao? Eleicao { get; set; }

    public string Numero { get; set; } = string.Empty;
    public int Ano { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string? Ementa { get; set; }
    public string? Conteudo { get; set; }

    public StatusDocumento Status { get; set; }

    public DateTime DataDocumento { get; set; }
    public DateTime? DataPublicacao { get; set; }
    public DateTime? DataVigencia { get; set; }
    public DateTime? DataRevogacao { get; set; }

    public Guid? NormativaRevogadaId { get; set; }
    public virtual Normativa? NormativaRevogada { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }

    public string? Observacoes { get; set; }

    public virtual ICollection<PublicacaoOficial> Publicacoes { get; set; } = new List<PublicacaoOficial>();
}
