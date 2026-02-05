using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Chapas;

public class PlataformaEleitoral : BaseEntity
{
    public Guid ChapaId { get; set; }
    public virtual ChapaEleicao Chapa { get; set; } = null!;

    public string Titulo { get; set; } = string.Empty;
    public string? Resumo { get; set; }
    public string? Conteudo { get; set; }

    public string? Missao { get; set; }
    public string? Visao { get; set; }
    public string? Valores { get; set; }

    public string? PropostasJson { get; set; }
    public string? MetasJson { get; set; }
    public string? EixosJson { get; set; }

    public string? VideoUrl { get; set; }
    public string? ApresentacaoUrl { get; set; }
    public string? ArquivoUrl { get; set; }
    public string? ConteudoCompleto { get; set; }

    public DateTime DataPublicacao { get; set; }
    public DateTime? DataAtualizacao { get; set; }
    public bool Publicada { get; set; }
}
