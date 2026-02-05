using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Usuarios;

public class LogAcesso : BaseEntity
{
    public Guid UsuarioId { get; set; }
    public virtual Usuario Usuario { get; set; } = null!;

    public string Acao { get; set; } = string.Empty;
    public string? Recurso { get; set; }
    public string? Metodo { get; set; }
    public string? Url { get; set; }

    public string? IpAddress { get; set; }
    public string? UserAgent { get; set; }

    public DateTime DataAcesso { get; set; }
    public bool Sucesso { get; set; }
    public string? Mensagem { get; set; }

    public string? DadosRequisicao { get; set; }
    public string? DadosResposta { get; set; }
}
