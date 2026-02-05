using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class AlegacoesDenuncia : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Denuncia Denuncia { get; set; } = null!;

    public TipoAlegacaoDenuncia Tipo { get; set; }
    public string Descricao { get; set; } = string.Empty;
    public string? Fundamentacao { get; set; }

    public DateTime DataApresentacao { get; set; }
    public DateTime? PrazoLimite { get; set; }
    public bool Tempestiva { get; set; }

    public int Ordem { get; set; }

    public virtual ICollection<ContraAlegacoesDenuncia> ContraAlegacoes { get; set; } = new List<ContraAlegacoesDenuncia>();
}
