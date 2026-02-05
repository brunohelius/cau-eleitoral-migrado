using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Chapas;

public class SubstituicaoMembroChapa : BaseEntity
{
    public Guid ChapaId { get; set; }
    public virtual ChapaEleicao Chapa { get; set; } = null!;

    public Guid MembroAntigoId { get; set; }
    public virtual MembroChapa MembroAntigo { get; set; } = null!;

    public Guid MembroNovoId { get; set; }
    public virtual MembroChapa MembroNovo { get; set; } = null!;

    public string Motivo { get; set; } = string.Empty;
    public DateTime DataSubstituicao { get; set; }

    public bool Aprovada { get; set; }
    public DateTime? DataAprovacao { get; set; }
    public string? AprovadoPor { get; set; }
    public string? MotivoReprovacao { get; set; }
}
