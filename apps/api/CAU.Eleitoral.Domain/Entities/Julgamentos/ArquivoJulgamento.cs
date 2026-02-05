using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class ArquivoJulgamento : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public TipoArquivoJulgamento Tipo { get; set; }
    public int Ordem { get; set; }

    public string NomeOriginal { get; set; } = string.Empty;
    public string NomeArmazenado { get; set; } = string.Empty;
    public string? Extensao { get; set; }
    public string? MimeType { get; set; }
    public long Tamanho { get; set; }

    public string ArquivoUrl { get; set; } = string.Empty;
    public string? ArquivoCaminhoFisico { get; set; }

    public string? HashMD5 { get; set; }
    public string? HashSHA256 { get; set; }

    public DateTime DataUpload { get; set; }
    public string? UploadPor { get; set; }

    public string? Descricao { get; set; }
    public string? Observacao { get; set; }

    public bool Publico { get; set; }
    public bool Assinado { get; set; }
    public DateTime? DataAssinatura { get; set; }
    public string? AssinadoPor { get; set; }
}
