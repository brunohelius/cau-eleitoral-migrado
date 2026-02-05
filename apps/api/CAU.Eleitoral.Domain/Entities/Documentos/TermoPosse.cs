using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Usuarios;
using CAU.Eleitoral.Domain.Entities.Chapas;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for possession terms
/// </summary>
public class TermoPosse : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? ChapaId { get; set; }
    public virtual ChapaEleicao? Chapa { get; set; }

    public Guid UsuarioId { get; set; }
    public virtual Usuario Usuario { get; set; } = null!;

    public string Numero { get; set; } = string.Empty;
    public int Ano { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string? Conteudo { get; set; }

    public StatusTermo Status { get; set; }

    public string? Cargo { get; set; }
    public string? Funcao { get; set; }
    public string? Mandato { get; set; }

    public DateTime DataDocumento { get; set; }
    public DateTime? DataPosse { get; set; }
    public DateTime? DataAssinatura { get; set; }
    public DateTime? DataInicioMandato { get; set; }
    public DateTime? DataFimMandato { get; set; }

    public string? LocalPosse { get; set; }
    public string? IpAssinatura { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }

    public string? Observacoes { get; set; }

    public virtual ICollection<AssinaturaDigital> Assinaturas { get; set; } = new List<AssinaturaDigital>();
}
