using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for certificates
/// </summary>
public class Certificado : BaseEntity
{
    public Guid? EleicaoId { get; set; }
    public virtual Eleicao? Eleicao { get; set; }

    public Guid UsuarioId { get; set; }
    public virtual Usuario Usuario { get; set; } = null!;

    public string Numero { get; set; } = string.Empty;
    public int Ano { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public string? Conteudo { get; set; }

    public StatusCertificado Status { get; set; }

    public string? TipoCertificado { get; set; }
    public string? Finalidade { get; set; }

    public DateTime DataDocumento { get; set; }
    public DateTime? DataExpedicao { get; set; }
    public DateTime? DataValidade { get; set; }
    public DateTime? DataEntrega { get; set; }

    public string? LocalExpedicao { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }

    public string? CodigoVerificacao { get; set; }

    public string? Observacoes { get; set; }

    public virtual ICollection<AssinaturaDigital> Assinaturas { get; set; } = new List<AssinaturaDigital>();
}
