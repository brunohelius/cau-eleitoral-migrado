using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Usuarios;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for declarations
/// </summary>
public class Declaracao : BaseEntity
{
    public Guid? EleicaoId { get; set; }
    public virtual Eleicao? Eleicao { get; set; }

    public Guid? UsuarioId { get; set; }
    public virtual Usuario? Usuario { get; set; }

    public string? Numero { get; set; }
    public int? Ano { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string? Conteudo { get; set; }

    public TipoDeclaracao Tipo { get; set; }
    public StatusDocumento Status { get; set; }

    public string? Finalidade { get; set; }
    public string? Destinatario { get; set; }

    public DateTime DataDocumento { get; set; }
    public DateTime? DataExpedicao { get; set; }
    public DateTime? DataValidade { get; set; }

    public string? LocalExpedicao { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }

    public string? CodigoVerificacao { get; set; }

    public string? Observacoes { get; set; }

    public virtual ICollection<AssinaturaDigital> Assinaturas { get; set; } = new List<AssinaturaDigital>();
}
