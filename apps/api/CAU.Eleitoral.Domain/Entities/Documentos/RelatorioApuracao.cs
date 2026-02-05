using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for counting reports
/// </summary>
public class RelatorioApuracao : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? ApuracaoId { get; set; }
    public virtual RegistroApuracaoVotos? Apuracao { get; set; }

    public Guid? RegionalId { get; set; }
    public virtual RegionalCAU? Regional { get; set; }

    public string Titulo { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public TipoRelatorio Tipo { get; set; }
    public StatusRelatorio Status { get; set; }

    public DateTime DataGeracao { get; set; }

    public Guid? GeradoPorId { get; set; }
    public virtual Usuario? GeradoPor { get; set; }

    public int TotalSecoesApuradas { get; set; }
    public int TotalSecoesPendentes { get; set; }
    public double PercentualApurado { get; set; }

    public int TotalVotosApurados { get; set; }
    public int TotalVotosValidos { get; set; }
    public int TotalVotosBrancos { get; set; }
    public int TotalVotosNulos { get; set; }
    public int TotalVotosAnulados { get; set; }

    public TimeSpan? TempoApuracao { get; set; }

    public string? DadosJson { get; set; }
    public string? ResumoChapas { get; set; }

    public FormatoExportacao? Formato { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }
    public long? ArquivoTamanho { get; set; }

    public string? Observacoes { get; set; }
}
