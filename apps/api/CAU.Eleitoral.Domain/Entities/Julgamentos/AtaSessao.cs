using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class AtaSessao : BaseEntity
{
    public Guid SessaoId { get; set; }
    public virtual SessaoJulgamento Sessao { get; set; } = null!;

    public string Numero { get; set; } = string.Empty;
    public string? Conteudo { get; set; }

    public DateTime? DataAprovacao { get; set; }
    public bool Aprovada { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? AssinaturaUrl { get; set; }

    public DateTime? DataPublicacao { get; set; }
    public bool Publicada { get; set; }
}
