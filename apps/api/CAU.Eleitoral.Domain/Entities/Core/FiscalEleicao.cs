using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Usuarios;
using CAU.Eleitoral.Domain.Entities.Chapas;

namespace CAU.Eleitoral.Domain.Entities.Core;

/// <summary>
/// Fiscal de eleicao designado para acompanhar a votacao
/// </summary>
public class FiscalEleicao : BaseEntity
{
    public Guid EleicaoId { get; set; }
    public virtual Eleicao Eleicao { get; set; } = null!;

    public Guid ProfissionalId { get; set; }
    public virtual Profissional Profissional { get; set; } = null!;

    public Guid? ChapaId { get; set; }
    public virtual ChapaEleicao? Chapa { get; set; }

    public Guid? MesaReceptoraId { get; set; }
    public virtual MesaReceptora? MesaReceptora { get; set; }

    public TipoFiscal Tipo { get; set; }
    public StatusFiscal Status { get; set; }

    public string? NumeroCredencial { get; set; }
    public DateTime? DataCredenciamento { get; set; }
    public DateTime? DataValidadeCredencial { get; set; }

    public string? Funcao { get; set; }
    public string? Turno { get; set; }

    public DateTime? DataInicioAtividade { get; set; }
    public DateTime? DataFimAtividade { get; set; }

    public string? Observacao { get; set; }
    public string? MotivoCancelamento { get; set; }
    public DateTime? DataCancelamento { get; set; }

    public string? CredenciadoPor { get; set; }
    public string? CanceladoPor { get; set; }
}
