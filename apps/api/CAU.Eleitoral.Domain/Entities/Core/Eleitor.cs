using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Core;

public class Eleitor : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid ProfissionalId { get; set; }
    public virtual Profissional Profissional { get; set; } = null!;

    public string? NumeroInscricao { get; set; }

    public bool Apto { get; set; }
    public string? MotivoInaptidao { get; set; }

    public bool Votou { get; set; }
    public DateTime? DataVoto { get; set; }

    public Guid? SecaoId { get; set; }
    public virtual SecaoEleitoral? Secao { get; set; }

    public string? ComprovanteVotacao { get; set; }
}
