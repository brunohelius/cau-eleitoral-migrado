using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Core;

public class Notificacao : BaseEntity
{
    public Guid UsuarioId { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string Mensagem { get; set; } = string.Empty;
    public string Tipo { get; set; } = "Sistema"; // Sistema, Eleicao, Votacao, Resultado, Denuncia, etc.
    public string Canal { get; set; } = "InApp"; // InApp, Email, SMS, Push
    public string Status { get; set; } = "Enviada"; // Pendente, Enviada, Entregue, Falha
    public bool Lida { get; set; }
    public DateTime? DataLeitura { get; set; }
    public DateTime DataEnvio { get; set; }
    public string? Link { get; set; }
    public string? Dados { get; set; }
}
