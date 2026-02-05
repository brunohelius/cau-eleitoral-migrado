using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class ArquivamentoJulgamento : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public string Protocolo { get; set; } = string.Empty;
    public MotivoArquivamento Motivo { get; set; }
    public StatusArquivamento Status { get; set; }

    public Guid? SolicitadoPorId { get; set; }
    public virtual MembroComissaoJulgadora? SolicitadoPor { get; set; }

    public DateTime DataSolicitacao { get; set; }
    public DateTime? DataAnalise { get; set; }
    public DateTime? DataArquivamento { get; set; }

    public string? Justificativa { get; set; }
    public string? FundamentoLegal { get; set; }
    public string? ParecerAnalise { get; set; }
    public string? MotivoIndeferimento { get; set; }

    public Guid? AnalisadoPorId { get; set; }
    public virtual MembroComissaoJulgadora? AnalisadoPor { get; set; }

    public bool Definitivo { get; set; }
    public bool Reativavel { get; set; }
    public DateTime? PrazoReativacao { get; set; }
    public string? CondicoesReativacao { get; set; }

    public bool Reativado { get; set; }
    public DateTime? DataReativacao { get; set; }
    public string? MotivoReativacao { get; set; }

    public string? ArquivoSolicitacaoUrl { get; set; }
    public string? ArquivoDecisaoUrl { get; set; }

    public string? LocalArquivamentoFisico { get; set; }
    public string? NumeroArquivamento { get; set; }
}
