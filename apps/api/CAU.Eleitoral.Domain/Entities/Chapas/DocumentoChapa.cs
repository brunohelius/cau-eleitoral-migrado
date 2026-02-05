using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Chapas;

public class DocumentoChapa : BaseEntity
{
    public Guid ChapaId { get; set; }
    public virtual ChapaEleicao Chapa { get; set; } = null!;

    public Guid? MembroId { get; set; }
    public virtual MembroChapa? Membro { get; set; }

    public TipoDocumentoChapa Tipo { get; set; }
    public StatusDocumentoChapa Status { get; set; }

    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public string ArquivoUrl { get; set; } = string.Empty;
    public string? ArquivoNome { get; set; }
    public string? ArquivoTipo { get; set; }
    public long? ArquivoTamanho { get; set; }

    public DateTime DataEnvio { get; set; }
    public DateTime? DataAnalise { get; set; }
    public string? AnalisadoPor { get; set; }
    public string? MotivoRejeicao { get; set; }

    public bool Obrigatorio { get; set; }
    public int Ordem { get; set; }
}
