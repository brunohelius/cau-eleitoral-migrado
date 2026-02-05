using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class AnexoDenuncia : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Denuncia Denuncia { get; set; } = null!;

    public TipoAnexoDenuncia Tipo { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public string? Referencia { get; set; }

    public string ArquivoUrl { get; set; } = string.Empty;
    public string? ArquivoNome { get; set; }
    public string? ArquivoTipo { get; set; }
    public long? ArquivoTamanho { get; set; }

    public DateTime DataAnexacao { get; set; }
    public int Ordem { get; set; }

    public bool Publico { get; set; }
    public bool Confidencial { get; set; }
    public string? Hash { get; set; }

    public string? Origem { get; set; }
    public string? Observacao { get; set; }
}
