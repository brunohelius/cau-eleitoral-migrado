using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for terms
/// </summary>
public class Termo : BaseEntity
{
    public Guid? EleicaoId { get; set; }
    public virtual Eleicao? Eleicao { get; set; }

    public Guid? UsuarioId { get; set; }
    public virtual Usuario? Usuario { get; set; }

    public string? Numero { get; set; }
    public int? Ano { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string Conteudo { get; set; } = string.Empty;

    public TipoTermo Tipo { get; set; }
    public StatusTermo Status { get; set; }

    public DateTime DataDocumento { get; set; }
    public DateTime? DataAssinatura { get; set; }
    public DateTime? DataValidade { get; set; }

    public string? LocalAssinatura { get; set; }
    public string? IpAssinatura { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }

    public string? Observacoes { get; set; }

    public virtual ICollection<AssinaturaDigital> Assinaturas { get; set; } = new List<AssinaturaDigital>();
}
