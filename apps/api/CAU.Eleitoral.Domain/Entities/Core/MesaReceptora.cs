using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Core;

/// <summary>
/// Mesa receptora de votos (ponto de votacao presencial)
/// </summary>
public class MesaReceptora : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? RegiaoPleitoId { get; set; }
    public virtual RegiaoPleito? RegiaoPleito { get; set; }

    public Guid? SecaoId { get; set; }
    public virtual SecaoEleitoral? Secao { get; set; }

    public string Numero { get; set; } = string.Empty;
    public string Nome { get; set; } = string.Empty;
    public string? Local { get; set; }
    public string? Endereco { get; set; }
    public string? Sala { get; set; }

    public StatusMesa Status { get; set; }

    public DateTime? DataInstalacao { get; set; }
    public DateTime? DataAbertura { get; set; }
    public DateTime? DataEncerramento { get; set; }

    public TimeSpan? HoraAbertura { get; set; }
    public TimeSpan? HoraEncerramento { get; set; }

    public int? CapacidadeEleitores { get; set; }
    public int TotalEleitoresVotaram { get; set; }

    public Guid? PresidenteId { get; set; }
    public virtual Usuario? Presidente { get; set; }

    public Guid? SecretarioId { get; set; }
    public virtual Usuario? Secretario { get; set; }

    public Guid? UrnaId { get; set; }
    public virtual UrnaEletronica? Urna { get; set; }

    public bool Acessivel { get; set; }
    public string? Observacao { get; set; }
    public bool Ativo { get; set; } = true;

    public virtual ICollection<FiscalEleicao> Fiscais { get; set; } = new List<FiscalEleicao>();
}
