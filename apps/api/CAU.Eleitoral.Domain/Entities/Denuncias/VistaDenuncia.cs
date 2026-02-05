using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class VistaDenuncia : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Denuncia Denuncia { get; set; } = null!;

    public TipoVistaDenuncia Tipo { get; set; }
    public StatusVista Status { get; set; }

    public Guid? SolicitanteId { get; set; }
    public virtual Usuario? Solicitante { get; set; }

    public string? NomeSolicitante { get; set; }
    public string? CpfSolicitante { get; set; }
    public string? OabSolicitante { get; set; }

    public string? Motivo { get; set; }
    public string? Observacao { get; set; }

    public DateTime DataSolicitacao { get; set; }
    public DateTime? DataConcessao { get; set; }
    public DateTime? DataInicio { get; set; }
    public DateTime? DataFim { get; set; }
    public int? PrazoDias { get; set; }

    public bool Prorrogada { get; set; }
    public DateTime? DataProrrogacao { get; set; }
    public int? PrazoProrrogacao { get; set; }

    public string? MotivoNegativa { get; set; }

    public Guid? AutorizadorId { get; set; }
    public virtual Usuario? Autorizador { get; set; }
}
