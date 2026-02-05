using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for digital signatures
/// </summary>
public class AssinaturaDigital : BaseEntity
{
    public Guid? DocumentoId { get; set; }
    public virtual Documento? Documento { get; set; }

    public Guid? TermoId { get; set; }
    public virtual Termo? Termo { get; set; }

    public Guid? TermoPosseId { get; set; }
    public virtual TermoPosse? TermoPosse { get; set; }

    public Guid? DiplomaId { get; set; }
    public virtual Diploma? Diploma { get; set; }

    public Guid? CertificadoId { get; set; }
    public virtual Certificado? Certificado { get; set; }

    public Guid? DeclaracaoId { get; set; }
    public virtual Declaracao? Declaracao { get; set; }

    public Guid? AtaReuniaoId { get; set; }
    public virtual AtaReuniao? AtaReuniao { get; set; }

    public Guid? AtaApuracaoId { get; set; }
    public virtual AtaApuracao? AtaApuracao { get; set; }

    public Guid SignatarioId { get; set; }
    public virtual Usuario Signatario { get; set; } = null!;

    public TipoAssinatura Tipo { get; set; }
    public StatusAssinatura Status { get; set; }

    public string? NomeSignatario { get; set; }
    public string? CpfSignatario { get; set; }
    public string? EmailSignatario { get; set; }
    public string? CargoSignatario { get; set; }

    public DateTime DataAssinatura { get; set; }
    public DateTime? DataValidadeAssinatura { get; set; }

    public string? HashDocumento { get; set; }
    public string? AssinaturaCriptografada { get; set; }
    public string? CertificadoBase64 { get; set; }

    public Guid? CertificadoDigitalId { get; set; }
    public virtual CertificadoDigital? CertificadoDigitalUsado { get; set; }

    public Guid? CarimboTempoId { get; set; }
    public virtual CarimboTempo? CarimboTempo { get; set; }

    public string? IpAssinatura { get; set; }
    public string? UserAgentAssinatura { get; set; }
    public string? Geolocalizacao { get; set; }

    public string? MotivoRecusa { get; set; }
    public DateTime? DataRecusa { get; set; }

    public bool Validada { get; set; }
    public DateTime? DataValidacao { get; set; }
    public string? ResultadoValidacao { get; set; }

    public string? Observacao { get; set; }
}
