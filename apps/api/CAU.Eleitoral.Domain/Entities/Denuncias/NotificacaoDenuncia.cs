using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Denuncias;

public class NotificacaoDenuncia : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Denuncia Denuncia { get; set; } = null!;

    public string Tipo { get; set; } = string.Empty;
    public string Destinatario { get; set; } = string.Empty;
    public string? Email { get; set; }

    public string Assunto { get; set; } = string.Empty;
    public string Conteudo { get; set; } = string.Empty;

    public DateTime DataEnvio { get; set; }
    public bool Enviada { get; set; }
    public DateTime? DataLeitura { get; set; }

    public string? Erro { get; set; }
    public int TentativasEnvio { get; set; }
}
