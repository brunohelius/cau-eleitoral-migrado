using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Chapas;

namespace CAU.Eleitoral.Domain.Entities.Core;

public class Eleicao : BaseEntity
{
    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public TipoEleicao Tipo { get; set; }
    public StatusEleicao Status { get; set; }
    public FaseEleicao FaseAtual { get; set; }

    public int Ano { get; set; }
    public int? Mandato { get; set; }

    public DateTime DataInicio { get; set; }
    public DateTime DataFim { get; set; }
    public DateTime? DataVotacaoInicio { get; set; }
    public DateTime? DataVotacaoFim { get; set; }
    public DateTime? DataApuracao { get; set; }

    public Guid? RegionalId { get; set; }
    public virtual RegionalCAU? Regional { get; set; }

    public ModoVotacao ModoVotacao { get; set; }
    public bool PermiteVotoOnline { get; set; }
    public bool PermiteVotoPresencial { get; set; }

    public int? QuantidadeVagas { get; set; }
    public int? QuantidadeSuplentes { get; set; }

    public virtual ICollection<Calendario> Calendarios { get; set; } = new List<Calendario>();
    public virtual ICollection<ChapaEleicao> Chapas { get; set; } = new List<ChapaEleicao>();
    public virtual ICollection<ConfiguracaoEleicao> Configuracoes { get; set; } = new List<ConfiguracaoEleicao>();
    public virtual ICollection<EleicaoSituacao> HistoricoSituacoes { get; set; } = new List<EleicaoSituacao>();
    public virtual ICollection<EtapaEleicao> Etapas { get; set; } = new List<EtapaEleicao>();
    public virtual ICollection<RegiaoPleito> RegioesPleito { get; set; } = new List<RegiaoPleito>();
    public virtual ICollection<UrnaEletronica> Urnas { get; set; } = new List<UrnaEletronica>();
    public virtual ICollection<MesaReceptora> MesasReceptoras { get; set; } = new List<MesaReceptora>();
    public virtual ICollection<FiscalEleicao> Fiscais { get; set; } = new List<FiscalEleicao>();
    public virtual ICollection<ApuracaoResultado> Apuracoes { get; set; } = new List<ApuracaoResultado>();
}
