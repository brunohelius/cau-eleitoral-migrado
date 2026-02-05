using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for data export
/// </summary>
public class ExportacaoDados : BaseEntity
{
    public Guid? EleicaoId { get; set; }
    public virtual Eleicao? Eleicao { get; set; }

    public Guid SolicitanteId { get; set; }
    public virtual Usuario Solicitante { get; set; } = null!;

    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public TipoExportacao Tipo { get; set; }
    public FormatoExportacao Formato { get; set; }
    public StatusExportacao Status { get; set; }

    public DateTime DataSolicitacao { get; set; }
    public DateTime? DataInicio { get; set; }
    public DateTime? DataConclusao { get; set; }
    public DateTime? DataExpiracao { get; set; }

    public string? Filtros { get; set; }
    public string? Campos { get; set; }

    public int? TotalRegistros { get; set; }
    public int? RegistrosExportados { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }
    public long? ArquivoTamanho { get; set; }
    public string? ArquivoHash { get; set; }

    public string? MensagemErro { get; set; }

    public int DownloadsRealizados { get; set; }
    public int? LimiteDownloads { get; set; }

    public string? Observacoes { get; set; }
}
