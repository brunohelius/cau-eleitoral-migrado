using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

public class ArquivoDocumento : BaseEntity
{
    public Guid DocumentoId { get; set; }
    public virtual Documento Documento { get; set; } = null!;

    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public string ArquivoUrl { get; set; } = string.Empty;
    public string? ArquivoNome { get; set; }
    public string? ArquivoTipo { get; set; }
    public long? ArquivoTamanho { get; set; }

    public int Ordem { get; set; }
    public DateTime DataUpload { get; set; }
}
