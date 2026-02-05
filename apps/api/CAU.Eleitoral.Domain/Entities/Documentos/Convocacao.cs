using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for summons
/// </summary>
public class Convocacao : BaseEntity
{
    public Guid? EleicaoId { get; set; }
    public virtual Eleicao? Eleicao { get; set; }

    public string? Numero { get; set; }
    public int? Ano { get; set; }
    public string Titulo { get; set; } = string.Empty;
    public string? Assunto { get; set; }
    public string Conteudo { get; set; } = string.Empty;

    public TipoConvocacao Tipo { get; set; }
    public StatusConvocacao Status { get; set; }

    public DateTime DataDocumento { get; set; }
    public DateTime? DataPublicacao { get; set; }
    public DateTime DataEvento { get; set; }
    public TimeSpan? HoraEvento { get; set; }

    public string? Local { get; set; }
    public string? Endereco { get; set; }
    public string? LinkOnline { get; set; }

    public string? Pauta { get; set; }
    public string? Convocados { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }

    public string? Observacoes { get; set; }

    public virtual ICollection<PublicacaoOficial> Publicacoes { get; set; } = new List<PublicacaoOficial>();
}
