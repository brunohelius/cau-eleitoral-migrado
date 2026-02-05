using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Chapas;

public class ChapaEleicao : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public string Numero { get; set; } = string.Empty;
    public string Nome { get; set; } = string.Empty;
    public string? Slogan { get; set; }
    public string? Sigla { get; set; }

    public StatusChapa Status { get; set; }
    public DateTime DataInscricao { get; set; }
    public DateTime? DataHomologacao { get; set; }
    public DateTime? DataIndeferimento { get; set; }
    public string? MotivoIndeferimento { get; set; }

    public string? LogoUrl { get; set; }
    public string? FotoUrl { get; set; }
    public string? CorPrimaria { get; set; }
    public string? CorSecundaria { get; set; }

    public int OrdemSorteio { get; set; }

    public virtual ICollection<MembroChapa> Membros { get; set; } = new List<MembroChapa>();
    public virtual ICollection<DocumentoChapa> Documentos { get; set; } = new List<DocumentoChapa>();
    public virtual ICollection<HistoricoChapaEleicao> Historicos { get; set; } = new List<HistoricoChapaEleicao>();
    public virtual PlataformaEleitoral? Plataforma { get; set; }
}
