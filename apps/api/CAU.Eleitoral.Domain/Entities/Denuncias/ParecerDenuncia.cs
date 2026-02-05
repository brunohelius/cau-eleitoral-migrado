using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class ParecerDenuncia : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Denuncia Denuncia { get; set; } = null!;

    public TipoParecerDenuncia Tipo { get; set; }
    public StatusParecerDenuncia Status { get; set; }

    public Guid? PareceristaId { get; set; }
    public virtual Usuario? Parecerista { get; set; }

    public string Numero { get; set; } = string.Empty;
    public string? Assunto { get; set; }
    public string Ementa { get; set; } = string.Empty;
    public string Conteudo { get; set; } = string.Empty;
    public string? Fundamentacao { get; set; }
    public string? Conclusao { get; set; }

    public DateTime DataElaboracao { get; set; }
    public DateTime? DataRevisao { get; set; }
    public DateTime? DataAprovacao { get; set; }
    public DateTime? DataPublicacao { get; set; }

    public Guid? RevisorId { get; set; }
    public virtual Usuario? Revisor { get; set; }

    public string? DocumentoUrl { get; set; }

    public bool? Favoravel { get; set; }
    public string? Recomendacao { get; set; }
}
