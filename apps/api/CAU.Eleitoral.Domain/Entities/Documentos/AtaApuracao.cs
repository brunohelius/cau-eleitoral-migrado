using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for counting minutes
/// </summary>
public class AtaApuracao : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? ResultadoId { get; set; }
    public virtual ResultadoEleicao? Resultado { get; set; }

    public string Numero { get; set; } = string.Empty;
    public int Ano { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string? Conteudo { get; set; }

    public StatusAta Status { get; set; }

    public DateTime DataApuracao { get; set; }
    public TimeSpan? HoraInicio { get; set; }
    public TimeSpan? HoraFim { get; set; }

    public string? Local { get; set; }

    public string? ComissaoApuradora { get; set; }
    public string? Presidente { get; set; }
    public string? Secretario { get; set; }
    public string? Membros { get; set; }

    public int TotalUrnas { get; set; }
    public int TotalVotosApurados { get; set; }
    public int TotalVotosValidos { get; set; }
    public int TotalVotosBrancos { get; set; }
    public int TotalVotosNulos { get; set; }

    public string? Ocorrencias { get; set; }
    public string? Impugnacoes { get; set; }
    public string? Decisoes { get; set; }

    public DateTime? DataHomologacao { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }

    public string? Observacoes { get; set; }

    public virtual ICollection<AssinaturaDigital> Assinaturas { get; set; } = new List<AssinaturaDigital>();
}
