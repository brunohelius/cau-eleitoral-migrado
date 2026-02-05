using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class EncaminhamentoDenuncia : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Denuncia Denuncia { get; set; } = null!;

    public TipoEncaminhamento Tipo { get; set; }
    public StatusEncaminhamento Status { get; set; }

    public Guid? RemetenteId { get; set; }
    public virtual Usuario? Remetente { get; set; }

    public Guid? DestinatarioId { get; set; }
    public virtual Usuario? Destinatario { get; set; }

    public string? SetorDestino { get; set; }
    public string? Motivo { get; set; }
    public string? Observacao { get; set; }

    public DateTime DataEncaminhamento { get; set; }
    public DateTime? DataRecebimento { get; set; }
    public DateTime? PrazoResposta { get; set; }

    public string? Despacho { get; set; }
    public string? Resposta { get; set; }
    public DateTime? DataResposta { get; set; }
}
