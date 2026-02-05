using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for official publications (Diario Oficial, etc.)
/// </summary>
public class PublicacaoOficial : BaseEntity
{
    public Guid? EleicaoId { get; set; }
    public virtual Eleicao? Eleicao { get; set; }

    public Guid? DocumentoId { get; set; }
    public virtual Documento? Documento { get; set; }

    public TipoPublicacaoOficial Tipo { get; set; }
    public StatusPublicacaoOficial Status { get; set; }

    public string Titulo { get; set; } = string.Empty;
    public string? Ementa { get; set; }
    public string? Conteudo { get; set; }

    public string? NumeroPublicacao { get; set; }
    public int? AnoPublicacao { get; set; }

    public DateTime? DataAgendada { get; set; }
    public DateTime? DataPublicacao { get; set; }
    public DateTime? DataConfirmacao { get; set; }

    public string? VeiculoPublicacao { get; set; }
    public string? Edicao { get; set; }
    public string? Secao { get; set; }
    public string? Pagina { get; set; }

    public string? LinkPublicacao { get; set; }
    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }

    public string? Observacoes { get; set; }
}
