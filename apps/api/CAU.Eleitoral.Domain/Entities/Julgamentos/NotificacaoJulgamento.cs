using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class NotificacaoJulgamento : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public string Protocolo { get; set; } = string.Empty;
    public TipoNotificacaoJulgamento Tipo { get; set; }
    public StatusNotificacaoJulgamento Status { get; set; }
    public FormaNotificacao Forma { get; set; }

    public string Destinatario { get; set; } = string.Empty;
    public string? EmailDestinatario { get; set; }
    public string? EnderecoDestinatario { get; set; }
    public string? TelefoneDestinatario { get; set; }

    public string? Assunto { get; set; }
    public string? Conteudo { get; set; }
    public string? Observacao { get; set; }

    public DateTime DataEmissao { get; set; }
    public DateTime? DataEnvio { get; set; }
    public DateTime? DataRecebimento { get; set; }
    public DateTime? DataPrazo { get; set; }

    public string? NumeroAR { get; set; }
    public string? ComprovanteRecebimento { get; set; }
    public string? MotivoNaoRecebimento { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ComprovanteUrl { get; set; }

    public int TentativasEnvio { get; set; }
    public DateTime? UltimaTentativa { get; set; }
}
