using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class CertidaoJulgamento : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public string Numero { get; set; } = string.Empty;
    public int Ano { get; set; }
    public TipoCertidao Tipo { get; set; }

    public string? Requerente { get; set; }
    public string? Finalidade { get; set; }

    public DateTime DataEmissao { get; set; }
    public DateTime? DataValidade { get; set; }

    public string? Conteudo { get; set; }
    public string? Observacao { get; set; }

    public string? EmitidoPor { get; set; }
    public string? CargoEmissor { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? CodigoVerificacao { get; set; }
    public string? QRCodeUrl { get; set; }

    public bool Assinada { get; set; }
    public DateTime? DataAssinatura { get; set; }
    public string? AssinadoPor { get; set; }
    public string? CertificadoDigitalId { get; set; }
}
