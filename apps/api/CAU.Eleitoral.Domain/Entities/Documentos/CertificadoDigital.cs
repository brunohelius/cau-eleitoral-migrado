using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for digital certificates
/// </summary>
public class CertificadoDigital : BaseEntity
{
    public Guid? UsuarioId { get; set; }
    public virtual Usuario? Usuario { get; set; }

    public TipoCertificadoDigital Tipo { get; set; }
    public StatusCertificadoDigital Status { get; set; }

    public string NumeroSerie { get; set; } = string.Empty;
    public string? SubjectName { get; set; }
    public string? IssuerName { get; set; }
    public string? Thumbprint { get; set; }

    public string? NomeTitular { get; set; }
    public string? CpfCnpjTitular { get; set; }
    public string? EmailTitular { get; set; }

    public DateTime DataEmissao { get; set; }
    public DateTime DataValidade { get; set; }

    public string? AutoridadeCertificadora { get; set; }
    public string? CadeiaConfianca { get; set; }
    public string? UrlCRL { get; set; }
    public string? UrlOCSP { get; set; }

    public string? ChavePublicaBase64 { get; set; }
    public string? CertificadoBase64 { get; set; }

    public bool EhIcpBrasil { get; set; }
    public bool Revogado { get; set; }
    public DateTime? DataRevogacao { get; set; }
    public string? MotivoRevogacao { get; set; }

    public DateTime? UltimaValidacao { get; set; }
    public string? ResultadoUltimaValidacao { get; set; }

    public string? Observacao { get; set; }

    public virtual ICollection<AssinaturaDigital> Assinaturas { get; set; } = new List<AssinaturaDigital>();
}
