using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Core;

/// <summary>
/// Urna eletronica para votacao presencial ou virtual
/// </summary>
public class UrnaEletronica : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid? RegiaoPleitoId { get; set; }
    public virtual RegiaoPleito? RegiaoPleito { get; set; }

    public Guid? SecaoId { get; set; }
    public virtual SecaoEleitoral? Secao { get; set; }

    public string NumeroSerie { get; set; } = string.Empty;
    public string Codigo { get; set; } = string.Empty;
    public string? Modelo { get; set; }
    public string? Versao { get; set; }

    public StatusUrna Status { get; set; }
    public TipoUrna Tipo { get; set; }

    public DateTime? DataInstalacao { get; set; }
    public DateTime? DataAtivacao { get; set; }
    public DateTime? DataDesativacao { get; set; }

    public string? HashInicial { get; set; }
    public string? HashFinal { get; set; }
    public string? ChavePublica { get; set; }

    public int TotalVotosRegistrados { get; set; }
    public int TotalVotosConfirmados { get; set; }

    public string? Ip { get; set; }
    public string? MacAddress { get; set; }
    public string? Localizacao { get; set; }

    public bool Ativo { get; set; } = true;

    public virtual ICollection<Voto> Votos { get; set; } = new List<Voto>();
}
