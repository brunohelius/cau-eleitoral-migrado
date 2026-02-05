using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class AdmissibilidadeDenuncia : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Denuncia Denuncia { get; set; } = null!;

    public Guid? AnalistId { get; set; }
    public virtual Usuario? Analista { get; set; }

    public bool Admitida { get; set; }
    public string? Fundamentacao { get; set; }
    public string? Observacao { get; set; }

    public DateTime DataAnalise { get; set; }
    public DateTime? DataPublicacao { get; set; }

    public string? DocumentoUrl { get; set; }
}
