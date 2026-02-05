using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class ContrarrazoesRecursoDenuncia : BaseEntity
{
    public Guid RecursoId { get; set; }
    public virtual RecursoDenuncia Recurso { get; set; } = null!;

    public Guid? ProfissionalId { get; set; }
    public virtual Profissional? Profissional { get; set; }

    public string Conteudo { get; set; } = string.Empty;
    public string? Fundamentacao { get; set; }

    public DateTime DataApresentacao { get; set; }
    public DateTime PrazoLimite { get; set; }
    public bool Tempestiva { get; set; }

    public string? ArquivoUrl { get; set; }
}
