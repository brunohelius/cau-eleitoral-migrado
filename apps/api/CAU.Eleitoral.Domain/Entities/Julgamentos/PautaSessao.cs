using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class PautaSessao : BaseEntity
{
    public Guid SessaoId { get; set; }
    public virtual SessaoJulgamento Sessao { get; set; } = null!;

    public int Ordem { get; set; }
    public TipoJulgamento TipoProcesso { get; set; }
    public string NumeroProcesso { get; set; } = string.Empty;

    public string? Partes { get; set; }
    public string? Assunto { get; set; }

    public Guid? RelatorId { get; set; }
    public virtual MembroComissaoJulgadora? Relator { get; set; }

    public bool Julgado { get; set; }
    public bool Adiado { get; set; }
    public string? MotivoAdiamento { get; set; }

    public string? Observacao { get; set; }
}
