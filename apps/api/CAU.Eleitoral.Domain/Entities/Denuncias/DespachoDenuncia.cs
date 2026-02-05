using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class DespachoDenuncia : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Denuncia Denuncia { get; set; } = null!;

    public TipoDespachoDenuncia Tipo { get; set; }
    public StatusDespacho Status { get; set; }

    public Guid? AutoridadeId { get; set; }
    public virtual Usuario? Autoridade { get; set; }

    public string Numero { get; set; } = string.Empty;
    public string? Assunto { get; set; }
    public string Conteudo { get; set; } = string.Empty;
    public string? Determinacao { get; set; }

    public DateTime DataDespacho { get; set; }
    public DateTime? DataAssinatura { get; set; }
    public DateTime? DataPublicacao { get; set; }
    public DateTime? PrazoCumprimento { get; set; }

    public bool Cumprido { get; set; }
    public DateTime? DataCumprimento { get; set; }
    public string? ObservacaoCumprimento { get; set; }

    public string? DocumentoUrl { get; set; }
}
