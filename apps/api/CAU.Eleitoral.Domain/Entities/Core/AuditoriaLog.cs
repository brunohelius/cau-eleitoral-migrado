using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Core;

public class AuditoriaLog : BaseEntity
{
    public Guid? UsuarioId { get; set; }
    public string? UsuarioNome { get; set; }
    public string? UsuarioEmail { get; set; }
    public string Acao { get; set; } = string.Empty;
    public string EntidadeTipo { get; set; } = string.Empty;
    public Guid? EntidadeId { get; set; }
    public string? EntidadeNome { get; set; }
    public string? Detalhes { get; set; }
    public string? ValorAnterior { get; set; }
    public string? ValorNovo { get; set; }
    public string? IpAddress { get; set; }
    public string? UserAgent { get; set; }
    public string? Recurso { get; set; }
    public string? Metodo { get; set; }
    public int? StatusCode { get; set; }
    public bool Sucesso { get; set; } = true;
    public string? Mensagem { get; set; }
    public string Nivel { get; set; } = "info"; // info, warning, error, success
    public DateTime DataAcao { get; set; }
}
