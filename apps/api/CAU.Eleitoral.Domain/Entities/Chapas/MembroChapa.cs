using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Chapas;

public class MembroChapa : BaseEntity
{
    public Guid ChapaId { get; set; }
    public virtual ChapaEleicao Chapa { get; set; } = null!;

    public Guid? ProfissionalId { get; set; }
    public virtual Profissional? Profissional { get; set; }

    public string Nome { get; set; } = string.Empty;
    public string? Cpf { get; set; }
    public string? RegistroCAU { get; set; }
    public string? Email { get; set; }
    public string? Telefone { get; set; }

    public TipoMembroChapa Tipo { get; set; }
    public StatusMembroChapa Status { get; set; }
    public int Ordem { get; set; }

    public string? Cargo { get; set; }
    public bool Titular { get; set; }

    public DateTime? DataConfirmacao { get; set; }
    public string? TokenConfirmacao { get; set; }
    public DateTime? TokenConfirmacaoExpiracao { get; set; }

    public string? MotivoRecusa { get; set; }
    public string? MotivoInabilitacao { get; set; }

    public string? FotoUrl { get; set; }
    public string? CurriculoResumo { get; set; }

    // Substituicao
    public Guid? SubstituidoPorId { get; set; }
    public virtual MembroChapa? SubstituidoPor { get; set; }
    public virtual ICollection<MembroChapa> SubstituidosDe { get; set; } = new List<MembroChapa>();
    public DateTime? DataSubstituicao { get; set; }
    public string? MotivoSubstituicao { get; set; }

    public virtual ICollection<ConfirmacaoMembroChapa> Confirmacoes { get; set; } = new List<ConfirmacaoMembroChapa>();
}
