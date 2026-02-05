using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for detailed vote counting document/record (different from Core.ApuracaoResultado)
/// Represents the documentary record of vote counting for audit purposes
/// </summary>
public class RegistroApuracaoVotos : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? SecaoId { get; set; }
    public virtual SecaoEleitoral? Secao { get; set; }

    public Guid? ZonaId { get; set; }
    public virtual ZonaEleitoral? Zona { get; set; }

    public Guid? RegionalId { get; set; }
    public virtual RegionalCAU? Regional { get; set; }

    public TipoApuracao Tipo { get; set; }
    public StatusApuracao Status { get; set; }

    public int NumeroSequencial { get; set; }

    public DateTime DataInicio { get; set; }
    public DateTime? DataFim { get; set; }
    public TimeSpan? Duracao { get; set; }

    public string? ResponsavelId { get; set; }
    public string? EquipeApuracao { get; set; }

    public int TotalVotosApurados { get; set; }
    public int TotalVotosValidos { get; set; }
    public int TotalVotosBrancos { get; set; }
    public int TotalVotosNulos { get; set; }
    public int TotalVotosAnulados { get; set; }

    public int? DiscrepanciasEncontradas { get; set; }
    public string? DescricaoDiscrepancias { get; set; }

    public bool Auditada { get; set; }
    public DateTime? DataAuditoria { get; set; }
    public string? ResultadoAuditoria { get; set; }

    public string? HashVerificacao { get; set; }

    public string? Observacoes { get; set; }

    public virtual ICollection<TotalVotos> TotaisVotos { get; set; } = new List<TotalVotos>();
}
