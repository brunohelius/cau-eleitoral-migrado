using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class ArquivoDefesa : BaseEntity
{
    public Guid DefesaId { get; set; }
    public virtual DefesaDenuncia Defesa { get; set; } = null!;

    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public string ArquivoUrl { get; set; } = string.Empty;
    public string? ArquivoNome { get; set; }
    public string? ArquivoTipo { get; set; }
    public long? ArquivoTamanho { get; set; }

    public DateTime DataEnvio { get; set; }
    public int Ordem { get; set; }
}
