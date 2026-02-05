using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Chapas;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class DefesaDenuncia : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Denuncia Denuncia { get; set; } = null!;

    public Guid? ChapaId { get; set; }
    public virtual ChapaEleicao? Chapa { get; set; }

    public Guid? MembroId { get; set; }
    public virtual MembroChapa? Membro { get; set; }

    public StatusDefesa Status { get; set; }
    public string Conteudo { get; set; } = string.Empty;
    public string? Fundamentacao { get; set; }

    public DateTime DataApresentacao { get; set; }
    public DateTime PrazoLimite { get; set; }
    public bool Tempestiva { get; set; }

    public virtual ICollection<ArquivoDefesa> Arquivos { get; set; } = new List<ArquivoDefesa>();
}
