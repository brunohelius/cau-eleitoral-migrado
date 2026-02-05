using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Core;

/// <summary>
/// Região de pleito para organização da votação
/// </summary>
public class RegiaoPleito : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? RegionalId { get; set; }
    public virtual RegionalCAU? Regional { get; set; }

    public Guid? CircunscricaoId { get; set; }
    public virtual Circunscricao? Circunscricao { get; set; }

    public string Codigo { get; set; } = string.Empty;
    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public string? Abrangencia { get; set; }
    public string? UFs { get; set; }
    public string? Municipios { get; set; }

    public int? QuantidadeEleitores { get; set; }
    public int? QuantidadeVagas { get; set; }
    public int? QuantidadeSuplentes { get; set; }

    public bool Ativo { get; set; } = true;

    public virtual ICollection<MesaReceptora> MesasReceptoras { get; set; } = new List<MesaReceptora>();
    public virtual ICollection<UrnaEletronica> Urnas { get; set; } = new List<UrnaEletronica>();
}
