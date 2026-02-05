using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Core;

/// <summary>
/// Etapas específicas dentro de cada fase da eleição
/// </summary>
public class EtapaEleicao : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public FaseEleicao Fase { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public int Ordem { get; set; }
    public StatusCalendario Status { get; set; }

    public DateTime? DataPrevista { get; set; }
    public DateTime? DataInicio { get; set; }
    public DateTime? DataFim { get; set; }
    public TimeSpan? HoraInicio { get; set; }
    public TimeSpan? HoraFim { get; set; }

    public bool Obrigatoria { get; set; }
    public bool Concluida { get; set; }
    public DateTime? DataConclusao { get; set; }

    public Guid? EtapaAnteriorId { get; set; }
    public virtual EtapaEleicao? EtapaAnterior { get; set; }

    public string? ResponsavelId { get; set; }
    public string? Observacao { get; set; }

    public virtual ICollection<EtapaEleicao> EtapasPosteriores { get; set; } = new List<EtapaEleicao>();
}
