using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class EmendaJulgamento : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public Guid? SessaoId { get; set; }
    public virtual SessaoJulgamento? Sessao { get; set; }

    public string Numero { get; set; } = string.Empty;
    public TipoEmenda Tipo { get; set; }
    public StatusEmenda Status { get; set; }
    public int Ordem { get; set; }

    public Guid ProponenteId { get; set; }
    public virtual MembroComissaoJulgadora Proponente { get; set; } = null!;

    public DateTime DataApresentacao { get; set; }
    public DateTime? DataVotacao { get; set; }

    public string? Titulo { get; set; }
    public string? TextoOriginal { get; set; }
    public string? TextoProposto { get; set; }
    public string? Justificativa { get; set; }
    public string? Fundamentacao { get; set; }

    public int VotosFavoraveis { get; set; }
    public int VotosContrarios { get; set; }
    public int Abstencoes { get; set; }

    public string? ResultadoVotacao { get; set; }
    public string? Observacao { get; set; }

    public string? ArquivoUrl { get; set; }

    public virtual ICollection<VotoEmenda> Votos { get; set; } = new List<VotoEmenda>();
}
