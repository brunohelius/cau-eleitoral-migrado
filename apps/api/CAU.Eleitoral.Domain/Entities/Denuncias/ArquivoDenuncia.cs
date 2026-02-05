using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class ArquivoDenuncia : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Denuncia Denuncia { get; set; } = null!;

    public TipoArquivoDenuncia Tipo { get; set; }
    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public string ArquivoUrl { get; set; } = string.Empty;
    public string? ArquivoNome { get; set; }
    public string? ArquivoTipo { get; set; }
    public long? ArquivoTamanho { get; set; }

    public DateTime DataEnvio { get; set; }
    public int Ordem { get; set; }

    public bool Publico { get; set; }
    public string? Hash { get; set; }
}
