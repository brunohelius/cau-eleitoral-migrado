using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class ComissaoJulgadora : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public string? Sigla { get; set; }

    public string? Portaria { get; set; }
    public DateTime? DataPortaria { get; set; }
    public DateTime? DataInicio { get; set; }
    public DateTime? DataFim { get; set; }

    public bool Ativa { get; set; } = true;

    public virtual ICollection<MembroComissaoJulgadora> Membros { get; set; } = new List<MembroComissaoJulgadora>();
    public virtual ICollection<SessaoJulgamento> Sessoes { get; set; } = new List<SessaoJulgamento>();
}
