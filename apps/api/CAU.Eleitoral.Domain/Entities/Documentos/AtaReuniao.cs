using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for meeting minutes
/// </summary>
public class AtaReuniao : BaseEntity
{
    public Guid? EleicaoId { get; set; }
    public virtual Eleicao? Eleicao { get; set; }

    public string? Numero { get; set; }
    public int? Ano { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string? Conteudo { get; set; }

    public TipoAta Tipo { get; set; }
    public StatusAta Status { get; set; }

    public DateTime DataReuniao { get; set; }
    public TimeSpan? HoraInicio { get; set; }
    public TimeSpan? HoraFim { get; set; }

    public string? Local { get; set; }
    public string? Modalidade { get; set; }

    public string? OrgaoResponsavel { get; set; }
    public string? Presidente { get; set; }
    public string? Secretario { get; set; }

    public string? Pauta { get; set; }
    public string? Deliberacoes { get; set; }
    public string? Presentes { get; set; }
    public string? Ausentes { get; set; }
    public string? Justificativas { get; set; }

    public DateTime? DataAprovacao { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }

    public string? Observacoes { get; set; }

    public virtual ICollection<AssinaturaDigital> Assinaturas { get; set; } = new List<AssinaturaDigital>();
}
