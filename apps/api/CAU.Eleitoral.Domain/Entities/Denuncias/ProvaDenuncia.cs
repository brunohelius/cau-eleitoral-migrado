using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class ProvaDenuncia : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Denuncia Denuncia { get; set; } = null!;

    public TipoProva Tipo { get; set; }
    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public string ArquivoUrl { get; set; } = string.Empty;
    public string? ArquivoNome { get; set; }
    public string? ArquivoTipo { get; set; }
    public long? ArquivoTamanho { get; set; }

    public DateTime DataEnvio { get; set; }
    public int Ordem { get; set; }

    public bool Aceita { get; set; }
    public string? MotivoRejeicao { get; set; }
}
