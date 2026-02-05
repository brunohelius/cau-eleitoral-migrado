using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for data import
/// </summary>
public class ImportacaoDados : BaseEntity
{
    public Guid? EleicaoId { get; set; }
    public virtual Eleicao? Eleicao { get; set; }

    public Guid SolicitanteId { get; set; }
    public virtual Usuario Solicitante { get; set; } = null!;

    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public TipoImportacao Tipo { get; set; }
    public FormatoExportacao Formato { get; set; }
    public StatusImportacao Status { get; set; }

    public DateTime DataSolicitacao { get; set; }
    public DateTime? DataInicio { get; set; }
    public DateTime? DataConclusao { get; set; }

    public string ArquivoUrl { get; set; } = string.Empty;
    public string? ArquivoNome { get; set; }
    public long? ArquivoTamanho { get; set; }
    public string? ArquivoHash { get; set; }

    public int? TotalRegistros { get; set; }
    public int? RegistrosProcessados { get; set; }
    public int? RegistrosSucesso { get; set; }
    public int? RegistrosErro { get; set; }
    public int? RegistrosDuplicados { get; set; }

    public string? Mapeamento { get; set; }
    public string? ConfiguracaoValidacao { get; set; }

    public string? LogErros { get; set; }
    public string? LogProcessamento { get; set; }

    public string? ArquivoErrosUrl { get; set; }

    public string? MensagemErro { get; set; }

    public string? Observacoes { get; set; }
}
