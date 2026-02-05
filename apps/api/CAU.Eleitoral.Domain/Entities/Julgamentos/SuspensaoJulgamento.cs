using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class SuspensaoJulgamento : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public string Protocolo { get; set; } = string.Empty;
    public TipoSuspensao Tipo { get; set; }
    public MotivoSuspensao Motivo { get; set; }

    public Guid? DeterminadaPorId { get; set; }
    public virtual MembroComissaoJulgadora? DeterminadaPor { get; set; }

    public DateTime DataInicio { get; set; }
    public DateTime? DataPrevistaRetorno { get; set; }
    public DateTime? DataRetorno { get; set; }

    public string? Fundamentacao { get; set; }
    public string? MotivoDetalhado { get; set; }
    public string? CondicaoRetorno { get; set; }

    public bool Ativa { get; set; }
    public string? MotivoEncerramento { get; set; }

    public int? DiligenciaRelacionadaId { get; set; }
    public string? ProcessoRelacionado { get; set; }

    public string? ArquivoDecisaoUrl { get; set; }
    public string? ArquivoRetomadaUrl { get; set; }
}
