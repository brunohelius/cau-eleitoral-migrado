using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class ProvaJulgamento : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public string Protocolo { get; set; } = string.Empty;
    public TipoProvaJulgamento Tipo { get; set; }
    public StatusProvaJulgamento Status { get; set; }
    public int Ordem { get; set; }

    public string? Parte { get; set; }
    public DateTime DataApresentacao { get; set; }
    public DateTime? DataAnalise { get; set; }

    public string? Titulo { get; set; }
    public string? Descricao { get; set; }
    public string? Objetivo { get; set; }
    public string? Observacao { get; set; }

    public string? ParecerAdmissibilidade { get; set; }
    public string? MotivoInadmissao { get; set; }

    public Guid? AnalisadoPorId { get; set; }
    public virtual MembroComissaoJulgadora? AnalisadoPor { get; set; }

    public string? ArquivoUrl { get; set; }
    public long? TamanhoArquivo { get; set; }
    public string? TipoArquivo { get; set; }
    public string? HashArquivo { get; set; }
}
