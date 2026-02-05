using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Usuarios;
using CAU.Eleitoral.Domain.Entities.Chapas;

namespace CAU.Eleitoral.Domain.Entities.Core;

public class Voto : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? ChapaId { get; set; }
    public virtual ChapaEleicao? Chapa { get; set; }

    public TipoVoto Tipo { get; set; }
    public StatusVoto Status { get; set; }
    public ModoVotacao Modo { get; set; }

    public string HashEleitor { get; set; } = string.Empty;
    public string HashVoto { get; set; } = string.Empty;

    public DateTime DataVoto { get; set; }
    public string? IpAddress { get; set; }
    public string? UserAgent { get; set; }

    public Guid? SecaoId { get; set; }
    public virtual SecaoEleitoral? Secao { get; set; }

    public Guid? UrnaId { get; set; }
    public virtual UrnaEletronica? Urna { get; set; }

    public string? Comprovante { get; set; }
}
