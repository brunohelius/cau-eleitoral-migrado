using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Chapas;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Impugnacoes;

public class ImpugnacaoResultado : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public string Protocolo { get; set; } = string.Empty;
    public TipoImpugnacao Tipo { get; set; }
    public StatusImpugnacao Status { get; set; }

    public Guid? ChapaImpugnanteId { get; set; }
    public virtual ChapaEleicao? ChapaImpugnante { get; set; }

    public Guid? ChapaImpugnadaId { get; set; }
    public virtual ChapaEleicao? ChapaImpugnada { get; set; }

    public Guid? MembroImpugnadoId { get; set; }
    public virtual MembroChapa? MembroImpugnado { get; set; }

    public Guid? ImpugnanteId { get; set; }
    public virtual Profissional? Impugnante { get; set; }

    public string Titulo { get; set; } = string.Empty;
    public string Descricao { get; set; } = string.Empty;
    public string? Fundamentacao { get; set; }

    public DateTime DataImpugnacao { get; set; }
    public DateTime? DataRecebimento { get; set; }
    public DateTime? PrazoAlegacoes { get; set; }
    public DateTime? PrazoContraAlegacoes { get; set; }

    public virtual ICollection<PedidoImpugnacao> Pedidos { get; set; } = new List<PedidoImpugnacao>();
    public virtual ICollection<AlegacaoImpugnacaoResultado> Alegacoes { get; set; } = new List<AlegacaoImpugnacaoResultado>();
    public virtual ICollection<DefesaImpugnacao> Defesas { get; set; } = new List<DefesaImpugnacao>();
    public virtual JulgamentoImpugnacao? Julgamento { get; set; }
    public virtual ICollection<RecursoImpugnacao> Recursos { get; set; } = new List<RecursoImpugnacao>();
    public virtual ICollection<HistoricoImpugnacao> Historicos { get; set; } = new List<HistoricoImpugnacao>();
}
