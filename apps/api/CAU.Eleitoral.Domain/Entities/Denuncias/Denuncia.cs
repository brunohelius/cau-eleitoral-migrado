using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Chapas;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class Denuncia : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public string Protocolo { get; set; } = string.Empty;
    public TipoDenuncia Tipo { get; set; }
    public StatusDenuncia Status { get; set; }

    public Guid? ChapaId { get; set; }
    public virtual ChapaEleicao? Chapa { get; set; }

    public Guid? MembroId { get; set; }
    public virtual MembroChapa? Membro { get; set; }

    public Guid? DenuncianteId { get; set; }
    public virtual Profissional? Denunciante { get; set; }
    public bool Anonima { get; set; }

    public string Titulo { get; set; } = string.Empty;
    public string Descricao { get; set; } = string.Empty;
    public string? Fundamentacao { get; set; }

    public DateTime DataDenuncia { get; set; }
    public DateTime? DataRecebimento { get; set; }
    public DateTime? PrazoDefesa { get; set; }
    public DateTime? PrazoRecurso { get; set; }

    public virtual ICollection<ProvaDenuncia> Provas { get; set; } = new List<ProvaDenuncia>();
    public virtual ICollection<DefesaDenuncia> Defesas { get; set; } = new List<DefesaDenuncia>();
    public virtual AdmissibilidadeDenuncia? Admissibilidade { get; set; }
    public virtual JulgamentoDenuncia? Julgamento { get; set; }
    public virtual ICollection<RecursoDenuncia> Recursos { get; set; } = new List<RecursoDenuncia>();
    public virtual ICollection<HistoricoDenuncia> Historicos { get; set; } = new List<HistoricoDenuncia>();
    public virtual ICollection<NotificacaoDenuncia> Notificacoes { get; set; } = new List<NotificacaoDenuncia>();

    // New navigation properties
    public virtual ICollection<DenunciaChapa> DenunciaChapas { get; set; } = new List<DenunciaChapa>();
    public virtual ICollection<DenunciaMembro> DenunciaMembros { get; set; } = new List<DenunciaMembro>();
    public virtual ICollection<ArquivoDenuncia> Arquivos { get; set; } = new List<ArquivoDenuncia>();
    public virtual ICollection<AnaliseDenuncia> Analises { get; set; } = new List<AnaliseDenuncia>();
    public virtual ICollection<AlegacoesDenuncia> Alegacoes { get; set; } = new List<AlegacoesDenuncia>();
    public virtual ICollection<EncaminhamentoDenuncia> Encaminhamentos { get; set; } = new List<EncaminhamentoDenuncia>();
    public virtual ICollection<ParecerDenuncia> Pareceres { get; set; } = new List<ParecerDenuncia>();
    public virtual ICollection<DespachoDenuncia> Despachos { get; set; } = new List<DespachoDenuncia>();
    public virtual ICollection<VistaDenuncia> Vistas { get; set; } = new List<VistaDenuncia>();
    public virtual ICollection<AnexoDenuncia> Anexos { get; set; } = new List<AnexoDenuncia>();
}
