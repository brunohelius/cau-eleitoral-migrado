using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class IntimacaoJulgamento : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public string Protocolo { get; set; } = string.Empty;
    public StatusNotificacaoJulgamento Status { get; set; }
    public FormaNotificacao Forma { get; set; }

    public string Intimado { get; set; } = string.Empty;
    public string? QualificacaoIntimado { get; set; }
    public string? EmailIntimado { get; set; }
    public string? EnderecoIntimado { get; set; }

    public string? Finalidade { get; set; }
    public string? Conteudo { get; set; }
    public string? FundamentoLegal { get; set; }

    public DateTime DataExpedicao { get; set; }
    public DateTime? DataCiencia { get; set; }
    public DateTime DataPrazo { get; set; }
    public int PrazoDias { get; set; }

    public bool PrazoUtil { get; set; }
    public DateTime? DataInicioContagem { get; set; }
    public DateTime? DataFimContagem { get; set; }

    public string? ComprovanteUrl { get; set; }
    public string? ArquivoUrl { get; set; }

    public bool Cumprida { get; set; }
    public DateTime? DataCumprimento { get; set; }
    public string? ObservacaoCumprimento { get; set; }
}
