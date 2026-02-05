using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class AnaliseDenuncia : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Denuncia Denuncia { get; set; } = null!;

    public Guid? AnalistaId { get; set; }
    public virtual Usuario? Analista { get; set; }

    public StatusAnaliseDenuncia Status { get; set; }
    public string? Parecer { get; set; }
    public string? Fundamentacao { get; set; }
    public string? Observacao { get; set; }

    public DateTime DataInicio { get; set; }
    public DateTime? DataConclusao { get; set; }
    public DateTime? PrazoAnalise { get; set; }

    public bool? Recomendacao { get; set; }
    public string? RecomendacaoDescricao { get; set; }

    public string? DocumentoUrl { get; set; }
}
